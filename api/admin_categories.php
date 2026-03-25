<?php
/**
 * api/admin_categories.php
 * Handles creating, updating, and deleting course categories.
 */
require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

// Strict Admin Validation
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

$action = $input['action'] ?? '';

// Helper function to create slugs
function createSlug($string)
{
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    return $slug;
}

try {
    if ($action === 'add_category') {
        $name = trim($input['name'] ?? '');
        $icon = trim($input['icon'] ?? 'fa-code'); // Default icon

        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Category Name is required.']);
            exit();
        }

        $slug = createSlug($name);

        // Check uniqueness
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'A category with this name/slug already exists.']);
            exit();
        }

        $stmt = $pdo->prepare("INSERT INTO categories (name, icon, slug) VALUES (?, ?, ?)");
        $stmt->execute([$name, $icon, $slug]);

        echo json_encode(['success' => true, 'message' => 'Category successfully created.']);
    }
    elseif ($action === 'edit_category') {
        $cat_id = $input['id'] ?? 0;
        $name = trim($input['name'] ?? '');
        $icon = trim($input['icon'] ?? 'fa-code');

        if (empty($name) || !$cat_id) {
            echo json_encode(['success' => false, 'message' => 'ID and Name are required.']);
            exit();
        }

        $slug = createSlug($name);

        // Check uniqueness, excluding the current category
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ? AND id != ?");
        $stmt->execute([$slug, $cat_id]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Another category with this name already exists.']);
            exit();
        }

        $stmt = $pdo->prepare("UPDATE categories SET name = ?, icon = ?, slug = ? WHERE id = ?");
        $stmt->execute([$name, $icon, $slug, $cat_id]);

        echo json_encode(['success' => true, 'message' => 'Category successfully updated.']);
    }
    elseif ($action === 'delete_category') {
        $cat_id = $input['id'] ?? 0;

        // Check if there are courses using this category
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE category_id = ?");
        $stmt->execute([$cat_id]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete: Courses currently exist in this category.']);
            exit();
        }

        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$cat_id]);

        echo json_encode(['success' => true, 'message' => 'Category permanently deleted.']);
    }
    else {
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    }
}
catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
