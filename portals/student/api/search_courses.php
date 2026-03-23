<?php
require_once '../../../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? 'all';

try {
    $sql = "
        SELECT c.*, u.full_name as instructor_name, e.progress_percent
        FROM enrollments e
        JOIN courses c ON e.course_id = c.id
        JOIN users u ON c.instructor_id = u.id
        WHERE e.student_id = ?
    ";

    $params = [$user_id];

    if ($filter === 'free')
        $sql .= " AND c.price = 0";
    if ($filter === 'locked')
        $sql .= " AND c.price > 0";
    if (!empty($search)) {
        $sql .= " AND c.title LIKE ?";
        $params[] = "%$search%";
    }

    $sql .= " ORDER BY e.enrolled_at DESC LIMIT 10"; // Match current pagination limit for now

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return HTML fragment for the grid
    ob_start();
    if (empty($courses)) {
?>
        <div style="grid-column: 1/-1; text-align: center; padding: 80px; background: #f7f9fa; border-radius: 12px; border: 1px dashed #d1d7dc;">
            <i class="fa fa-search" style="font-size: 48px; color: #d1d7dc; margin-bottom: 20px; display: block;"></i>
            <h3 style="font-weight: 700; color: #1c1d1f; margin-bottom: 10px;">We couldn't find any courses</h3>
            <p style="color: #6a6f73; margin-bottom: 25px;">Try adjusting your filters or search terms.</p>
        </div>
        <?php
    }
    else {
        foreach ($courses as $course) {
            $is_locked = ($course['price'] > 0);
?>
            <div class="course-card-premium <?php echo $is_locked ? 'locked' : ''; ?>" style="position: relative; animation: fadeIn 0.5s ease-out;">
                <?php if ($is_locked): ?>
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 160px; background: rgba(0,0,0,0.4); z-index: 2; display: flex; align-items: center; justify-content: center; color: white; font-size: 30px; border-radius: 8px 8px 0 0;">
                        <i class="fa fa-lock"></i>
                    </div>
                <?php
            endif; ?>
                
                <div style="position: relative; height: 160px; overflow: hidden; border-radius: 8px 8px 0 0;">
                    <img src="../../assets/img/courses/<?php echo $course['thumbnail'] ?: 'default.jpg'; ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; <?php echo $is_locked ? 'filter: grayscale(0.5) blur(2px);' : ''; ?>" onerror="this.src='https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=400&q=80'">
                    <div style="position: absolute; top: 12px; right: 12px; background: <?php echo $is_locked ? '#ff8a00' : '#2ecc71'; ?>; color: white; padding: 5px 12px; border-radius: 4px; font-size: 10px; font-weight: 800; text-transform: uppercase; z-index: 3; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <?php echo $is_locked ? 'Locked' : 'Free'; ?>
                    </div>
                </div>
                <div style="padding: 20px;">
                    <h3 style="font-size: 16px; font-weight: 800; color: #1c1d1f; margin-bottom: 8px; line-height: 1.4; min-height: 44px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                        <?php echo htmlspecialchars($course['title']); ?>
                    </h3>
                    <p style="font-size: 12px; color: #6a6f73; margin-bottom: 15px; font-weight: 600;">By <?php echo htmlspecialchars($course['instructor_name']); ?></p>
                    
                    <?php if ($is_locked): ?>
                        <div style="margin-bottom: 20px; padding: 12px; background: #fffcf0; border: 1px solid #feeac1; border-radius: 8px; font-size: 12px; color: #b4690e; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                            <i class="fa fa-shopping-cart"></i> Single purchase required
                        </div>
                        <a href="#" class="btn btn-primary" style="width: 100%; padding: 12px; font-weight: 800; background: #1c1d1f; color: white; border: none; border-radius: 8px;">Unlock for $<?php echo number_format($course['price'], 2); ?></a>
                    <?php
            else: ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; font-size: 12px; font-weight: 700; color: #1c1d1f;">
                            <span><?php echo $course['progress_percent']; ?>% Complete</span>
                        </div>
                        <div class="premium-progress" style="margin: 0 0 20px 0; height: 6px; background: #d1d7dc;">
                            <div class="premium-progress-bar" style="width: <?php echo $course['progress_percent']; ?>%; height: 100%;"></div>
                        </div>
                        <a href="player.php?course_id=<?php echo $course['id']; ?>" class="btn btn-secondary" style="width: 100%; padding: 12px; font-weight: 800; border-radius: 8px;">View Course</a>
                    <?php
            endif; ?>
                </div>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();
    echo json_encode(['html' => $html, 'count' => count($courses)]);

}
catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
