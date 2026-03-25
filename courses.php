<?php
require_once 'config/db.php';
session_start();

$base_url = isset($base_url) ? $base_url : "";
$category_slug = $_GET['category'] ?? 'all';
$search_query = $_GET['search'] ?? '';



// Fetch Category Name if applicable
$page_title = 'All Courses';
if ($category_slug !== 'all') {
    $stmt = $pdo->prepare("SELECT name FROM categories WHERE slug = ?");
    $stmt->execute([$category_slug]);
    $cat_name = $stmt->fetchColumn();
    if ($cat_name)
        $page_title = $cat_name;
}
if (!empty($search_query)) {
    $page_title = "Results for \"" . htmlspecialchars($search_query) . "\"";
}

// Pagination logic
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1)
    $page = 1;
$offset = ($page - 1) * $limit;

// Count Total Records for Pagination
$count_sql = "SELECT COUNT(*) FROM courses c WHERE c.status = 'published'";
$count_params = [];
if (!empty($search_query)) {
    $count_sql .= " AND (c.title LIKE ? OR c.description LIKE ?)";
    $count_params[] = '%' . $search_query . '%';
    $count_params[] = '%' . $search_query . '%';
}
if ($category_slug !== 'all') {
    $count_sql .= " AND c.category_id = (SELECT id FROM categories WHERE slug = ?)";
    $count_params[] = $category_slug;
}
$stmt = $pdo->prepare($count_sql);
$stmt->execute($count_params);
$total_records = $stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Build Dynamic Query
$params = [];
$sql = "SELECT c.*, u.full_name as instructor_name, 
        (SELECT COUNT(*) FROM lessons l WHERE l.course_id = c.id) as lesson_count,
        COALESCE((SELECT SUM(duration_mins) FROM lessons l WHERE l.course_id = c.id), 0) as total_duration
        FROM courses c 
        JOIN users u ON c.instructor_id = u.id WHERE 1=1 AND c.status = 'published'";

if (!empty($search_query)) {
    $sql .= " AND (c.title LIKE ? OR c.description LIKE ?)";
    $params[] = '%' . $search_query . '%';
    $params[] = '%' . $search_query . '%';
}

if ($category_slug !== 'all') {
    $sql .= " AND c.category_id = (SELECT id FROM categories WHERE slug = ?)";
    $params[] = $category_slug;
}

$stmt = $pdo->prepare($sql . " LIMIT $limit OFFSET $offset");
$stmt->execute($params);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="container" style="padding: 40px 0;">
    <div style="margin-bottom: 40px;">
        <h1 style="font-size: 32px; margin-bottom: 10px;"><?php echo $page_title; ?></h1>
        <p style="color: var(--gray-color); font-size: 16px;">Explore the best courses in <?php echo strtolower($page_title); ?>.</p>
    </div>

    <div style="display: flex; gap: 40px;">
        <!-- Sidebar Filters -->
        <aside style="width: 260px; flex-shrink: 0;" class="hide-mobile">
            <div class="filter-section">
                <h4 style="margin-bottom: 15px; font-size: 16px;">Ratings</h4>
                <div class="filter-item"><input type="checkbox"> 4.5 & up <span style="color: var(--gray-color); font-size: 12px; margin-left: 5px;">(1,248)</span></div>
                <div class="filter-item"><input type="checkbox"> 4.0 & up <span style="color: var(--gray-color); font-size: 12px; margin-left: 5px;">(2,500)</span></div>
                <div class="filter-item"><input type="checkbox"> 3.5 & up <span style="color: var(--gray-color); font-size: 12px; margin-left: 5px;">(450)</span></div>
            </div>

            <div class="filter-section" style="margin-top: 30px; border-top: 1px solid var(--border-color); padding-top: 20px;">
                <h4 style="margin-bottom: 15px; font-size: 16px;">Video Duration</h4>
                <div class="filter-item"><input type="checkbox"> 0-1 Hours</div>
                <div class="filter-item"><input type="checkbox"> 1-3 Hours</div>
                <div class="filter-item"><input type="checkbox"> 3-6 Hours</div>
                <div class="filter-item"><input type="checkbox"> 6+ Hours</div>
            </div>

            <div class="filter-section" style="margin-top: 30px; border-top: 1px solid var(--border-color); padding-top: 20px;">
                <h4 style="margin-bottom: 15px; font-size: 16px;">Level</h4>
                <div class="filter-item"><input type="checkbox"> Beginner</div>
                <div class="filter-item"><input type="checkbox"> Intermediate</div>
                <div class="filter-item"><input type="checkbox"> Expert</div>
            </div>
        </aside>

        <!-- Course List -->
        <div style="flex: 1;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <button class="btn btn-secondary" style="padding: 10px 20px; font-size: 14px;"><i class="fa fa-filter"></i> Filter</button>
                <div style="font-weight: 700; font-size: 14px;"><?php echo count($courses); ?> results</div>
            </div>

            <div class="course-list-stack">
                <?php if (empty($courses)): ?>
                    <div style="text-align: center; padding: 50px; background: #f7f9fa; border-radius: 8px;">
                        <i class="fa fa-search" style="font-size: 40px; color: #d1d7dc; margin-bottom: 15px;"></i>
                        <h3 style="font-size: 20px; margin-bottom: 10px;">No courses found</h3>
                        <p style="color: #6a6f73;">Try adjusting your search or filters to find what you're looking for.</p>
                    </div>
                <?php
