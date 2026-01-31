<?php
// app/controllers/ForumController.php
require_once '../app/models/Forum.php';
require_once '../app/views/json.php';

class ForumController {

    /**
     * Get forum categories with stats
     */
    public static function getCategories() {
        $forum = new Forum();
        $categories = $forum->getCategories(true);

        jsonResponse(true, 'Forum categories retrieved', ['categories' => $categories]);
    }

    /**
     * Get threads for a category
     */
    public static function getThreads() {
        $category_id = $_GET['category_id'] ?? null;
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 20;
        $sort = $_GET['sort'] ?? 'latest';
        $offset = ($page - 1) * $limit;

        $forum = new Forum();
        $threads = $forum->getThreads($category_id, $limit, $offset, $sort);

        jsonResponse(true, 'Threads retrieved', [
            'threads' => $threads,
            'page' => $page,
            'limit' => $limit
        ]);
    }

    /**
     * Get single thread with posts
     */
    public static function getThread() {
        $thread_id = $_GET['id'] ?? 0;

        if (!$thread_id) {
            jsonResponse(false, 'Thread ID required');
            return;
        }

        $forum = new Forum();
        $thread = $forum->getThread($thread_id);

        if ($thread) {
            jsonResponse(true, 'Thread retrieved', $thread);
        } else {
            jsonResponse(false, 'Thread not found');
        }
    }

    /**
     * Create new thread
     */
    public static function createThread() {
        $data = [
            'category_id' => $_POST['category_id'] ?? 0,
            'title' => trim($_POST['title'] ?? ''),
            'content' => trim($_POST['content'] ?? ''),
            'author_id' => $_SESSION['anggota_id'] ?? 0
        ];

        if (!$data['category_id'] || empty($data['title']) || empty($data['content']) || !$data['author_id']) {
            jsonResponse(false, 'Category, title, content, and author are required');
            return;
        }

        if (strlen($data['title']) < 5 || strlen($data['content']) < 10) {
            jsonResponse(false, 'Title must be at least 5 characters and content at least 10 characters');
            return;
        }

        $forum = new Forum();
        $thread_id = $forum->createThread($data);

        if ($thread_id) {
            jsonResponse(true, 'Thread created successfully', ['thread_id' => $thread_id]);
        } else {
            jsonResponse(false, 'Failed to create thread');
        }
    }

    /**
     * Create new post/reply
     */
    public static function createPost() {
        $data = [
            'thread_id' => $_POST['thread_id'] ?? 0,
            'content' => trim($_POST['content'] ?? ''),
            'author_id' => $_SESSION['anggota_id'] ?? 0,
            'parent_post_id' => $_POST['parent_post_id'] ?? null
        ];

        if (!$data['thread_id'] || empty($data['content']) || !$data['author_id']) {
            jsonResponse(false, 'Thread ID, content, and author are required');
            return;
        }

        if (strlen($data['content']) < 5) {
            jsonResponse(false, 'Content must be at least 5 characters');
            return;
        }

        $forum = new Forum();
        $post_id = $forum->createPost($data);

        if ($post_id) {
            jsonResponse(true, 'Post created successfully', ['post_id' => $post_id]);
        } else {
            jsonResponse(false, 'Failed to create post');
        }
    }

    /**
     * Search forum
     */
    public static function search() {
        $query = trim($_GET['q'] ?? '');
        $category_id = $_GET['category_id'] ?? null;
        $limit = $_GET['limit'] ?? 20;

        if (empty($query) || strlen($query) < 3) {
            jsonResponse(false, 'Search query must be at least 3 characters');
            return;
        }

        $forum = new Forum();
        $results = $forum->search($query, $category_id, $limit);

        jsonResponse(true, 'Search completed', [
            'query' => $query,
            'results' => $results,
            'total' => count($results)
        ]);
    }

    /**
     * Subscribe to thread
     */
    public static function subscribe() {
        $thread_id = $_POST['thread_id'] ?? 0;
        $anggota_id = $_SESSION['anggota_id'] ?? 0;

        if (!$thread_id || !$anggota_id) {
            jsonResponse(false, 'Thread ID and user ID required');
            return;
        }

        $forum = new Forum();
        if ($forum->subscribeToThread($thread_id, $anggota_id)) {
            jsonResponse(true, 'Subscribed to thread');
        } else {
            jsonResponse(false, 'Failed to subscribe');
        }
    }

    /**
     * Unsubscribe from thread
     */
    public static function unsubscribe() {
        $thread_id = $_POST['thread_id'] ?? 0;
        $anggota_id = $_SESSION['anggota_id'] ?? 0;

        if (!$thread_id || !$anggota_id) {
            jsonResponse(false, 'Thread ID and user ID required');
            return;
        }

        $forum = new Forum();
        if ($forum->unsubscribeFromThread($thread_id, $anggota_id)) {
            jsonResponse(true, 'Unsubscribed from thread');
        } else {
            jsonResponse(false, 'Failed to unsubscribe');
        }
    }

