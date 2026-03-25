<?php
require_once '../../config/db.php';
session_start();

$root = "../../";
$page_title = 'Profile Settings';
include '../../includes/instructor/instructor_header.php';

$inst_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$inst_id]);
$user = $stmt->fetch();

$msg = '';
$msg_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bio = trim($_POST['bio'] ?? '');
    $expertise = trim($_POST['expertise'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');

    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, bio = ?, expertise = ? WHERE id = ?");
    $stmt->execute([$full_name, $bio, $expertise, $inst_id]);

    $_SESSION['full_name'] = $full_name;
    $msg = 'Profile updated successfully!';
    $msg_type = 'success';
    // Re-fetch
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$inst_id]);
    $user = $stmt->fetch();
}
?>

<div style="max-width: 650px; margin: 0 auto;">
    <h1 style="font-size: 28px; font-weight: 800; color: var(--dark-color); margin-bottom: 8px;">Profile Settings</h1>
    <p style="color: var(--gray-color); margin-bottom: 30px;">Update your public instructor profile that students see on your course pages.</p>

    <?php if ($msg): ?>
    <div style="background: rgba(46,204,113,0.1); border: 1px solid #2ecc71; color: #2ecc71; padding: 12px 18px; border-radius: 8px; margin-bottom: 25px; font-weight: 700;">
        <i class="fa fa-check-circle"></i> <?php echo htmlspecialchars($msg); ?>
    </div>
    <?php
endif; ?>

    <form method="POST">
        <div style="background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color); padding: 30px; box-shadow: var(--shadow);">
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; font-size: 14px; color: var(--dark-color); margin-bottom: 8px;">Full Name</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-family: inherit;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; font-size: 14px; color: var(--dark-color); margin-bottom: 8px;">Expertise / Headline</label>
                <input type="text" name="expertise" value="<?php echo htmlspecialchars($user['expertise'] ?? ''); ?>" placeholder="e.g. Full Stack Developer | 10+ Years Experience" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-family: inherit;">
            </div>
            <div style="margin-bottom: 25px;">
                <label style="display: block; font-weight: 700; font-size: 14px; color: var(--dark-color); margin-bottom: 8px;">Bio</label>
                <textarea name="bio" rows="6" placeholder="Tell students about yourself, your experience, and what you teach..." style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-family: inherit; resize: vertical;"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
            </div>
            <div style="background: var(--light-gray); border-radius: 8px; padding: 15px; margin-bottom: 25px; border: 1px solid var(--border-color);">
                <div style="font-size: 13px; color: var(--gray-color);">
                    <i class="fa fa-envelope" style="margin-right: 6px;"></i><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?>
                    <span style="margin-left: 15px; color: var(--gray-color); font-size: 11px;">(Contact Admin to change email)</span>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 13px; font-size: 16px; font-weight: 800; background: #9b59b6; border-color: #9b59b6;">
                <i class="fa fa-save" style="margin-right: 8px;"></i> Save Profile
            </button>
        </div>
    </form>
</div>

<?php include '../../includes/instructor/instructor_footer.php'; ?>
