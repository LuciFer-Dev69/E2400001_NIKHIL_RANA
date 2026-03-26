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

        // [NOTIFICATION TRIGGER] Notify Instructor
        require_once '../includes/NotificationManager.php';
        NotificationManager::init($pdo);

        // Fetch course details for the notification
        $cStmt = $pdo->prepare("SELECT title, instructor_id FROM courses WHERE id = ?");
        $cStmt->execute([$course_id]);
        $courseData = $cStmt->fetch();

        if ($courseData) {
            $title = $status === 'published' ? 'Course Approved! 🚀' : 'Course Status Updated';
            $msg = $status === 'published'
                ? "Congratulations! Your course '{$courseData['title']}' is now live on SkillEdu."
                : "The status of your course '{$courseData['title']}' has been changed to " . ucfirst($status) . ".";

            NotificationManager::notify(
                $courseData['instructor_id'],
                'update',
                $title,
                $msg,
                "portals/instructor/courses.php"
            );
        }

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
