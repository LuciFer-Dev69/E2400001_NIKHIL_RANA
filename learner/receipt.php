<?php
// learner/receipt.php - REFACTORED TO PDO
include '../includes/db_connect.php';
include '../includes/functions.php';

check_role(['Learner']);

if (!isset($_GET['tx'])) {
    header("Location: dashboard.php");
    exit();
}

$tx = $_GET['tx'];

// Fetch enrollment (PDO)
$sql = "SELECT e.*, c.title, u.full_name as learner_name, u.email as learner_email 
        FROM enrollments e 
        JOIN courses c ON e.course_id = c.id 
        JOIN users u ON e.learner_id = u.id
        WHERE e.transaction_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$tx]);
$data = $stmt->fetch();

if (!$data) {
    header("Location: dashboard.php");
    exit();
}

include '../includes/header.php';
?>

<div class="row justify-content-center text-dark">
    <div class="col-md-7">
        <div class="card ems-card p-5 border-0 shadow-lg text-center" style="background-color: var(--bg-light);">
            <div class="mb-4">
                <div class="bg-success text-white d-inline-block p-3 rounded-circle mb-3">
                    <i class="bi bi-check-lg fs-1"></i>
                </div>
                <h2 class="fw-bold">Enrollment Successful!</h2>
                <p class="text-muted">You now have lifetime access to the course content.</p>
            </div>

            <div class="receipt-box text-start border p-4 rounded bg-light mb-4 shadow-sm">
                <h5 class="fw-bold border-bottom pb-2 mb-3">Official Receipt</h5>
                <div class="row mb-2">
                    <div class="col-4 text-muted">Transaction ID:</div>
                    <div class="col-8 fw-bold"><?php echo $data['transaction_id']; ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-4 text-muted">Learner:</div>
                    <div class="col-8"><?php echo $data['learner_name']; ?> (<?php echo $data['learner_email']; ?>)</div>
                </div>
                <div class="row mb-2">
                    <div class="col-4 text-muted">Course:</div>
                    <div class="col-8 fw-bold"><?php echo $data['title']; ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-4 text-muted">Amount Paid:</div>
                    <div class="col-8 text-success fw-bold"><?php echo format_price($data['amount_paid']); ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-4 text-muted">Merchant:</div>
                    <div class="col-8">EduSkill Marketplace (EMS)</div>
                </div>
                <div class="row mt-3 border-top pt-3">
                    <div class="col-12 text-center small text-muted">
                        Date: <?php echo date('d M Y, h:i A', strtotime($data['enrolled_at'])); ?>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-center mt-3">
                <button onclick="window.print()" class="btn btn-outline-secondary px-4 border-2"><i class="bi bi-printer me-2"></i> Print Receipt</button>
                <a href="dashboard.php" class="btn btn-primary-custom text-white px-5 shadow">Go to Learning Dashboard</a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
