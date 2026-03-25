<?php
/**
 * migrate_instructor.php
 * Upgrades the skilledu_db schema to support the advanced Instructor Portal.
 */

// Load DB config
require_once __DIR__ . '/config/db.php';

echo "Starting Instructor Portal Database Migration...\n\n";

try {
    // 1. Update Users Table (Instructor profiles)
    echo "1. Updating `users` table... ";
    $pdo->exec("ALTER TABLE users 
                ADD COLUMN IF NOT EXISTS expertise VARCHAR(255) DEFAULT '',
                ADD COLUMN IF NOT EXISTS rating DECIMAL(3,2) DEFAULT 0.00");
    echo "Done.\n";

    // 2. Update Courses Table (Difficulty & Pending Status)
    echo "2. Updating `courses` table... ";
    // MySQL ALTER TABLE MODIFY COLUMN is safe if we include the full definition
    $pdo->exec("ALTER TABLE courses 
                ADD COLUMN IF NOT EXISTS difficulty_level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner';");
    // Expand the ENUM for status to include 'pending' (Instructor submits for admin review)
    $pdo->exec("ALTER TABLE courses 
                MODIFY COLUMN status ENUM('draft', 'pending', 'published', 'archived') DEFAULT 'draft'");
    echo "Done.\n";

    // 3. Update Lessons Table (Support for Text & Quiz lessons)
    echo "3. Updating `lessons` table... ";
    $pdo->exec("ALTER TABLE lessons 
                ADD COLUMN IF NOT EXISTS lesson_type ENUM('video', 'text', 'quiz') DEFAULT 'video',
                ADD COLUMN IF NOT EXISTS content TEXT");
    echo "Done.\n";

    // 4. Create Quizzes Table
    echo "4. Creating `quizzes` table... ";
    $pdo->exec("CREATE TABLE IF NOT EXISTS quizzes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_id INT NOT NULL,
        question TEXT NOT NULL,
        options JSON NOT NULL,
        correct_answer VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "Done.\n";

    echo "\nMigration completed successfully!\n";

}
catch (PDOException $e) {
    echo "\n[ERROR] Migration failed: " . $e->getMessage() . "\n";
}
?>
