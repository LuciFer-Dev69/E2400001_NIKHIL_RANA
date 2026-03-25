<div class="tab-content-container">
    <?php
require_once __DIR__ . '/../config/db.php';

// Get top 6 categories that have courses
$cat_stmt = $pdo->prepare("SELECT * FROM categories LIMIT 6");
$cat_stmt->execute();
$categories = $cat_stmt->fetchAll();

$is_first = true;
foreach ($categories as $cat):
    // Fetch top 4 courses for this category
    $course_stmt = $pdo->prepare("
            SELECT c.*, u.full_name as instructor_name 
            FROM courses c 
            JOIN users u ON c.instructor_id = u.id 
            WHERE c.category_id = ? AND c.status = 'published' 
            LIMIT 4
        ");
    $course_stmt->execute([$cat['id']]);
    $courses = $course_stmt->fetchAll();
?>
    <div id="tab-<?php echo htmlspecialchars($cat['slug']); ?>" class="tab-pane" style="display: <?php echo $is_first ? 'block' : 'none'; ?>;">
        <div class="course-grid-row" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px;">
            <?php if (empty($courses)): ?>
                <div style="grid-column: 1/-1; padding: 40px; text-align: center; background: #f7f9fa; border-radius: 8px; color: #6a6f73;">
                    <i class="fa fa-info-circle"></i> No courses available in this category yet.
                </div>
            <?php
    else:
        foreach ($courses as $c): ?>
                <div class="course-card-v2" style="display: flex; flex-direction: column; height: 100%;">
                    <a href="course_details.php?id=<?php echo $c['id']; ?>" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; flex: 1;">
                        <div class="thumb" style="background: url('assets/img/courses/<?php echo htmlspecialchars($c['thumbnail'] ?: 'default.jpg'); ?>') center/cover no-repeat; height: 145px; border-radius: 4px; border: 1px solid rgba(0,0,0,0.05);" onerror="this.style.backgroundImage='url(https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&q=80&w=400)'"></div>
                        <h4 style="font-size: 16px; font-weight: 800; margin: 10px 0 5px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 44px;"><?php echo htmlspecialchars($c['title']); ?></h4>
                        <div class="instructor" style="font-size: 12px; color: #6a6f73; margin-bottom: 5px;"><?php echo htmlspecialchars($c['instructor_name']); ?></div>
                        <div class="rating" style="display: flex; align-items: center; gap: 4px; font-weight: 700; font-size: 14px; color: #b4690e;">
                            4.8 <span style="font-weight: 400; color: #6a6f73; font-size: 12px;">(<?php echo rand(100, 5000); ?> ratings)</span>
                        </div>
                        <div class="price-row" style="font-weight: 800; font-size: 16px; margin: 8px 0;">
                            <?php if ($c['price'] > 0): ?>
                                $<?php echo number_format($c['price'], 2); ?> <span class="price-old" style="text-decoration: line-through; color: #6a6f73; font-size: 13px; font-weight: 400; margin-left: 5px;">$<?php echo number_format($c['price'] + 20, 2); ?></span>
                            <?php
            else: ?>
                                <span style="color: #2ecc71;">FREE</span>
                            <?php
            endif; ?>
                        </div>
                    </a>
                    <a href="checkout.php?id=<?php echo $c['id']; ?>" class="btn btn-primary" style="padding: 10px; font-size: 13px; font-weight: 800; text-align: center; border-radius: 4px; margin-top: auto; <?php echo $c['price'] == 0 ? 'background: #2ecc71; border-color: #2ecc71;' : ''; ?>">
                        <?php echo $c['price'] > 0 ? 'Buy Now' : 'Enroll Free'; ?>
                    </a>
                </div>
            <?php
        endforeach;
    endif; ?>
        </div>
        <a href="courses.php?category=<?php echo urlencode($cat['slug']); ?>" style="display: inline-block; margin-top: 20px; color: var(--primary-color); font-weight: 700; text-decoration: none;">Show all <?php echo htmlspecialchars($cat['name']); ?> courses <i class="fa fa-chevron-right" style="font-size: 10px;"></i></a>
    </div>
    <?php
    $is_first = false;
endforeach;
?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabItems = document.querySelectorAll('.skills-nav-section .tab-item');
        const tabPanes = document.querySelectorAll('.skills-nav-section .tab-pane');

        tabItems.forEach(tab => {
            tab.addEventListener('click', () => {
                tabItems.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                tabPanes.forEach(pane => pane.style.display = 'none');
                
                const category = tab.getAttribute('data-category');
                const targetPane = document.getElementById('tab-' + category);
                if (targetPane) {
                    targetPane.style.display = 'block';
                }
            });
        });
    });
</script>
