<?php
require_once '../../config/db.php';
session_start();

$root = "../../";
$page_title = 'User Details';
include '../../includes/admin/admin_header.php';

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$user_id) {
    echo "<div class='container' style='padding: 40px;'>User not found.</div>";
    include '../../includes/admin/admin_footer.php';
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "<div class='container' style='padding: 40px;'>User not found.</div>";
    include '../../includes/admin/admin_footer.php';
    exit();
}

// Stats
$stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE student_id = ?");
$stmt->execute([$user_id]);
$enrolled_count = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE instructor_id = ?");
$stmt->execute([$user_id]);
$created_courses_count = $stmt->fetchColumn();
?>

<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px;">
    <div>
        <a href="users.php" style="color: var(--primary-color); text-decoration: none; font-weight: 700; font-size: 14px; margin-bottom: 10px; display: inline-block;">&larr; Back to Users</a>
        <h1 style="font-size: 28px; font-weight: 800; color: var(--dark-color); margin-bottom: 8px;">User Profile: <?php echo htmlspecialchars($user['full_name']); ?></h1>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
    <!-- Left Column: Quick Actions & Stats -->
    <div>
        <div style="background: var(--bg-card); padding: 30px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow); text-align: center; margin-bottom: 20px;">
            <img src="../../assets/img/users/<?php echo $user['profile_img'] ?: 'default.png'; ?>" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; margin-bottom: 20px; border: 4px solid var(--light-gray);" onerror="this.src='https://via.placeholder.com/120'">
            <h3 style="font-size: 20px; font-weight: 800; color: var(--dark-color); margin-bottom: 5px;"><?php echo htmlspecialchars($user['full_name']); ?></h3>
            <p style="color: var(--gray-color); font-size: 14px; margin-bottom: 15px;">@<?php echo htmlspecialchars($user['username'] ?? 'user'); ?></p>
            
            <div style="display: inline-block; padding: 5px 15px; border-radius: 30px; font-size: 12px; font-weight: 800; text-transform: uppercase; margin-bottom: 20px;
                <?php
if ($user['role'] == 'admin')
    echo 'background: rgba(155, 89, 182, 0.1); color: #9b59b6;';
elseif ($user['role'] == 'instructor')
    echo 'background: rgba(243, 156, 18, 0.1); color: #f39c12;';
else
    echo 'background: rgba(46, 204, 113, 0.1); color: #2ecc71;';
?>
            ">
                <?php echo strtoupper($user['role']); ?>
            </div>

            <div style="display: flex; justify-content: space-around; padding-top: 20px; border-top: 1px solid var(--border-color);">
                <div>
                    <div style="font-size: 20px; font-weight: 800; color: var(--dark-color);"><?php echo $enrolled_count; ?></div>
                    <div style="font-size: 11px; color: var(--gray-color); text-transform: uppercase;">Enrolled</div>
                </div>
                <?php if ($user['role'] === 'instructor'): ?>
                <div>
                    <div style="font-size: 20px; font-weight: 800; color: var(--dark-color);"><?php echo $created_courses_count; ?></div>
                    <div style="font-size: 11px; color: var(--gray-color); text-transform: uppercase;">Courses</div>
                </div>
                <?php
endif; ?>
            </div>
        </div>
        
        <div style="background: var(--bg-card); padding: 25px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow);">
            <h4 style="font-size: 16px; font-weight: 800; color: var(--dark-color); margin-bottom: 15px;">Account Danger Zone</h4>
            
            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                <?php if ($user['status'] !== 'suspended'): ?>
                    <button class="btn btn-secondary" style="width: 100%; border-color: #e67e22; color: #e67e22; margin-bottom: 15px;" onclick="suspendUser(<?php echo $user['id']; ?>, 'suspended')"><i class="fa fa-ban"></i> Suspend Account</button>
                <?php
    else: ?>
                    <button class="btn btn-secondary" style="width: 100%; border-color: #2ecc71; color: #2ecc71; margin-bottom: 15px;" onclick="suspendUser(<?php echo $user['id']; ?>, 'active')"><i class="fa fa-check-circle"></i> Reactivate Account</button>
                <?php
    endif; ?>
                <button class="btn btn-secondary" style="width: 100%; border-color: #e74c3c; color: #e74c3c;" onclick="deleteUser(<?php echo $user['id']; ?>)"><i class="fa fa-trash-alt"></i> Delete Permanently</button>
            <?php
