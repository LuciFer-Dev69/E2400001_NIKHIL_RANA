<?php
// learner/review_course.php - REFACTORED TO PDO
include '../includes/db_connect.php';
include '../includes/functions.php';

check_role(['Learner']);
$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$course_id = (int)$_GET['id'];

// Verify enrollment (PDO)
$stmt = $pdo->prepare("SELECT id FROM enrollments WHERE learner_id = ? AND course_id = ? AND payment_status = 'PAID'");
$stmt->execute([$user_id, $course_id]);
$enrollment = $stmt->fetch();

if (!$enrollment) {
    die("You must be enrolled to review this course!");
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!validate_csrf_token($_POST['csrf_token'])) {
        die("CSRF Token Invalid");
    }

    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);
    $e_id = $enrollment['id'];

    // Upsert review (PDO)
    $stmt = $pdo->prepare("INSERT INTO reviews (enrollment_id, rating, comment) VALUES (?, ?, ?) 
                          ON DUPLICATE KEY UPDATE rating=?, comment=?");

    if ($stmt->execute([$e_id, $rating, $comment, $rating, $comment])) {
        log_action($pdo, "COURSE_REVIEW", "Learner $user_id reviewed course $course_id with rating $rating");
        $success = "Thank you for your feedback! Your review has been saved.";
    }
    else {
        $error = "System Error. Failed to save review.";
    }
}

include '../includes/header.php';
?>

<div class="row justify-content-center text-dark">
    <div class="col-md-6">
        <div class="card ems-card p-4 border-0 shadow-lg">
            <h4 class="fw-bold mb-4 text-center">Course Review & Feedback</h4>
            
            <?php if ($error): ?>
                <div class="alert alert-danger shadow-sm"><?php echo $error; ?></div>
            <?php
endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success shadow-sm"><?php echo $success; ?></div>
            <?php
endif; ?>

            <form method="POST" class="needs-validation" novalidate>
                <?php csrf_field(); ?>
                <div class="mb-4 text-center">
                    <label class="form-label d-block fw-bold mb-3">How would you rate your experience?</label>
                    <div class="btn-group w-100" role="group">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <input type="radio" class="btn-check" name="rating" id="star<?php echo $i; ?>" value="<?php echo $i; ?>" required>
                            <label class="btn btn-outline-warning py-3" for="star<?php echo $i; ?>"><?php echo $i; ?> <i class="bi bi-star-fill"></i></label>
                        <?php
endfor; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Detailed Feedback</label>
                    <textarea name="comment" class="form-control" rows="5" placeholder="What did you like? What could be improved?" required></textarea>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary-custom text-white py-3 shadow">Submit Review</button>
                    <a href="dashboard.php" class="btn btn-link mt-2 text-muted">Back to Dashboard</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
