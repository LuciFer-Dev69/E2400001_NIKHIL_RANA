<?php
require_once '../../config/db.php';
session_start();

$root = "../../";
$page_title = 'Manage Users';
include '../../includes/admin/admin_header.php';

// Pagination setup
$limit = 15;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Search/Filter Setup
$search = $_GET['search'] ?? '';
$role_filter = $_GET['role'] ?? '';

$sql = "SELECT id, full_name, email, role, created_at, verification_doc FROM users WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (full_name LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if (!empty($role_filter) && in_array($role_filter, ['student', 'instructor', 'admin'])) {
    $sql .= " AND role = ?";
    $params[] = $role_filter;
}

// Get Total for Pagination
$count_sql = str_replace("SELECT id, full_name, email, role, created_at", "SELECT COUNT(*)", $sql);
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_users = $stmt->fetchColumn();
$total_pages = ceil($total_users / $limit);

// Fetch Paginated List
$sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$stmt = $pdo->prepare($sql);
// PDO requires integers for LIMIT/OFFSET when using emulate prepares (which is default sometimes). 
// Binding manually to be safe.
foreach ($params as $key => $val) {
    if (is_int($val)) {
        $stmt->bindValue($key + 1, $val, PDO::PARAM_INT);
    }
    else {
        $stmt->bindValue($key + 1, $val, PDO::PARAM_STR);
    }
}
$stmt->execute();
$users = $stmt->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px;">
    <div>
        <h1 style="font-size: 28px; font-weight: 800; color: var(--dark-color); margin-bottom: 8px;">Manage Users</h1>
        <p style="color: var(--gray-color); font-size: 15px;">View, moderate, and change roles for all platform users.</p>
    </div>
    <div>
        <button class="btn btn-primary" onclick="openAddUserModal()" style="padding: 10px 20px; font-weight: 700;"><i class="fa fa-user-plus" style="margin-right: 8px;"></i> Add New User</button>
    </div>
</div>

<!-- Role Tabs -->
<div style="display: flex; gap: 2px; margin-bottom: 25px; border-bottom: 2px solid var(--border-color); padding-bottom: 0;">
    <a href="users.php" class="tab-btn <?php echo $role_filter === '' ? 'active' : ''; ?>" style="padding: 12px 25px; text-decoration: none; color: <?php echo $role_filter === '' ? 'var(--primary-color)' : 'var(--gray-color)'; ?>; font-weight: 700; border-bottom: 2px solid <?php echo $role_filter === '' ? 'var(--primary-color)' : 'transparent'; ?>; margin-bottom: -2px; transition: 0.2s;">All Users</a>
    <a href="users.php?role=instructor" class="tab-btn <?php echo $role_filter === 'instructor' ? 'active' : ''; ?>" style="padding: 12px 25px; text-decoration: none; color: <?php echo $role_filter === 'instructor' ? 'var(--primary-color)' : 'var(--gray-color)'; ?>; font-weight: 700; border-bottom: 2px solid <?php echo $role_filter === 'instructor' ? 'var(--primary-color)' : 'transparent'; ?>; margin-bottom: -2px; transition: 0.2s;">Instructors</a>
    <a href="users.php?role=student" class="tab-btn <?php echo $role_filter === 'student' ? 'active' : ''; ?>" style="padding: 12px 25px; text-decoration: none; color: <?php echo $role_filter === 'student' ? 'var(--primary-color)' : 'var(--gray-color)'; ?>; font-weight: 700; border-bottom: 2px solid <?php echo $role_filter === 'student' ? 'var(--primary-color)' : 'transparent'; ?>; margin-bottom: -2px; transition: 0.2s;">Students</a>
    <a href="users.php?role=admin" class="tab-btn <?php echo $role_filter === 'admin' ? 'active' : ''; ?>" style="padding: 12px 25px; text-decoration: none; color: <?php echo $role_filter === 'admin' ? 'var(--primary-color)' : 'var(--gray-color)'; ?>; font-weight: 700; border-bottom: 2px solid <?php echo $role_filter === 'admin' ? 'var(--primary-color)' : 'transparent'; ?>; margin-bottom: -2px; transition: 0.2s;">Admins</a>
