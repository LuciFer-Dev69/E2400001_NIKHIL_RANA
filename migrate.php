<?php
require_once 'config/db.php';

try {
    $pdo->exec("ALTER TABLE users ADD COLUMN location VARCHAR(255) DEFAULT ''");
    echo "Added location column.\n";
}
catch (PDOException $e) {
    echo "location exists or error: " . $e->getMessage() . "\n";
}

try {
    $pdo->exec("ALTER TABLE users ADD COLUMN status ENUM('active', 'suspended', 'banned') DEFAULT 'active'");
    echo "Added status column.\n";
}
catch (PDOException $e) {
    echo "status exists or error: " . $e->getMessage() . "\n";
}

try {
    $pdo->exec("ALTER TABLE users ADD COLUMN plain_password VARCHAR(255) DEFAULT ''");
    echo "Added plain_password column.\n";
}
catch (PDOException $e) {
    echo "plain_password exists or error: " . $e->getMessage() . "\n";
}

echo "Database migrations completed successfully.\n";
?>
