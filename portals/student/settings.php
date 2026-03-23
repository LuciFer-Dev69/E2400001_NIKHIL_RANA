<?php
require_once '../../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$portal_type = 'student';

// Fetch user data
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
}
catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$base_url = "../../";
$portal_context = 'student';
include '../../includes/header.php';
?>

<div class="portal-shell">
    <?php include '../../includes/portal_header_sidebar.php'; ?>
    <div class="portal-content">
        
        <div style="max-width: 800px; margin: 0 auto; padding-top: 20px;">
            <div style="background: #fff; border: 1px solid #d1d7dc; border-radius: 8px; overflow: hidden;">
                <!-- Settings Header -->
                <div style="padding: 30px; border-bottom: 1px solid #d1d7dc; text-align: center;">
                    <?php
$initials = '';
$nameParts = explode(' ', $user['full_name']);
foreach ($nameParts as $part) {
    $initials .= strtoupper(substr($part, 0, 1));
}
if (strlen($initials) > 2)
    $initials = substr($initials, 0, 2);
?>
                    <div style="width: 100px; height: 100px; background: #1c1d1f; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 36px; font-weight: 700; margin: 0 auto 15px auto;">
                        <?php echo $initials; ?>
                    </div>
                    <h1 style="font-size: 24px; font-weight: 700; color: #1c1d1f; margin-bottom: 5px;"><?php echo htmlspecialchars($user['full_name']); ?></h1>
                    <p style="color: #6a6f73; font-size: 14px;"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>

                <!-- Settings Tabs -->
                <div style="display: flex; border-bottom: 1px solid #d1d7dc; background: #f7f9fa;">
                    <button class="settings-tab active" data-target="profile-tab" style="flex: 1; padding: 15px; background: none; border: none; font-weight: 700; font-size: 15px; color: #1c1d1f; border-bottom: 3px solid #1c1d1f; cursor: pointer;">Public Profile</button>
                    <button class="settings-tab" data-target="account-tab" style="flex: 1; padding: 15px; background: none; border: none; font-weight: 700; font-size: 15px; color: #6a6f73; border-bottom: 3px solid transparent; cursor: pointer;">Account Security</button>
                </div>

                <!-- Tab Contents -->
                <div style="padding: 30px;">
                    
                    <!-- Profile Tab -->
                    <div id="profile-tab" class="settings-content">
                        <form id="profile-form">
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; font-weight: 700; margin-bottom: 8px; font-size: 14px;">Full Name</label>
                                <input type="text" id="profile-name" value="<?php echo htmlspecialchars($user['full_name']); ?>" style="width: 100%; padding: 12px; border: 1px solid #d1d7dc; border-radius: 4px; font-size: 15px;">
                            </div>
                            <div style="margin-bottom: 25px;">
                                <label style="display: block; font-weight: 700; margin-bottom: 8px; font-size: 14px;">Headline / Bio</label>
                                <textarea id="profile-bio" rows="4" style="width: 100%; padding: 12px; border: 1px solid #d1d7dc; border-radius: 4px; font-size: 15px; font-family: inherit;" placeholder="Tell us a bit about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                            </div>
                            <div id="profile-msg" style="margin-bottom: 15px; font-weight: 700; font-size: 14px;"></div>
                            <button type="submit" class="btn btn-primary" id="save-profile-btn" style="background: #1c1d1f; color: white;">Save Profile</button>
                        </form>
                    </div>

                    <!-- Account Tab -->
                    <div id="account-tab" class="settings-content" style="display: none;">
                        <form id="password-form">
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; font-weight: 700; margin-bottom: 8px; font-size: 14px;">Current Password</label>
                                <input type="password" id="current-password" required style="width: 100%; padding: 12px; border: 1px solid #d1d7dc; border-radius: 4px; font-size: 15px;">
                            </div>
                            <div style="margin-bottom: 25px;">
                                <label style="display: block; font-weight: 700; margin-bottom: 8px; font-size: 14px;">New Password</label>
                                <input type="password" id="new-password" required style="width: 100%; padding: 12px; border: 1px solid #d1d7dc; border-radius: 4px; font-size: 15px;">
                            </div>
                            <div id="password-msg" style="margin-bottom: 15px; font-weight: 700; font-size: 14px;"></div>
                            <button type="submit" class="btn btn-primary" id="save-password-btn" style="background: #1c1d1f; color: white;">Change Password</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // Tab Switching
    document.querySelectorAll('.settings-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            // Reset active states
            document.querySelectorAll('.settings-tab').forEach(t => {
                t.style.borderBottomColor = 'transparent';
                t.style.color = '#6a6f73';
                t.classList.remove('active');
            });
            document.querySelectorAll('.settings-content').forEach(c => c.style.display = 'none');
            
            // Set new active state
            tab.style.borderBottomColor = '#1c1d1f';
            tab.style.color = '#1c1d1f';
            tab.classList.add('active');
            document.getElementById(tab.dataset.target).style.display = 'block';
        });
    });

    // Profile Update
    document.getElementById('profile-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('save-profile-btn');
        const msg = document.getElementById('profile-msg');
        
        btn.disabled = true;
        btn.innerText = 'Saving...';
        msg.innerText = '';

        try {
            const res = await fetch('api/update_profile.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'update_profile',
                    full_name: document.getElementById('profile-name').value,
                    bio: document.getElementById('profile-bio').value
                })
            });
            const data = await res.json();
            
            msg.innerText = data.message;
            msg.style.color = data.success ? '#2ecc71' : '#e74c3c';
            
            if(data.success) {
                setTimeout(() => location.reload(), 1000);
            }
        } catch (err) {
            msg.innerText = 'Network error occurred.';
            msg.style.color = '#e74c3c';
        }
        btn.disabled = false;
        btn.innerText = 'Save Profile';
    });

    // Password Update
    document.getElementById('password-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('save-password-btn');
        const msg = document.getElementById('password-msg');
        
        btn.disabled = true;
        btn.innerText = 'Verifying...';
        msg.innerText = '';

        try {
            const res = await fetch('api/update_profile.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'change_password',
                    current_password: document.getElementById('current-password').value,
                    new_password: document.getElementById('new-password').value
                })
            });
            const data = await res.json();
            
            msg.innerText = data.message;
            msg.style.color = data.success ? '#2ecc71' : '#e74c3c';
            
            if(data.success) {
                document.getElementById('current-password').value = '';
                document.getElementById('new-password').value = '';
            }
        } catch (err) {
            msg.innerText = 'Network error occurred.';
            msg.style.color = '#e74c3c';
        }
        btn.disabled = false;
        btn.innerText = 'Change Password';
    });
</script>

<?php include '../../includes/footer.php'; ?>
