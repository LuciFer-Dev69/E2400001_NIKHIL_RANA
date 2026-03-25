<?php
require_once '../../config/db.php';
session_start();

$root = "../../";
$page_title = 'Instructor Dashboard';
include '../../includes/instructor/instructor_header.php';

$inst_id = $_SESSION['user_id'];

// 1. Fetch High Level Stats
try {
    // Total Courses
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE instructor_id = ?");
    $stmt->execute([$inst_id]);
    $total_courses = $stmt->fetchColumn();

    // Published Courses
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE instructor_id = ? AND status = 'published'");
    $stmt->execute([$inst_id]);
    $published_courses = $stmt->fetchColumn();

    // Pending Courses
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE instructor_id = ? AND status = 'pending'");
    $stmt->execute([$inst_id]);
    $pending_courses = $stmt->fetchColumn();

    // Total Students (Unique Enrollments in this instructor's courses)
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT e.student_id) 
                           FROM enrollments e
                           JOIN courses c ON e.course_id = c.id
                           WHERE c.instructor_id = ?");
    $stmt->execute([$inst_id]);
    $total_students = $stmt->fetchColumn();

    // Total Watch Time / Engagement (Simulated from enrollments progress)
    // We calculate the sum of progress_percent across all enrollments. It's a rough proxy for engagement.
    $stmt = $pdo->prepare("SELECT AVG(e.progress_percent) 
                           FROM enrollments e
                           JOIN courses c ON e.course_id = c.id
                           WHERE c.instructor_id = ? AND e.progress_percent > 0");
    $stmt->execute([$inst_id]);
    $avg_completion = round($stmt->fetchColumn() ?: 0);

}
catch (PDOException $e) {
    die("Database Error.");
}
?>

<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px;">
    <div>
        <h1 style="font-size: 28px; font-weight: 800; color: var(--dark-color); margin-bottom: 8px;">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h1>
        <p style="color: var(--gray-color); font-size: 15px;">Here is your Creator Analytics overview for today.</p>
    </div>
    <div>
        <a href="create_course.php" class="btn btn-primary" style="padding: 10px 20px; font-weight: 700; background: #9b59b6; border-color: #9b59b6;">
            <i class="fa fa-magic" style="margin-right: 8px;"></i> Create New Course
        </a>
    </div>
</div>

<!-- STAT CARDS -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 40px;">
    <div style="background: var(--bg-card); padding: 25px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
            <div style="font-size: 13px; font-weight: 800; color: var(--gray-color); text-transform: uppercase;">Total Students</div>
            <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(52, 152, 219, 0.1); color: #3498db; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa fa-users"></i>
            </div>
        </div>
        <div style="font-size: 32px; font-weight: 800; color: var(--dark-color);"><?php echo number_format($total_students); ?></div>
        <div style="font-size: 12px; color: #2ecc71; margin-top: 8px; font-weight: 700;"><i class="fa fa-arrow-up"></i> Unique learners</div>
    </div>

    <div style="background: var(--bg-card); padding: 25px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
            <div style="font-size: 13px; font-weight: 800; color: var(--gray-color); text-transform: uppercase;">Published Courses</div>
            <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(155, 89, 182, 0.1); color: #9b59b6; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa fa-video"></i>
            </div>
        </div>
        <div style="font-size: 32px; font-weight: 800; color: var(--dark-color);"><?php echo $published_courses; ?></div>
        <div style="font-size: 12px; color: var(--gray-color); margin-top: 8px;"><span style="color: #f1c40f; font-weight: 700;"><?php echo $pending_courses; ?></span> pending approval</div>
    </div>

    <div style="background: var(--bg-card); padding: 25px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
            <div style="font-size: 13px; font-weight: 800; color: var(--gray-color); text-transform: uppercase;">Avg Completion</div>
            <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(46, 204, 113, 0.1); color: #2ecc71; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa fa-check-circle"></i>
            </div>
        </div>
        <div style="font-size: 32px; font-weight: 800; color: var(--dark-color);"><?php echo $avg_completion; ?>%</div>
        <div style="font-size: 12px; color: var(--gray-color); margin-top: 8px;">Across all active learners</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
    <!-- Analytics Chart -->
    <div style="background: var(--bg-card); padding: 25px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow);">
        <h3 style="font-size: 16px; font-weight: 800; color: var(--dark-color); margin-bottom: 20px;">Student Enrollment Trends</h3>
        <canvas id="enrollmentChart" height="120"></canvas>
    </div>

    <!-- Quick Actions / Status -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <div style="background: var(--bg-card); padding: 25px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow);">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--dark-color); margin-bottom: 20px;">Course Statuses</h3>
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border-color);">
                <span style="color: var(--gray-color); font-weight: 700;">Published</span>
                <span style="font-weight: 800; color: #2ecc71;"><?php echo $published_courses; ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border-color);">
                <span style="color: var(--gray-color); font-weight: 700;">Pending Review</span>
                <span style="font-weight: 800; color: #f1c40f;"><?php echo $pending_courses; ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 10px 0;">
                <span style="color: var(--gray-color); font-weight: 700;">Drafts</span>
                <span style="font-weight: 800; color: var(--dark-color);"><?php echo($total_courses - $published_courses - $pending_courses); ?></span>
            </div>
        </div>

        <div style="background: linear-gradient(135deg, #9b59b6, #8e44ad); padding: 25px; border-radius: 12px; color: white; box-shadow: 0 10px 20px rgba(155, 89, 182, 0.3);">
            <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 10px;"><i class="fa fa-graduation-cap"></i> Instructor Tips</h3>
            <p style="font-size: 13px; line-height: 1.6; opacity: 0.9;">Courses with high-quality thumbnails and clear Difficulty Levels receive 40% more enrollments. Be sure to completely fill out your Course Details before submitting for approval!</p>
        </div>
    </div>
</div>

<script>
    // Placeholder chart data - in production this would be fed by a real JSON endpoint
    function initCharts() {
        const ctx = document.getElementById('enrollmentChart');
        if(!ctx) return;
        
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
        const textColor = isDark ? '#a0a0a0' : '#7f8c8d';

        window.analyticsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                datasets: [{
                    label: 'New Enrollments',
                    data: [12, 19, 3, 25, 22, 30, 45],
                    borderColor: '#9b59b6',
                    backgroundColor: 'rgba(155, 89, 182, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor } },
                    x: { grid: { display: false }, ticks: { color: textColor } }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', initCharts);
</script>

<?php include '../../includes/instructor/instructor_footer.php'; ?>
