<?php
require_once '../../config/db.php';
session_start();

$root = "../../";
$page_title = 'My Wishlist';
include '../../includes/portal_header.php';

$user_id = $_SESSION['user_id'];

// Fetch Wishlisted Courses
$stmt = $pdo->prepare("
    SELECT c.*, u.full_name as instructor_name, w.id as wishlist_id
    FROM wishlists w
    JOIN courses c ON w.course_id = c.id
    JOIN users u ON c.instructor_id = u.id
    WHERE w.user_id = ? AND c.status = 'published'
    ORDER BY w.created_at DESC
");
$stmt->execute([$user_id]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="portal-container" style="padding-top: 40px; min-height: 70vh;">
    <h1 style="font-size: 32px; font-weight: 800; color: var(--dark-color); margin-bottom: 20px;">My Wishlist</h1>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px;">
        <?php if (empty($courses)): ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: var(--bg-card); border-radius: 12px; border: 1px dashed var(--border-color);">
                <i class="fa fa-heart-broken" style="font-size: 50px; color: var(--gray-color); margin-bottom: 20px;"></i>
                <h2 style="font-size: 24px; color: var(--dark-color); margin-bottom: 10px;">Your wishlist is empty</h2>
                <p style="color: var(--gray-color); margin-bottom: 20px;">Explore our catalog and find your next great skill step.</p>
                <a href="../../courses.php" class="btn btn-primary" style="padding: 12px 30px; border-radius: 30px;">Browse Courses</a>
            </div>
        <?php
else: ?>
            <?php foreach ($courses as $c): ?>
                <div class="course-card" id="wishlist-item-<?php echo $c['id']; ?>" style="background: var(--bg-card); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow); border: 1px solid var(--border-color); display: flex; flex-direction: column;">
                    <div style="position: relative; height: 160px; background: url('../../assets/img/courses/<?php echo $c['thumbnail'] ?: 'default.jpg'; ?>') center/cover;">
                        <button onclick="removeFromWishlist(<?php echo $c['id']; ?>)" style="position: absolute; top: 10px; right: 10px; background: white; border: none; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #e74c3c; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                            <i class="fa fa-heart"></i>
                        </button>
                    </div>
                    
                    <div style="padding: 20px; flex: 1; display: flex; flex-direction: column;">
                        <h3 style="font-size: 16px; font-weight: 800; color: var(--dark-color); margin-bottom: 8px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php echo htmlspecialchars($c['title']); ?></h3>
                        <p style="font-size: 13px; color: var(--gray-color); margin-bottom: 15px;"><?php echo htmlspecialchars($c['instructor_name']); ?></p>
                        
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: auto;">
                            <div style="font-weight: 800; font-size: 18px; color: var(--dark-color);"><?php echo $c['price'] > 0 ? '$' . number_format($c['price'], 2) : 'Free'; ?></div>
                        </div>
                    </div>
                    
                    <div style="padding: 15px 20px; border-top: 1px solid var(--border-color); background: var(--light-gray);">
                        <a href="../../course_details.php?id=<?php echo $c['id']; ?>" class="btn btn-primary" style="width: 100%; text-align: center; padding: 10px; border-radius: 6px;">Enroll Now</a>
                    </div>
                </div>
            <?php
    endforeach; ?>
        <?php
endif; ?>
    </div>
</div>

<script>
function removeFromWishlist(courseId) {
    fetch('../../api/wishlist_api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ action: 'toggle', course_id: courseId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'removed') {
            const el = document.getElementById('wishlist-item-' + courseId);
            if (el) {
                el.style.transition = 'all 0.3s ease';
                el.style.opacity = '0';
                el.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    el.remove();
                    // If no items left, we should reload to show the empty state easily
                    if(document.querySelectorAll('.course-card').length === 0) {
                        location.reload();
                    }
                }, 300);
            }
        }
    })
    .catch(err => console.error('Error:', err));
}
</script>

<?php include '../../includes/portal_footer.php'; ?>
