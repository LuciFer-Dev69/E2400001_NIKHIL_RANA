<?php
require_once '../config/db.php';
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
    // 1. Ensure wishlist table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS wishlist_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        course_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY user_course (user_id, course_id)
    )");

    if ($action === 'toggle') {
        $course_id = (int)$data['course_id'];
        if (!$course_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid course_id']);
            exit();
        }

        // Check if exists
        $stmt = $pdo->prepare("SELECT id FROM wishlist_items WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$user_id, $course_id]);
        $exists = $stmt->fetchColumn();

        if ($exists) {
            // Remove
            $stmt = $pdo->prepare("DELETE FROM wishlist_items WHERE id = ?");
            $stmt->execute([$exists]);
            $is_wished = false;
        }
        else {
            // Add
            $stmt = $pdo->prepare("INSERT INTO wishlist_items (user_id, course_id) VALUES (?, ?)");
            $stmt->execute([$user_id, $course_id]);
            $is_wished = true;
        }

        echo json_encode(['success' => true, 'is_wished' => $is_wished]);
        exit();
    }

    if ($action === 'get') {
        // Fetch all wishlist courses for user
        $stmt = $pdo->prepare("
            SELECT c.id, c.title, c.price, u.full_name as instructor_name, c.thumbnail 
            FROM wishlist_items w
            JOIN courses c ON w.course_id = c.id
            JOIN users u ON c.instructor_id = u.id
            WHERE w.user_id = ?
            ORDER BY w.created_at DESC
            LIMIT 5
        ");
        $stmt->execute([$user_id]);
        $wishlist = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'wishlist' => $wishlist]);
        exit();
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action']);

}
catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
