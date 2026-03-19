<?php
// index.php - REFACTORED TO PDO
include 'includes/db_connect.php';
include 'includes/functions.php';

// Fetch active courses via PDO
$stmt = $pdo->query("SELECT c.*, p.organization_name FROM courses c JOIN providers p ON c.provider_id = p.id WHERE c.status = 'ACTIVE' ORDER BY c.created_at DESC");
$courses = $stmt->fetchAll();

include 'includes/header.php';
?>

<!-- Hero Section -->
<div class="row align-items-center py-5">
    <div class="col-lg-6">
        <h1 class="display-4 fw-bold mb-3" style="color: var(--secondary-color);">Professional Skills For The Next Generation.</h1>
        <p class="lead text-muted mb-4">Access premium training from Ministry-vetted providers. Start your learning journey today with the EduSkill Marketplace.</p>
        <div class="d-flex gap-3">
            <a href="register.php" class="btn btn-primary-custom px-4 py-2 text-white shadow">Get Started</a>
            <a href="#courses" class="btn btn-outline-secondary px-4 py-2 border-2">Browse Courses</a>
        </div>
    </div>
    <div class="col-lg-6 d-none d-lg-block">
        <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80" class="img-fluid rounded-4 shadow-lg" alt="Learning Themes">
    </div>
</div>

<!-- Course List -->
<div id="courses" class="mt-5 pt-4">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h2 class="fw-bold text-dark">Vetted Courses</h2>
            <p class="text-muted">Quality Education from Trusted Providers</p>
        </div>
    </div>

    <div class="row g-4">
        <?php foreach ($courses as $row): ?>
            <div class="col-md-4">
                <div class="card ems-card border-0 h-100 shadow-sm overflow-hidden text-dark">
                    <img src="assets/uploads/<?php echo $row['thumbnail'] ?: 'default_course.jpg'; ?>" class="card-img-top" style="height: 180px; object-fit: cover;" onerror="this.src='https://placehold.co/600x400?text=Course+Thumbnail'">
                    <div class="card-body">
                        <span class="badge border text-dark mb-2" style="border-color: var(--primary-color) !important;"><?php echo $row['category']; ?></span>
                        <h5 class="card-title fw-bold"><?php echo $row['title']; ?></h5>
                        <p class="text-muted small">By <?php echo $row['organization_name']; ?></p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="fw-bold fs-5" style="color: var(--secondary-color);"><?php echo format_price($row['price']); ?></span>
                            <a href="course_details.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary border-2 px-3">Details</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php
endforeach; ?>
        
        <?php if (empty($courses)): ?>
            <div class="col-12 text-center py-5">
                <div class="h4 text-muted">No courses available at the moment.</div>
            </div>
        <?php
endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
