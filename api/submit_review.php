<?php
require_once '../../config/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit();
}

$user_id = $_SESSION['user_id'];
$course_id = $input['course_id'] ?? 0;
$rating = (int)($input['rating'] ?? 0);
$comment = trim($input['comment'] ?? '');

if (!$course_id || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Valid course ID and rating (1-5) are required.']);
    exit();
}

try {
    // Check if user is enrolled
    $check = $pdo->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
    $check->execute([$user_id, $course_id]);
    if (!$check->fetchColumn()) {
        echo json_encode(['success' => false, 'message' => 'You must be enrolled in this course to leave a review.']);
        exit();
    }

    // Check if already reviewed (Optional: we can either block or update)
    $stmt = $pdo->prepare("SELECT id FROM reviews WHERE user_id = ? AND course_id = ?");
    $stmt->execute([$user_id, $course_id]);
    $existing = $stmt->fetchColumn();

    if ($existing) {
        // Update existing review
        $update = $pdo->prepare("UPDATE reviews SET rating = ?, comment = ?, status = 'pending', created_at = NOW() WHERE id = ?");
        $update->execute([$rating, $comment, $existing]);
        echo json_encode(['success' => true, 'message' => 'Review updated and pending moderation.']);
    }
    else {
        // Insert new review
        $insert = $pdo->prepare("INSERT INTO reviews (user_id, course_id, rating, comment, status) VALUES (?, ?, ?, ?, 'pending')");
        $insert->execute([$user_id, $course_id, $rating, $comment]);
        echo json_encode(['success' => true, 'message' => 'Review submitted and pending moderation.']);
    }

}
catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
