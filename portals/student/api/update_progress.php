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

$lesson_id = isset($data['lesson_id']) ? (int)$data['lesson_id'] : 0;
$course_id = isset($data['course_id']) ? (int)$data['course_id'] : 0;
$status = isset($data['status']) ? $data['status'] : 'completed';

if (!$lesson_id || !$course_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

try {
    // 1. Update or Insert Progress
    $stmt = $pdo->prepare("INSERT INTO user_lesson_progress (user_id, lesson_id, course_id, status, completed_at) 
                           VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP) 
                           ON DUPLICATE KEY UPDATE status = ?, completed_at = CURRENT_TIMESTAMP");
    $stmt->execute([$user_id, $lesson_id, $course_id, $status, $status]);

    // 2. Calculate Overall Course Progress
    // Count total lessons in course
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM lessons WHERE course_id = ?");
    $stmt->execute([$course_id]);
    $total_lessons = $stmt->fetchColumn();

    // Count completed lessons for this user
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_lesson_progress WHERE user_id = ? AND course_id = ? AND status = 'completed'");
    $stmt->execute([$user_id, $course_id]);
    $completed_lessons = $stmt->fetchColumn();

    $progress_percent = $total_lessons > 0 ? round(($completed_lessons / $total_lessons) * 100) : 0;

    // 3. Update Enrollment Progress
    $stmt = $pdo->prepare("UPDATE enrollments SET progress_percent = ? WHERE student_id = ? AND course_id = ?");
    $stmt->execute([$progress_percent, $user_id, $course_id]);

    echo json_encode([
        'success' => true,
        'progress_percent' => $progress_percent,
        'completed_lessons' => $completed_lessons,
        'total_lessons' => $total_lessons
    ]);

}
catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
