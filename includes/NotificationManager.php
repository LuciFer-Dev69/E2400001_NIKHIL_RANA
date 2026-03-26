<?php
/**
 * includes/NotificationManager.php
 * 
 * Core business logic for the system-wide notification engine.
 * Handles creation, retrieval, and status updates for user alerts.
 */

class NotificationManager
{
    private static $pdo;

    public static function init($pdo)
    {
        self::$pdo = $pdo;
    }

    /**
     * Dispatch a notification to a specific user.
     */
    public static function notify($userId, $type, $title, $message = '', $link = '')
    {
        try {
            $stmt = self::$pdo->prepare("
                INSERT INTO notifications (user_id, type, title, message, link)
                VALUES (?, ?, ?, ?, ?)
            ");
            return $stmt->execute([$userId, $type, $title, $message, $link]);
        }
        catch (PDOException $e) {
            error_log("Notification Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Broadcast a notification to all users of a specific role.
     */
    public static function broadcast($role, $type, $title, $message = '', $link = '')
    {
        try {
            $stmt = self::$pdo->prepare("
                INSERT INTO notifications (user_id, type, title, message, link)
                SELECT id, ?, ?, ?, ? FROM users WHERE user_role = ?
            ");
            return $stmt->execute([$type, $title, $message, $link, $role]);
        }
        catch (PDOException $e) {
            error_log("Broadcast Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch unread notifications for a user.
     */
    public static function getUnread($userId, $limit = 10)
    {
        try {
            $stmt = self::$pdo->prepare("
                SELECT * FROM notifications 
                WHERE user_id = ? AND is_read = FALSE 
                ORDER BY created_at DESC LIMIT ?
            ");
            $stmt->bindValue(1, $userId, PDO::PARAM_INT);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Mark a single notification as read.
     */
    public static function markAsRead($notificationId, $userId)
    {
        try {
            $stmt = self::$pdo->prepare("
                UPDATE notifications SET is_read = TRUE 
                WHERE id = ? AND user_id = ?
            ");
            return $stmt->execute([$notificationId, $userId]);
        }
        catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Mark all notifications as read for a user.
     */
    public static function markAllAsRead($userId)
    {
        try {
            $stmt = self::$pdo->prepare("
                UPDATE notifications SET is_read = TRUE 
                WHERE user_id = ?
            ");
            return $stmt->execute([$userId]);
        }
        catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Log a system activity.
     */
    public static function logActivity($userId, $action, $metadata = [])
    {
        try {
            $stmt = self::$pdo->prepare("
                INSERT INTO activity_log (user_id, action, metadata)
                VALUES (?, ?, ?)
            ");
            return $stmt->execute([$userId, $action, json_encode($metadata)]);
        }
        catch (PDOException $e) {
            return false;
        }
    }
}
