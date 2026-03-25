<?php
require_once '../../config/db.php';
session_start();

$root = "../../";
$page_title = 'Global Announcements';
include '../../includes/admin/admin_header.php';

$msg = '';
$msg_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_announcement'])) {
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);
    $target_audience = $_POST['target_audience'] ?? 'all';

    if (empty($title) || empty($message)) {
        $msg = "Title and message are required.";
        $msg_type = "error";
    }
    else {
        try {
            // Determine who gets this
            $sql = "SELECT id FROM users";
            if ($target_audience === 'students') {
                $sql .= " WHERE role = 'student'";
            }
            elseif ($target_audience === 'instructors') {
                $sql .= " WHERE role = 'instructor'";
            }

            $users = $pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN);

            if (empty($users)) {
                $msg = "No users found in the selected audience.";
                $msg_type = "error";
            }
            else {
                // Batch insert into notifications
                // Standard MySQL has limits, but thousands of rows in one query usually fine.
                // For safety in shared hosting environments, a prepared statement in loop or chunking is best.
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message) VALUES (?, ?, ?)");

                $count = 0;
                foreach ($users as $uid) {
                    $stmt->execute([$uid, $title, $message]);
                    $count++;
                }

                $pdo->commit();
                $msg = "Announcement successfully sent to $count user(s)!";
                $msg_type = "success";
            }
        }
        catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $msg = "Database Error: " . $e->getMessage();
            $msg_type = "error";
        }
    }
}
?>

<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px;">
    <div>
        <h1 style="font-size: 28px; font-weight: 800; color: var(--dark-color); margin-bottom: 8px;">Announcements</h1>
        <p style="color: var(--gray-color); font-size: 15px;">Send global push notifications to users across the platform.</p>
    </div>
</div>

<?php if ($msg): ?>
<div style="background: <?php echo $msg_type === 'error' ? 'rgba(231, 76, 60, 0.1)' : 'rgba(46, 204, 113, 0.1)'; ?>; border: 1px solid <?php echo $msg_type === 'error' ? '#e74c3c' : '#2ecc71'; ?>; color: <?php echo $msg_type === 'error' ? '#e74c3c' : '#2ecc71'; ?>; padding: 15px; border-radius: 8px; margin-bottom: 25px; font-weight: 700;">
    <?php echo htmlspecialchars($msg); ?>
</div>
<?php
endif; ?>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
    
    <!-- Composer Form -->
    <div style="background: var(--bg-card); border-radius: 12px; padding: 30px; border: 1px solid var(--border-color); box-shadow: var(--shadow);">
        <h3 style="font-size: 18px; font-weight: 800; color: var(--dark-color); margin-bottom: 25px;"><i class="fa fa-paper-plane" style="color: var(--primary-color);"></i> Compose New Announcement</h3>
        
        <form method="POST">
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 8px;">Target Audience</label>
                <select name="target_audience" style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-size: 15px; cursor: pointer;">
                    <option value="all">All Registered Users</option>
                    <option value="students">Students Only</option>
                    <option value="instructors">Instructors Only</option>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 8px;">Notification Title *</label>
                <input type="text" name="title" required placeholder="e.g. Platform Maintenance Saturday" style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-size: 15px; font-family: inherit;">
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 8px;">Message Content *</label>
                <textarea name="message" required rows="5" placeholder="Details of the announcement..." style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-size: 15px; font-family: inherit; resize: vertical;"></textarea>
                <div style="font-size: 12px; color: var(--gray-color); margin-top: 5px;">This will appear in the user's notification bell menu.</div>
            </div>

            <button type="submit" name="send_announcement" class="btn btn-primary" style="width: 100%; font-size: 16px; padding: 15px; border-radius: 8px;">
                <i class="fa fa-paper-plane"></i> Broadcast Message
            </button>
        </form>
    </div>

    <!-- Guidelines / Tips -->
    <div style="background: var(--light-gray); border-radius: 12px; padding: 30px; border: 1px solid var(--border-color);">
        <h3 style="font-size: 16px; font-weight: 800; color: var(--dark-color); margin-bottom: 15px;"><i class="fa fa-info-circle"></i> Best Practices</h3>
        <ul style="color: var(--gray-color); font-size: 14px; line-height: 1.6; padding-left: 20px;">
            <li style="margin-bottom: 10px;"><strong>Be Concise:</strong> Keep titles short and messages under 200 characters for best display in the dropdown.</li>
            <li style="margin-bottom: 10px;"><strong>Use Emojis:</strong> A quick emoji (🎉, ⚠️, 🚀) in the title catches attention immediately.</li>
            <li style="margin-bottom: 10px;"><strong>Target Correctly:</strong> Don't spam instructors with student-only promotions.</li>
        </ul>
        <div style="margin-top: 30px; padding: 15px; background: rgba(229, 57, 53, 0.1); border-radius: 8px; border: 1px dashed var(--primary-color);">
            <strong style="color: var(--primary-color); display: block; margin-bottom: 5px;"><i class="fa fa-exclamation-triangle"></i> Warning</strong>
            <span style="font-size: 12px; color: var(--dark-color);">Announcements are instantaneous and cannot be unsent. Review carefully before broadcasting.</span>
        </div>
    </div>

</div>

<?php include '../../includes/admin/admin_footer.php'; ?>
