<?php
require_once '../../config/db.php';
session_start();

$root = "../../";
$page_title = 'Platform Settings';
include '../../includes/admin/admin_header.php';

$msg = '';
$msg_type = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {

    // We expect the form to submit an array of settings
    if (isset($_POST['settings']) && is_array($_POST['settings'])) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");

            foreach ($_POST['settings'] as $key => $value) {
                // Ensure safe string
                $safe_val = trim($value);
                $stmt->execute([$key, $safe_val, $safe_val]);
            }

            $pdo->commit();
            $msg = "Platform settings updated successfully!";
            $msg_type = "success";
        }
        catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $msg = "Failed to update settings: " . $e->getMessage();
            $msg_type = "error";
        }
    }
}

// Fetch Current Settings
$settings_query = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
$current_settings = [];
while ($row = $settings_query->fetch(PDO::FETCH_ASSOC)) {
    $current_settings[$row['setting_key']] = $row['setting_value'];
}

// Defaults fallbacks if missing
$site_name = $current_settings['site_name'] ?? 'SkillEdu Platform';
$contact_email = $current_settings['contact_email'] ?? 'support@skilledu.com';
$maintenance_mode = $current_settings['maintenance_mode'] ?? 'false';
$hero_title = $current_settings['hero_title'] ?? 'Master Your Future';
$hero_subtitle = $current_settings['hero_subtitle'] ?? 'Learn from industry experts and take your career to the next level.';
?>

<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px;">
    <div>
        <h1 style="font-size: 28px; font-weight: 800; color: var(--dark-color); margin-bottom: 8px;">CMS & Platform Settings</h1>
        <p style="color: var(--gray-color); font-size: 15px;">Manage global configurations, contact details, and dynamic homepage content.</p>
    </div>
</div>

<?php if ($msg): ?>
<div style="background: <?php echo $msg_type === 'error' ? 'rgba(231, 76, 60, 0.1)' : 'rgba(46, 204, 113, 0.1)'; ?>; border: 1px solid <?php echo $msg_type === 'error' ? '#e74c3c' : '#2ecc71'; ?>; color: <?php echo $msg_type === 'error' ? '#e74c3c' : '#2ecc71'; ?>; padding: 15px; border-radius: 8px; margin-bottom: 25px; font-weight: 700;">
    <?php echo htmlspecialchars($msg); ?>
</div>
<?php
endif; ?>

<form method="POST">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        
        <!-- General System Settings -->
        <div style="background: var(--bg-card); border-radius: 12px; padding: 30px; border: 1px solid var(--border-color); box-shadow: var(--shadow);">
            <h3 style="font-size: 18px; font-weight: 800; color: var(--dark-color); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
                <i class="fa fa-cogs" style="color: var(--primary-color);"></i> System Settings
            </h3>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 8px;">Site Name</label>
                <input type="text" name="settings[site_name]" value="<?php echo htmlspecialchars($site_name); ?>" required style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-size: 15px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 8px;">Support Contact Email</label>
                <input type="email" name="settings[contact_email]" value="<?php echo htmlspecialchars($contact_email); ?>" required style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-size: 15px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 8px;">Maintenance Mode</label>
                <select name="settings[maintenance_mode]" style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-size: 15px;">
                    <option value="false" <?php echo $maintenance_mode === 'false' ? 'selected' : ''; ?>>Disabled (Site is Live)</option>
                    <option value="true" <?php echo $maintenance_mode === 'true' ? 'selected' : ''; ?>>Enabled (Show Maintenance Screen)</option>
                </select>
                <div style="font-size: 12px; color: #e74c3c; margin-top: 5px;"><i class="fa fa-exclamation-triangle"></i> Enabling this prevents non-admins from logging in.</div>
            </div>
        </div>

        <!-- Homepage Content CMS -->
        <div style="background: var(--bg-card); border-radius: 12px; padding: 30px; border: 1px solid var(--border-color); box-shadow: var(--shadow);">
            <h3 style="font-size: 18px; font-weight: 800; color: var(--dark-color); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
                <i class="fa fa-paint-brush" style="color: var(--primary-color);"></i> Homepage Content
            </h3>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 8px;">Hero Section Title</label>
                <input type="text" name="settings[hero_title]" value="<?php echo htmlspecialchars($hero_title); ?>" style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-size: 15px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 8px;">Hero Section Subtitle</label>
                <textarea name="settings[hero_subtitle]" rows="4" style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-size: 15px; font-family: inherit; resize: vertical;"><?php echo htmlspecialchars($hero_subtitle); ?></textarea>
            </div>

            <div style="margin-top: 30px; padding: 15px; background: rgba(52, 152, 219, 0.1); border-radius: 8px; border: 1px dashed #3498db;">
                <span style="font-size: 13px; color: var(--dark-color);"><i class="fa fa-info-circle" style="color: #3498db;"></i> CMS variables are immediately applied to the public-facing index page up completion of bridging.</span>
            </div>
        </div>

    </div>

    <!-- Sticky Save Bar -->
    <div style="position: sticky; bottom: 20px; background: var(--bg-card); padding: 20px; border-radius: 12px; border: 1px solid var(--border-color); margin-top: 30px; display: flex; justify-content: flex-end; box-shadow: 0 -4px 15px rgba(0,0,0,0.1);">
        <button type="submit" name="save_settings" class="btn btn-primary" style="padding: 12px 40px; font-size: 16px;">
            <i class="fa fa-save"></i> Save All Settings
        </button>
    </div>
</form>

<?php include '../../includes/admin/admin_footer.php'; ?>
