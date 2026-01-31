<?php
// app/controllers/VotingController.php
require_once '../app/models/Voting.php';
require_once '../app/views/json.php';

class VotingController {

    /**
     * Get all RAT agendas
     */
    public static function getRATAgendas() {
        $voting = new Voting();
        $agendas = $voting->getRATAgendas();

        jsonResponse(true, 'RAT agendas retrieved', ['agendas' => $agendas]);
    }

    /**
     * Get RAT agenda details
     */
    public static function getRATAgenda() {
        $id = $_GET['id'] ?? 0;

        if (!$id) {
            jsonResponse(false, 'RAT agenda ID required');
            return;
        }

        $voting = new Voting();
        $agenda = $voting->getRATAgenda($id);

        if ($agenda) {
            $agenda['topics'] = $voting->getVotingTopics($id, Auth::hasRole('pengurus'));
            jsonResponse(true, 'RAT agenda retrieved', $agenda);
        } else {
            jsonResponse(false, 'RAT agenda not found');
        }
    }

    /**
     * Create new RAT agenda
     */
    public static function createRATAgenda() {
        Auth::requireRole('pengurus');

        $data = [
            'tahun' => $_POST['tahun'] ?? date('Y'),
            'judul' => $_POST['judul'] ?? '',
            'deskripsi' => $_POST['deskripsi'] ?? '',
            'tanggal_mulai' => $_POST['tanggal_mulai'] ?? '',
            'tanggal_selesai' => $_POST['tanggal_selesai'] ?? '',
            'status' => $_POST['status'] ?? 'draft',
            'created_by' => $_SESSION['user_id']
        ];

        if (empty($data['judul']) || empty($data['tanggal_mulai']) || empty($data['tanggal_selesai'])) {
            jsonResponse(false, 'Judul, tanggal mulai, dan tanggal selesai wajib diisi');
            return;
        }

        $voting = new Voting();
        $agenda_id = $voting->createRATAgenda($data);

        if ($agenda_id) {
            jsonResponse(true, 'RAT agenda berhasil dibuat', ['agenda_id' => $agenda_id]);
        } else {
            jsonResponse(false, 'Gagal membuat RAT agenda');
        }
    }

    /**
     * Update RAT agenda
     */
    public static function updateRATAgenda() {
        Auth::requireRole('pengurus');

        $id = $_POST['id'] ?? 0;
        $data = [
            'judul' => $_POST['judul'] ?? '',
            'deskripsi' => $_POST['deskripsi'] ?? '',
            'tanggal_mulai' => $_POST['tanggal_mulai'] ?? '',
            'tanggal_selesai' => $_POST['tanggal_selesai'] ?? '',
            'status' => $_POST['status'] ?? 'draft'
        ];

        if (!$id || empty($data['judul'])) {
            jsonResponse(false, 'ID dan judul wajib diisi');
            return;
        }

        $voting = new Voting();
        if ($voting->updateRATAgenda($id, $data)) {
            jsonResponse(true, 'RAT agenda berhasil diperbarui');
        } else {
            jsonResponse(false, 'Gagal memperbarui RAT agenda');
        }
    }

    /**
     * Get voting topics for a RAT agenda
     */
    public static function getVotingTopics() {
        $rat_agenda_id = $_GET['rat_agenda_id'] ?? 0;
        $include_results = isset($_GET['include_results']);

        if (!$rat_agenda_id) {
            jsonResponse(false, 'RAT agenda ID required');
            return;
        }

        $voting = new Voting();
        $topics = $voting->getVotingTopics($rat_agenda_id, $include_results);

        jsonResponse(true, 'Voting topics retrieved', ['topics' => $topics]);
    }

    /**
     * Create voting topic
     */
    public static function createVotingTopic() {
        Auth::requireRole('pengurus');

        $data = [
            'rat_agenda_id' => $_POST['rat_agenda_id'] ?? 0,
            'judul' => $_POST['judul'] ?? '',
            'deskripsi' => $_POST['deskripsi'] ?? '',
            'tipe' => $_POST['tipe'] ?? 'single_choice',
            'options' => json_decode($_POST['options'] ?? '[]', true),
            'required_quorum' => $_POST['required_quorum'] ?? 0,
            'voting_start' => $_POST['voting_start'] ?? null,
            'voting_end' => $_POST['voting_end'] ?? null
        ];

        if (!$data['rat_agenda_id'] || empty($data['judul']) || empty($data['options'])) {
            jsonResponse(false, 'RAT agenda ID, judul, dan opsi voting wajib diisi');
            return;
        }

        $voting = new Voting();
        $topic_id = $voting->createVotingTopic($data);

        if ($topic_id) {
            jsonResponse(true, 'Voting topic berhasil dibuat', ['topic_id' => $topic_id]);
        } else {
            jsonResponse(false, 'Gagal membuat voting topic');
        }
    }

