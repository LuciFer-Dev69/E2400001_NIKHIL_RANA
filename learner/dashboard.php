<?php
// learner/dashboard.php - REFACTORED TO PDO
include '../includes/db_connect.php';
include '../includes/functions.php';

check_role(['Learner']);
$user_id = $_SESSION['user_id'];

// Get Enrolled (PDO)
$sql = "SELECT e.*, c.title, c.thumbnail, p.organization_name 
        FROM enrollments e 
        JOIN courses c ON e.course_id = c.id 
        JOIN providers p ON c.provider_id = p.id
        WHERE e.learner_id = ? AND e.payment_status = 'PAID'
        ORDER BY e.enrolled_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$my_courses = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 text-dark">
    <h2 class="fw-bold">My Learning Dashboard</h2>
    <a href="../index.php" class="btn btn-outline-primary border-2 px-4 shadow-sm">Explore Courses</a>
</div>

<div class="row g-4 text-dark">
    <?php if (count($my_courses) > 0): ?>
        <?php foreach ($my_courses as $course): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card ems-card border-0 shadow-sm h-100 overflow-hidden">
                    <img src="../assets/uploads/<?php echo $course['thumbnail'] ?: 'default_course.jpg'; ?>" class="card-img-top" style="height: 160px; object-fit: cover;" onerror="this.src='https://placehold.co/400x200?text=C'">
                    <div class="card-body">
                        <p class="text-muted small mb-1"><?php echo $course['organization_name']; ?></p>
                        <h5 class="fw-bold mb-3"><?php echo $course['title']; ?></h5>
                        <div class="d-grid gap-2">
                            <a href="receipt.php?tx=<?php echo $course['transaction_id']; ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-file-earmark-text me-1"></i> Transaction Receipt
                            </a>
                            <a href="review_course.php?id=<?php echo $course['course_id']; ?>" class="btn btn-sm btn-primary-custom text-white">
                                <i class="bi bi-star me-1"></i> Write Review
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php
    endforeach; ?>
    <?php
else: ?>
        <div class="col-12 text-center py-5">
            <div class="bg-light p-5 rounded-4 border-dashed border-2">
                <h4 class="text-muted">No enrollments found</h4>
                <p>Start your learning journey by exploring our verified courses.</p>
                <a href="../index.php" class="btn btn-primary-custom text-white px-4">Browse Now</a>
            </div>
        </div>
    <?php
endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
