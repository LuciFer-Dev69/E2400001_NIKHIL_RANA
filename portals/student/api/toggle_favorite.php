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

if (!$data || !isset($data['course_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

$course_id = (int)$data['course_id'];

try {
    // Check if already favorited
    $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND course_id = ?");
    $stmt->execute([$user_id, $course_id]);
    $exists = $stmt->fetch();

    if ($exists) {
        $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$user_id, $course_id]);
        echo json_encode(['success' => true, 'is_favorite' => false]);
    }
    else {
        $stmt = $pdo->prepare("INSERT INTO favorites (user_id, course_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $course_id]);
        echo json_encode(['success' => true, 'is_favorite' => true]);
    }
}
catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
