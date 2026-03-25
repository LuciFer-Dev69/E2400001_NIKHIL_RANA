<?php
require_once '../../config/db.php';
session_start();

$root = "../../";
$page_title = 'My Students';
include '../../includes/instructor/instructor_header.php';

$inst_id = $_SESSION['user_id'];

// Fetch all students enrolled in this instructor's courses
$sql = "
    SELECT 
        u.id, u.full_name, u.email, u.profile_img,
        c.title as course_title, c.id as course_id,
        e.enrolled_at, e.progress_percent
    FROM enrollments e
    JOIN users u ON e.student_id = u.id
    JOIN courses c ON e.course_id = c.id
    WHERE c.instructor_id = ?
    ORDER BY e.enrolled_at DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$inst_id]);
$enrollments = $stmt->fetchAll();

// Group by student
$students = [];
foreach ($enrollments as $row) {
    $sid = $row['id'];
    if (!isset($students[$sid])) {
        $students[$sid] = [
            'id' => $sid,
            'full_name' => $row['full_name'],
            'email' => $row['email'],
            'courses' => []
        ];
    }
    $students[$sid]['courses'][] = [
        'title' => $row['course_title'],
        'id' => $row['course_id'],
        'enrolled_at' => $row['enrolled_at'],
        'progress' => $row['progress_percent']
    ];
}
?>

<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px;">
    <div>
        <h1 style="font-size: 28px; font-weight: 800; color: var(--dark-color); margin-bottom: 8px;">My Students</h1>
        <p style="color: var(--gray-color);"><?php echo count($students); ?> unique learner<?php echo count($students) !== 1 ? 's' : ''; ?> enrolled across your courses.</p>
    </div>
</div>

<?php if (empty($students)): ?>
<div style="background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color); padding: 60px; text-align: center;">
    <i class="fa fa-user-graduate" style="font-size: 50px; color: var(--gray-color); opacity: 0.4; margin-bottom: 20px;"></i>
    <h3 style="font-weight: 800; color: var(--dark-color); margin-bottom: 10px;">No enrollments yet</h3>
    <p style="color: var(--gray-color);">Once students enroll in your courses, they will appear here.</p>
</div>
<?php
else: ?>

<div style="background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow); overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color); background: var(--light-gray);">
                <th style="padding: 14px 20px; text-align: left; font-size: 12px; font-weight: 800; color: var(--gray-color); text-transform: uppercase; letter-spacing: 1px;">Student</th>
                <th style="padding: 14px 20px; text-align: left; font-size: 12px; font-weight: 800; color: var(--gray-color); text-transform: uppercase; letter-spacing: 1px;">Enrolled Course(s)</th>
                <th style="padding: 14px 20px; text-align: center; font-size: 12px; font-weight: 800; color: var(--gray-color); text-transform: uppercase; letter-spacing: 1px;">Total Courses</th>
                <th style="padding: 14px 20px; text-align: center; font-size: 12px; font-weight: 800; color: var(--gray-color); text-transform: uppercase; letter-spacing: 1px;">Avg Progress</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($students as $student):
        $total_courses = count($student['courses']);
        $avg_progress = round(array_sum(array_column($student['courses'], 'progress')) / max(1, $total_courses));
        $initials = '';
        foreach (explode(' ', $student['full_name']) as $p) {
            if (!empty($p))
                $initials .= strtoupper($p[0]);
        }
        $initials = substr($initials, 0, 2);
?>
        <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s;" onmouseover="this.style.background='var(--light-gray)'" onmouseout="this.style.background='transparent'">
            <!-- Student -->
            <td style="padding: 15px 20px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div class="user-avatar" style="width: 38px; height: 38px; font-size: 14px; flex-shrink: 0;"><?php echo $initials; ?></div>
                    <div>
                        <div style="font-weight: 800; color: var(--dark-color); font-size: 14px;"><?php echo htmlspecialchars($student['full_name']); ?></div>
                        <div style="font-size: 12px; color: var(--gray-color);"><?php echo htmlspecialchars($student['email']); ?></div>
                    </div>
                </div>
            </td>
            <!-- Courses -->
            <td style="padding: 15px 20px; max-width: 300px;">
                <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                    <?php foreach ($student['courses'] as $c): ?>
                    <span style="background: rgba(155,89,182,0.1); color: #9b59b6; padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;" title="<?php echo htmlspecialchars($c['title']); ?>"><?php echo htmlspecialchars($c['title']); ?></span>
                    <?php
        endforeach; ?>
                </div>
            </td>
            <!-- Count -->
            <td style="padding: 15px 20px; text-align: center;">
                <span style="font-weight: 800; font-size: 18px; color: var(--dark-color);"><?php echo $total_courses; ?></span>
            </td>
            <!-- Progress -->
            <td style="padding: 15px 20px; text-align: center;">
                <div style="display: flex; flex-direction: column; align-items: center; gap: 5px;">
                    <div style="width: 70px; height: 6px; background: var(--border-color); border-radius: 3px; overflow: hidden;">
                        <div style="width: <?php echo $avg_progress; ?>%; height: 100%; background: <?php echo $avg_progress >= 80 ? '#2ecc71' : ($avg_progress >= 40 ? '#f1c40f' : '#e74c3c'); ?>; border-radius: 3px;"></div>
                    </div>
                    <span style="font-size: 12px; font-weight: 800; color: var(--dark-color);"><?php echo $avg_progress; ?>%</span>
                </div>
            </td>
        </tr>
        <?php
    endforeach; ?>
        </tbody>
    </table>
</div>

<?php
endif; ?>

<?php include '../../includes/instructor/instructor_footer.php'; ?>
