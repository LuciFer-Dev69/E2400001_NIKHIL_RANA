<?php
require_once 'config/db.php';
try {
    $pdo->exec('ALTER TABLE enrollments ADD COLUMN is_purchased BOOLEAN DEFAULT FALSE');
    echo "Column added.";
}
catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "Column already exists.";
    }
    else {
        echo "Error: " . $e->getMessage();
    }
}
?>