else: ?>
                    <?php foreach ($courses as $c): ?>
                    <div class="course-horizontal-card" style="display: flex; cursor: default; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; margin-bottom: 20px; transition: transform 0.2s, box-shadow 0.2s;">
                        <a href="<?php echo $base_url; ?>course_details.php?id=<?php echo $c['id']; ?>" style="display: flex; flex: 1; text-decoration: none; color: inherit;">
                            <div class="card-thumb" style="width: 260px; flex-shrink: 0; background: url('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&q=80&w=400') center/cover;" onerror="this.style.backgroundImage='url(https://via.placeholder.com/260x145)'">
                            </div>
                            <div class="card-info" style="flex: 1; padding: 15px 20px; display: flex; flex-direction: column;">
                                <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 5px;"><?php echo htmlspecialchars($c['title']); ?></h3>
                                <p class="desc" style="font-size: 14px; color: #4d5156; margin-bottom: 5px; line-height: 1.4;"><?php echo htmlspecialchars(substr($c['description'], 0, 120)) . '...'; ?></p>
                                <p class="instructor" style="font-size: 12px; color: #6a6f73; margin-bottom: 5px; font-weight: 600;">By <?php echo htmlspecialchars($c['instructor_name']); ?></p>
                                <div class="rating" style="display: flex; align-items: center; gap: 5px;">
                                    <span style="font-weight: 700; color: #b4690e;">4.8</span>
                                    <i class="fa fa-star" style="color: #b4690e; font-size: 12px;"></i>
                                    <span style="color: #6a6f73; font-size: 13px;">(2,105 ratings)</span>
                                </div>
                                <div style="font-size: 12px; color: #6a6f73; margin-top: auto;">
                                    <?php echo round($c['total_duration'] / 60, 1); ?> total hours &bull; <?php echo $c['lesson_count']; ?> lectures &bull; All Levels
                                </div>
                            </div>
                        </a>
                        <div class="card-price" style="width: 160px; text-align: right; padding: 15px 20px; display: flex; flex-direction: column; justify-content: center; align-items: flex-end; gap: 10px; flex-shrink: 0; border-left: 1px solid var(--border-color); background: var(--light-gray);">
                            <div style="font-weight: 800; font-size: 20px; color: var(--dark-color);"><?php echo $c['price'] > 0 ? '$' . number_format($c['price'], 2) : 'Free'; ?></div>
                            <a href="<?php echo $base_url; ?>checkout.php?id=<?php echo $c['id']; ?>" 
                               class="btn btn-primary" 
                               style="padding: 10px 15px; font-size: 13px; font-weight: 800; white-space: nowrap; width: 100%; text-align: center; <?php echo $c['price'] == 0 ? 'background: #2ecc71; border-color: #2ecc71;' : ''; ?>">
                                <?php echo $c['price'] > 0 ? 'Buy Now' : 'Enroll Free'; ?>
                            </a>
                        </div>
                    </div>
                    <?php
    endforeach; ?>
                <?php
endif; ?>
            </div>

            <!-- Pagination Controls -->
            <?php if ($total_pages > 1): ?>
            <div style="display: flex; justify-content: center; align-items: center; gap: 15px; margin-top: 40px; margin-bottom: 20px;">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&category=<?php echo urlencode($category_slug); ?>&search=<?php echo urlencode($search_query); ?>" class="btn btn-secondary" style="padding: 10px 20px; font-weight: 700; border-radius: 8px;">
                        <i class="fa fa-chevron-left" style="margin-right: 8px;"></i> Previous
                    </a>
                <?php
    else: ?>
                    <button class="btn btn-secondary" disabled style="padding: 10px 20px; font-weight: 700; border-radius: 8px; border: 1px solid var(--border-color); opacity: 0.5; cursor: not-allowed;">
                        <i class="fa fa-chevron-left" style="margin-right: 8px;"></i> Previous
                    </button>
                <?php
    endif; ?>

                <div style="display: flex; gap: 8px; align-items: center;">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&category=<?php echo urlencode($category_slug); ?>&search=<?php echo urlencode($search_query); ?>" 
                           style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; text-decoration: none; border-radius: 50%; border: 1px solid <?php echo $i == $page ? 'var(--primary-color)' : 'var(--border-color)'; ?>; background: <?php echo $i == $page ? 'var(--primary-color)' : 'transparent'; ?>; color: <?php echo $i == $page ? 'white' : 'var(--dark-color)'; ?>; font-weight: 700; transition: all 0.2s;">
                            <?php echo $i; ?>
                        </a>
                    <?php
    endfor; ?>
                </div>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&category=<?php echo urlencode($category_slug); ?>&search=<?php echo urlencode($search_query); ?>" class="btn btn-secondary" style="padding: 10px 20px; font-weight: 700; border-radius: 8px;">
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
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
