<?php
require_once '../../config/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['lesson_id']) || !isset($data['course_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

$lesson_id = (int)$data['lesson_id'];
$course_id = (int)$data['course_id'];
$status = isset($data['status']) ? $data['status'] : 'completed';
$watched_time = isset($data['watched_time']) ? (int)$data['watched_time'] : 0;

try {
    // 1. Update Lesson Progress
    $stmt = $pdo->prepare("
        INSERT INTO user_lesson_progress (user_id, lesson_id, course_id, status, last_watched_time)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE status = VALUES(status), last_watched_time = VALUES(last_watched_time)
    ");
    $stmt->execute([$user_id, $lesson_id, $course_id, $status, $watched_time]);

    // 2. Recalculate Overall Course Progress
    // Count total lessons
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM lessons WHERE course_id = ?");
    $stmt->execute([$course_id]);
    $total_lessons = $stmt->fetchColumn();

    // Count completed lessons
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_lesson_progress WHERE user_id = ? AND course_id = ? AND status = 'completed'");
    $stmt->execute([$user_id, $course_id]);
    $completed_lessons = $stmt->fetchColumn();

    $progress_percent = ($total_lessons > 0) ? round(($completed_lessons / $total_lessons) * 100) : 0;

    // Update Enrollments table
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
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
