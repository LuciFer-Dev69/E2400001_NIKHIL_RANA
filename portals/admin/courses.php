<?php
require_once '../../config/db.php';
session_start();

$root = "../../";
$page_title = 'Manage Courses';
include '../../includes/admin/admin_header.php';

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Search/Filter Setup
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$category_filter = $_GET['category'] ?? '';

// Fetch categories for filter dropdown
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_KEY_PAIR);

$sql = "
    SELECT c.id, c.title, c.status, c.created_at, c.price, c.thumbnail,
           u.full_name as instructor_name, cat.name as category_name,
           (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) as enroll_count,
           (SELECT COUNT(*) FROM lessons WHERE course_id = c.id) as lesson_count
    FROM courses c
    JOIN users u ON c.instructor_id = u.id
    JOIN categories cat ON c.category_id = cat.id
    WHERE 1=1
";
$params = [];

if (!empty($search)) {
    $sql .= " AND (c.title LIKE ? OR u.full_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if (!empty($status_filter) && in_array($status_filter, ['draft', 'pending', 'published', 'archived'])) {
    $sql .= " AND c.status = ?";
    $params[] = $status_filter;
}
if (!empty($category_filter)) {
    $sql .= " AND c.category_id = ?";
    $params[] = $category_filter;
}

// Get Total for Pagination
// Since the query has joins, a simple replace might be tricky if we select derived columns, so we wrap it.
$count_sql = "SELECT COUNT(*) FROM ($sql) AS subquery";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_courses = $stmt->fetchColumn();
$total_pages = ceil($total_courses / $limit);

// Fetch Paginated List
$sql .= " ORDER BY c.created_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $val) {
    if (is_int($val))
        $stmt->bindValue($key + 1, $val, PDO::PARAM_INT);
    else
        $stmt->bindValue($key + 1, $val, PDO::PARAM_STR);
}
$stmt->execute();
$courses = $stmt->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px;">
    <div>
        <h1 style="font-size: 28px; font-weight: 800; color: var(--dark-color); margin-bottom: 8px;">Course & Content Control</h1>
        <p style="color: var(--gray-color); font-size: 15px;">Monitor, approve, and manage all courses on the platform.</p>
    </div>
    
    <!-- Using a future route that might be built if Admin creates courses directly -->
    <a href="course_editor.php?action=create" class="btn btn-primary"><i class="fa fa-plus"></i> Create New Course</a>
</div>

<!-- Filters Bar -->
<div style="background: var(--bg-card); padding: 20px; border-radius: 8px; border: 1px solid var(--border-color); box-shadow: var(--shadow); margin-bottom: 25px; display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
    <form method="GET" style="display: flex; gap: 15px; flex: 1; flex-wrap: wrap; min-width: 300px;">
        <input type="text" name="search" placeholder="Search course or instructor..." value="<?php echo htmlspecialchars($search); ?>" style="flex: 1; min-width: 250px; padding: 10px 15px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-family: inherit;">
        
        <select name="status" style="padding: 10px 15px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-family: inherit;">
            <option value="">All Statuses</option>
            <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>⏳ Pending Review</option>
            <option value="published" <?php echo $status_filter === 'published' ? 'selected' : ''; ?>>Published (Live)</option>
            <option value="draft" <?php echo $status_filter === 'draft' ? 'selected' : ''; ?>>Draft</option>
            <option value="archived" <?php echo $status_filter === 'archived' ? 'selected' : ''; ?>>Archived</option>
        </select>

        <select name="category" style="padding: 10px 15px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-family: inherit;">
            <option value="">All Categories</option>
            <?php foreach ($categories as $id => $name): ?>
                <option value="<?php echo $id; ?>" <?php echo $category_filter == $id ? 'selected' : ''; ?>><?php echo htmlspecialchars($name); ?></option>
            <?php
endforeach; ?>
        </select>
        
        <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">Filter</button>
        <?php if (!empty($search) || !empty($status_filter) || !empty($category_filter)): ?>
            <a href="courses.php" class="btn btn-secondary" style="padding: 10px 20px;">Clear</a>
        <?php
endif; ?>
    </form>
    <div style="font-weight: 700; color: var(--gray-color); text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">
        Total Courses: <?php echo number_format($total_courses); ?>
    </div>
