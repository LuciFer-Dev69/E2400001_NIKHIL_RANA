<?php
/**
 * api/admin_reviews.php
 * Handles review moderation actions (approve, reject, delete)
 */
require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

$action = $input['action'] ?? '';
$review_id = $input['review_id'] ?? 0;

try {
    if ($action === 'change_status') {
        $status = $input['status'] ?? 'pending';
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid status.']);
            exit();
        }

        $stmt = $pdo->prepare("UPDATE reviews SET status = ? WHERE id = ?");
        $stmt->execute([$status, $review_id]);

        echo json_encode(['success' => true, 'message' => "Review " . ucfirst($status) . "."]);
    }
    elseif ($action === 'delete_review') {
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->execute([$review_id]);
        echo json_encode(['success' => true, 'message' => 'Review deleted permanently.']);
    }
    else {
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    }
}
catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
