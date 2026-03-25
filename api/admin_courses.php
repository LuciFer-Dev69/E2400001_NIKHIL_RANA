<?php
/**
 * api/admin_courses.php
 * Handles course moderation actions (change status, delete)
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
$course_id = $input['course_id'] ?? 0;

try {
    if ($action === 'change_status') {
        $status = $input['status'] ?? 'draft';
        if (!in_array($status, ['draft', 'published', 'archived'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid status.']);
            exit();
        }

        $stmt = $pdo->prepare("UPDATE courses SET status = ? WHERE id = ?");
        $stmt->execute([$status, $course_id]);

        echo json_encode(['success' => true, 'message' => "Course status updated to " . ucfirst($status) . "."]);
    }
    elseif ($action === 'delete_course') {
        // Warning: This cascades deletion of lessons and enrollments due to schema constraints.
        $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->execute([$course_id]);

        echo json_encode(['success' => true, 'message' => 'Course and all associated data permanently deleted.']);
    }
    else {
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    }
}
catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
