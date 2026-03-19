<?php
// register.php
include 'includes/db_connect.php';
include 'includes/functions.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF Check
    if (!validate_csrf_token($_POST['csrf_token'])) {
        die("CSRF Token Invalid");
    }

    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'Learner';

    // Check if email exists (PDO Prepared Statement)
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        $error = "Email already registered!";
    }
    else {
        $sql = "INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$full_name, $email, $password, $role])) {
            log_action($pdo, "USER_REGISTER", "New learner registered: $email");
            $success = "Registration successful! You can now <a href='login.php'>Login</a>.";
        }
        else {
            $error = "System Error. Please try again later.";
        }
    }
}

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card ems-card p-4">
            <h3 class="text-center mb-4">Create Learner Account</h3>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php
endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php
endif; ?>

            <form method="POST" class="needs-validation" novalidate>
                <?php csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <button type="submit" class="btn btn-primary-custom w-100 text-white">Register</button>
            </form>
            
            <div class="mt-4 text-center">
                <p>Want to teach? <a href="register_provider.php">Register as Training Provider</a></p>
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
