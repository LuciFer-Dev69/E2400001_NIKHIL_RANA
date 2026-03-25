<?php
/**
 * api/admin_users.php
 * Handles user management actions (change role, delete)
 */
require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

// Strict Admin Validation
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

$action = $input['action'] ?? '';
$target_id = $input['user_id'] ?? 0;

if ($target_id == $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'You cannot modify your own admin account.']);
    exit();
}

try {
    if ($action === 'change_role') {
        $new_role = $input['role'] ?? 'student';
        if (!in_array($new_role, ['student', 'instructor', 'admin'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid role selected.']);
            exit();
        }

        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$new_role, $target_id]);

        echo json_encode(['success' => true, 'message' => "Role successfully updated to " . ucfirst($new_role) . "."]);
    }
    elseif ($action === 'edit_user') {
        $full_name = trim($input['full_name'] ?? '');
        $username = trim($input['username'] ?? '');
        $email = trim($input['email'] ?? '');
        $location = trim($input['location'] ?? '');
        $role = $input['role'] ?? 'student';
        $bio = trim($input['bio'] ?? '');
        $password = trim($input['password'] ?? '');

        if (empty($full_name) || empty($email)) {
            echo json_encode(['success' => false, 'message' => 'Full Name and Email are required.']);
            exit();
        }

        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, location = ?, role = ?, bio = ?, password = ?, plain_password = ? WHERE id = ?");
            $stmt->execute([$full_name, $username, $email, $location, $role, $bio, $hashed, $password, $target_id]);
        }
        else {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, location = ?, role = ?, bio = ? WHERE id = ?");
            $stmt->execute([$full_name, $username, $email, $location, $role, $bio, $target_id]);
        }

        echo json_encode(['success' => true, 'message' => 'User details successfully updated.']);
    }
    elseif ($action === 'suspend_user') {
        $status = $input['status'] === 'suspended' ? 'suspended' : 'active';
        $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->execute([$status, $target_id]);

        echo json_encode(['success' => true, 'message' => "User account has been $status."]);
    }
    elseif ($action === 'add_user') {
        $full_name = trim($input['full_name'] ?? '');
        $username = trim($input['username'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = trim($input['password'] ?? '');
        $role = $input['role'] ?? 'student';

        if (empty($full_name) || empty($username) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit();
        }

        // Check uniqueness
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Email or Username already exists.']);
            exit();
        }

        $hashed = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("INSERT INTO users (full_name, username, email, password, plain_password, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $username, $email, $hashed, $password, $role]);

        echo json_encode(['success' => true, 'message' => 'User created successfully!']);
    }
    elseif ($action === 'delete_user') {
        // Warning: This cascades deletion of enrollments/courses due to our schema constraints.
        // It's a destructive action.
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$target_id]);

        echo json_encode(['success' => true, 'message' => 'User account permanently deleted.']);
    }
    else {
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    }
}
catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
