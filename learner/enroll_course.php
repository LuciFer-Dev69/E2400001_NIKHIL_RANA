<?php
// learner/enroll_course.php - REFACTORED TO PDO
include '../includes/db_connect.php';
include '../includes/functions.php';

check_role(['Learner']);
$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();

}

$id = (int)$_GET['id'];

// Get course via PDO
$stmt = $pdo->prepare("SELECT c.*, p.organization_name FROM courses c JOIN providers p ON c.provider_id = p.id WHERE c.id = ? AND c.status = 'ACTIVE'");
$stmt->execute([$id]);
$course = $stmt->fetch();

if (!$course) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!validate_csrf_token($_POST['csrf_token'])) {
        die("CSRF Token Invalid");
    }

    // Redirect to payment sim
    header("Location: payment_sim.php?id=" . $id);
    exit();
}

include '../includes/header.php';
?>

<div class="row justify-content-center text-dark">
    <div class="col-md-6">
        <div class="card ems-card p-4 border-0 shadow-lg">
            <h4 class="fw-bold mb-4">Finalize Enrollment</h4>
            
            <div class="d-flex align-items-center mb-4 p-3 bg-light rounded shadow-sm">
                <img src="../assets/uploads/<?php echo $course['thumbnail'] ?: 'default_course.jpg'; ?>" width="80" class="rounded" onerror="this.src='https://placehold.co/100x100?text=C'">
                <div class="ms-3">
                    <h5 class="mb-1 fw-bold"><?php echo $course['title']; ?></h5>
                    <p class="mb-0 fw-bold" style="color: var(--secondary-color);"><?php echo format_price($course['price']); ?></p>
                </div>
            </div>

            <form method="POST" class="needs-validation" novalidate>
                <?php csrf_field(); ?>
                <h6 class="fw-bold mb-3">Billing Information</h6>
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="billing_name" class="form-control" value="<?php echo $_SESSION['full_name']; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="2" required></textarea>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary-custom text-white py-3 shadow">Proceed to Payment Gateway</button>
                    <a href="../course_details.php?id=<?php echo $id; ?>" class="btn btn-link mt-2">Cancel and Return</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
