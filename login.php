<?php
// login.php - REFACTORED TO PDO
include 'includes/db_connect.php';
include 'includes/functions.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF Check
    if (!validate_csrf_token($_POST['csrf_token'])) {
        die("CSRF Token Invalid");
    }

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // PDO Prepared Statement
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $login_allowed = false;

            if ($user['role'] == 'Provider') {
                $stmt_p = $pdo->prepare("SELECT status, id FROM providers WHERE user_id = ?");
                $stmt_p->execute([$user['id']]);
                $p_data = $stmt_p->fetch();

                if ($p_data['status'] != 'APPROVED') {
                    $error = "Your account is still pending approval or has been rejected.";
                }
                else {
                    $login_allowed = true;
                    $_SESSION['provider_id'] = $p_data['id'];
                }
            }
            else {
                $login_allowed = true;
            }

            if ($login_allowed) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                log_action($pdo, "USER_LOGIN", "User logged in: " . $user['email']);

                if ($user['role'] == 'Admin')
                    header("Location: admin/dashboard.php");
                elseif ($user['role'] == 'Provider')
                    header("Location: provider/dashboard.php");
                else
                    header("Location: index.php");
                exit();
            }
        }
        else {
            $error = "Invalid password!";
        }
    }
    else {
        $error = "Email not found!";
    }
}

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card ems-card p-4">
            <h3 class="text-center mb-4">Welcome Back</h3>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php
endif; ?>

            <form method="POST" class="needs-validation" novalidate>
                <?php csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary-custom w-100 text-white shadow">Login</button>
            </form>
            
            <div class="mt-4 text-center">
                <p>New to EMS? <a href="register.php">Create an account</a></p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