    /**
     * Check subscription status
     */
    public static function checkSubscription() {
        $thread_id = $_GET['thread_id'] ?? 0;
        $anggota_id = $_SESSION['anggota_id'] ?? 0;

        if (!$thread_id || !$anggota_id) {
            jsonResponse(false, 'Thread ID and user ID required');
            return;
        }

        $forum = new Forum();
        $is_subscribed = $forum->isSubscribed($thread_id, $anggota_id);

        jsonResponse(true, 'Subscription status checked', [
            'is_subscribed' => $is_subscribed
        ]);
    }

    /**
     * Get forum statistics
     */
    public static function getStats() {
        $forum = new Forum();
        $stats = $forum->getForumStats();

        jsonResponse(true, 'Forum statistics retrieved', $stats);
    }

    /**
     * Get user forum profile
     */
    public static function getUserProfile() {
        $anggota_id = $_GET['user_id'] ?? $_SESSION['anggota_id'] ?? 0;

        if (!$anggota_id) {
            jsonResponse(false, 'User ID required');
            return;
        }

        $forum = new Forum();
        $profile = $forum->getUserProfile($anggota_id);
        $recent_posts = $forum->getUserRecentPosts($anggota_id, 5);

        jsonResponse(true, 'User profile retrieved', [
            'profile' => $profile,
            'recent_posts' => $recent_posts
        ]);
    }

    /**
     * Report post (for moderation)
     */
    public static function reportPost() {
        $post_id = $_POST['post_id'] ?? 0;
        $reason = $_POST['reason'] ?? '';
        $reporter_id = $_SESSION['anggota_id'] ?? 0;

        if (!$post_id || !$reason || !$reporter_id) {
            jsonResponse(false, 'Post ID, reason, and reporter required');
            return;
        }

        // In a real implementation, you might want to create a reports table
        // For now, we'll just log it
        error_log("Forum post report: Post $post_id reported by user $reporter_id. Reason: $reason");

        jsonResponse(true, 'Post reported successfully. Moderator will review it.');
    }

    /**
     * Get user's subscribed threads
     */
    public static function getSubscriptions() {
        $anggota_id = $_SESSION['anggota_id'] ?? 0;

        if (!$anggota_id) {
            jsonResponse(false, 'User ID required');
            return;
        }

        $stmt = Database::getConnection()->prepare("
            SELECT
                ft.id,
                ft.title,
                ft.created_at,
                fc.name as category_name,
                ft.last_reply_at,
                COUNT(fp.id) as new_replies
            FROM forum_subscriptions fs
            JOIN forum_threads ft ON fs.thread_id = ft.id
            JOIN forum_categories fc ON ft.category_id = fc.id
            LEFT JOIN forum_posts fp ON ft.id = fp.thread_id AND fp.created_at > fs.created_at
            WHERE fs.user_id = ?
            GROUP BY ft.id
            ORDER BY ft.last_reply_at DESC
        ");
        $stmt->bind_param("i", $anggota_id);
        $stmt->execute();
        $subscriptions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        jsonResponse(true, 'Subscriptions retrieved', ['subscriptions' => $subscriptions]);
    }

    /**
     * Mark thread as read (update subscription timestamp)
     */
    public static function markAsRead() {
        $thread_id = $_POST['thread_id'] ?? 0;
        $anggota_id = $_SESSION['anggota_id'] ?? 0;

        if (!$thread_id || !$anggota_id) {
            jsonResponse(false, 'Thread ID and user ID required');
            return;
        }

        $stmt = Database::getConnection()->prepare("
            UPDATE forum_subscriptions
            SET created_at = NOW()
            WHERE thread_id = ? AND user_id = ?
        ");
        $stmt->bind_param("ii", $thread_id, $anggota_id);

        if ($stmt->execute()) {
            jsonResponse(true, 'Thread marked as read');
        } else {
            jsonResponse(false, 'Failed to mark as read');
        }
    }

    /**
     * Get recent forum activity
     */
    public static function getRecentActivity() {
        $limit = $_GET['limit'] ?? 10;

        $stmt = Database::getConnection()->prepare("
            (SELECT
                'thread' as type,
                ft.id,
                ft.title as title,
                ft.content as preview,
                ft.created_at as activity_time,
                a.nama as author_name,
                fc.name as category_name,
                fc.icon as category_icon
            FROM forum_threads ft
            JOIN anggota a ON ft.author_id = a.id
            JOIN forum_categories fc ON ft.category_id = fc.id)
            UNION
            (SELECT
                'post' as type,
                fp.id,
                ft.title as title,
                fp.content as preview,
                fp.created_at as activity_time,
                a.nama as author_name,
                fc.name as category_name,
                fc.icon as category_icon
            FROM forum_posts fp
            JOIN forum_threads ft ON fp.thread_id = ft.id
            JOIN anggota a ON fp.author_id = a.id
            JOIN forum_categories fc ON ft.category_id = fc.id)
            ORDER BY activity_time DESC
            LIMIT ?
        ");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $activities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        jsonResponse(true, 'Recent activity retrieved', ['activities' => $activities]);
    }
}
?>
