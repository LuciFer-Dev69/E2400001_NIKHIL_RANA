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

// Pagination logic
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1)
    $page = 1;
$offset = ($page - 1) * $limit;

// Filter logic
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

// Fetch enrolled courses with instructor name and progress
try {
    // 1. Get Total Count for Pagination
    $count_sql = "
        SELECT COUNT(*)
        FROM enrollments e
        JOIN courses c ON e.course_id = c.id
        WHERE e.student_id = ?
    ";
    $count_params = [$user_id];
    if ($filter === 'free')
        $count_sql .= " AND c.price = 0";
    if ($filter === 'locked')
        $count_sql .= " AND c.price > 0";
    if (!empty($search)) {
        $count_sql .= " AND c.title LIKE ?";
        $count_params[] = "%$search%";
    }

    $stmt = $pdo->prepare($count_sql);
    $stmt->execute($count_params);
    $total_records = $stmt->fetchColumn();
    $total_pages = ceil($total_records / $limit);

    // 2. Fetch Paginated Records
    $sql = "
        SELECT c.*, c.id AS course_id, u.full_name as instructor_name, e.progress_percent, e.enrolled_at, e.is_purchased
        FROM enrollments e
        JOIN courses c ON e.course_id = c.id
        JOIN users u ON c.instructor_id = u.id
        WHERE e.student_id = ?
    ";

    $params = [$user_id];

    if ($filter === 'free') {
        $sql .= " AND c.price = 0";
    }
    elseif ($filter === 'locked') {
        $sql .= " AND c.price > 0";
    }

    if (!empty($search)) {
        $sql .= " AND c.title LIKE ?";
        $params[] = "%$search%";
    }

    $sql .= " ORDER BY e.enrolled_at DESC LIMIT $limit OFFSET $offset";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $courses_enrolled = $stmt->fetchAll();

}
catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$root = "../../";
$page_title = 'My Enrolled Courses';
include '../../includes/portal_header.php';
?>



        <div class="portal-inner-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px; padding: 25px; border-radius: 12px; border: 1px solid var(--border-color);">
            <div class="header-section">
                <h1 style="font-size: 28px; font-weight: 800; color: #1c1d1f; margin-bottom: 5px;">Learning</h1>
                <p style="color: #6a6f73; font-size: 14px;">Browse and continue your enrolled courses.</p>
            </div>
            
            <div style="display: flex; gap: 15px;">
                <form id="search-form" action="" method="GET" style="display: flex; gap: 10px;">
                    <div style="position: relative;">
                        <input type="text" id="live-search" name="search" placeholder="Search my courses..." value="<?php echo htmlspecialchars($search); ?>" 
                               style="padding: 12px 15px 12px 45px; border: 1px solid #d1d7dc; border-radius: 8px; font-size: 14px; width: 300px; transition: border-color 0.3s;" autocomplete="off">
                        <i class="fa fa-search" style="position: absolute; left: 18px; top: 15px; color: #6a6f73;"></i>
                    </div>
                    <select name="filter" id="filter-select" onchange="performSearch()" style="padding: 12px 15px; border: 1px solid #d1d7dc; border-radius: 8px; font-size: 14px; background: white; cursor: pointer; font-weight: 600;">
                        <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Access</option>
                        <option value="free" <?php echo $filter === 'free' ? 'selected' : ''; ?>>Free Courses</option>
                        <option value="locked" <?php echo $filter === 'locked' ? 'selected' : ''; ?>>Premium (Locked)</option>
                    </select>
                </form>
            </div>
        </div>

        <div id="courses-grid" class="courses-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; margin-bottom: 40px;">
            <?php if (empty($courses_enrolled)): ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 80px; background: #f7f9fa; border-radius: 12px; border: 1px dashed #d1d7dc;">
                    <i class="fa fa-search" style="font-size: 48px; color: #d1d7dc; margin-bottom: 20px; display: block;"></i>
                    <h3 style="font-weight: 700; color: #1c1d1f; margin-bottom: 10px;">We couldn't find any courses</h3>
                    <p style="color: #6a6f73; margin-bottom: 25px;">Try adjusting your filters or search terms to find what you're looking for.</p>
                    <a href="courses.php" class="btn btn-secondary">Clear all filters</a>
                </div>
            <?php
