<?php
require_once '../../../config/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$action = isset($data['action']) ? $data['action'] : '';

try {
    if ($action === 'update_profile') {
        $full_name = trim($data['full_name'] ?? '');
        $bio = trim($data['bio'] ?? '');

        if (empty($full_name)) {
            echo json_encode(['success' => false, 'message' => 'Full name is required.']);
            exit();
        }

        // Add bio column if it doesn't exist
        try {
            $pdo->exec("ALTER TABLE users ADD COLUMN bio TEXT DEFAULT NULL");
        }
        catch (PDOException $e) {
        // Ignore if column exists
        }

        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, bio = ? WHERE id = ?");
        $stmt->execute([$full_name, $bio, $user_id]);

        $_SESSION['full_name'] = $full_name;

        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
        exit();
    }

    if ($action === 'change_password') {
        $current_password = $data['current_password'] ?? '';
        $new_password = $data['new_password'] ?? '';

        if (empty($current_password) || empty($new_password)) {
            echo json_encode(['success' => false, 'message' => 'Passwords cannot be empty.']);
            exit();
        }

        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!password_verify($current_password, $user['password'])) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
            exit();
        }

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $user_id]);

        echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
        exit();
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action']);

}
catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
