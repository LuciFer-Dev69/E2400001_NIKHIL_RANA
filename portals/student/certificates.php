<?php
require_once '../../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$portal_type = 'student';

// Fetch courses that are 100% physically completed or marked complete in enrollments
try {
    $stmt = $pdo->prepare("
        SELECT c.*, e.progress_percent, e.enrolled_at, u.full_name as instructor_name
        FROM enrollments e
        JOIN courses c ON e.course_id = c.id
        JOIN users u ON c.instructor_id = u.id
        WHERE e.student_id = ? AND e.progress_percent = 100
        ORDER BY e.enrolled_at DESC
    ");
    $stmt->execute([$user_id]);
    $completed_courses = $stmt->fetchAll();
}
catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$base_url = "../../";
$portal_context = 'student';
include '../../includes/header.php';
?>

<div class="portal-shell">
    <?php include '../../includes/portal_header_sidebar.php'; ?>
    <div class="portal-content">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px; background: #fff; padding: 25px; border-radius: 12px; border: 1px solid var(--border-color);">
            <div>
                <h1 style="font-size: 28px; font-weight: 800; color: #1c1d1f; margin-bottom: 5px;">My Certificates</h1>
                <p style="color: #6a6f73; font-size: 14px;">You have earned <strong style="color: #1c1d1f;"><?php echo count($completed_courses); ?></strong> certificates.</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px;">
            <?php if (empty($completed_courses)): ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 80px; background: #fff; border-radius: 12px; border: 1px solid #d1d7dc;">
                    <i class="fa fa-award" style="font-size: 60px; color: #d1d7dc; margin-bottom: 20px;"></i>
                    <h3 style="font-size: 20px; font-weight: 700; color: #1c1d1f; margin-bottom: 10px;">No certificates yet</h3>
                    <p style="color: #6a6f73; margin-bottom: 25px;">Complete 100% of a course to earn your first certificate.</p>
                    <a href="courses.php" class="btn btn-primary" style="background: #1c1d1f; color: white;">Continue Learning</a>
                </div>
            <?php
else: ?>
                <?php foreach ($completed_courses as $course): ?>
                    <div style="background: #fff; border: 1px solid #d1d7dc; border-radius: 8px; overflow: hidden; display: flex; flex-direction: column;">
                        <div style="height: 160px; position: relative;">
                            <img src="../../assets/img/courses/<?php echo $course['thumbnail'] ?: 'default.jpg'; ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=400&q=80'">
                            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; color: white;">
                                <i class="fa fa-award" style="font-size: 40px; color: #f1c40f;"></i>
                            </div>
                        </div>
                        <div style="padding: 20px; flex: 1; display: flex; flex-direction: column;">
                            <h3 style="font-size: 16px; font-weight: 700; color: #1c1d1f; margin-bottom: 8px; line-height: 1.4;"><?php echo htmlspecialchars($course['title']); ?></h3>
                            <p style="font-size: 13px; color: #6a6f73; margin-bottom: 15px;">Instructor: <?php echo htmlspecialchars($course['instructor_name']); ?></p>
                            
                            <div style="margin-top: auto;">
                                <a href="certificate_view.php?course_id=<?php echo $course['id']; ?>" target="_blank" class="btn btn-secondary" style="width: 100%; text-align: center; font-weight: 700;">View Certificate</a>
                            </div>
                        </div>
                    </div>
                <?php
    endforeach; ?>
            <?php
endif; ?>
        </div>

    </div>
</div>

<?php include '../../includes/footer.php'; ?>