</div>

<!-- Courses Grid (Replaced standard table with a rich card list layout for visual appeal) -->
<div style="display: flex; flex-direction: column; gap: 20px;">
    <?php if (empty($courses)): ?>
        <div style="background: var(--bg-card); padding: 50px; text-align: center; border-radius: 12px; border: 1px solid var(--border-color);">
            <i class="fa fa-folder-open" style="font-size: 40px; color: var(--gray-color); margin-bottom: 20px;"></i>
            <h3 style="font-size: 18px; color: var(--dark-color);">No courses found.</h3>
            <p style="color: var(--gray-color);">Try adjusting your search criteria.</p>
        </div>
    <?php
else: ?>
        <?php foreach ($courses as $course): ?>
        <div id="course-row-<?php echo $course['id']; ?>" style="display: flex; align-items: stretch; background: var(--bg-card); border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color); box-shadow: var(--shadow); transition: transform 0.2s;">
            <div style="width: 200px; flex-shrink: 0; background: var(--light-gray); position: relative;">
                <?php
        $thumb = $course['thumbnail'] ?: 'default.jpg';
        $is_external = (strpos($thumb, 'http') === 0);
        $thumb_url = $is_external ? $thumb : "../../assets/img/courses/" . $thumb;
?>
                <img src="<?php echo $thumb_url; ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=400&q=80'">
                <?php if ($course['price'] == 0): ?>
                    <div style="position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.7); color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 800;">FREE</div>
                <?php
        else: ?>
                    <div style="position: absolute; top: 10px; left: 10px; background: var(--primary-color); color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 800;">$<?php echo number_format($course['price'], 2); ?></div>
                <?php
        endif; ?>
            </div>
            
            <div style="flex: 1; padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                        <h2 style="font-size: 18px; font-weight: 800; color: var(--dark-color); margin: 0; line-height: 1.3;"><a href="course_editor.php?id=<?php echo $course['id']; ?>" style="color: inherit; text-decoration: none;"><?php echo htmlspecialchars($course['title']); ?></a></h2>
                        
                        <!-- Status Badge & Quick Actions -->
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <?php if ($course['status'] === 'pending'): ?>
                            <button onclick="approveCourse(<?php echo $course['id']; ?>)" style="padding: 6px 14px; border-radius: 20px; background: rgba(46,204,113,0.1); border: 1px solid #2ecc71; color: #2ecc71; font-weight: 800; font-size: 12px; cursor: pointer;"><i class="fa fa-check"></i> Approve</button>
                            <?php
        endif; ?>
                            <select onchange="updateCourseStatus(<?php echo $course['id']; ?>, this.value)" style="padding: 6px 12px; border-radius: 50px; font-size: 12px; font-weight: 800; text-transform: uppercase; cursor: pointer; border: 1px solid <?php echo $course['status'] === 'published' ? '#2ecc71' : ($course['status'] === 'pending' ? '#f1c40f' : ($course['status'] === 'archived' ? 'var(--border-color)' : '#95a5a6')); ?>; background: <?php echo $course['status'] === 'published' ? 'rgba(46,204,113,0.1)' : ($course['status'] === 'pending' ? 'rgba(241,196,15,0.1)' : ($course['status'] === 'archived' ? 'rgba(106,111,115,0.1)' : 'rgba(149,165,166,0.1)')); ?>; color: <?php echo $course['status'] === 'published' ? '#2ecc71' : ($course['status'] === 'pending' ? '#f1c40f' : ($course['status'] === 'archived' ? 'var(--gray-color)' : '#95a5a6')); ?>; outline: none;">
                                <option value="pending" <?php echo $course['status'] === 'pending' ? 'selected' : ''; ?> style="color:#000;">Pending</option>
                                <option value="published" <?php echo $course['status'] === 'published' ? 'selected' : ''; ?> style="color:#000;">Published</option>
                                <option value="draft" <?php echo $course['status'] === 'draft' ? 'selected' : ''; ?> style="color:#000;">Draft</option>
                                <option value="archived" <?php echo $course['status'] === 'archived' ? 'selected' : ''; ?> style="color:#000;">Archived</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="font-size: 13px; color: var(--gray-color); margin-bottom: 15px; display: flex; gap: 15px;">
                        <span><i class="fa fa-user-tie"></i> Instr: <strong style="color: var(--dark-color);"><?php echo htmlspecialchars($course['instructor_name']); ?></strong></span>
                        <span><i class="fa fa-folder"></i> <?php echo htmlspecialchars($course['category_name']); ?></span>
                        <span><i class="fa fa-calendar-alt"></i> Created: <?php echo date('M j, Y', strtotime($course['created_at'])); ?></span>
                    </div>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 1px solid var(--border-color);">
                    <div style="display: flex; gap: 20px; font-size: 13px; color: var(--gray-color); font-weight: 700;">
                        <span><i class="fa fa-users" style="color: var(--primary-color);"></i> <?php echo number_format($course['enroll_count']); ?> Enrollments</span>
                        <span><i class="fa fa-play-circle" style="color: #2ecc71;"></i> <?php echo number_format($course['lesson_count']); ?> Lessons</span>
                    </div>
                    <div class="action-btns">
                        <a href="course_editor.php?id=<?php echo $course['id']; ?>" class="btn-icon" title="Edit Course Content"><i class="fa fa-edit"></i></a>
                        <button class="btn-icon" title="Delete Course" style="color: #e74c3c; border-color: rgba(231,76,60,0.3);" onclick="deleteCourse(<?php echo $course['id']; ?>)"><i class="fa fa-trash-alt"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    endforeach; ?>
    <?php
