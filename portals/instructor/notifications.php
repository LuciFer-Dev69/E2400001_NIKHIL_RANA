<?php
/**
 * portals/instructor/notifications.php
 * 
 * Instructor Notification Center.
 */
require_once '../../config/db.php';
require_once '../../includes/NotificationManager.php';

session_start();
NotificationManager::init($pdo);

$userId = $_SESSION['user_id'];
$page_title = 'Notifications | SkillEdu Instructor';
include '../../includes/instructor/instructor_header.php';

// Fetch all notifications for this user
try {
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
    $notifications = [];
}
?>

<div class="instructor-notifications">
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 5px;">Instructor Notifications</h1>
        <p style="color: var(--gray-color);">Stay updated with student enrollments and platform announcements.</p>
    </div>

    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; min-height: 400px; overflow: hidden;">
        <?php if (empty($notifications)): ?>
            <div style="padding: 100px; text-align: center;">
                <div style="font-size: 50px; color: var(--light-gray); margin-bottom: 20px;"><i class="fa fa-bell-slash"></i></div>
                <h3 style="color: var(--dark-color);">No notifications yet</h3>
                <p style="color: var(--gray-color);">We'll alert you here when something important happens.</p>
            </div>
        <?php
else: ?>
            <div style="padding: 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: rgba(0,0,0,0.02);">
                <span style="font-weight: 700; font-size: 14px;"><?php echo count($notifications); ?> total notifications</span>
                <button onclick="markAllRead()" style="background: none; border: none; color: var(--primary-color); font-weight: 800; cursor: pointer; font-size: 13px;">Clear all</button>
            </div>
            
            <div class="notification-full-list">
                <?php foreach ($notifications as $n): ?>
                    <div style="padding: 25px; border-bottom: 1px solid var(--border-color); display: flex; gap: 20px; transition: background 0.2s; <?php echo $n['is_read'] ? 'opacity: 0.7;' : 'border-left: 4px solid var(--primary-color);'; ?>">
                        <div class="notification-icon notif-<?php echo $n['type']; ?>" style="width: 50px; height: 50px; font-size: 22px; border-radius: 12px; flex-shrink: 0;">
                            <i class="fa <?php
        switch ($n['type']) {
            case 'enrollment':
                echo 'fa-user-plus';
                break;
            case 'announcement':
                echo 'fa-bullhorn';
                break;
            case 'update':
                echo 'fa-sync-alt';
                break;
            default:
                echo 'fa-bell';
        }
?>"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                <h4 style="font-weight: 800; font-size: 16px; margin: 0;"><?php echo htmlspecialchars($n['title']); ?></h4>
                                <span style="font-size: 11px; color: var(--gray-color);"><?php echo date('M d, Y h:i A', strtotime($n['created_at'])); ?></span>
                            </div>
                            <p style="font-size: 14px; color: var(--gray-color); margin-bottom: 15px; line-height: 1.5;"><?php echo htmlspecialchars($n['message']); ?></p>
                            <?php if ($n['link']): ?>
                                <a href="<?php echo htmlspecialchars($n['link']); ?>" class="btn btn-secondary" style="font-size: 12px; padding: 6px 15px;">View Details</a>
                            <?php
        endif; ?>
                        </div>
                    </div>
                <?php
    endforeach; ?>
            </div>
        <?php
endif; ?>
    </div>
</div>

<?php include '../../includes/instructor/instructor_footer.php'; ?>
