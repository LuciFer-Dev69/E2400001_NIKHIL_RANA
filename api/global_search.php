<?php
require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

$query = isset($_GET['query']) ? trim($_GET['query']) : '';

if (strlen($query) < 2) {
    echo json_encode([]);
    exit();
}

try {
    $stmt = $pdo->prepare("
        SELECT c.id, c.title, c.price, u.full_name as instructor_name, c.thumbnail
        FROM courses c
        JOIN users u ON c.instructor_id = u.id
        WHERE c.title LIKE ? OR c.description LIKE ?
        LIMIT 5
    ");
    $stmt->execute(["%$query%", "%$query%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
}
catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