endif; ?>
</div>

<!-- Pagination -->
<?php if ($total_pages > 1): ?>
<div style="display: flex; justify-content: flex-end; align-items: center; gap: 10px; margin-top: 30px;">
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&category=<?php echo urlencode($category_filter); ?>" class="btn btn-secondary" style="padding: 8px 15px; font-weight: 700;"><i class="fa fa-chevron-left"></i></a>
    <?php
    else: ?>
        <button class="btn btn-secondary" disabled style="padding: 8px 15px; opacity: 0.5;"><i class="fa fa-chevron-left"></i></button>
    <?php
    endif; ?>

    <span style="font-weight: 700; font-size: 14px; color: var(--dark-color);">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>

    <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&category=<?php echo urlencode($category_filter); ?>" class="btn btn-secondary" style="padding: 8px 15px; font-weight: 700;"><i class="fa fa-chevron-right"></i></a>
    <?php
    else: ?>
        <button class="btn btn-secondary" disabled style="padding: 8px 15px; opacity: 0.5;"><i class="fa fa-chevron-right"></i></button>
    <?php
    endif; ?>
</div>
<?php
endif; ?>

<script>
    function approveCourse(id) {
        if(confirm('Approve this course? It will immediately go Live on the platform.')) {
            fetch('../../api/admin_courses.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'change_status', course_id: id, status: 'published' })
            }).then(res => res.json()).then(data => {
                if(data.success) location.reload();
                else alert('Error: ' + data.message);
            });
        }
    }

    function updateCourseStatus(id, newStatus) {
        fetch('../../api/admin_courses.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'change_status', course_id: id, status: newStatus })
        }).then(res => res.json()).then(data => {
            if(data.success) {
                // To visually reflect the color changes of the select box quickly, a reload is safest, 
                // but for seamless UX we could do DOM manipulation. For now, reload.
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        }).catch(e => {
            alert('Network error occurred.');
        });
    }

    function deleteCourse(id) {
        if(confirm("DANGER: Are you sure you want to permanently delete this course? ALL lessons and student enrollments will be wiped immediately.")) {
            const row = document.getElementById('course-row-' + id);
            if(row) row.style.opacity = '0.3';
            
            fetch('../../api/admin_courses.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete_course', course_id: id })
            }).then(res => res.json()).then(data => {
                if(data.success) {
                    if(row) {
                        row.style.transition = "all 0.5s ease";
                        row.style.transform = "translateX(-100px)";
                        setTimeout(() => row.remove(), 500);
                    } else {
                        location.reload();
                    }
                } else {
                    alert('Error: ' + data.message);
                    if(row) row.style.opacity = '1';
                }
            }).catch(e => {
                alert('Network error occurred.');
                if(row) row.style.opacity = '1';
            });
        }
    }
</script>

<?php include '../../includes/admin/admin_footer.php'; ?>
