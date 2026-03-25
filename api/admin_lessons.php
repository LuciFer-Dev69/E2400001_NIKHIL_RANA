<?php
/**
 * api/admin_lessons.php
 * Handles curriculum management (adding, editing, deleting lessons)
 */
require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

$action = $input['action'] ?? '';

try {
    if ($action === 'save_lesson') {
        $lesson_id = $input['lesson_id'] ?? 0;
        $course_id = $input['course_id'] ?? 0;
        $title = trim($input['title'] ?? '');
        $video_url = trim($input['video_url'] ?? '');
        $duration = (int)($input['duration_mins'] ?? 0);
        $order = (int)($input['order_num'] ?? 0);

        if (empty($title) || !$course_id) {
            echo json_encode(['success' => false, 'message' => 'Title and Course ID required.']);
            exit();
        }

        if ($lesson_id > 0) {
            // Update
            $stmt = $pdo->prepare("UPDATE lessons SET title=?, video_url=?, duration_mins=?, order_num=? WHERE id=? AND course_id=?");
            $stmt->execute([$title, $video_url, $duration, $order, $lesson_id, $course_id]);
            echo json_encode(['success' => true, 'message' => 'Lesson updated.']);
        }
        else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO lessons (course_id, title, video_url, duration_mins, order_num) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$course_id, $title, $video_url, $duration, $order]);
            echo json_encode(['success' => true, 'message' => 'Lesson added.']);
        }
    }
    elseif ($action === 'delete_lesson') {
        $lesson_id = $input['lesson_id'] ?? 0;
        $stmt = $pdo->prepare("DELETE FROM lessons WHERE id = ?");
        $stmt->execute([$lesson_id]);
        echo json_encode(['success' => true, 'message' => 'Lesson deleted.']);
    }
    else {
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    }
}
catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
