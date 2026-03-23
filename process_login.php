<?php
// process_login.php
require_once 'config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Please enter both email and password.";
        header("Location: login.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Login success
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['username'] = $user['username'] ?? explode(' ', $user['full_name'])[0];
            $_SESSION['user_role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: portals/admin/index.php");
            }
            elseif ($user['role'] === 'instructor') {
                header("Location: portals/instructor/index.php");
            }
            else {
                header("Location: portals/student/index.php");
            }
            exit();
        }
        else {
            $_SESSION['error'] = "Invalid email or password.";
            header("Location: login.php");
            exit();
        }
    }
    catch (\PDOException $e) {
        $_SESSION['error'] = "Login failed. Please try again.";
        header("Location: login.php");
        exit();
    }
}
else {
    header("Location: login.php");
    exit();
}
?>
