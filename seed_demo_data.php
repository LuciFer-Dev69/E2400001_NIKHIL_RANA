<?php
/**
 * seed_demo_data.php
 * Seeds the database with sample courses and enrolls the student user.
 * Run once via browser: http://localhost/E2400001_NIKHIL_RANA/seed_demo_data.php
 */
require_once 'config/db.php';

echo "<pre>";

// 1. Find or create a demo instructor
$stmt = $pdo->prepare("SELECT id FROM users WHERE user_role = 'instructor' LIMIT 1");
$stmt->execute();
$instructor = $stmt->fetch();

if (!$instructor) {
    $pdo->prepare("INSERT INTO users (full_name, email, password, user_role, created_at) VALUES (?, ?, ?, 'instructor', NOW())")
        ->execute(['Demo Instructor', 'instructor@demo.com', password_hash('demo123', PASSWORD_DEFAULT)]);
    $instructorId = $pdo->lastInsertId();
    echo "[CREATED] Instructor with id: $instructorId\n";
}
else {
    $instructorId = $instructor['id'];
    echo "[FOUND] Instructor with id: $instructorId\n";
}

// 2. Find or create a default category
$stmt = $pdo->prepare("SELECT id FROM categories LIMIT 1");
$stmt->execute();
$category = $stmt->fetch();

if (!$category) {
    $pdo->prepare("INSERT INTO categories (name, slug) VALUES ('Web Development', 'web-development')")
        ->execute();
    $categoryId = $pdo->lastInsertId();
    echo "[CREATED] Category with id: $categoryId\n";
}
else {
    $categoryId = $category['id'];
    echo "[FOUND] Category with id: $categoryId\n";
}

// 3. Seed 8 demo courses if fewer than 5 exist
$stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE status = 'published'");
$stmt->execute();
$courseCount = $stmt->fetchColumn();

$insertedCourseIds = [];

if ($courseCount < 5) {
    $demoCourses = [
        ['Advanced React & Redux 2024', 'Master React hooks, Redux Toolkit, and build real-world applications from scratch.', 9.99, 'beginner'],
        ['Complete Python Bootcamp', 'Go from zero to hero in Python 3! Learn Python programming by building real projects.', 12.99, 'beginner'],
        ['Graphic Design Theory', 'Learn the principles of graphic design and create stunning visual content.', 0.00, 'beginner'],
        ['Photography Masterclass A-Z', 'A complete guide to photography from beginner to professional level.', 14.99, 'intermediate'],
        ['Machine Learning Fundamentals', 'Introduction to machine learning concepts, algorithms, and Python tools.', 19.99, 'intermediate'],
        ['Web Development Bootcamp', 'Full-stack web development with HTML, CSS, JavaScript, Node.js, and more.', 0.00, 'beginner'],
        ['Digital Marketing 2024', 'Learn SEO, social media marketing, Google Ads, and content marketing.', 7.99, 'beginner'],
        ['UI/UX Design Masterclass', 'Design beautiful user interfaces and amazing user experiences with Figma.', 11.99, 'intermediate'],
    ];

    foreach ($demoCourses as $c) {
        $stmt = $pdo->prepare("
            INSERT INTO courses (instructor_id, category_id, title, description, price, difficulty_level, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 'published', NOW())
        ");
        $stmt->execute([$instructorId, $categoryId, $c[0], $c[1], $c[2], $c[3]]);
        $cid = $pdo->lastInsertId();
        $insertedCourseIds[] = $cid;
        echo "[CREATED] Course: {$c[0]} (id: $cid)\n";
    }
}
else {
    // Get existing published course IDs
    $stmt = $pdo->query("SELECT id FROM courses WHERE status = 'published' LIMIT 8");
    $insertedCourseIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "[INFO] Courses already exist ($courseCount published). Using existing course IDs.\n";
}

// 4. Find student user and enroll in all courses
$stmt = $pdo->prepare("SELECT id, full_name FROM users WHERE user_role = 'student' LIMIT 1");
$stmt->execute();
$student = $stmt->fetch();

if (!$student) {
    echo "[WARNING] No student user found. Creating a demo student...\n";
    $pdo->prepare("INSERT INTO users (full_name, email, password, user_role, created_at) VALUES (?, ?, ?, 'student', NOW())")
        ->execute(['Demo Student', 'student@demo.com', password_hash('demo123', PASSWORD_DEFAULT)]);
    $studentId = $pdo->lastInsertId();
}
else {
    $studentId = $student['id'];
    echo "[FOUND] Student: {$student['full_name']} (id: $studentId)\n";
}

// 5. Enroll student in all courses (skip if already enrolled)
$enrolled = 0;
foreach ($insertedCourseIds as $courseId) {
    $check = $pdo->prepare("SELECT id FROM enrollments WHERE student_id = ? AND course_id = ?");
    $check->execute([$studentId, $courseId]);
    if (!$check->fetch()) {
        $pdo->prepare("INSERT INTO enrollments (student_id, course_id, enrolled_at, progress_percent, is_purchased, status) VALUES (?, ?, NOW(), ?, 1, 'active')")
            ->execute([$studentId, $courseId, rand(0, 85)]);
        $enrolled++;
    }
}

echo "\n[SUCCESS] Enrolled student (id: $studentId) in $enrolled new courses.\n";
echo "\n--- Done! Refresh your Student Portal to see the courses. ---\n";
echo "</pre>";
