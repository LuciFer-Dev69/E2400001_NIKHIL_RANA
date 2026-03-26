<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to subscribe.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$plan = $_POST['plan'] ?? '';

if (empty($plan)) {
    echo json_encode(['success' => false, 'message' => 'No plan selected.']);
    exit();
}

try {
    // Check for existing active subscription
    $stmt = $pdo->prepare("SELECT id FROM user_subscriptions WHERE user_id = ? AND status = 'active' AND (expires_at IS NULL OR expires_at > NOW())");
    $stmt->execute([$user_id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'You already have an active subscription!']);
        exit();
    }

    // Process dummy subscription (In reality, verify payment here)
    $expires_at = date('Y-m-d H:i:s', strtotime('+1 month'));

    $stmt = $pdo->prepare("INSERT INTO user_subscriptions (user_id, plan_name, status, expires_at) VALUES (?, ?, 'active', ?)");
    $stmt->execute([$user_id, $plan, $expires_at]);

    echo json_encode(['success' => true, 'message' => 'Subscription successful! Welcome to ' . $plan . '.']);
}
catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
