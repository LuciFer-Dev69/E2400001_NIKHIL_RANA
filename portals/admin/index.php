<?php
require_once '../../config/db.php';
session_start();

$root = "../../";
$page_title = 'Admin Dashboard';
include '../../includes/admin/admin_header.php';

// 1. Fetch High-Level Stats
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_students = $pdo->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn();
$total_instructors = $pdo->query("SELECT COUNT(*) FROM users WHERE role='instructor'")->fetchColumn();

$total_courses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$active_courses = $pdo->query("SELECT COUNT(*) FROM courses WHERE status='published'")->fetchColumn();

// Total Revenue Calculation (Enrollments where course has a price and is purchased if we use is_purchased in future, but based on current schema, we simply sum price of enrolled courses)
// Assuming all enrollments in priced courses generate revenue for this dashboard demo.
$revenue_query = $pdo->query("SELECT SUM(c.price) as total_rev FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE c.price > 0");
$total_revenue = $revenue_query->fetchColumn() ?: 0.00;

// 2. Fetch Recent Enrollments (Activity)
$recent_enrollments = $pdo->query("
    SELECT e.enrolled_at, u.full_name as student_name, c.title as course_title 
    FROM enrollments e 
    JOIN users u ON e.student_id = u.id 
    JOIN courses c ON e.course_id = c.id 
    ORDER BY e.enrolled_at DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// 3. Data for Charts (Course Popularity)
$top_courses = $pdo->query("
    SELECT c.title, COUNT(e.id) as enroll_count 
    FROM courses c 
    LEFT JOIN enrollments e ON c.id = e.course_id 
    GROUP BY c.id 
    ORDER BY enroll_count DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

$course_labels = json_encode(array_column($top_courses, 'title'));
$course_counts = json_encode(array_column($top_courses, 'enroll_count'));
?>

<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px;">
    <div>
        <h1 style="font-size: 32px; font-weight: 800; color: var(--dark-color); margin-bottom: 8px;">Platform Overview</h1>
        <p style="color: var(--gray-color); font-size: 16px;">Welcome back, Admin. Here's what's happening on SkillEdu today.</p>
    </div>
    <div style="background: var(--light-gray); border: 1px solid var(--border-color); padding: 10px 20px; border-radius: 8px; font-weight: 700; color: var(--dark-color);">
        <i class="fa fa-calendar-alt" style="color: var(--primary-color);"></i> <?php echo date('F j, Y'); ?>
    </div>
</div>

<!-- Highlight Cards -->
<div class="analytics-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #11998e, #38ef7d);">
            <i class="fa fa-users"></i>
        </div>
        <div>
            <div style="font-size: 13px; font-weight: 700; color: var(--gray-color); text-transform: uppercase;">Total Users</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--dark-color);"><?php echo number_format($total_users); ?></div>
            <div style="font-size: 12px; color: #2ecc71; margin-top: 4px;"><i class="fa fa-arrow-up"></i> Active community</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #FF416C, #FF4B2B);">
            <i class="fa fa-video"></i>
        </div>
        <div>
            <div style="font-size: 13px; font-weight: 700; color: var(--gray-color); text-transform: uppercase;">Active Courses</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--dark-color);"><?php echo number_format($active_courses); ?> <span style="font-size: 14px; color: var(--gray-color);">/ <?php echo $total_courses; ?></span></div>
            <div style="font-size: 12px; color: var(--gray-color); margin-top: 4px;">Published courses</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #f2994a, #f2c94c);">
            <i class="fa fa-chalkboard-teacher"></i>
        </div>
        <div>
            <div style="font-size: 13px; font-weight: 700; color: var(--gray-color); text-transform: uppercase;">Instructors</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--dark-color);"><?php echo number_format($total_instructors); ?></div>
            <div style="font-size: 12px; color: var(--gray-color); margin-top: 4px;">Content creators</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #2193b0, #6dd5ed);">
            <i class="fa fa-wallet"></i>
        </div>
        <div>
            <div style="font-size: 13px; font-weight: 700; color: var(--gray-color); text-transform: uppercase;">Total Revenue</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--dark-color);">$<?php echo number_format($total_revenue, 2); ?></div>
            <div style="font-size: 12px; color: #2ecc71; margin-top: 4px;"><i class="fa fa-arrow-up"></i> Estimated lifetime</div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-bottom: 40px;">
    
    <!-- Charts Area -->
    <div style="background: var(--bg-card); padding: 25px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow);">
        <h3 style="font-size: 18px; color: var(--dark-color); margin-bottom: 20px;">Top Performing Courses (Enrollments)</h3>
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="popularityChart"></canvas>
        </div>
    </div>

    <!-- Recent Activity List -->
    <div style="background: var(--bg-card); padding: 25px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow);">
        <h3 style="font-size: 18px; color: var(--dark-color); margin-bottom: 20px;">Recent Enrollments</h3>
        
        <?php if (empty($recent_enrollments)): ?>
            <p style="color: var(--gray-color); text-align: center; margin-top: 40px;">No recent enrollments.</p>
        <?php
else: ?>
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <?php foreach ($recent_enrollments as $act): ?>
                <div style="display: flex; align-items: flex-start; gap: 15px; padding-bottom: 15px; border-bottom: 1px solid var(--border-color);">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--light-gray); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                        <i class="fa fa-graduation-cap"></i>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 14px; color: var(--dark-color); margin-bottom: 4px;">
                            <?php echo htmlspecialchars($act['student_name']); ?>
                        </div>
                        <div style="font-size: 13px; color: var(--gray-color);">
                            Enrolled in <strong style="color: var(--dark-color);"><?php echo htmlspecialchars($act['course_title']); ?></strong>
                        </div>
                        <div style="font-size: 11px; color: var(--gray-color); margin-top: 4px;">
                            <?php echo date('M j, Y g:i A', strtotime($act['enrolled_at'])); ?>
                        </div>
                    </div>
                </div>
                <?php
    endforeach; ?>
            </div>
        <?php
endif; ?>
    </div>
</div>

<script>
    function initCharts() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const textColor = isDark ? '#f7f9fa' : '#6a6f73';
        const gridColor = isDark ? '#3e4143' : '#e4e7ea';

        Chart.defaults.color = textColor;
        Chart.defaults.font.family = "'Outfit', sans-serif";

        const ctxPop = document.getElementById('popularityChart');
        if (ctxPop) {
            window.usersChart = new Chart(ctxPop, {
                type: 'bar',
                data: {
                    labels: <?php echo $course_labels; ?>,
                    datasets: [{
                        label: 'Enrollments',
                        data: <?php echo $course_counts; ?>,
                        backgroundColor: '#FF416C',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { 
                        y: { 
                            beginAtZero: true,
                            grid: { color: gridColor, drawBorder: false }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { 
                                callback: function(value) {
                                    // Truncate long course names
                                    let label = this.getLabelForValue(value);
                                    return label.length > 15 ? label.substring(0, 15) + '...' : label;
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', initCharts);
</script>

<?php include '../../includes/admin/admin_footer.php'; ?>
