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

$action = isset($data['action']) ? $data['action'] : 'save';

if ($action === 'save') {
    $course_id = (int)$data['course_id'];
    $lesson_id = (int)$data['lesson_id'];
    $note_text = trim($data['note_text']);
    $video_timestamp = isset($data['video_timestamp']) ? (int)$data['video_timestamp'] : 0;

    if (empty($note_text)) {
        echo json_encode(['success' => false, 'message' => 'Note text cannot be empty']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO user_notes (user_id, course_id, lesson_id, note_text, video_timestamp) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $course_id, $lesson_id, $note_text, $video_timestamp]);
        $note_id = $pdo->lastInsertId();

        echo json_encode([
            'success' => true,
            'note' => [
                'id' => $note_id,
                'note_text' => $note_text,
                'video_timestamp' => $video_timestamp,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
    catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
