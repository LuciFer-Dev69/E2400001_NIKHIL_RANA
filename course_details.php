<?php
require_once 'config/db.php';
session_start();

$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$course_id) {
    header("Location: courses.php");
    exit();
}

// Fetch Course Data
$stmt = $pdo->prepare("
    SELECT c.*, u.full_name as instructor_name, cat.name as category_name
    FROM courses c
    JOIN users u ON c.instructor_id = u.id
    LEFT JOIN categories cat ON c.category_id = cat.id
    WHERE c.id = ?
");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

if (!$course) {
    die("Course not found.");
}

// Fetch Lessons 
$stmt = $pdo->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY order_num ASC");
$stmt->execute([$course_id]);
$lessons = $stmt->fetchAll();

$total_lessons = count($lessons);
$total_mins = array_sum(array_column($lessons, 'duration_mins'));
$total_hours = round($total_mins / 60, 1);

// Check if Already Enrolled
$is_enrolled = false;
$is_purchased = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT is_purchased FROM enrollments WHERE student_id = ? AND course_id = ?");
    $stmt->execute([$_SESSION['user_id'], $course_id]);
    $enrollment = $stmt->fetch();
    if ($enrollment) {
        $is_enrolled = true;
        $is_purchased = (bool)$enrollment['is_purchased'];
    }
}

$page_title = $course['title'];
include 'includes/header.php';
?>

<!-- Dark Hero Section -->
<div style="background: var(--dark-color); color: white; padding: 60px 0;">
    <div class="container" style="display: flex; gap: 40px; align-items: flex-start;">
        <div style="flex: 2;">
            <div style="margin-bottom: 15px; font-size: 14px; color: #a435f0; font-weight: 700;">
                <a href="courses.php?category=<?php echo urlencode($course['category_name']); ?>" style="color: inherit; text-decoration: none;"><i class="fa fa-folder-open"></i> <?php echo htmlspecialchars($course['category_name']); ?></a>
            </div>
            <h1 style="font-size: 36px; font-weight: 800; margin-bottom: 15px; line-height: 1.2;"><?php echo htmlspecialchars($course['title']); ?></h1>
            <p style="font-size: 18px; line-height: 1.6; margin-bottom: 20px; color: #d1d7dc;"><?php echo htmlspecialchars(substr($course['description'], 0, 150)) . '...'; ?></p>
            
            <div style="display: flex; gap: 20px; align-items: center; font-size: 14px; margin-bottom: 20px;">
                <div style="color: #f1c40f; font-weight: 800;"><i class="fa fa-star"></i> 4.8 <span style="font-weight: 400; color: #d1d7dc;">(1,234 ratings)</span></div>
                <div><i class="fa fa-users"></i> 15,200 students</div>
                <div><i class="fa fa-language"></i> English</div>
            </div>
            
            <div style="font-size: 14px; color: #d1d7dc;">
                Created by <a href="#" style="color: #a435f0; text-decoration: underline;"><?php echo htmlspecialchars($course['instructor_name']); ?></a>
            </div>
        </div>
        
        <!-- Sticky Sidebar Card -->
        <div style="flex: 1; background: var(--bg-card); color: var(--dark-color); border-radius: 8px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2); position: sticky; top: 20px;">
            <div style="height: 200px; background: url('assets/img/courses/<?php echo $course['thumbnail'] ?: 'default.jpg'; ?>') center/cover;" onerror="this.style.backgroundImage='url(https://via.placeholder.com/400x200)'">
                <div style="width: 100%; height: 100%; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; cursor: pointer;">
                    <i class="fa fa-play-circle" style="font-size: 60px; color: white; opacity: 0.9;"></i>
                </div>
            </div>
            <div style="padding: 24px;">
                <h2 style="font-size: 32px; font-weight: 800; margin-bottom: 20px;">
                    <?php echo $course['price'] > 0 ? '$' . number_format($course['price'], 2) : 'Free'; ?>
                </h2>
                
                <?php if ($is_enrolled): ?>
                    <a href="portals/student/player.php?course_id=<?php echo $course['id']; ?>" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 16px; margin-bottom: 15px; text-align: center;"><i class="fa fa-play"></i> Go to Course Player</a>
                <?php
else: ?>
                    <a href="checkout.php?id=<?php echo $course['id']; ?>" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 16px; margin-bottom: 15px; text-align: center;">
                        <?php echo $course['price'] > 0 ? 'Buy Now' : 'Enroll for Free'; ?>
                    </a>
                <?php
