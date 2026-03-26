<?php
/**
 * portals/student/notifications.php
 * 
 * Student Notification Center.
 */
require_once '../../config/db.php';
require_once '../../includes/NotificationManager.php';

session_start();
NotificationManager::init($pdo);

$userId = $_SESSION['user_id'];
$portal_type = 'student';
$page_title = 'Notifications | SkillEdu';
$root = "../../";

include '../../includes/portal_header.php';

// Fetch all notifications for this student
try {
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
    $notifications = [];
}
?>

<div class="student-notifications" style="padding: 20px 0;">
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 5px;">My Notifications</h1>
        <p style="color: var(--gray-color);">Stay updated with your courses, rewards, and platform news.</p>
    </div>

    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; min-height: 400px; overflow: hidden; box-shadow: var(--shadow);">
        <?php if (empty($notifications)): ?>
            <div style="padding: 100px; text-align: center;">
                <div style="font-size: 50px; color: var(--light-gray); margin-bottom: 20px;"><i class="fa fa-bell-slash"></i></div>
                <h3 style="color: var(--dark-color);">Quiet as a mouse!</h3>
                <p style="color: var(--gray-color);">No notifications here. Check back later for course updates.</p>
            </div>
        <?php
else: ?>
            <div style="padding: 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: rgba(0,0,0,0.02);">
                <span style="font-weight: 700; font-size: 14px;"><?php echo count($notifications); ?> notifications</span>
                <button onclick="markAllRead()" style="background: none; border: none; color: var(--primary-color); font-weight: 800; cursor: pointer; font-size: 13px;">Mark all as read</button>
            </div>
            
            <div class="notification-full-list">
                <?php foreach ($notifications as $n): ?>
                    <div style="padding: 25px; border-bottom: 1px solid var(--border-color); display: flex; gap: 20px; transition: background 0.2s; <?php echo $n['is_read'] ? 'opacity: 0.7;' : 'background: rgba(231, 76, 60, 0.02); border-left: 4px solid var(--primary-color);'; ?>">
                        <div class="notification-icon notif-<?php echo $n['type']; ?>" style="width: 50px; height: 50px; font-size: 22px; border-radius: 12px; flex-shrink: 0; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                            <i class="fa <?php
        switch ($n['type']) {
            case 'enrollment':
                echo 'fa-play-circle';
                break;
            case 'announcement':
                echo 'fa-bullhorn';
                break;
            case 'update':
                echo 'fa-rocket';
                break;
            default:
                echo 'fa-bell';
        }
?>"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                <h4 style="font-weight: 800; font-size: 17px; margin: 0; color: var(--dark-color);"><?php echo htmlspecialchars($n['title']); ?></h4>
                                <span style="font-size: 11px; color: var(--gray-color); font-weight: 600;"><?php echo date('M d, Y h:i A', strtotime($n['created_at'])); ?></span>
                            </div>
                            <p style="font-size: 14px; color: var(--gray-color); margin-bottom: 15px; line-height: 1.6;"><?php echo htmlspecialchars($n['message']); ?></p>
                            <?php if ($n['link']): ?>
                                <a href="<?php echo htmlspecialchars($n['link']); ?>" class="btn btn-primary" style="font-size: 12px; padding: 8px 18px; border-radius: 6px;">Check it out</a>
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

<?php

// No student footer identified, just ending div
echo '</div></main></div>';
include '../../includes/footer.php';

?>
