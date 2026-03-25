<?php
require_once '../../config/db.php';
session_start();

$root = "../../";
$page_title = 'My Courses';
include '../../includes/instructor/instructor_header.php';

$inst_id = $_SESSION['user_id'];

// Fetch all courses for this instructor with enrollment counts
$stmt = $pdo->prepare("
    SELECT c.*, cat.name as category_name, COUNT(e.id) as enrollments
    FROM courses c
    LEFT JOIN categories cat ON c.category_id = cat.id
    LEFT JOIN enrollments e ON e.course_id = c.id
    WHERE c.instructor_id = ?
    GROUP BY c.id
    ORDER BY c.created_at DESC
");
$stmt->execute([$inst_id]);
$courses = $stmt->fetchAll();

$status_colors = [
    'draft' => ['bg' => 'rgba(149,165,166,0.15)', 'text' => '#95a5a6', 'label' => 'Draft'],
    'pending' => ['bg' => 'rgba(241,196,15,0.15)', 'text' => '#f1c40f', 'label' => 'Pending Review'],
    'published' => ['bg' => 'rgba(46,204,113,0.15)', 'text' => '#2ecc71', 'label' => 'Published'],
    'archived' => ['bg' => 'rgba(231,76,60,0.15)', 'text' => '#e74c3c', 'label' => 'Archived'],
];
?>

<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px;">
    <div>
        <h1 style="font-size: 28px; font-weight: 800; color: var(--dark-color); margin-bottom: 8px;">My Courses</h1>
        <p style="color: var(--gray-color);">Manage your course catalog and track student enrollments.</p>
    </div>
    <a href="create_course.php" class="btn btn-primary" style="padding: 10px 20px; font-weight: 700; background: #9b59b6; border-color: #9b59b6;">
        <i class="fa fa-plus" style="margin-right: 8px;"></i> Create Course
    </a>
</div>

<?php if (empty($courses)): ?>
<div style="background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color); padding: 60px; text-align: center;">
    <i class="fa fa-video-slash" style="font-size: 50px; color: var(--gray-color); opacity: 0.4; margin-bottom: 20px;"></i>
    <h3 style="font-weight: 800; color: var(--dark-color); margin-bottom: 10px;">No courses yet!</h3>
    <p style="color: var(--gray-color); margin-bottom: 25px;">Start creating your first course and share your knowledge with the world.</p>
    <a href="create_course.php" class="btn btn-primary" style="background: #9b59b6; border-color: #9b59b6;">Create Your First Course</a>
</div>
<?php
else: ?>
<div style="display: grid; gap: 15px;">
    <?php foreach ($courses as $course):
        $sc = $status_colors[$course['status']] ?? $status_colors['draft'];
?>
    <div style="background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color); padding: 22px; display: flex; gap: 20px; align-items: center; box-shadow: var(--shadow); transition: all 0.2s;" onmouseover="this.style.transform='translateX(4px)'" onmouseout="this.style.transform='translateX(0)'">
        <!-- Thumbnail -->
        <div style="width: 90px; height: 65px; border-radius: 8px; overflow: hidden; flex-shrink: 0; background: var(--light-gray);">
            <?php
        $thumb = $course['thumbnail'] ?? 'course_default.png';
        $thumb_path = file_exists($root . 'assets/images/thumbnails/' . $thumb) ? $root . 'assets/images/thumbnails/' . $thumb : $root . 'assets/images/' . $thumb;
?>
            <img src="<?php echo $thumb_path; ?>" alt="Thumbnail" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <!-- Info -->
        <div style="flex: 1; min-width: 0;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
                <span style="background: <?php echo $sc['bg']; ?>; color: <?php echo $sc['text']; ?>; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase;"><?php echo $sc['label']; ?></span>
                <?php if ($course['status'] === 'pending'): ?>
                <span style="font-size: 11px; color: var(--gray-color);"><i class="fa fa-clock"></i> Awaiting Admin Approval</span>
                <?php
        endif; ?>
            </div>
            <h3 style="font-weight: 800; color: var(--dark-color); margin: 0 0 5px 0; font-size: 16px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($course['title']); ?></h3>
            <div style="font-size: 13px; color: var(--gray-color); display: flex; gap: 15px;">
                <span><i class="fa fa-tag"></i> <?php echo htmlspecialchars($course['category_name'] ?? 'Uncategorized'); ?></span>
                <span><i class="fa fa-users"></i> <?php echo $course['enrollments']; ?> students</span>
                <span><i class="fa fa-signal"></i> <?php echo ucfirst($course['difficulty_level'] ?? 'beginner'); ?></span>
                <span><i class="fa fa-dollar-sign"></i> <?php echo($course['price'] > 0 ? '$' . number_format($course['price'], 2) : 'Free'); ?></span>
            </div>
        </div>
        <!-- Actions -->
        <div style="display: flex; gap: 10px; flex-shrink: 0;">
            <?php if ($course['status'] === 'draft'): ?>
            <a href="edit_course.php?id=<?php echo $course['id']; ?>" class="btn" style="padding: 8px 15px; border: 1px solid var(--border-color); color: var(--dark-color); font-size: 13px;"><i class="fa fa-edit"></i> Edit</a>
            <?php
        endif; ?>
            <a href="../../course_details.php?id=<?php echo $course['id']; ?>" target="_blank" class="btn" style="padding: 8px 15px; border: 1px solid var(--border-color); color: var(--dark-color); font-size: 13px;"><i class="fa fa-eye"></i> Preview</a>
        </div>
    </div>
    <?php
    endforeach; ?>
</div>
<?php
endif; ?>

<?php include '../../includes/instructor/instructor_footer.php'; ?>
