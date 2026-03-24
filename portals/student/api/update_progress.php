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
$watched_time = isset($data['watched_time']) ? (int)$data['watched_time'] : 0;

if (!$lesson_id || !$course_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

try {
    // 1. Update or Insert Progress
    $stmt = $pdo->prepare("INSERT INTO user_lesson_progress (user_id, lesson_id, course_id, status, last_watched_time) 
                           VALUES (?, ?, ?, ?, ?) 
                           ON DUPLICATE KEY UPDATE status = VALUES(status), last_watched_time = IF(VALUES(status) = 'started', VALUES(last_watched_time), last_watched_time)");
    $stmt->execute([$user_id, $lesson_id, $course_id, $status, $watched_time]);

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

    // 4. GAMIFICATION LOGIC
    $xp_awarded = 0;
    if ($status === 'completed') {
        // Ensure user has a gamification row
        $pdo->prepare("INSERT IGNORE INTO gamification_stats (user_id, xp, streak_days, last_login_date) VALUES (?, 0, 1, CURDATE())")->execute([$user_id]);

        // Award XP (e.g., 50 XP per lesson)
        $xp_awarded = 50;
        $pdo->prepare("UPDATE gamification_stats SET xp = xp + ? WHERE user_id = ?")->execute([$xp_awarded, $user_id]);

        // Award badge if it's their 1st completed lesson ever
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_lesson_progress WHERE user_id = ? AND status = 'completed'");
        $stmt->execute([$user_id]);
        if ($stmt->fetchColumn() == 1) {
            $pdo->prepare("INSERT IGNORE INTO badges_earned (user_id, badge_name) VALUES (?, 'First Steps')")->execute([$user_id]);
            $pdo->prepare("INSERT INTO notifications (user_id, title, message) VALUES (?, 'Badge Unlocked! 🏆', 'You earned the First Steps badge!')")->execute([$user_id]);
        }

        // Award badge if course is 100% completed
        if ($progress_percent == 100) {
            $pdo->prepare("INSERT IGNORE INTO badges_earned (user_id, badge_name) VALUES (?, 'Course Finisher')")->execute([$user_id]);
            $xp_awarded += 200; // Bonus XP for finishing a course
            $pdo->prepare("UPDATE gamification_stats SET xp = xp + 200 WHERE user_id = ?")->execute([$user_id]);
            $pdo->prepare("INSERT INTO notifications (user_id, title, message) VALUES (?, 'Course Completed! 🎉', 'You earned the Course Finisher badge and 200 XP!')")->execute([$user_id]);
        }
    }

    echo json_encode([
        'success' => true,
        'progress_percent' => $progress_percent,
        'completed_lessons' => $completed_lessons,
        'total_lessons' => $total_lessons,
        'xp_awarded' => $xp_awarded
    ]);

}
catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
