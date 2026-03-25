<?php
require_once __DIR__ . '/config/db.php';

try {
    // 1. Setup Admin User (Password: admin123)
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES ('Admin User', 'admin@gmail.com', ?, 'admin') ON DUPLICATE KEY UPDATE role='admin'");
    $stmt->execute([$password]);
    echo "Admin user (admin@gmail.com) created or updated successfully.<br>";

    // 2. Add site_settings table for CMS
    $pdo->exec("CREATE TABLE IF NOT EXISTS site_settings (
        setting_key VARCHAR(100) PRIMARY KEY,
        setting_value TEXT NOT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "site_settings table ready.<br>";

    // Insert Default System Settings
    $pdo->exec("INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES 
        ('site_name', 'SkillEdu Platform'),
        ('contact_email', 'support@skilledu.com'),
        ('maintenance_mode', 'false')");
    echo "Default settings inserted.<br>";

    // 3. Add reviews table
    $pdo->exec("CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        course_id INT NOT NULL,
        rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
        comment TEXT,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "reviews table ready.<br>";

    echo "<br>Initial admin setup complete. You can delete this file.";

}
catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
