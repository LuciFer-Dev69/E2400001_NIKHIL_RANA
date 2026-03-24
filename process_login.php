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
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['username'] = $user['username'] ?? explode(' ', $user['full_name'])[0];
            $_SESSION['user_role'] = $user['role'];

            // Ensure gamification row exists for student
            if ($user['role'] === 'student') {
                $pdo->prepare("INSERT IGNORE INTO gamification_stats (user_id, xp, streak_days, last_login_date) VALUES (?, 0, 0, CURDATE())")->execute([$user['id']]);
                // Send welcome notification only once
                $notif_check = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ?");
                $notif_check->execute([$user['id']]);
                if ($notif_check->fetchColumn() == 0) {
                    $pdo->prepare("INSERT INTO notifications (user_id, title, message) VALUES (?, 'Welcome to SkillEdu! 🎉', 'Start your first lesson to earn XP and badges!')")->execute([$user['id']]);
                }
            }

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
