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
        WHERE e.student_id = ?
    ");
    $stmt->execute([$user_id]);
    $total_enrolled_count = $stmt->fetchColumn() ?: 0;
    $total_pages = ceil($total_enrolled_count / $limit);

    // Fetch user favorites
    $stmt = $pdo->prepare("SELECT course_id FROM favorites WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $favorites_array = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

    // 2. Fetch paginated courses with instructor name, progress, and pin status
    $stmt = $pdo->prepare("
        SELECT c.*, c.id AS course_id, u.full_name as instructor_name, e.is_purchased, e.progress_percent, e.enrolled_at,
               (SELECT COUNT(*) FROM pinned_courses p WHERE p.user_id = :pin_user_id AND p.course_id = c.id) as is_pinned
        FROM enrollments e
        JOIN courses c ON e.course_id = c.id
        JOIN users u ON c.instructor_id = u.id
        WHERE e.student_id = :user_id
        ORDER BY is_pinned DESC, e.enrolled_at DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':pin_user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $courses_enrolled = $stmt->fetchAll();

    // Fetch Smart Recommendations
    $stmt = $pdo->prepare("
        SELECT c.category_id, COUNT(*) as cat_count
        FROM enrollments e
        JOIN courses c ON e.course_id = c.id
        WHERE e.student_id = ?
        GROUP BY c.category_id
        ORDER BY cat_count DESC LIMIT 1
    ");
    $stmt->execute([$user_id]);
    $top_cat = $stmt->fetch();

    $recommendations = [];
    $rec_title = "Trending Courses";
    if ($top_cat) {
        $stmt = $pdo->prepare("
            SELECT c.*, u.full_name as instructor_name, cat.name as category_name
            FROM courses c
            JOIN users u ON c.instructor_id = u.id
            JOIN categories cat ON c.category_id = cat.id
            WHERE c.category_id = ? AND c.status = 'published' AND c.id NOT IN (
                SELECT course_id FROM enrollments WHERE student_id = ?
            )
            ORDER BY c.created_at DESC LIMIT 4
        ");
        $stmt->execute([$top_cat['category_id'], $user_id]);
        $recommendations = $stmt->fetchAll();
        if (count($recommendations) > 0) {
            $rec_title = "Because you enrolled in " . $recommendations[0]['category_name'];
        }
    }

    if (empty($recommendations)) {
        $stmt = $pdo->query("
            SELECT c.*, u.full_name as instructor_name, cat.name as category_name
            FROM courses c
            JOIN users u ON c.instructor_id = u.id
            JOIN categories cat ON c.category_id = cat.id
            WHERE c.status = 'published'
            LIMIT 4
        ");
        $recommendations = $stmt->fetchAll();
    }

    // 3. Calculate Completed Count across ALL valid enrollments
    $stmt = $pdo->prepare("SELECT e.progress_percent FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.student_id = ?");
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
        SELECT c.*, c.id AS course_id, e.progress_percent, e.is_purchased
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

$root = "../../";
$page_title = 'My Dashboard';
include '../../includes/portal_header.php';
?>

<div class="welcome-header" style="margin-bottom: 35px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="font-size: 32px; font-weight: 800; color: var(--dark-color); margin-bottom: 8px;">Welcome back, <?php echo explode(' ', $_SESSION['full_name'] ?? 'User')[0]; ?></h1>
        <p style="color: var(--gray-color); font-size: 16px;">Ready to hit your daily goal? Keep it up! 🚀</p>
    </div>
    <div class="streak-badge">
        <i class="fa fa-fire"></i>
        <span>3 Day Streak</span>
    </div>
</div>

<div class="stats-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-bottom: 30px;">
    <div class="stat-card skeleton" style="padding: 25px; border-radius: 12px; display: flex; align-items: center; gap: 20px; border: 1px solid var(--border-color); background: var(--bg-card);">
        <div class="stat-icon bg-red" style="animation: none;"><i class="fa fa-book-open"></i></div>
        <div>
            <div style="font-size: 13px; font-weight: 700; color: var(--gray-color); text-transform: uppercase;">Enrolled</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--dark-color);"><?php echo count($courses_enrolled); ?></div>
        </div>
    </div>
    <div class="stat-card skeleton" style="padding: 25px; border-radius: 12px; display: flex; align-items: center; gap: 20px; border: 1px solid var(--border-color); background: var(--bg-card);">
        <div class="stat-icon bg-green" style="animation: none;"><i class="fa fa-award"></i></div>
        <div>
            <div style="font-size: 13px; font-weight: 700; color: var(--gray-color); text-transform: uppercase;">Completed</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--dark-color);"><?php echo $completed_count; ?></div>
        </div>
    </div>
    <div class="stat-card skeleton" style="padding: 25px; border-radius: 12px; display: flex; align-items: center; gap: 20px; border: 1px solid var(--border-color); background: var(--bg-card);">
        <div class="stat-icon bg-blue" style="animation: none;"><i class="fa fa-clock"></i></div>
        <div>
            <div style="font-size: 13px; font-weight: 700; color: var(--gray-color); text-transform: uppercase;">Learning Hours</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--dark-color);"><?php echo $learning_hours; ?>h</div>
        </div>
    </div>
</div>

<h2 style="font-size: 22px; margin-bottom: 20px; color: var(--dark-color);">Activity & Goals</h2>
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px; margin-bottom: 40px;">
    <!-- Chart: Weekly Learning Time -->
    <div style="background: var(--white); padding: 25px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow); transition: transform 0.3s ease;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="font-size: 16px; color: var(--dark-color); font-weight: 700;">Weekly Learning Time</h3>
            <span style="font-size: 12px; color: var(--gray-color);"><i class="fa fa-arrow-trend-up" style="color: #2ecc71;"></i> +15% from last week</span>
        </div>
        <div style="position: relative; height: 180px; width: 100%;">
            <canvas id="learningChart"></canvas>
        </div>
    </div>
    
    <!-- Daily Goal Widget -->
    <div style="background: var(--white); padding: 25px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow); display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative;">
        <div style="position: absolute; top: 15px; right: 15px; color: #b4690e;"><i class="fa fa-bullseye"></i></div>
        <h3 style="font-size: 16px; margin-bottom: 15px; color: var(--dark-color); font-weight: 700;">Daily Goal</h3>
        <div style="position: relative; width: 120px; height: 120px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));">
            <canvas id="goalChart" width="120" height="120"></canvas>
            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <span style="font-size: 24px; font-weight: 800; color: var(--dark-color);">1/2</span>
                <span style="font-size: 10px; color: var(--gray-color); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Lessons</span>
            </div>
        </div>
        <p style="margin-top: 15px; font-size: 14px; color: var(--gray-color); text-align: center;">Complete <b>1 more lesson</b> to reach your daily goal! 🔥</p>
    </div>
</div>

<?php if ($continue_course): ?>
<div class="continue-learning" style="background: var(--light-gray); padding: 30px; border-radius: 12px; margin-bottom: 40px; display: flex; align-items: center; gap: 30px;">
    <div class="continue-thumbnail" style="width: 180px; height: 120px; border-radius: 8px; overflow: hidden; flex-shrink: 0;">
        <img src="../../assets/img/courses/<?php echo $continue_course['thumbnail'] ?: 'default.jpg'; ?>" alt="<?php echo $continue_course['title']; ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=400&q=80'">
    </div>
    <div class="continue-content">
        <h2 style="font-size: 20px; font-weight: 800; color: var(--dark-color); margin-bottom: 10px;">Continue Learning</h2>
        <h3 style="font-size: 18px; font-weight: 700; color: var(--dark-color); margin-bottom: 8px;"><?php echo $continue_course['title']; ?></h3>
        <?php if ($next_lesson): ?>
            <p style="font-size: 14px; color: var(--gray-color); margin-bottom: 15px;">Next: <?php echo $next_lesson['title']; ?></p>
            <a href="player.php?course_id=<?php echo $continue_course['id']; ?>&lesson_id=<?php echo $next_lesson['id']; ?>" class="btn btn-primary">Resume Course</a>
        <?php
    else: ?>
            <p style="font-size: 14px; color: var(--gray-color); margin-bottom: 15px;">You've completed all lessons in this course!</p>
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
    $is_locked = ($course['price'] > 0 && ($course['is_purchased'] ?? 0) == 0);
    $course_id = $course['course_id'];
    $is_pinned = $course['is_pinned'] > 0;
    $is_favorite = in_array($course_id, $favorites_array);
?>
    <div class="course-card-premium <?php echo $is_locked ? 'locked' : ''; ?>" style="position: relative;">
        <!-- Pin and Favorite Controls -->
        <div style="position: absolute; top: 10px; right: 10px; z-index: 10; display: flex; gap: 8px;">
            <button class="icon-toggle-btn pin-btn <?php echo $is_pinned ? 'active' : ''; ?>" data-id="<?php echo $course_id; ?>" style="background: var(--bg-card); border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; color: <?php echo $is_pinned ? 'var(--primary-color)' : 'var(--gray-color)'; ?>; box-shadow: var(--shadow); transition: all 0.2s;">
                <i class="fas fa-thumbtack"></i>
            </button>
            <button class="icon-toggle-btn fav-btn <?php echo $is_favorite ? 'active' : ''; ?>" data-id="<?php echo $course_id; ?>" style="background: var(--bg-card); border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; color: <?php echo $is_favorite ? 'var(--primary-color)' : 'var(--gray-color)'; ?>; box-shadow: var(--shadow); transition: all 0.2s;">
                <i class="<?php echo $is_favorite ? 'fas' : 'far'; ?> fa-heart"></i>
            </button>
        </div>

        <?php if ($is_locked): ?>
            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 160px; background: rgba(0,0,0,0.4); z-index: 2; display: flex; align-items: center; justify-content: center; color: white; font-size: 30px;">
                <i class="fa fa-lock"></i>
            </div>
        <?php
    endif; ?>
        
        <div style="position: relative; height: 160px; overflow: hidden;">
            <img src="../../assets/img/courses/<?php echo $course['thumbnail'] ?: 'default.jpg'; ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; <?php echo $is_locked ? 'filter: grayscale(0.5) blur(2px);' : ''; ?>" onerror="this.src='https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=400&q=80'">
            <?php if ($is_locked): ?>
            <div style="position: absolute; top: 10px; right: 80px; background: #ff8a00; color: white; padding: 4px 10px; border-radius: 4px; font-size: 10px; font-weight: 800; text-transform: uppercase; z-index: 3;">
                Locked
            </div>
            <?php
    endif; ?>
            <div style="position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.7); color: white; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; z-index: 3;">
                <?php echo htmlspecialchars($course['category_name'] ?? 'SkillEdu', ENT_QUOTES); ?>
            </div>
        </div>
        <div style="padding: 20px;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--dark-color); margin-bottom: 10px; line-height: 1.4; min-height: 44px;">
                <?php echo htmlspecialchars($course['title'], ENT_QUOTES); ?>
            </h3>
            <p style="font-size: 12px; color: var(--gray-color); margin-bottom: 15px;">By <?php echo htmlspecialchars($course['instructor_name'], ENT_QUOTES); ?></p>
            
            <?php if ($is_locked): ?>
                <div style="margin-bottom: 20px; padding: 10px; background: var(--bg-page); border: 1px dashed var(--border-color); border-radius: 6px; font-size: 12px; color: var(--primary-color); font-weight: 700; text-align: center;">
                    <i class="fa fa-shopping-cart"></i> Purchase required
                </div>
                <a href="../../checkout.php?id=<?php echo $course['course_id']; ?>" class="btn btn-primary" style="width: 100%; padding: 10px; font-weight: 800; border: none;"><i class="fa fa-lock-open" style="margin-right: 6px;"></i>Unlock for $<?php echo number_format($course['price'], 2); ?></a>
            <?php
    else: ?>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; font-size: 12px; font-weight: 700;">
                    <span color="#1c1d1f"><?php echo $course['progress_percent']; ?>% Complete</span>
                </div>
                <div class="premium-progress" style="margin: 0 0 20px 0;">
                    <div class="premium-progress-bar" style="width: <?php echo $course['progress_percent']; ?>%;"></div>
                </div>
                <a href="player.php?course_id=<?php echo $course_id; ?>" class="btn btn-secondary" style="width: 100%; padding: 10px; font-weight: 700;">View Course</a>
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
        <a href="?page=<?php echo $page - 1; ?>" class="btn btn-secondary" style="padding: 10px 20px; font-weight: 700; border-radius: 8px;">
            <i class="fa fa-chevron-left" style="margin-right: 8px;"></i> Previous
        </a>
    <?php
    else: ?>
        <button class="btn btn-secondary" disabled style="padding: 10px 20px; font-weight: 700; border-radius: 8px; border: 1px solid var(--border-color); opacity: 0.5; cursor: not-allowed;">
            <i class="fa fa-chevron-left" style="margin-right: 8px;"></i> Previous
        </button>
    <?php
    endif; ?>

    <span style="font-weight: 700; color: var(--dark-color); font-size: 15px; padding: 0 10px;">
        Page <?php echo $page; ?> of <?php echo $total_pages; ?>
    </span>

    <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?>" class="btn btn-secondary" style="padding: 10px 20px; font-weight: 700; border-radius: 8px;">
            Next <i class="fa fa-chevron-right" style="margin-left: 8px;"></i>
        </a>
    <?php
    else: ?>
        <button class="btn btn-secondary" disabled style="padding: 10px 20px; font-weight: 700; border-radius: 8px; border: 1px solid var(--border-color); opacity: 0.5; cursor: not-allowed;">
            Next <i class="fa fa-chevron-right" style="margin-left: 8px;"></i>
        </button>
    <?php
    endif; ?>
</div>
<?php
endif; ?>
    
    <!-- Smart Recommendations -->
    <?php if (!empty($recommendations)): ?>
    <div style="margin-top: 50px;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 25px;">
            <i class="fa fa-lightbulb" style="color: #FF416C; font-size: 24px;"></i>
            <h2 style="font-size: 24px; color: var(--dark-color);"><?php echo htmlspecialchars($rec_title); ?></h2>
        </div>
        <div class="courses-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px;">
            <?php foreach ($recommendations as $rec): ?>
            <div class="course-card-v2" style="background: var(--white); border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; transition: transform 0.3s ease;">
                <div style="height: 160px; background: url('../../assets/img/courses/<?php echo $rec['thumbnail'] ?: 'default.jpg'; ?>') center/cover no-repeat;" onerror="this.style.backgroundImage='url(https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=400&q=80)'"></div>
                <div style="padding: 20px;">
                    <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 10px; min-height: 44px; color: var(--dark-color);"><?php echo htmlspecialchars($rec['title'], ENT_QUOTES); ?></h3>
                    <p style="font-size: 12px; color: var(--gray-color); margin-bottom: 15px;"><?php echo htmlspecialchars($rec['instructor_name'], ENT_QUOTES); ?></p>
                    <div style="font-weight: 800; font-size: 18px; margin-bottom: 15px; color: var(--dark-color);">$<?php echo number_format($rec['price'], 2); ?></div>
                    <a href="<?php echo $root; ?>courses.php?id=<?php echo $rec['id']; ?>" class="btn btn-secondary" style="width: 100%; border: 1px solid var(--border-color); color: var(--dark-color);">See Details</a>
                </div>
            </div>
            <?php
    endforeach; ?>
        </div>
    </div>
    <?php
endif; ?>

<?php
function h($str)
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<?php include '../../includes/portal_footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Remove skeleton class once page is loaded
    window.addEventListener('load', () => {
        document.querySelectorAll('.skeleton').forEach(el => {
            el.classList.remove('skeleton');
        });
    });

    // Determine colors based on active theme
    const getChartColors = () => {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        return {
            text: isDark ? '#f7f9fa' : '#6a6f73',
            grid: isDark ? '#3e4143' : '#e4e7ea',
            primary: '#FF416C',
            accent: '#2ecc71',
            background: isDark ? '#2d2f31' : '#e4e7ea'
        };
    };

    let learningChart, goalChart;

    function initCharts() {
        const colors = getChartColors();
        
        Chart.defaults.color = colors.text;
        Chart.defaults.font.family = "'Outfit', sans-serif";

        const ctxLearning = document.getElementById('learningChart');
        if (ctxLearning) {
            learningChart = new Chart(ctxLearning, {
                type: 'bar',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Minutes',
                        data: [45, 30, 0, 60, 25, 90, 15],
                        backgroundColor: colors.primary,
                        borderRadius: 6,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { 
                        y: { 
                            beginAtZero: true,
                            grid: { color: colors.grid, drawBorder: false },
                            border: { display: false }
                        },
                        x: {
                            grid: { display: false },
                            border: { display: false }
                        }
                    }
                }
            });
        }

        const ctxGoal = document.getElementById('goalChart');
        if (ctxGoal) {
            goalChart = new Chart(ctxGoal, {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'Remaining'],
                    datasets: [{
                        data: [1, 1],
                        backgroundColor: [colors.accent, colors.background],
                        borderWidth: 0,
                        cutout: '80%',
                        borderRadius: 20
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                    animation: { animateScale: true }
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        initCharts();
        
        const icon = document.getElementById('theme-icon');
        if (localStorage.getItem('skilledu_theme') === 'dark' && icon) {
            icon.className = 'fas fa-sun';
        }

        // Handle Pin & Favorite Buttons
        document.querySelectorAll('.pin-btn, .fav-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const courseId = btn.getAttribute('data-id');
                const isPin = btn.classList.contains('pin-btn');
                const endpoint = isPin ? 'api/toggle_pin.php' : 'api/toggle_favorite.php';
                
                // Optimistic UI update
                const isActive = btn.classList.contains('active');
                btn.classList.toggle('active');
                btn.style.color = isActive ? 'var(--gray-color)' : 'var(--primary-color)';
                
                if (!isPin) {
                    const iconEl = btn.querySelector('i');
                    iconEl.className = isActive ? 'far fa-heart' : 'fas fa-heart';
                }

                fetch(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ course_id: courseId })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (isPin) {
                            window.location.reload(); // Refresh to reorder pinned courses
                        }
                    } else {
                        console.error('Error toggling:', data.message);
                    }
                })
                .catch(err => console.error('Request failed', err));
            });
        });
    });

    });
</script>
