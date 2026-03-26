<?php
/**
 * portals/admin/notifications.php
 * 
 * Admin Announcement Center.
 * Allows administrators to broadcast system-wide alerts to students and instructors.
 */
require_once '../../config/db.php';
require_once '../../includes/NotificationManager.php';

session_start();
NotificationManager::init($pdo);

$success_msg = '';
$error_msg = '';

// Handle Broadcast Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['broadcast'])) {
    $target_role = $_POST['target_role']; // 'student', 'instructor', or 'all'
    $type = 'announcement';
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);
    $link = trim($_POST['link']);

    if (empty($title) || empty($message)) {
        $error_msg = "Title and message are required.";
    }
    else {
        if ($target_role === 'all') {
            NotificationManager::broadcast('student', $type, $title, $message, $link);
            NotificationManager::broadcast('instructor', $type, $title, $message, $link);
        }
        else {
            NotificationManager::broadcast($target_role, $type, $title, $message, $link);
        }
        $success_msg = "Announcement broadcasted successfully to all " . ($target_role === 'all' ? 'users' : $target_role . 's') . "!";

        // Log activity
        NotificationManager::logActivity($_SESSION['user_id'], 'broadcast_announcement', [
            'target' => $target_role,
            'title' => $title
        ]);
    }
}

$page_title = 'Announcements | SkillEdu Admin';
include '../../includes/admin/admin_header.php';
?>

<div class="admin-notifications">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 5px;">Announcement Center</h1>
            <p style="color: var(--gray-color);">Broadcasting system-wide alerts and platform updates.</p>
        </div>
    </div>

    <?php if ($success_msg): ?>
        <div style="background: rgba(46, 204, 113, 0.1); color: #2ecc71; padding: 15px; border-radius: 8px; margin-bottom: 25px; border: 1px solid rgba(46, 204, 113, 0.2); font-weight: 700;">
            <i class="fa fa-check-circle" style="margin-right: 10px;"></i> <?php echo $success_msg; ?>
        </div>
    <?php
endif; ?>

    <?php if ($error_msg): ?>
        <div style="background: rgba(231, 76, 60, 0.1); color: #e74c3c; padding: 15px; border-radius: 8px; margin-bottom: 25px; border: 1px solid rgba(231, 76, 60, 0.2); font-weight: 700;">
            <i class="fa fa-exclamation-circle" style="margin-right: 10px;"></i> <?php echo $error_msg; ?>
        </div>
    <?php
endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 30px;">
        <!-- Broadcast Form -->
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 30px; box-shadow: var(--shadow);">
            <h3 style="margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
                <i class="fa fa-paper-plane" style="color: var(--primary-color);"></i> New Broadcast
            </h3>
            
            <form action="" method="POST">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 700; font-size: 14px;">Target Audience</label>
                    <select name="target_role" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--dark-color);">
                        <option value="all">All Users (Students & Instructors)</option>
                        <option value="student">Students Only</option>
                        <option value="instructor">Instructors Only</option>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 700; font-size: 14px;">Announcement Title</label>
                    <input type="text" name="title" placeholder="e.g. Major Platform Update" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--dark-color);">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 700; font-size: 14px;">Message Content</label>
                    <textarea name="message" rows="5" placeholder="Detailed announcement text..." required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--dark-color);"></textarea>
                </div>

                <div style="margin-bottom: 30px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 700; font-size: 14px;">Call to Action Link (Optional)</label>
                    <input type="text" name="link" placeholder="https://..." style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--dark-color);">
                </div>

                <button type="submit" name="broadcast" style="width: 100%; padding: 15px; border-radius: 10px; border: none; background: var(--primary-gradient); color: white; font-weight: 800; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                    <i class="fa fa-broadcast-tower" style="margin-right: 10px;"></i> Broadcast Announcement
                </button>
            </form>
        </div>

        <!-- Sidebar / Recent Alerts -->
        <div>
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 25px; margin-bottom: 20px;">
                <h4 style="margin-bottom: 15px; font-size: 16px; font-weight: 800;">Announcement Tips</h4>
                <ul style="padding-left: 20px; font-size: 13px; color: var(--gray-color); line-height: 1.6;">
                    <li>Keep titles short and punchy.</li>
                    <li>Use links to direct users to new features.</li>
                    <li>Broadcasts are real-time via polling.</li>
                    <li>Announcements are saved for auditing.</li>
                </ul>
            </div>

            <div style="background: rgba(155, 89, 182, 0.1); border: 1px solid rgba(155, 89, 182, 0.2); border-radius: 16px; padding: 25px; color: #9b59b6;">
                <h4 style="margin-bottom: 10px; color: #9b59b6; font-weight: 800;">Real-time Engine</h4>
                <p style="font-size: 12px; line-height: 1.5;">The system uses a 15-second polling cycle with server-side validation to ensure all active sessions receive alerts without high server load.</p>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/admin/admin_footer.php'; ?>
