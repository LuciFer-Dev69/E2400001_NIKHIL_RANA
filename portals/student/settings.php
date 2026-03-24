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

    // Fetch Gamification Stats
    $stmt = $pdo->prepare("SELECT * FROM gamification_stats WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $g_stats = $stmt->fetch();

    // Fetch Badges
    $stmt = $pdo->prepare("SELECT badge_name, unlocked_at FROM badges_earned WHERE user_id = ? ORDER BY unlocked_at DESC");
    $stmt->execute([$user_id]);
    $my_badges = $stmt->fetchAll();
}
catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$root = "../../";
$page_title = 'Profile & Settings';
include '../../includes/portal_header.php';
?>
        
        <div style="max-width: 800px; margin: 0 auto; padding-top: 20px;">
            <div class="settings-box" style="border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; background: var(--bg-card);">
                <!-- Settings Header -->
                <div style="padding: 30px; border-bottom: 1px solid var(--border-color); text-align: center;">
                    <?php
$initials = '';
$nameParts = explode(' ', $user['full_name']);
foreach ($nameParts as $part) {
    $initials .= strtoupper(substr($part, 0, 1));
}
if (strlen($initials) > 2)
    $initials = substr($initials, 0, 2);
?>
                    <div class="user-avatar" style="width: 100px; height: 100px; font-size: 36px; margin: 0 auto 15px auto;">
                        <?php echo $initials; ?>
                    </div>
                    <h1 style="font-size: 24px; font-weight: 700; color: var(--dark-color); margin-bottom: 5px;"><?php echo htmlspecialchars($user['full_name']); ?></h1>
                    <p style="color: var(--gray-color); font-size: 14px; margin-bottom: 25px;"><?php echo htmlspecialchars($user['email']); ?></p>

                    <div style="display: flex; gap: 40px; justify-content: center; background: var(--light-gray); padding: 20px; border-radius: 8px;">
                        <div style="text-align: center;">
                            <div style="font-size: 28px; font-weight: 800; color: #FF416C;"><i class="fa fa-star" style="font-size: 18px; position: relative; top: -3px;"></i> <?php echo number_format($g_stats['xp'] ?? 0); ?></div>
                            <div style="font-size: 11px; color: var(--gray-color); font-weight: 700; text-transform: uppercase;">Total XP</div>
                        </div>
                        <div style="width: 1px; background: #d1d7dc;"></div>
                        <div style="text-align: center;">
                            <div style="font-size: 28px; font-weight: 800; color: #2ecc71;"><i class="fa fa-award" style="font-size: 18px; position: relative; top: -3px;"></i> <?php echo count($my_badges); ?></div>
                            <div style="font-size: 11px; color: #6a6f73; font-weight: 700; text-transform: uppercase;">Badges</div>
                        </div>
                        <div style="width: 1px; background: #d1d7dc;"></div>
                        <div style="text-align: center;">
                            <div style="font-size: 28px; font-weight: 800; color: #e67e22;"><i class="fa fa-fire" style="font-size: 18px; position: relative; top: -3px;"></i> <?php echo $g_stats['streak_days'] ?? 0; ?></div>
                            <div style="font-size: 11px; color: #6a6f73; font-weight: 700; text-transform: uppercase;">Day Streak</div>
                        </div>
                    </div>
                </div>

                <!-- Settings Tabs -->
                <div style="display: flex; border-bottom: 1px solid var(--border-color); background: var(--light-gray);">
                    <button class="settings-tab active" data-target="profile-tab" style="flex: 1; padding: 15px; background: none; border: none; font-weight: 700; font-size: 15px; color: var(--dark-color); border-bottom: 3px solid var(--dark-color); cursor: pointer;">Public Profile</button>
                    <button class="settings-tab" data-target="badges-tab" style="flex: 1; padding: 15px; background: none; border: none; font-weight: 700; font-size: 15px; color: var(--gray-color); border-bottom: 3px solid transparent; cursor: pointer;">Achievements</button>
                    <button class="settings-tab" data-target="account-tab" style="flex: 1; padding: 15px; background: none; border: none; font-weight: 700; font-size: 15px; color: var(--gray-color); border-bottom: 3px solid transparent; cursor: pointer;">Account Security</button>
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

                    <!-- Achievements Tab -->
                    <div id="badges-tab" class="settings-content" style="display: none;">
                        <h2 style="font-size: 20px; font-weight: 800; margin-bottom: 20px; color: var(--dark-color);">Your Badges</h2>
                        <?php if (empty($my_badges)): ?>
                            <div style="text-align: center; padding: 40px; background: #f7f9fa; border-radius: 8px; border: 1px dashed #d1d7dc;">
                                <i class="fa fa-award" style="font-size: 40px; color: #d1d7dc; margin-bottom: 15px;"></i>
                                <p style="color: #6a6f73; font-weight: 700;">You haven't earned any badges yet.</p>
                                <p style="font-size: 14px; color: #6a6f73;">Complete lessons and courses to unlock achievements!</p>
                            </div>
                        <?php
else: ?>
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
                                <?php foreach ($my_badges as $badge): ?>
                                    <div style="background: #fff; border: 1px solid #d1d7dc; border-radius: 8px; padding: 20px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                                        <div style="width: 60px; height: 60px; background: #e8f5e9; color: #2ecc71; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 15px auto;">
                                            <i class="fa fa-medal"></i>
                                        </div>
                                        <h3 style="font-size: 16px; font-weight: 800; color: var(--dark-color); margin-bottom: 5px;"><?php echo htmlspecialchars($badge['badge_name']); ?></h3>
                                        <p style="font-size: 12px; color: var(--gray-color);">Unlocked on<br><?php echo date('M j, Y', strtotime($badge['unlocked_at'])); ?></p>
                                    </div>
                                <?php
    endforeach; ?>
                            </div>
                        <?php
endif; ?>
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
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const activeColor = isDark ? '#ffffff' : '#1c1d1f';
            const inactiveColor = isDark ? 'var(--gray-color)' : '#6a6f73';

            tab.style.borderBottomColor = activeColor;
            tab.style.color = activeColor;
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

<?php include '../../includes/portal_footer.php'; ?>
