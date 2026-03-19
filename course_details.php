<?php
// course_details.php - REFACTORED TO PDO
include 'includes/db_connect.php';
include 'includes/functions.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];

// Get details via PDO
$stmt = $pdo->prepare("SELECT c.*, p.organization_name FROM courses c JOIN providers p ON c.provider_id = p.id WHERE c.id = ? AND c.status = 'ACTIVE'");
$stmt->execute([$id]);
$course = $stmt->fetch();

if (!$course) {
    header("Location: index.php");
    exit();
}

include 'includes/header.php';
?>

<div class="row">
    <div class="col-lg-8">
        <div class="card ems-card border-0 shadow-sm overflow-hidden mb-4 text-dark">
            <img src="assets/uploads/<?php echo $course['thumbnail'] ?: 'default_course.jpg'; ?>" class="img-fluid" style="width: 100%; height: 400px; object-fit: cover;" onerror="this.src='https://placehold.co/800x400?text=Course+Thumbnail'">
            <div class="card-body p-4">
                <span class="badge mb-3" style="background-color: var(--primary-color);"><?php echo $course['category']; ?></span>
                <h1 class="fw-bold"><?php echo $course['title']; ?></h1>
                <p class="text-muted">Offered by <span class="fw-bold text-dark"><?php echo $course['organization_name']; ?></span></p>
                <hr>
                <h4 class="fw-bold mb-3">About This Course</h4>
                <div style="white-space: pre-wrap;" class="lead fs-6"><?php echo htmlspecialchars($course['description']); ?></div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 text-dark">
        <div class="card ems-card border-0 shadow-sm p-4 sticky-top" style="top: 100px;">
            <div class="mb-4">
                <span class="text-muted small text-uppercase fw-bold">One-time payment</span>
                <h2 class="fw-bold display-5" style="color: var(--secondary-color);"><?php echo format_price($course['price']); ?></h2>
            </div>
            
            <ul class="list-unstyled mb-4">
                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Lifetime Access</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Vetted Quality Content</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Skill Certification</li>
            </ul>

            <div class="d-grid gap-2">
                <a href="learner/enroll_course.php?id=<?php echo $course['id']; ?>" class="btn btn-primary-custom text-white py-3 fs-5 shadow">Enroll Now</a>
                <p class="text-center text-muted small mt-2">30-day satisfaction guarantee</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