endif; ?>
                
                <p style="text-align: center; font-size: 12px; color: var(--gray-color); margin-bottom: 20px;">30-Day Money-Back Guarantee</p>
                
                <h4 style="font-size: 16px; font-weight: 800; margin-bottom: 10px;">This course includes:</h4>
                <ul style="list-style: none; padding: 0; font-size: 14px; color: var(--gray-color);">
                    <li style="margin-bottom: 10px;"><i class="fa fa-video" style="width: 20px; text-align: center;"></i> <?php echo $total_hours; ?> hours on-demand video</li>
                    <li style="margin-bottom: 10px;"><i class="fa fa-file-alt" style="width: 20px; text-align: center;"></i> 5 articles</li>
                    <li style="margin-bottom: 10px;"><i class="fa fa-mobile-alt" style="width: 20px; text-align: center;"></i> Access on mobile and TV</li>
                    <li style="margin-bottom: 10px;"><i class="fa fa-certificate" style="width: 20px; text-align: center;"></i> Certificate of completion</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="container" style="padding: 40px 0; display: flex; gap: 60px;">
    <!-- Main Content -->
    <div style="flex: 2;">
        
        <!-- What you'll learn box -->
        <div style="border: 1px solid var(--border-color); padding: 24px; border-radius: 8px; margin-bottom: 40px; background: var(--bg-card);">
            <h2 style="font-size: 20px; font-weight: 800; margin-bottom: 20px;">What you'll learn</h2>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 14px; line-height: 1.5;">
                <div><i class="fa fa-check" style="color: var(--primary-color); margin-right: 10px;"></i>Master the core concepts of the subject from scratch.</div>
                <div><i class="fa fa-check" style="color: var(--primary-color); margin-right: 10px;"></i>Build real-world projects to add to your portfolio.</div>
                <div><i class="fa fa-check" style="color: var(--primary-color); margin-right: 10px;"></i>Learn industry best practices directly from an expert.</div>
                <div><i class="fa fa-check" style="color: var(--primary-color); margin-right: 10px;"></i>Gain the confidence to ace interviews and pass exams.</div>
            </div>
        </div>

        <!-- Description -->
        <div style="margin-bottom: 40px;">
            <h2 style="font-size: 24px; font-weight: 800; margin-bottom: 20px;">Description</h2>
            <div style="font-size: 15px; line-height: 1.8; color: var(--dark-color);">
                <?php echo nl2br(htmlspecialchars($course['description'])); ?>
            </div>
        </div>

        <!-- Curriculum -->
        <div style="margin-bottom: 40px;">
            <h2 style="font-size: 24px; font-weight: 800; margin-bottom: 5px;">Course Content</h2>
            <p style="color: var(--gray-color); font-size: 14px; margin-bottom: 20px;"><?php echo $total_lessons; ?> lectures • <?php echo $total_hours; ?> total length</p>
            
            <div style="border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; background: var(--bg-card);">
                <?php if (empty($lessons)): ?>
                    <div style="padding: 20px; color: var(--gray-color); text-align: center;">Curriculum is being prepared. Check back soon!</div>
                <?php
else: ?>
                    <?php foreach ($lessons as $idx => $lesson): ?>
                        <div style="padding: 15px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: <?php echo $idx % 2 === 0 ? 'transparent' : 'var(--light-gray)'; ?>;">
                            <div style="display: flex; align-items: center; gap: 15px; font-size: 15px;">
                                <i class="fa fa-play-circle" style="color: var(--gray-color);"></i>
                                <span><?php echo htmlspecialchars($lesson['title']); ?></span>
                            </div>
                            <div style="font-size: 14px; color: var(--gray-color);">
                                <?php echo $lesson['duration_mins']; ?>:00 
                                <?php if ($idx < 2 && !$is_enrolled): ?>
                                    <span style="color: #a435f0; font-weight: 700; margin-left: 10px; cursor: pointer;">Preview</span>
                                <?php
        endif; ?>
                            </div>
                        </div>
                    <?php
    endforeach; ?>
                <?php
endif; ?>
            </div>
        </div>
        
    </div>
    
    <!-- Empty flex slot to offset sticky sidebar width -->
    <div style="flex: 1;"></div>
</div>

<?php include 'includes/footer.php'; ?>
