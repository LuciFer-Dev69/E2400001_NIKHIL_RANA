<?php
/**
 * api/notifications.php
 * 
 * AJAX endpoint for notification interactions.
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/NotificationManager.php';

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

NotificationManager::init($pdo);
$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_latest':
        $notifications = NotificationManager::getUnread($userId);
        echo json_encode(['success' => true, 'notifications' => $notifications]);
        break;

    case 'mark_read':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        if ($id) {
            $success = NotificationManager::markAsRead($id, $userId);
            echo json_encode(['success' => $success]);
        }
        else {
            echo json_encode(['success' => false, 'message' => 'Missing ID']);
        }
        break;

    case 'mark_all_read':
        $success = NotificationManager::markAllAsRead($userId);
        echo json_encode(['success' => $success]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