</div>

<!-- Filters Bar -->
<div style="background: var(--bg-card); padding: 20px; border-radius: 8px; border: 1px solid var(--border-color); box-shadow: var(--shadow); margin-bottom: 25px; display: flex; gap: 15px; align-items: center;">
    <form method="GET" style="display: flex; gap: 15px; flex: 1;">
        <input type="text" name="search" placeholder="Search name or email..." value="<?php echo htmlspecialchars($search); ?>" style="flex: 1; padding: 10px 15px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); max-width: 400px; font-family: inherit;">
        
        <select name="role" style="padding: 10px 15px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-family: inherit;">
            <option value="">All Roles</option>
            <option value="student" <?php echo $role_filter === 'student' ? 'selected' : ''; ?>>Student</option>
            <option value="instructor" <?php echo $role_filter === 'instructor' ? 'selected' : ''; ?>>Instructor</option>
            <option value="admin" <?php echo $role_filter === 'admin' ? 'selected' : ''; ?>>Admin</option>
        </select>
        
        <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">Filter</button>
        <?php if (!empty($search) || !empty($role_filter)): ?>
            <a href="users.php" class="btn btn-secondary" style="padding: 10px 20px;">Clear</a>
        <?php
endif; ?>
    </form>
    <div style="font-weight: 700; color: var(--gray-color); text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">
        Total: <?php echo number_format($total_users); ?>
    </div>
</div>

<!-- Users Table -->
<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <?php if ($role_filter === 'instructor'): ?>
                    <th>Documents</th>
                <?php
endif; ?>
                <th>Joined</th>
                <th style="width: 150px; text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--gray-color);">No users found.</td></tr>
            <?php
else: ?>
                <?php foreach ($users as $user): ?>
                <tr id="user-row-<?php echo $user['id']; ?>">
                    <td style="font-weight: 700; color: var(--gray-color);">#<?php echo $user['id']; ?></td>
                    <td style="font-weight: 800;"><a href="user_details.php?id=<?php echo $user['id']; ?>" style="color: var(--dark-color); text-decoration: none;"><?php echo htmlspecialchars($user['full_name']); ?></a></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <?php
        if ($user['role'] === 'admin')
            echo '<span class="badge badge-primary">Admin</span>';
        elseif ($user['role'] === 'instructor')
            echo '<span class="badge badge-warning">Instructor</span>';
        else
            echo '<span class="badge badge-success">Student</span>';
?>
                    </td>
                    <?php if ($role_filter === 'instructor'): ?>
                        <td>
                            <?php if (!empty($user['verification_doc'])): ?>
                                <a href="<?php echo $root . $user['verification_doc']; ?>" target="_blank" class="btn btn-secondary" style="padding: 5px 10px; font-size: 11px;">
                                    <i class="fa fa-file-alt"></i> View Doc
                                </a>
                            <?php
            else: ?>
                                <span style="font-size: 11px; color: var(--gray-color);">No Doc</span>
                            <?php
            endif; ?>
                        </td>
                    <?php
        endif; ?>
                    <td style="font-size: 13px; color: var(--gray-color);"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <div class="action-btns" style="justify-content: center;">
                            <a href="user_details.php?id=<?php echo $user['id']; ?>" class="btn-icon" title="View Details"><i class="fa fa-eye"></i></a>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <button class="btn-icon" title="Change Role" onclick="openRoleModal(<?php echo $user['id']; ?>, '<?php echo $user['role']; ?>', '<?php echo htmlspecialchars(addslashes($user['full_name'])); ?>')"><i class="fa fa-user-edit"></i></button>
                                <button class="btn-icon" title="Delete User" style="color: #e74c3c; border-color: rgba(231,76,60,0.3);" onclick="deleteUser(<?php echo $user['id']; ?>)"><i class="fa fa-trash-alt"></i></button>
                            <?php
        endif; ?>
                        </div>
                    </td>
                </tr>
                <?php
    endforeach; ?>
            <?php
endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php if ($total_pages > 1): ?>
<div style="display: flex; justify-content: flex-end; align-items: center; gap: 10px; margin-top: 20px;">
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>" class="btn btn-secondary" style="padding: 8px 15px; font-weight: 700;"><i class="fa fa-chevron-left"></i></a>
    <?php
    else: ?>
        <button class="btn btn-secondary" disabled style="padding: 8px 15px; opacity: 0.5;"><i class="fa fa-chevron-left"></i></button>
    <?php
    endif; ?>

    <span style="font-weight: 700; font-size: 14px; color: var(--dark-color);">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>

    <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>" class="btn btn-secondary" style="padding: 8px 15px; font-weight: 700;"><i class="fa fa-chevron-right"></i></a>
    <?php
    else: ?>
        <button class="btn btn-secondary" disabled style="padding: 8px 15px; opacity: 0.5;"><i class="fa fa-chevron-right"></i></button>
    <?php
    endif; ?>
</div>
<?php
endif; ?>

<!-- Role Change Modal -->
<div id="role-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center;">
    <div style="background: var(--bg-card); width: 100%; max-width: 400px; border-radius: 12px; padding: 30px; border: 1px solid var(--border-color); box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
        <h2 style="font-size: 20px; font-weight: 800; color: var(--dark-color); margin-bottom: 20px;">Change Role: <span id="role-modal-name" style="color: var(--primary-color);"></span></h2>
        
        <input type="hidden" id="modal-user-id">
        <div style="margin-bottom: 20px;">
            <select id="modal-role-select" style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-size: 15px;">
                <option value="student">Student</option>
                <option value="instructor">Instructor</option>
                <option value="admin">Administrator</option>
            </select>
        </div>
        
        <div id="modal-msg" style="margin-bottom: 15px; font-weight: 700; font-size: 13px;"></div>
        
        <div style="display: flex; gap: 10px;">
            <button class="btn btn-secondary" style="flex: 1;" onclick="closeRoleModal()">Cancel</button>
            <button class="btn btn-primary" style="flex: 1;" id="modal-save-btn" onclick="saveRole()">Save</button>
        </div>
    </div>
</div>

<script>
    function openRoleModal(id, currentRole, name) {
        document.getElementById('modal-user-id').value = id;
        document.getElementById('modal-role-select').value = currentRole;
        document.getElementById('role-modal-name').innerText = name;
        document.getElementById('modal-msg').innerText = '';
        document.getElementById('role-modal').style.display = 'flex';
    }

    function closeRoleModal() {
        document.getElementById('role-modal').style.display = 'none';
    }

    async function saveRole() {
        const id = document.getElementById('modal-user-id').value;
        const role = document.getElementById('modal-role-select').value;
        const btn = document.getElementById('modal-save-btn');
        const msg = document.getElementById('modal-msg');
        
        btn.disabled = true;
        btn.innerText = 'Saving...';
        msg.innerText = '';

        try {
            const res = await fetch('../../api/admin_users.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'change_role', user_id: id, role: role })
            });
            const data = await res.json();
            
            msg.innerText = data.message;
            msg.style.color = data.success ? '#2ecc71' : '#e74c3c';
            
            if(data.success) {
                setTimeout(() => location.reload(), 800);
            } else {
                btn.disabled = false;
                btn.innerText = 'Save';
            }
        } catch (e) {
            msg.innerText = 'Network error.';
            msg.style.color = '#e74c3c';
            btn.disabled = false;
            btn.innerText = 'Save';
        }
    }

    function deleteUser(id) {
        if(confirm("DANGER: Are you sure you want to permanently delete this user? ALL their enrollments, courses, and data will be wiped immediately.")) {
            // Optimistic UI Removal
            const row = document.getElementById('user-row-' + id);
            if(row) row.style.opacity = '0.3';
            
            fetch('../../api/admin_users.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete_user', user_id: id })
            }).then(res => res.json()).then(data => {
                if(data.success) {
                    if(row) {
                        row.style.transition = "all 0.5s ease";
                        row.style.transform = "translateX(-100px)";
                        setTimeout(() => row.remove(), 500);
                    } else {
                        location.reload();
                    }
                } else {
                    alert('Error: ' + data.message);
                    if(row) row.style.opacity = '1';
                }
            }).catch(e => {
                alert('Network error occurred.');
                if(row) row.style.opacity = '1';
            });
        }
    }
