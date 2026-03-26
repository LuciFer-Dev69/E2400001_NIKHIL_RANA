<?php
// process_signup.php
require_once 'config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'student'; // Default to student

    // Basic Validation
    if (empty($full_name) || empty($username) || empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: signup.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: signup.php");
        exit();
    }

    // Check if username already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = "Username already taken.";
        header("Location: signup.php");
        exit();
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = "Email already registered.";
        header("Location: signup.php");
        exit();
    }

    // Hash Password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $verification_doc = null;

    // Handle Instructor Document Upload
    if ($role === 'instructor') {
        if (!isset($_FILES['verification_doc']) || $_FILES['verification_doc']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = "Verification document is required for instructors.";
            header("Location: signup.php");
            exit();
        }

        $file = $_FILES['verification_doc'];
        $allowed_types = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowed_types)) {
            $_SESSION['error'] = "Invalid file type. Please upload a PDF or an image.";
            header("Location: signup.php");
            exit();
        }

        $upload_dir = 'uploads/instructor_docs/';
        $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid('doc_', true) . '.' . $file_ext;
        $upload_path = $upload_dir . $new_filename;

        if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
            $_SESSION['error'] = "Failed to upload verification document.";
            header("Location: signup.php");
            exit();
        }
        $verification_doc = $upload_path;
    }

    $expertise = trim($_POST['expertise'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    try {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, username, email, password, plain_password, role, verification_doc, expertise, bio) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $username, $email, $hashed_password, $password, $role, $verification_doc, $expertise, $bio]);

        $_SESSION['success'] = "Registration successful! Please log in.";
        header("Location: login.php");
        exit();
    }
    catch (\PDOException $e) {
        $_SESSION['error'] = "Registration failed. Please try again.";
        header("Location: signup.php");
        exit();
    }
}
else {
    header("Location: signup.php");
    exit();
}
?>
