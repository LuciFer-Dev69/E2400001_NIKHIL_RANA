<?php
require_once '../../config/db.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$portal_type = 'student';
$completed_count = 0;
$learning_hours = 0;
// Pagination setup
$limit = 8;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

try {
    // 1. Get total valid enrolled courses for pagination and stats
    $filter_sql = "WHERE e.student_id = :user_id AND (c.price = 0 OR e.is_purchased = 1)";
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM enrollments e
        JOIN courses c ON e.course_id = c.id
        " . str_replace(':user_id', '?', $filter_sql) . "
    ");
    $stmt->execute([$user_id]);
    $total_enrolled_count = $stmt->fetchColumn() ?: 0;
    $total_pages = ceil($total_enrolled_count / $limit);

    // 2. Fetch paginated courses with instructor name and progress
    $stmt = $pdo->prepare("
        SELECT c.*, u.full_name as instructor_name, e.progress_percent, e.enrolled_at
        FROM enrollments e
        JOIN courses c ON e.course_id = c.id
        JOIN users u ON c.instructor_id = u.id
        $filter_sql
        ORDER BY e.enrolled_at DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $courses_enrolled = $stmt->fetchAll();

    // 3. Calculate Completed Count across ALL valid enrollments
    $stmt = $pdo->prepare("SELECT e.progress_percent FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.student_id = ? AND (c.price = 0 OR e.is_purchased = 1)");
    $stmt->execute([$user_id]);
    $all_enrolled = $stmt->fetchAll();
    foreach ($all_enrolled as $c) {
        if ($c['progress_percent'] == 100)
            $completed_count++;
    }

    // 4. Calculate Real Learning Hours
    $stmt = $pdo->prepare("
        SELECT SUM(l.duration_mins) as total_mins
        FROM user_lesson_progress ulp
        JOIN lessons l ON ulp.lesson_id = l.id
        WHERE ulp.user_id = ? AND ulp.status = 'completed'
    ");
    $stmt->execute([$user_id]);
    $total_mins = $stmt->fetchColumn() ?: 0;
    $learning_hours = round($total_mins / 60, 1);

    // 5. Identification of "Continue Learning" & Next Lesson
    $continue_course = null;
    $next_lesson = null;

    // Get the most recent valid course regardless of page
    $stmt = $pdo->prepare("
        SELECT c.*, e.progress_percent 
        FROM enrollments e 
        JOIN courses c ON e.course_id = c.id 
        WHERE e.student_id = ? AND (c.price = 0 OR e.is_purchased = 1) 
        ORDER BY e.enrolled_at DESC LIMIT 1
    ");
    $stmt->execute([$user_id]);
    $continue_course = $stmt->fetch();

    if ($continue_course) {
        $stmt = $pdo->prepare("
            SELECT l.title, l.id
            FROM lessons l
            LEFT JOIN user_lesson_progress ulp ON l.id = ulp.lesson_id AND ulp.user_id = ?
            WHERE l.course_id = ? AND (ulp.status IS NULL OR ulp.status != 'completed')
            ORDER BY l.order_num ASC LIMIT 1
        ");
        $stmt->execute([$user_id, $continue_course['id']]);
        $next_lesson = $stmt->fetch();
    }

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

<div class="welcome-header" style="margin-bottom: 35px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="font-size: 32px; font-weight: 800; color: #1c1d1f; margin-bottom: 8px;">Welcome back, <?php echo explode(' ', $_SESSION['full_name'] ?? 'User')[0]; ?></h1>
        <p style="color: #6a6f73; font-size: 16px;">Ready to hit your daily goal? Keep it up! 🚀</p>
    </div>
    <div class="streak-badge">
        <i class="fa fa-fire"></i>
        <span>3 Day Streak</span>
    </div>
</div>

<div class="stats-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-bottom: 40px;">
    <div class="stat-card" style="padding: 25px; border-radius: 12px; display: flex; align-items: center; gap: 20px;">
        <div class="stat-icon bg-red"><i class="fa fa-book-open"></i></div>
        <div>
            <div style="font-size: 13px; font-weight: 700; color: #6a6f73; text-transform: uppercase;">Enrolled</div>
            <div style="font-size: 28px; font-weight: 800; color: #1c1d1f;"><?php echo count($courses_enrolled); ?></div>
        </div>
    </div>
    <div class="stat-card" style="padding: 25px; border-radius: 12px; display: flex; align-items: center; gap: 20px;">
        <div class="stat-icon bg-green"><i class="fa fa-award"></i></div>
        <div>
            <div style="font-size: 13px; font-weight: 700; color: #6a6f73; text-transform: uppercase;">Completed</div>
            <div style="font-size: 28px; font-weight: 800; color: #1c1d1f;"><?php echo $completed_count; ?></div>
        </div>
    </div>
    <div class="stat-card" style="padding: 25px; border-radius: 12px; display: flex; align-items: center; gap: 20px;">
        <div class="stat-icon bg-blue"><i class="fa fa-clock"></i></div>
        <div>
            <div style="font-size: 13px; font-weight: 700; color: #6a6f73; text-transform: uppercase;">Learning Hours</div>
            <div style="font-size: 28px; font-weight: 800; color: #1c1d1f;"><?php echo $learning_hours; ?>h</div>
        </div>
    </div>
</div>

<?php if ($continue_course): ?>
<div class="continue-learning" style="background: #f7f9fa; padding: 30px; border-radius: 12px; margin-bottom: 40px; display: flex; align-items: center; gap: 30px;">
    <div class="continue-thumbnail" style="width: 180px; height: 120px; border-radius: 8px; overflow: hidden; flex-shrink: 0;">
        <img src="../../assets/img/courses/<?php echo $continue_course['thumbnail'] ?: 'default.jpg'; ?>" alt="<?php echo $continue_course['title']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
    </div>
    <div class="continue-content">
        <h2 style="font-size: 20px; font-weight: 800; color: #1c1d1f; margin-bottom: 10px;">Continue Learning</h2>
        <h3 style="font-size: 18px; font-weight: 700; color: #1c1d1f; margin-bottom: 8px;"><?php echo $continue_course['title']; ?></h3>
        <?php if ($next_lesson): ?>
            <p style="font-size: 14px; color: #6a6f73; margin-bottom: 15px;">Next: <?php echo $next_lesson['title']; ?></p>
            <a href="player.php?course_id=<?php echo $continue_course['id']; ?>&lesson_id=<?php echo $next_lesson['id']; ?>" class="btn btn-primary">Resume Course</a>
        <?php
    else: ?>
            <p style="font-size: 14px; color: #6a6f73; margin-bottom: 15px;">You've completed all lessons in this course!</p>
            <a href="player.php?course_id=<?php echo $continue_course['id']; ?>" class="btn btn-primary">Review Course</a>
        <?php
    endif; ?>
    </div>
</div>
<?php
endif; ?>

<h2 style="font-size: 24px; margin-bottom: 25px;">Courses</h2>
<div class="courses-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px;">
    <?php foreach ($courses_enrolled as $course):
    $is_locked = ($course['price'] > 0);
?>
    <div class="course-card-premium <?php echo $is_locked ? 'locked' : ''; ?>" style="position: relative;">
        <?php if ($is_locked): ?>
            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 160px; background: rgba(0,0,0,0.4); z-index: 2; display: flex; align-items: center; justify-content: center; color: white; font-size: 30px;">
                <i class="fa fa-lock"></i>
            </div>
        <?php
    endif; ?>
        
        <div style="position: relative; height: 160px; overflow: hidden;">
            <img src="../../assets/img/courses/<?php echo $course['thumbnail'] ?: 'default.jpg'; ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; <?php echo $is_locked ? 'filter: grayscale(0.5) blur(2px);' : ''; ?>" onerror="this.src='https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=400&q=80'">
            <div style="position: absolute; top: 10px; right: 10px; background: <?php echo $is_locked ? '#ff8a00' : '#2ecc71'; ?>; color: white; padding: 4px 10px; border-radius: 4px; font-size: 10px; font-weight: 800; text-transform: uppercase; z-index: 3;">
                <?php echo $is_locked ? 'Locked' : 'Free'; ?>
            </div>
            <div style="position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.7); color: white; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; z-index: 3;">
                <?php echo $course['category_name'] ?? 'SkillEdu'; ?>
            </div>
        </div>
        <div style="padding: 20px;">
            <h3 style="font-size: 16px; font-weight: 800; color: #1c1d1f; margin-bottom: 10px; line-height: 1.4; min-height: 44px;">
                <?php echo $course['title']; ?>
            </h3>
            <p style="font-size: 12px; color: #6a6f73; margin-bottom: 15px;">By <?php echo $course['instructor_name']; ?></p>
            
            <?php if ($is_locked): ?>
                <div style="margin-bottom: 20px; padding: 10px; background: #fffcf0; border: 1px dashed #ffe0e0; border-radius: 6px; font-size: 12px; color: #b4690e; font-weight: 700; text-align: center;">
                    <i class="fa fa-shopping-cart"></i> Single course purchase required
                </div>
                <a href="#" class="btn btn-primary" style="width: 100%; padding: 10px; font-weight: 800; background: #1c1d1f; color: white; border: none;">Unlock for $<?php echo number_format($course['price'], 2); ?></a>
            <?php
    else: ?>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; font-size: 12px; font-weight: 700;">
                    <span color="#1c1d1f"><?php echo $course['progress_percent']; ?>% Complete</span>
                </div>
                <div class="premium-progress" style="margin: 0 0 20px 0;">
                    <div class="premium-progress-bar" style="width: <?php echo $course['progress_percent']; ?>%;"></div>
                </div>
                <a href="player.php?course_id=<?php echo $course['id']; ?>" class="btn btn-secondary" style="width: 100%; padding: 10px; font-weight: 700;">View Course</a>
            <?php
    endif; ?>
        </div>
    </div>
    <?php
endforeach; ?>
</div>

<!-- Pagination Controls -->
<?php if ($total_pages > 1): ?>
<div style="display: flex; justify-content: center; align-items: center; gap: 15px; margin-top: 40px; margin-bottom: 20px;">
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>" class="btn btn-secondary" style="padding: 10px 20px; font-weight: 700; border-radius: 8px; border: 1px solid #d1d7dc;">
            <i class="fa fa-chevron-left" style="margin-right: 8px;"></i> Previous
        </a>
    <?php
    else: ?>
        <button class="btn btn-secondary" disabled style="padding: 10px 20px; font-weight: 700; border-radius: 8px; border: 1px solid #d1d7dc; opacity: 0.5; cursor: not-allowed;">
            <i class="fa fa-chevron-left" style="margin-right: 8px;"></i> Previous
        </button>
    <?php
    endif; ?>

    <span style="font-weight: 700; color: #1c1d1f; font-size: 15px; padding: 0 10px;">
        Page <?php echo $page; ?> of <?php echo $total_pages; ?>
    </span>

    <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?>" class="btn btn-secondary" style="padding: 10px 20px; font-weight: 700; border-radius: 8px; border: 1px solid #d1d7dc;">
            Next <i class="fa fa-chevron-right" style="margin-left: 8px;"></i>
        </a>
    <?php
    else: ?>
        <button class="btn btn-secondary" disabled style="padding: 10px 20px; font-weight: 700; border-radius: 8px; border: 1px solid #d1d7dc; opacity: 0.5; cursor: not-allowed;">
            Next <i class="fa fa-chevron-right" style="margin-left: 8px;"></i>
        </button>
    <?php
    endif; ?>
</div>
<?php
endif; ?>

    </div>
</div>
<?php

function h($str)
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<?php include '../../includes/footer.php'; ?>
