<?php
// register_provider.php - REFACTORED TO PDO
include 'includes/db_connect.php';
include 'includes/functions.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!validate_csrf_token($_POST['csrf_token'])) {
        die("CSRF Token Invalid");
    }

    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $org_name = trim($_POST['org_name']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $target_dir = "assets/uploads/";
    $file_name = time() . "_" . basename($_FILES["document"]["name"]);
    $target_file = $target_dir . $file_name;

    // Check email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        $error = "Email already registered!";
    }
    else {
        if (move_uploaded_file($_FILES["document"]["tmp_name"], $target_file)) {
            try {
                $pdo->beginTransaction();

                $stmt1 = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, 'Provider')");
                $stmt1->execute([$full_name, $email, $password]);
                $user_id = $pdo->lastInsertId();

                $stmt2 = $pdo->prepare("INSERT INTO providers (user_id, organization_name, document_path, status) VALUES (?, ?, ?, 'PENDING')");
                $stmt2->execute([$user_id, $org_name, $file_name]);

                $pdo->commit();
                log_action($pdo, "PROVIDER_APPLY", "Provider app submitted for: $org_name");
                $success = "Application submitted! Wait for Admin approval.";
            }
            catch (Exception $e) {
                $pdo->rollBack();
                $error = "System Error: " . $e->getMessage();
            }
        }
        else {
            $error = "Error uploading document.";
        }
    }
}

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card ems-card p-4">
            <h3 class="text-center mb-4 text-primary fw-bold">Training Provider Registration</h3>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php
endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php
endif; ?>

            <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                <?php csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label">Contact Person Name</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Organization Name</label>
                    <input type="text" name="org_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Supporting Document (PDF/Image)</label>
                    <input type="file" name="document" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Create Password</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <button type="submit" class="btn btn-primary-custom w-100 text-white shadow">Submit Application</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