else: ?>
                <?php foreach ($courses_enrolled as $course):
        $is_locked = ($course['price'] > 0 && $course['is_purchased'] == 0);
?>
                <div class="course-card-premium <?php echo $is_locked ? 'locked' : ''; ?>" style="position: relative;">
                    <?php if ($is_locked): ?>
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 160px; background: rgba(0,0,0,0.4); z-index: 2; display: flex; align-items: center; justify-content: center; color: white; font-size: 30px; border-radius: 8px 8px 0 0;">
                            <i class="fa fa-lock"></i>
                        </div>
                    <?php
        endif; ?>
                    
                    <div style="position: relative; height: 160px; overflow: hidden; border-radius: 8px 8px 0 0;">
                        <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&q=80&w=400" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; <?php echo $is_locked ? 'filter: grayscale(0.5) blur(2px);' : ''; ?>" onerror="this.src='https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=400&q=80'">
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
                            <a href="../../checkout_subscription.php" class="btn btn-primary" style="width: 100%; padding: 12px; font-weight: 800; background: #1c1d1f; color: white; border: none; border-radius: 8px;"><i class="fa fa-lock-open" style="margin-right: 6px;"></i>Unlock for $<?php echo number_format($course['price'], 2); ?></a>
                        <?php
        else: ?>
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; font-size: 12px; font-weight: 700; color: #1c1d1f;">
                                <span><?php echo $course['progress_percent']; ?>% Complete</span>
                            </div>
                            <div class="premium-progress" style="margin: 0 0 20px 0; height: 6px; background: #d1d7dc;">
                                <div class="premium-progress-bar" style="width: <?php echo $course['progress_percent']; ?>%; height: 100%;"></div>
                            </div>
                            <a href="../../ai_fundamentals.php" class="btn btn-secondary" style="width: 100%; padding: 12px; font-weight: 800; border-radius: 8px;">View Course</a>
                        <?php
        endif; ?>
                    </div>
                </div>
                <?php
    endforeach; ?>
            <?php
endif; ?>
        </div>

        <script>
            const searchInput = document.getElementById('live-search');
            const filterSelect = document.getElementById('filter-select');
            const grid = document.getElementById('courses-grid');
            const totalCount = document.getElementById('total-course-count');
            let debounceTimer;

            searchInput.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(performSearch, 300);
            });

            async function performSearch() {
                const search = searchInput.value;
                const filter = filterSelect.value;
                
                grid.style.opacity = '0.5';
                
                try {
                    const response = await fetch(`api/search_courses.php?search=${encodeURIComponent(search)}&filter=${filter}`);
                    const data = await response.json();
                    
                    grid.innerHTML = data.html;
                    grid.style.opacity = '1';
                    
                    // Hide pagination on search for simplicity (or update it if needed)
                    const pagination = document.getElementById('pagination-controls');
                    if (pagination) pagination.style.display = search ? 'none' : 'flex';
                    
                } catch (error) {
                    console.error('Search error:', error);
                    grid.style.opacity = '1';
                }
            }
        </script>

        <style>
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>

        <!-- Pagination Controls -->
        <?php if ($total_pages > 1): ?>
        <div id="pagination-controls" style="display: flex; justify-content: center; gap: 10px; margin-top: 40px;">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&filter=<?php echo $filter; ?>&search=<?php echo urlencode($search); ?>" 
                   style="padding: 10px 20px; border: 1px solid #d1d7dc; border-radius: 8px; text-decoration: none; color: #1c1d1f; font-weight: 700; background: white;">
                   <i class="fa fa-chevron-left"></i> Previous
                </a>
            <?php
    endif; ?>

            <div style="display: flex; align-items: center; gap: 5px; font-weight: 700; color: #6a6f73;">
                Page <?php echo $page; ?> of <?php echo $total_pages; ?>
            </div>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>&filter=<?php echo $filter; ?>&search=<?php echo urlencode($search); ?>" 
                   style="padding: 10px 20px; border: 1px solid #d1d7dc; border-radius: 8px; text-decoration: none; color: #1c1d1f; font-weight: 700; background: white;">
                   Next <i class="fa fa-chevron-right"></i>
                </a>
            <?php
    endif; ?>
        </div>
        <?php
endif; ?>

    </div>

<?php include '../../includes/portal_footer.php'; ?>
