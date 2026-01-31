<?php
// app/models/Voting.php
require_once '../core/Database.php';

class Voting {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Get all RAT agendas
     */
    public function getRATAgendas($limit = 20, $offset = 0) {
        $stmt = $this->db->prepare("
            SELECT ra.*, u.username as creator_name
            FROM rat_agenda ra
            LEFT JOIN users u ON ra.created_by = u.id
            ORDER BY ra.tahun DESC, ra.tanggal_mulai DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get RAT agenda by ID
     */
    public function getRATAgenda($id) {
        $stmt = $this->db->prepare("
            SELECT ra.*, u.username as creator_name
            FROM rat_agenda ra
            LEFT JOIN users u ON ra.created_by = u.id
            WHERE ra.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Create new RAT agenda
     */
    public function createRATAgenda($data) {
        $stmt = $this->db->prepare("
            INSERT INTO rat_agenda
            (tahun, judul, deskripsi, tanggal_mulai, tanggal_selesai, status, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "ssssssi",
            $data['tahun'],
            $data['judul'],
            $data['deskripsi'],
            $data['tanggal_mulai'],
            $data['tanggal_selesai'],
            $data['status'] ?? 'draft',
            $data['created_by']
        );
        return $stmt->execute() ? $this->db->insert_id : false;
    }

    /**
     * Update RAT agenda
     */
    public function updateRATAgenda($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE rat_agenda SET
                judul = ?, deskripsi = ?, tanggal_mulai = ?,
                tanggal_selesai = ?, status = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        $stmt->bind_param(
            "sssssi",
            $data['judul'],
            $data['deskripsi'],
            $data['tanggal_mulai'],
            $data['tanggal_selesai'],
            $data['status'],
            $id
        );
        return $stmt->execute();
    }

    /**
     * Get voting topics for a RAT agenda
     */
    public function getVotingTopics($rat_agenda_id, $include_results = false) {
        $topics = [];

        $stmt = $this->db->prepare("
            SELECT * FROM voting_topics
            WHERE rat_agenda_id = ?
            ORDER BY created_at ASC
        ");
        $stmt->bind_param("i", $rat_agenda_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($topic = $result->fetch_assoc()) {
            $topic['options'] = json_decode($topic['options'], true);

            if ($include_results) {
                $topic['results'] = $this->getVotingResults($topic['id']);
                $topic['total_votes'] = $this->countVotes($topic['id']);
            }

            $topics[] = $topic;
        }

        return $topics;
    }

    /**
     * Create voting topic
     */
    public function createVotingTopic($data) {
        $options_json = json_encode($data['options']);

        $stmt = $this->db->prepare("
            INSERT INTO voting_topics
            (rat_agenda_id, judul, deskripsi, tipe, options, required_quorum, voting_start, voting_end)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "issssiss",
            $data['rat_agenda_id'],
            $data['judul'],
            $data['deskripsi'],
            $data['tipe'],
            $options_json,
            $data['required_quorum'] ?? 0,
            $data['voting_start'],
            $data['voting_end']
        );
        return $stmt->execute() ? $this->db->insert_id : false;
    }

    /**
     * Submit vote
     */
    public function submitVote($voting_topic_id, $anggota_id, $pilihan) {
        // Check if voting is still active
        $topic = $this->getVotingTopic($voting_topic_id);
        if (!$topic || !$topic['is_active']) {
            return ['success' => false, 'message' => 'Voting tidak aktif'];
        }

        // Check if voting period is valid
        $now = date('Y-m-d H:i:s');
        if ($now < $topic['voting_start'] || $now > $topic['voting_end']) {
            return ['success' => false, 'message' => 'Voting belum dimulai atau sudah berakhir'];
        }

        // Check if member already voted
        if ($this->hasVoted($voting_topic_id, $anggota_id)) {
            return ['success' => false, 'message' => 'Anda sudah memberikan suara untuk topik ini'];
        }

        // Insert vote
        $pilihan_json = json_encode($pilihan);
        $stmt = $this->db->prepare("
            INSERT INTO votes
            (voting_topic_id, anggota_id, pilihan, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "iisss",
            $voting_topic_id,
            $anggota_id,
            $pilihan_json,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        );

        if ($stmt->execute()) {
            // Update results cache
            $this->updateResultsCache($voting_topic_id);
            return ['success' => true, 'message' => 'Suara berhasil disimpan'];
        }

        return ['success' => false, 'message' => 'Gagal menyimpan suara'];
    }

    /**
     * Check if member has voted
     */
    public function hasVoted($voting_topic_id, $anggota_id) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM votes
            WHERE voting_topic_id = ? AND anggota_id = ?
        ");
        $stmt->bind_param("ii", $voting_topic_id, $anggota_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] > 0;
    }

    /**
     * Get voting results
     */
    public function getVotingResults($voting_topic_id) {
        // Try cache first
        $cached = $this->getCachedResults($voting_topic_id);
        if ($cached) {
            return $cached;
        }

        // Calculate results
        $stmt = $this->db->prepare("
            SELECT pilihan FROM votes WHERE voting_topic_id = ?
        ");
        $stmt->bind_param("i", $voting_topic_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $results = [];
        while ($row = $result->fetch_assoc()) {
            $pilihan = json_decode($row['pilihan'], true);
            if (is_array($pilihan)) {
                foreach ($pilihan as $choice) {
                    if (!isset($results[$choice])) {
                        $results[$choice] = 0;
                    }
                    $results[$choice]++;
                }
            }
        }

        // Cache results
        $this->updateResultsCache($voting_topic_id, $results);

        return $results;
    }

    /**
     * Count total votes for a topic
     */
    public function countVotes($voting_topic_id) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total FROM votes WHERE voting_topic_id = ?
        ");
        $stmt->bind_param("i", $voting_topic_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'];
    }

    /**
     * Get voting topic details
     */
    public function getVotingTopic($id) {
        $stmt = $this->db->prepare("
            SELECT vt.*, ra.judul as agenda_judul, ra.tahun
            FROM voting_topics vt
            JOIN rat_agenda ra ON vt.rat_agenda_id = ra.id
            WHERE vt.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            $result['options'] = json_decode($result['options'], true);
        }

        return $result;
    }

    /**
     * Get cached results
     */
    private function getCachedResults($voting_topic_id) {
        $stmt = $this->db->prepare("
            SELECT results FROM voting_results_cache
            WHERE voting_topic_id = ? AND last_updated > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
        ");
        $stmt->bind_param("i", $voting_topic_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            return json_decode($result['results'], true);
        }

        return null;
    }

    /**
     * Update results cache
     */
    private function updateResultsCache($voting_topic_id, $results = null) {
        if ($results === null) {
            $results = $this->getVotingResults($voting_topic_id);
        }

        $results_json = json_encode($results);
        $total_votes = array_sum($results);

        $stmt = $this->db->prepare("
            INSERT INTO voting_results_cache (voting_topic_id, total_votes, results, last_updated)
            VALUES (?, ?, ?, CURRENT_TIMESTAMP)
            ON DUPLICATE KEY UPDATE
                total_votes = VALUES(total_votes),
                results = VALUES(results),
                last_updated = CURRENT_TIMESTAMP
        ");
        $stmt->bind_param("iis", $voting_topic_id, $total_votes, $results_json);
        $stmt->execute();
    }

    /**
     * Register participant for RAT
     */
    public function registerParticipant($rat_agenda_id, $anggota_id) {
        $stmt = $this->db->prepare("
            INSERT INTO rat_participants (rat_agenda_id, anggota_id, status)
            VALUES (?, ?, 'registered')
            ON DUPLICATE KEY UPDATE status = 'registered'
        ");
        $stmt->bind_param("ii", $rat_agenda_id, $anggota_id);
        return $stmt->execute();
    }

    /**
     * Mark participant as attended
     */
    public function markAttended($rat_agenda_id, $anggota_id) {
        $stmt = $this->db->prepare("
            UPDATE rat_participants
            SET status = 'attended', attended_at = CURRENT_TIMESTAMP
            WHERE rat_agenda_id = ? AND anggota_id = ?
        ");
        $stmt->bind_param("ii", $rat_agenda_id, $anggota_id);
        return $stmt->execute();
    }

    /**
     * Get participants for RAT agenda
     */
    public function getParticipants($rat_agenda_id) {
        $stmt = $this->db->prepare("
            SELECT rp.*, a.nama, a.nrp, a.email
            FROM rat_participants rp
            JOIN anggota a ON rp.anggota_id = a.id
            WHERE rp.rat_agenda_id = ?
            ORDER BY rp.registered_at ASC
        ");
        $stmt->bind_param("i", $rat_agenda_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Check if voting period is active
     */
    public function isVotingActive($voting_topic_id) {
        $topic = $this->getVotingTopic($voting_topic_id);
        if (!$topic || !$topic['is_active']) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        return $now >= $topic['voting_start'] && $now <= $topic['voting_end'];
    }

    /**
     * Get voting statistics for dashboard
     */
    public function getVotingStats() {
        $stats = [];

        // Total RAT agendas
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM rat_agenda");
        $stmt->execute();
        $stats['total_agendas'] = $stmt->get_result()->fetch_assoc()['total'];

        // Active votings
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total FROM voting_topics
            WHERE is_active = 1 AND voting_end > NOW()
        ");
        $stmt->execute();
        $stats['active_votings'] = $stmt->get_result()->fetch_assoc()['total'];

        // Total votes cast
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM votes");
        $stmt->execute();
        $stats['total_votes'] = $stmt->get_result()->fetch_assoc()['total'];

        // Recent votings
        $stmt = $this->db->prepare("
            SELECT vt.judul, COUNT(v.id) as votes_count, vt.voting_end
            FROM voting_topics vt
            LEFT JOIN votes v ON vt.id = v.voting_topic_id
            WHERE vt.voting_end > DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY vt.id
            ORDER BY vt.voting_end DESC
            LIMIT 5
        ");
        $stmt->execute();
        $stats['recent_votings'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return $stats;
    }
}
?>
