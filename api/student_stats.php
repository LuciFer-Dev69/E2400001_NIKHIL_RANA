<?php
/**
 * api/student_stats.php
 * Provides JSON data for the student's learning dashboard.
 */
require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // 1. Enrollment stats
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE student_id = ?");
    $stmt->execute([$user_id]);
    $total_enrolled = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE student_id = ? AND progress_percent = 100");
    $stmt->execute([$user_id]);
    $completed = (int)$stmt->fetchColumn();

    // 2. Weekly Learning Progress (Mocked for now as we don't have per-day watch logs yet)
    $progress_data = [
        ['day' => 'Mon', 'minutes' => 45],
        ['day' => 'Tue', 'minutes' => 30],
        ['day' => 'Wed', 'minutes' => 60],
        ['day' => 'Thu', 'minutes' => 10],
        ['day' => 'Fri', 'minutes' => 90],
        ['day' => 'Sat', 'minutes' => 120],
        ['day' => 'Sun', 'minutes' => 20]
    ];

    // 3. Course-wise progress
    $stmt = $pdo->prepare("SELECT c.title, e.progress_percent 
                           FROM enrollments e 
                           JOIN courses c ON e.course_id = c.id 
                           WHERE e.student_id = ? 
                           ORDER BY e.enrolled_at DESC LIMIT 5");
    $stmt->execute([$user_id]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'stats' => [
            'total_enrolled' => $total_enrolled,
            'completed' => $completed,
            'certificates' => $completed,
            'points' => $total_enrolled * 150 + $completed * 500 // Mock points
        ],
        'weekly_learning' => $progress_data,
        'course_progress' => $courses
    ]);

}
catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
