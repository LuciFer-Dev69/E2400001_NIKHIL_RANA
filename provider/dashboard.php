<?php
// provider/dashboard.php - REFACTORED TO PDO
include '../includes/db_connect.php';
include '../includes/functions.php';

check_role(['Provider']);
$provider_id = $_SESSION['provider_id'];

// Stats via PDO
$stmt1 = $pdo->prepare("SELECT COUNT(*) as count FROM courses WHERE provider_id = ?");
$stmt1->execute([$provider_id]);
$course_count = $stmt1->fetch()['count'];

$stmt2 = $pdo->prepare("SELECT COUNT(*) as count FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE c.provider_id = ? AND e.payment_status = 'PAID'");
$stmt2->execute([$provider_id]);
$enrollment_count = $stmt2->fetch()['count'];

include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Provider Portal</h2>
    <span class="text-muted">Managed by <?php echo $_SESSION['full_name']; ?></span>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card ems-card p-3 text-center border-0 shadow-sm border-start border-primary border-4">
            <h5 class="text-muted">Your Courses</h5>
            <h1 class="fw-bold mb-0 text-primary"><?php echo $course_count; ?></h1>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card ems-card p-3 text-center border-0 shadow-sm border-start border-success border-4">
            <h5 class="text-muted">Total Enrolled</h5>
            <h1 class="fw-bold mb-0 text-success"><?php echo $enrollment_count; ?></h1>
        </div>
    </div>
</div>

<div class="mt-5">
    <h4>Actions</h4>
    <div class="row mt-3 g-3">
        <div class="col-md-3">
            <a href="add_course.php" class="btn btn-primary-custom w-100 p-3 text-white shadow-sm">Add New Course</a>
        </div>
        <div class="col-md-3">
            <a href="manage_courses.php" class="btn btn-outline-secondary w-100 p-3 shadow-sm border-2">Manage Courses</a>
        </div>
        <div class="col-md-3">
            <a href="reports.php" class="btn btn-outline-secondary w-100 p-3 shadow-sm border-2" style="border-color: var(--accent-color); color: var(--accent-color);">Performance Reports</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
