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
$action = isset($data['action']) ? $data['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

try {
    // 1. Ensure Q&A tables exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS course_questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_id INT NOT NULL,
        lesson_id INT NOT NULL,
        user_id INT NOT NULL,
        question_text TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS course_answers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question_id INT NOT NULL,
        user_id INT NOT NULL,
        answer_text TEXT NOT NULL,
        is_instructor BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    if ($action === 'ask') {
        $course_id = (int)$data['course_id'];
        $lesson_id = (int)$data['lesson_id'];
        $question_text = trim($data['question_text']);

        if (empty($question_text)) {
            echo json_encode(['success' => false, 'message' => 'Question cannot be empty']);
            exit();
        }

        $stmt = $pdo->prepare("INSERT INTO course_questions (course_id, lesson_id, user_id, question_text) VALUES (?, ?, ?, ?)");
        $stmt->execute([$course_id, $lesson_id, $user_id, $question_text]);
        $question_id = $pdo->lastInsertId();

        $fullName = $_SESSION['full_name'] ?? 'Student';

        echo json_encode([
            'success' => true,
            'question' => [
                'id' => $question_id,
                'user_name' => $fullName,
                'question_text' => $question_text,
                'created_at' => date('M d, Y'),
                'answers' => []
            ]
        ]);
        exit();
    }

    if ($action === 'get') {
        $lesson_id = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : 0;

        // Fetch questions
        $stmt = $pdo->prepare("
            SELECT q.*, u.full_name as user_name 
            FROM course_questions q
            JOIN users u ON q.user_id = u.id
            WHERE q.lesson_id = ?
            ORDER BY q.created_at DESC
        ");
        $stmt->execute([$lesson_id]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch answers for these questions
        if (!empty($questions)) {
            $q_ids = array_column($questions, 'id');
            $in = str_repeat('?,', count($q_ids) - 1) . '?';
            $stmt = $pdo->prepare("
                SELECT a.*, u.full_name as user_name 
                FROM course_answers a
                JOIN users u ON a.user_id = u.id
                WHERE a.question_id IN ($in)
                ORDER BY a.created_at ASC
            ");
            $stmt->execute($q_ids);
            $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Group answers by question
            $grouped_answers = [];
            foreach ($answers as $a) {
                $grouped_answers[$a['question_id']][] = $a;
            }

            foreach ($questions as &$q) {
                $q['created_at'] = date('M d, Y', strtotime($q['created_at']));
                $q['answers'] = $grouped_answers[$q['id']] ?? [];

                // Format answer dates
                foreach ($q['answers'] as &$ans) {
                    $ans['created_at'] = date('M d, Y', strtotime($ans['created_at']));
                }
            }
        }

        echo json_encode(['success' => true, 'questions' => $questions]);
        exit();
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action']);

}
catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
