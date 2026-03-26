<?php
/**
 * migrations/create_notifications_table.php
 * 
 * Sets up the notifications and activity_log tables.
 */
require_once __DIR__ . '/../config/db.php';

try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            type VARCHAR(50) NOT NULL, -- e.g. 'enrollment', 'announcement', 'update'
            title VARCHAR(255) NOT NULL,
            message TEXT,
            link VARCHAR(255),
            is_read BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS activity_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            action VARCHAR(100) NOT NULL,
            metadata JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    echo "Notifications and Activity Log tables created successfully!\n";
}
catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage());
}
