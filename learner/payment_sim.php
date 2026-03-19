<?php
// learner/payment_sim.php - REFACTORED TO PDO
include '../includes/db_connect.php';
include '../includes/functions.php';

check_role(['Learner']);
$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();

}

$course_id = (int)$_GET['id'];

// Get details (PDO)
$stmt = $pdo->prepare("SELECT price, title FROM courses WHERE id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

if (!$course) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $txn_id = generate_transaction_id();

    // Insert enrollment (PDO)
    $sql = "INSERT INTO enrollments (learner_id, course_id, transaction_id, payment_status, amount_paid) VALUES (?, ?, ?, 'PAID', ?)";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$user_id, $course_id, $txn_id, $course['price']])) {
        log_action($pdo, "COURSE_ENROLL", "Learner $user_id enrolled in course $course_id. TXN: $txn_id");
        header("Location: receipt.php?tx=" . $txn_id);
        exit();
    }
}

include '../includes/header.php';
?>

<div class="row justify-content-center text-dark">
    <div class="col-md-5">
        <div class="card ems-card p-5 border-0 shadow-lg text-center">
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h4 class="fw-bold">Contacting Payment Gateway...</h4>
            <p class="text-muted">Do not refresh or close this window.</p>
            <hr>
            <div class="d-flex justify-content-between mb-2">
                <span>Course:</span>
                <span class="fw-bold"><?php echo $course['title']; ?></span>
            </div>
            <div class="d-flex justify-content-between mb-4">
                <span>Total Amount:</span>
                <span class="fw-bold text-success"><?php echo format_price($course['price']); ?></span>
            </div>
            
            <form method="POST" id="paymentForm">
                <button type="submit" id="payBtn" class="btn btn-primary-custom w-100 py-3 text-white shadow">Simulate Successful Payment</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Automate the "processing" feel
    document.getElementById('payBtn').addEventListener('click', function(e) {
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Finalizing Transaction...';
        this.classList.add('disabled');
    });
</script>

<?php include '../includes/footer.php'; ?>
