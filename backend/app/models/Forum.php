<?php
// app/models/Forum.php
require_once '../core/Database.php';

class Forum {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Get all forum categories
     */
    public function getCategories($include_stats = false) {
        $categories = [];

        $stmt = $this->db->prepare("
            SELECT * FROM forum_categories
            WHERE is_active = 1
            ORDER BY sort_order ASC, name ASC
        ");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($category = $result->fetch_assoc()) {
            if ($include_stats) {
                $category['stats'] = $this->getCategoryStats($category['id']);
            }
            $categories[] = $category;
        }

        return $categories;
    }

    /**
     * Get category statistics
     */
    private function getCategoryStats($category_id) {
        // Get thread count
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as thread_count FROM forum_threads WHERE category_id = ?
        ");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $thread_count = $stmt->get_result()->fetch_assoc()['thread_count'];

        // Get post count
        $stmt = $this->db->prepare("
            SELECT COUNT(fp.id) as post_count
            FROM forum_posts fp
            JOIN forum_threads ft ON fp.thread_id = ft.id
            WHERE ft.category_id = ?
        ");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $post_count = $stmt->get_result()->fetch_assoc()['post_count'];

        // Get latest thread
        $stmt = $this->db->prepare("
            SELECT ft.title, ft.created_at, a.nama as author_name
            FROM forum_threads ft
            JOIN anggota a ON ft.author_id = a.id
            WHERE ft.category_id = ?
            ORDER BY ft.created_at DESC
            LIMIT 1
        ");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $latest = $stmt->get_result()->fetch_assoc();

        return [
            'thread_count' => $thread_count,
            'post_count' => $post_count,
            'latest_thread' => $latest
        ];
    }

    /**
     * Get threads for a category
     */
    public function getThreads($category_id = null, $limit = 20, $offset = 0, $sort = 'latest') {
        $where_clause = $category_id ? "WHERE ft.category_id = ?" : "";
        $sort_clause = $this->getSortClause($sort);

        $sql = "
            SELECT
                ft.*,
                fc.name as category_name,
                fc.icon as category_icon,
                a.nama as author_name,
                a.nrp as author_nrp,
                COALESCE(lr.nama, a.nama) as last_reply_name,
                fus.thread_count as author_thread_count,
                fus.post_count as author_post_count
            FROM forum_threads ft
            JOIN forum_categories fc ON ft.category_id = fc.id
            JOIN anggota a ON ft.author_id = a.id
            LEFT JOIN anggota lr ON ft.last_reply_by = lr.id
            LEFT JOIN forum_user_stats fus ON ft.author_id = fus.anggota_id
            {$where_clause}
            {$sort_clause}
            LIMIT ? OFFSET ?
        ";

        $stmt = $this->db->prepare($sql);

        if ($category_id) {
            $stmt->bind_param("iii", $category_id, $limit, $offset);
        } else {
            $stmt->bind_param("ii", $limit, $offset);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get sort clause for threads
     */
    private function getSortClause($sort) {
        switch ($sort) {
            case 'oldest':
                return "ORDER BY ft.is_pinned DESC, ft.is_sticky DESC, ft.created_at ASC";
            case 'most_replies':
                return "ORDER BY ft.is_pinned DESC, ft.is_sticky DESC, ft.reply_count DESC, ft.created_at DESC";
            case 'most_views':
                return "ORDER BY ft.is_pinned DESC, ft.is_sticky DESC, ft.view_count DESC, ft.created_at DESC";
            case 'latest_reply':
                return "ORDER BY ft.is_pinned DESC, ft.is_sticky DESC, ft.last_reply_at DESC";
            case 'latest':
            default:
                return "ORDER BY ft.is_pinned DESC, ft.is_sticky DESC, ft.created_at DESC";
        }
    }

    /**
     * Get single thread with posts
     */
    public function getThread($thread_id, $include_posts = true) {
        $stmt = $this->db->prepare("
            SELECT
                ft.*,
                fc.name as category_name,
                fc.icon as category_icon,
                a.nama as author_name,
                a.nrp as author_nrp
            FROM forum_threads ft
            JOIN forum_categories fc ON ft.category_id = fc.id
            JOIN anggota a ON ft.author_id = a.id
            WHERE ft.id = ?
        ");
        $stmt->bind_param("i", $thread_id);
        $stmt->execute();
        $thread = $stmt->get_result()->fetch_assoc();

        if (!$thread) {
            return null;
        }

        // Increment view count
        $this->incrementViewCount($thread_id);

        if ($include_posts) {
            $thread['posts'] = $this->getThreadPosts($thread_id);
        }

        return $thread;
    }

    /**
     * Increment thread view count
     */
    private function incrementViewCount($thread_id) {
        $stmt = $this->db->prepare("
            UPDATE forum_threads SET view_count = view_count + 1 WHERE id = ?
        ");
        $stmt->bind_param("i", $thread_id);
        $stmt->execute();
    }

    /**
     * Get posts for a thread
     */
    public function getThreadPosts($thread_id, $limit = 50, $offset = 0) {
        $stmt = $this->db->prepare("
            SELECT
                fp.*,
                a.nama as author_name,
                a.nrp as author_nrp,
                fus.post_count as author_post_count,
                fus.reputation as author_reputation,
                COALESCE(parent.content, '') as parent_content,
                COALESCE(pa.nama, '') as parent_author_name
            FROM forum_posts fp
            JOIN anggota a ON fp.author_id = a.id
            LEFT JOIN forum_user_stats fus ON fp.author_id = fus.anggota_id
            LEFT JOIN forum_posts parent ON fp.parent_post_id = parent.id
            LEFT JOIN anggota pa ON parent.author_id = pa.id
            WHERE fp.thread_id = ?
            ORDER BY fp.created_at ASC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("iii", $thread_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Create new thread
     */
    public function createThread($data) {
        $stmt = $this->db->prepare("
            INSERT INTO forum_threads
            (category_id, title, content, author_id, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param(
            "issii",
            $data['category_id'],
            $data['title'],
            $data['content'],
            $data['author_id']
        );

        if ($stmt->execute()) {
            $thread_id = $this->db->insert_id;

            // Update user stats
            $this->updateUserStats($data['author_id'], 'thread');

            return $thread_id;
        }

        return false;
    }

    /**
     * Create new post/reply
     */
    public function createPost($data) {
        $stmt = $this->db->prepare("
            INSERT INTO forum_posts
            (thread_id, content, author_id, parent_post_id, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param(
            "sisii",
            $data['thread_id'],
            $data['content'],
            $data['author_id'],
            $data['parent_post_id'] ?? null
        );

        if ($stmt->execute()) {
            $post_id = $this->db->insert_id;

            // Update thread reply count and last reply info
            $this->updateThreadReplyInfo($data['thread_id'], $data['author_id']);

            // Update user stats
            $this->updateUserStats($data['author_id'], 'post');

            return $post_id;
        }

        return false;
    }

    /**
     * Update thread reply information
     */
    private function updateThreadReplyInfo($thread_id, $last_reply_by) {
        $stmt = $this->db->prepare("
            UPDATE forum_threads
            SET reply_count = reply_count + 1,
                last_reply_at = NOW(),
                last_reply_by = ?
            WHERE id = ?
        ");
        $stmt->bind_param("ii", $last_reply_by, $thread_id);
        $stmt->execute();
    }

    /**
     * Update user forum statistics
     */
    private function updateUserStats($anggota_id, $type) {
        $column = $type === 'thread' ? 'thread_count' : 'post_count';

        $stmt = $this->db->prepare("
            INSERT INTO forum_user_stats (anggota_id, {$column})
            VALUES (?, 1)
            ON DUPLICATE KEY UPDATE
                {$column} = {$column} + 1,
                last_activity = NOW()
        ");
        $stmt->bind_param("i", $anggota_id);
        $stmt->execute();
    }

    /**
     * Search threads and posts
     */
    public function search($query, $category_id = null, $limit = 20) {
        $search_term = "%{$query}%";
        $where_clause = $category_id ? "AND ft.category_id = ?" : "";

        // Search in threads
        $sql = "
            SELECT
                'thread' as type,
                ft.id,
                ft.title as title,
                ft.content as content,
                ft.created_at,
                fc.name as category_name,
                a.nama as author_name
            FROM forum_threads ft
            JOIN forum_categories fc ON ft.category_id = fc.id
            JOIN anggota a ON ft.author_id = a.id
            WHERE (ft.title LIKE ? OR ft.content LIKE ?) {$where_clause}
            UNION
            SELECT
                'post' as type,
                fp.id,
                ft.title as thread_title,
                fp.content as content,
                fp.created_at,
                fc.name as category_name,
                a.nama as author_name
            FROM forum_posts fp
            JOIN forum_threads ft ON fp.thread_id = ft.id
            JOIN forum_categories fc ON ft.category_id = fc.id
            JOIN anggota a ON fp.author_id = a.id
            WHERE fp.content LIKE ? {$where_clause}
            ORDER BY created_at DESC
            LIMIT ?
        ";

        $stmt = $this->db->prepare($sql);

        if ($category_id) {
            $stmt->bind_param("sssii", $search_term, $search_term, $search_term, $category_id, $limit);
        } else {
            $stmt->bind_param("sssi", $search_term, $search_term, $search_term, $limit);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Subscribe to thread
     */
    public function subscribeToThread($thread_id, $anggota_id) {
        $stmt = $this->db->prepare("
            INSERT INTO forum_subscriptions (thread_id, user_id)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE created_at = NOW()
        ");
        $stmt->bind_param("ii", $thread_id, $anggota_id);
        return $stmt->execute();
    }

    /**
     * Unsubscribe from thread
     */
    public function unsubscribeFromThread($thread_id, $anggota_id) {
        $stmt = $this->db->prepare("
            DELETE FROM forum_subscriptions
            WHERE thread_id = ? AND user_id = ?
        ");
        $stmt->bind_param("ii", $thread_id, $anggota_id);
        return $stmt->execute();
    }

    /**
     * Check if user is subscribed to thread
     */
    public function isSubscribed($thread_id, $anggota_id) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM forum_subscriptions
            WHERE thread_id = ? AND user_id = ?
        ");
        $stmt->bind_param("ii", $thread_id, $anggota_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] > 0;
    }

    /**
     * Get forum statistics
     */
    public function getForumStats() {
        $stats = [];

        // Total categories
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM forum_categories WHERE is_active = 1");
        $stmt->execute();
        $stats['total_categories'] = $stmt->get_result()->fetch_assoc()['total'];

        // Total threads
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM forum_threads");
        $stmt->execute();
        $stats['total_threads'] = $stmt->get_result()->fetch_assoc()['total'];

        // Total posts
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM forum_posts");
        $stmt->execute();
        $stats['total_posts'] = $stmt->get_result()->fetch_assoc()['total'];

        // Active users (posted in last 30 days)
        $stmt = $this->db->prepare("
            SELECT COUNT(DISTINCT author_id) as total
            FROM forum_posts
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $stmt->execute();
        $stats['active_users_30d'] = $stmt->get_result()->fetch_assoc()['total'];

        // Latest activity
        $stmt = $this->db->prepare("
            SELECT GREATEST(
                (SELECT MAX(created_at) FROM forum_threads),
                (SELECT MAX(created_at) FROM forum_posts)
            ) as latest_activity
        ");
        $stmt->execute();
        $latest = $stmt->get_result()->fetch_assoc()['latest_activity'];
        $stats['latest_activity'] = $latest;

        return $stats;
    }

    /**
     * Get user forum profile
     */
    public function getUserProfile($anggota_id) {
        $stmt = $this->db->prepare("
            SELECT
                fus.*,
                a.nama,
                a.nrp,
                (SELECT COUNT(*) FROM forum_subscriptions WHERE user_id = ?) as subscription_count
            FROM forum_user_stats fus
            JOIN anggota a ON fus.anggota_id = a.id
            WHERE fus.anggota_id = ?
        ");
        $stmt->bind_param("ii", $anggota_id, $anggota_id);
        $stmt->execute();
        $profile = $stmt->get_result()->fetch_assoc();

        if (!$profile) {
            // Create default profile
            $stmt = $this->db->prepare("
                INSERT INTO forum_user_stats (anggota_id) VALUES (?)
            ");
            $stmt->bind_param("i", $anggota_id);
            $stmt->execute();

            // Get basic info
            $stmt = $this->db->prepare("SELECT nama, nrp FROM anggota WHERE id = ?");
            $stmt->bind_param("i", $anggota_id);
            $stmt->execute();
            $anggota = $stmt->get_result()->fetch_assoc();

            $profile = [
                'anggota_id' => $anggota_id,
                'nama' => $anggota['nama'],
                'nrp' => $anggota['nrp'],
                'thread_count' => 0,
                'post_count' => 0,
                'reputation' => 0,
                'subscription_count' => 0
            ];
        }

        return $profile;
    }

    /**
     * Get user's recent posts
     */
    public function getUserRecentPosts($anggota_id, $limit = 10) {
        $stmt = $this->db->prepare("
            SELECT
                fp.content,
                fp.created_at,
                ft.title as thread_title,
                ft.id as thread_id,
                fc.name as category_name
            FROM forum_posts fp
            JOIN forum_threads ft ON fp.thread_id = ft.id
            JOIN forum_categories fc ON ft.category_id = fc.id
            WHERE fp.author_id = ?
            ORDER BY fp.created_at DESC
            LIMIT ?
        ");
        $stmt->bind_param("ii", $anggota_id, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
