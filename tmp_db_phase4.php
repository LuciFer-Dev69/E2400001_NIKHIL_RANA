<?php
require_once 'c:\xampp\htdocs\EMS1\config\db.php';

try {
    $sql = "
    CREATE TABLE IF NOT EXISTS course_discussions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_id INT NOT NULL,
        lesson_id INT NULL,
        user_id INT NOT NULL,
        message TEXT NOT NULL,
        parent_id INT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        is_read BOOLEAN DEFAULT FALSE,
        link VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );
    ";

    $pdo->exec($sql);
    echo "SQL execution success.";
}
catch (PDOException $e) {
    echo "DB Error: " . $e->getMessage();
}
?>
