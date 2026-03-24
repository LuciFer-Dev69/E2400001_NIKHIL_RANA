<?php
require_once '../../../config/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $action = $_GET['action'] ?? '';

    if ($action === 'get') {
        $lesson_id = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : 0;
        if (!$lesson_id) {
            echo json_encode(['success' => false, 'message' => 'Missing lesson_id']);
            exit();
        }

        try {
            // Fetch top-level questions
            $stmt = $pdo->prepare("
                SELECT cd.id, cd.message as question_text, cd.created_at, u.full_name as user_name
                FROM course_discussions cd
                JOIN users u ON cd.user_id = u.id
                WHERE cd.lesson_id = ? AND cd.parent_id IS NULL
                ORDER BY cd.created_at DESC
            ");
            $stmt->execute([$lesson_id]);
            $questions_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Fetch answers for these questions
            $q_ids = array_column($questions_raw, 'id');
            $answers_by_q = [];

            if (!empty($q_ids)) {
                $inQuery = implode(',', array_fill(0, count($q_ids), '?'));
                $stmtAnswers = $pdo->prepare("
                    SELECT cd.id, cd.parent_id, cd.message as answer_text, cd.created_at, u.full_name as user_name, u.role
                    FROM course_discussions cd
                    JOIN users u ON cd.user_id = u.id
                    WHERE cd.parent_id IN ($inQuery)
                    ORDER BY cd.created_at ASC
                ");
                $stmtAnswers->execute($q_ids);
                $answers_raw = $stmtAnswers->fetchAll(PDO::FETCH_ASSOC);

                foreach ($answers_raw as $ans) {
                    $ans_formatted = [
                        'user_name' => $ans['user_name'],
                        'is_instructor' => ($ans['role'] === 'instructor') ? 1 : 0,
                        'created_at' => date('M j, Y g:i A', strtotime($ans['created_at'])),
                        'answer_text' => htmlspecialchars($ans['answer_text'])
                    ];
                    $answers_by_q[$ans['parent_id']][] = $ans_formatted;
                }
            }

            // Format final array
            $questions = [];
            foreach ($questions_raw as $q) {
                $questions[] = [
                    'id' => $q['id'],
                    'user_name' => htmlspecialchars($q['user_name']),
                    'created_at' => date('M j, Y g:i A', strtotime($q['created_at'])),
                    'question_text' => htmlspecialchars($q['question_text']),
                    'answers' => $answers_by_q[$q['id']] ?? []
                ];
            }

            echo json_encode(['success' => true, 'questions' => $questions]);

        }
        catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }
}
elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';

    if ($action === 'ask') {
        $course_id = (int)($data['course_id'] ?? 0);
        $lesson_id = (int)($data['lesson_id'] ?? 0);
        $question_text = trim($data['question_text'] ?? '');

        if (!$course_id || !$lesson_id || empty($question_text)) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            exit();
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO course_discussions (course_id, lesson_id, user_id, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$course_id, $lesson_id, $user_id, $question_text]);
            echo json_encode(['success' => true]);
        }
        catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }
}
?>
