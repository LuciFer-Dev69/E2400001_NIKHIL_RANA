<?php
// admin/dashboard.php - REFACTORED TO PDO
include '../includes/db_connect.php';
include '../includes/functions.php';

check_role(['Admin']);

// Get stats via PDO
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role != 'Admin'")->fetchColumn();
$pending_providers = $pdo->query("SELECT COUNT(*) FROM providers WHERE status = 'PENDING'")->fetchColumn();
$total_courses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$total_enrollments = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE payment_status = 'PAID'")->fetchColumn();

include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark">Admin Dashboard</h2>
    <span class="text-muted">System Overlook</span>
</div>

<div class="row g-4 text-dark">
    <div class="col-md-3">
        <div class="card ems-card p-3 text-center border-0 shadow-sm border-start border-primary border-4">
            <h5 class="text-muted small text-uppercase fw-bold">Total Users</h5>
            <h2 class="fw-bold mb-0"><?php echo $total_users; ?></h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card ems-card p-3 text-center border-0 shadow-sm border-start border-warning border-4">
            <h5 class="text-muted small text-uppercase fw-bold">Pending Providers</h5>
            <h2 class="fw-bold mb-0"><?php echo $pending_providers; ?></h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card ems-card p-3 text-center border-0 shadow-sm border-start border-info border-4">
            <h5 class="text-muted small text-uppercase fw-bold">Active Courses</h5>
            <h2 class="fw-bold mb-0"><?php echo $total_courses; ?></h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card ems-card p-3 text-center border-0 shadow-sm border-start border-success border-4">
            <h5 class="text-muted small text-uppercase fw-bold">Enrollments</h5>
            <h2 class="fw-bold mb-0"><?php echo $total_enrollments; ?></h2>
        </div>
    </div>
</div>

<div class="mt-5">
    <h4 class="fw-bold mb-4">Core Administration</h4>
    <div class="row g-3">
        <div class="col-md-4">
            <a href="approve_providers.php" class="btn btn-primary-custom w-100 p-3 text-white shadow-sm d-flex align-items-center justify-content-center">
                <i class="bi bi-person-check me-2"></i> Review Applications (<?php echo $pending_providers; ?>)
            </a>
        </div>
        <div class="col-md-4">
            <a href="reports.php" class="btn btn-outline-secondary w-100 p-3 shadow-sm border-2">
                <i class="bi bi-bar-chart me-2"></i> Global Analytical Reports
            </a>
        </div>
        <div class="col-md-4">
            <a href="audit_logs.php" class="btn btn-outline-secondary w-100 p-3 shadow-sm border-2">
                <i class="bi bi-shield-lock me-2"></i> System Audit Logs
            </a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