</script>

<!-- Add User Modal -->
<div id="add-user-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 2000; align-items: center; justify-content: center; overflow-y: auto; padding: 20px;">
    <div style="background: var(--bg-card); width: 100%; max-width: 500px; border-radius: 12px; padding: 30px; border: 1px solid var(--border-color); box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative; margin: auto;">
        <button onclick="closeAddUserModal()" style="position: absolute; top: 15px; right: 20px; background: none; border: none; font-size: 24px; cursor: pointer; color: var(--gray-color);">&times;</button>
        <h2 style="font-size: 22px; font-weight: 800; color: var(--dark-color); margin-bottom: 25px;">Add New User</h2>
        
        <form id="add-user-form">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 5px; color: var(--dark-color);">Full Name</label>
                    <input type="text" id="add-fname" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color);">
                </div>
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 5px; color: var(--dark-color);">Username</label>
                    <input type="text" id="add-uname" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color);">
                </div>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 5px; color: var(--dark-color);">Email Address</label>
                <input type="email" id="add-email" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color);">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 5px; color: var(--dark-color);">Password</label>
                <input type="text" id="add-pwd" required placeholder="User will use this to login" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color);">
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 5px; color: var(--dark-color);">Role</label>
                <select id="add-role" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color);">
                    <option value="student">Student</option>
                    <option value="instructor">Instructor</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            
            <div id="add-msg" style="margin-bottom: 15px; font-weight: 700; font-size: 13px; text-align: center;"></div>

            <button type="submit" id="add-submit-btn" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 16px;">Create User</button>
        </form>
    </div>
</div>

<script>
    function openAddUserModal() {
        document.getElementById('add-user-modal').style.display = 'flex';
        document.getElementById('add-msg').innerText = '';
    }

    function closeAddUserModal() {
        document.getElementById('add-user-modal').style.display = 'none';
        document.getElementById('add-user-form').reset();
    }

    document.getElementById('add-user-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('add-submit-btn');
        const msg = document.getElementById('add-msg');
        
        btn.disabled = true;
        btn.innerText = 'Creating...';
        msg.innerText = '';

        const data = {
            action: 'add_user',
            full_name: document.getElementById('add-fname').value,
            username: document.getElementById('add-uname').value,
            email: document.getElementById('add-email').value,
            password: document.getElementById('add-pwd').value,
            role: document.getElementById('add-role').value
        };

        fetch('../../api/admin_users.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
            msg.innerText = data.message;
            msg.style.color = data.success ? '#2ecc71' : '#e74c3c';
            if (data.success) {
                setTimeout(() => location.reload(), 1000);
            } else {
                btn.disabled = false;
                btn.innerText = 'Create User';
            }
        })
        .catch(err => {
            msg.innerText = 'Network error.';
            msg.style.color = '#e74c3c';
            btn.disabled = false;
            btn.innerText = 'Create User';
        });
    });
</script>

<?php include '../../includes/admin/admin_footer.php'; ?>