else: ?>
                <p style="font-size: 13px; color: var(--gray-color); font-style: italic;">You cannot suspend or delete your own admin account.</p>
            <?php
endif; ?>
        </div>
    </div>

    <!-- Right Column: Edit Form -->
    <div style="background: var(--bg-card); padding: 30px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow);">
        <h3 style="font-size: 20px; font-weight: 800; color: var(--dark-color); margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid var(--border-color);">Edit Details</h3>
        
        <form id="edit-user-form">
            <input type="hidden" name="action" value="edit_user">
            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 8px; font-size: 14px;">Full Name</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-family: inherit;">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 8px; font-size: 14px;">Username</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-family: inherit;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 8px; font-size: 14px;">Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-family: inherit;">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 8px; font-size: 14px;">Location</label>
                    <input type="text" name="location" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>" placeholder="E.g. New York, USA" style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-family: inherit;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 8px; font-size: 14px;">Role</label>
                    <select name="role" style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-family: inherit;">
                        <option value="student" <?php echo $user['role'] === 'student' ? 'selected' : ''; ?>>Student</option>
                        <option value="instructor" <?php echo $user['role'] === 'instructor' ? 'selected' : ''; ?>>Instructor</option>
                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Administrator</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0; position: relative;">
                    <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 8px; font-size: 14px;">User Password</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="user-plain-pwd" value="<?php echo htmlspecialchars($user['plain_password'] ?? ''); ?>" placeholder="Type to reset password" style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-family: inherit; font-family: monospace;">
                        <button type="button" onclick="togglePwdVisibility()" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--primary-color); cursor: pointer;"><i class="fa fa-eye" id="pwd-eye"></i></button>
                    </div>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 25px;">
                <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 8px; font-size: 14px;">Bio</label>
                <textarea name="bio" rows="4" style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-family: inherit;"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
            </div>

            <div id="form-msg" style="margin-bottom: 20px; font-weight: 700; font-size: 14px; text-align: center;"></div>

            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" id="save-user-btn" class="btn btn-primary" style="padding: 12px 30px; font-size: 16px;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePwdVisibility() {
        const p = document.getElementById('user-plain-pwd');
        const eye = document.getElementById('pwd-eye');
        if (p.type === 'password') {
            p.type = 'text';
            eye.className = 'fa fa-eye-slash';
        } else {
            p.type = 'password';
            eye.className = 'fa fa-eye';
        }
    }

    document.getElementById('edit-user-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('save-user-btn');
        const msg = document.getElementById('form-msg');
        
        btn.disabled = true;
        btn.innerText = 'Saving...';
        msg.innerText = '';
        
        const data = new FormData(this);
        const jsonData = {};
        for (const [k, v] of data.entries()) {
            jsonData[k] = v;
        }

        fetch('../../api/admin_users.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(jsonData)
        })
        .then(res => res.json())
        .then(res => {
            msg.innerText = res.message;
            msg.style.color = res.success ? '#2ecc71' : '#e74c3c';
            if(res.success) {
                setTimeout(() => window.location.reload(), 1000);
            } else {
                btn.disabled = false;
                btn.innerText = 'Save Changes';
            }
        })
        .catch(err => {
            msg.innerText = 'Network error.';
            msg.style.color = '#e74c3c';
            btn.disabled = false;
            btn.innerText = 'Save Changes';
        });
    });

    function suspendUser(id, newStatus) {
        const actionText = newStatus === 'suspended' ? 'suspend' : 'reactivate';
        if(confirm(`Are you sure you want to ${actionText} this user?`)) {
            fetch('../../api/admin_users.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'suspend_user', user_id: id, status: newStatus })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(e => alert('Network error.'));
        }
    }

    function deleteUser(id) {
        if(confirm("DANGER: Are you sure you want to permanently delete this user? ALL data will be wiped immediately.")) {
            fetch('../../api/admin_users.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete_user', user_id: id })
            }).then(res => res.json()).then(data => {
                if(data.success) {
                    window.location.href = 'users.php';
                } else {
                    alert('Error: ' + data.message);
                }
            }).catch(e => alert('Network error occurred.'));
        }
    }
</script>

<?php include '../../includes/admin/admin_footer.php'; ?>
