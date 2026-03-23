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

if (!$data || !isset($data['lesson_id']) || !isset($data['note_text'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

$lesson_id = (int)$data['lesson_id'];
$note_text = trim($data['note_text']);
$video_timestamp = isset($data['video_timestamp']) ? (int)$data['video_timestamp'] : 0;

if (empty($note_text)) {
    echo json_encode(['success' => false, 'message' => 'Note cannot be empty']);
    exit();
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO user_notes (user_id, lesson_id, note_text, video_timestamp)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, $lesson_id, $note_text, $video_timestamp]);

    echo json_encode(['success' => true, 'note_id' => $pdo->lastInsertId()]);

}
catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