    /**
     * Submit vote
     */
    public static function submitVote() {
        $voting_topic_id = $_POST['voting_topic_id'] ?? 0;
        $anggota_id = $_SESSION['anggota_id'] ?? 0; // Assuming session has anggota_id
        $pilihan = json_decode($_POST['pilihan'] ?? '[]', true);

        if (!$voting_topic_id || !$anggota_id || empty($pilihan)) {
            jsonResponse(false, 'Voting topic ID, anggota ID, dan pilihan wajib diisi');
            return;
        }

        $voting = new Voting();
        $result = $voting->submitVote($voting_topic_id, $anggota_id, $pilihan);

        jsonResponse($result['success'], $result['message']);
    }

    /**
     * Get voting results
     */
    public static function getVotingResults() {
        $voting_topic_id = $_GET['voting_topic_id'] ?? 0;

        if (!$voting_topic_id) {
            jsonResponse(false, 'Voting topic ID required');
            return;
        }

        $voting = new Voting();
        $results = $voting->getVotingResults($voting_topic_id);
        $total_votes = $voting->countVotes($voting_topic_id);

        jsonResponse(true, 'Voting results retrieved', [
            'results' => $results,
            'total_votes' => $total_votes
        ]);
    }

    /**
     * Check if user has voted
     */
    public static function checkVoteStatus() {
        $voting_topic_id = $_GET['voting_topic_id'] ?? 0;
        $anggota_id = $_SESSION['anggota_id'] ?? 0;

        if (!$voting_topic_id || !$anggota_id) {
            jsonResponse(false, 'Voting topic ID and anggota ID required');
            return;
        }

        $voting = new Voting();
        $has_voted = $voting->hasVoted($voting_topic_id, $anggota_id);
        $is_active = $voting->isVotingActive($voting_topic_id);

        jsonResponse(true, 'Vote status checked', [
            'has_voted' => $has_voted,
            'is_active' => $is_active
        ]);
    }

    /**
     * Register for RAT participation
     */
    public static function registerForRAT() {
        $rat_agenda_id = $_POST['rat_agenda_id'] ?? 0;
        $anggota_id = $_SESSION['anggota_id'] ?? 0;

        if (!$rat_agenda_id || !$anggota_id) {
            jsonResponse(false, 'RAT agenda ID and anggota ID required');
            return;
        }

        $voting = new Voting();
        if ($voting->registerParticipant($rat_agenda_id, $anggota_id)) {
            jsonResponse(true, 'Berhasil mendaftar sebagai peserta RAT');
        } else {
            jsonResponse(false, 'Gagal mendaftar sebagai peserta RAT');
        }
    }

    /**
     * Get RAT participants
     */
    public static function getRATParticipants() {
        Auth::requireRole('pengurus');

        $rat_agenda_id = $_GET['rat_agenda_id'] ?? 0;

        if (!$rat_agenda_id) {
            jsonResponse(false, 'RAT agenda ID required');
            return;
        }

        $voting = new Voting();
        $participants = $voting->getParticipants($rat_agenda_id);

        jsonResponse(true, 'RAT participants retrieved', ['participants' => $participants]);
    }

    /**
     * Mark participant as attended
     */
    public static function markAttended() {
        Auth::requireRole('pengurus');

        $rat_agenda_id = $_POST['rat_agenda_id'] ?? 0;
        $anggota_id = $_POST['anggota_id'] ?? 0;

        if (!$rat_agenda_id || !$anggota_id) {
            jsonResponse(false, 'RAT agenda ID and anggota ID required');
            return;
        }

        $voting = new Voting();
        if ($voting->markAttended($rat_agenda_id, $anggota_id)) {
            jsonResponse(true, 'Peserta berhasil ditandai hadir');
        } else {
            jsonResponse(false, 'Gagal menandai kehadiran peserta');
        }
    }

    /**
     * Get voting statistics
     */
    public static function getVotingStats() {
        $voting = new Voting();
        $stats = $voting->getVotingStats();

        jsonResponse(true, 'Voting statistics retrieved', $stats);
    }

    /**
     * Get active votings for current user
     */
    public static function getActiveVotings() {
        $anggota_id = $_SESSION['anggota_id'] ?? 0;

        if (!$anggota_id) {
            jsonResponse(false, 'Anggota ID not found in session');
            return;
        }

        $voting = new Voting();

        // Get active voting topics
        $stmt = $voting->db->prepare("
            SELECT vt.*, ra.judul as agenda_judul, ra.tahun,
                   CASE WHEN v.id IS NOT NULL THEN 1 ELSE 0 END as has_voted
            FROM voting_topics vt
            JOIN rat_agenda ra ON vt.rat_agenda_id = ra.id
            LEFT JOIN votes v ON vt.id = v.voting_topic_id AND v.anggota_id = ?
            WHERE vt.is_active = 1 AND vt.voting_end > NOW()
            ORDER BY vt.voting_start ASC
        ");
        $stmt->bind_param("i", $anggota_id);
        $stmt->execute();
        $active_votings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Process options for each voting
        foreach ($active_votings as &$voting_item) {
            $voting_item['options'] = json_decode($voting_item['options'], true);
        }

        jsonResponse(true, 'Active votings retrieved', ['votings' => $active_votings]);
    }
}
?>
