<?php
require_once '../../config/db.php';
session_start();

$root = "../../";
$course_id = $_GET['id'] ?? 0;
$action = $_GET['action'] ?? 'edit'; // 'edit' or 'create'
$is_new = ($action === 'create' || !$course_id);

$course = [
    'id' => 0, 'title' => '', 'description' => '', 'category_id' => 0,
    'price' => 0.00, 'status' => 'draft', 'thumbnail' => '', 'instructor_id' => $_SESSION['user_id']
];
$lessons = [];

if (!$is_new) {
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch();

    if (!$course) {
        die("Course not found.");
    }

    $stmt = $pdo->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY order_num ASC, id ASC");
    $stmt->execute([$course_id]);
    $lessons = $stmt->fetchAll();
}

// Handle Form Submission for Course Data
$msg = '';
$msg_type = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_course'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    $instructor_id = $_POST['instructor_id']; // For admin editing, they can assign to an instructor

    if (empty($title) || empty($category_id) || empty($instructor_id)) {
        $msg = "Title, Category, and Instructor are required.";
        $msg_type = "error";
    }
    else {
        if ($is_new) {
            $stmt = $pdo->prepare("INSERT INTO courses (title, description, category_id, price, status, instructor_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $category_id, $price, $status, $instructor_id]);
            $new_id = $pdo->lastInsertId();
            header("Location: course_editor.php?id=$new_id&success=created");
            exit();
        }
        else {
            $stmt = $pdo->prepare("UPDATE courses SET title=?, description=?, category_id=?, price=?, status=?, instructor_id=? WHERE id=?");
            $stmt->execute([$title, $description, $category_id, $price, $status, $instructor_id, $course_id]);
            $msg = "Course updated successfully.";
            $msg_type = "success";

            // Re-fetch updated data
            $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
            $stmt->execute([$course_id]);
            $course = $stmt->fetch();
        }
    }
}

if (isset($_GET['success']) && $_GET['success'] === 'created') {
    $msg = "Course created successfully! Now you can add lessons.";
    $msg_type = "success";
}

$page_title = $is_new ? 'Create Course' : 'Edit: ' . htmlspecialchars($course['title']);
include '../../includes/admin/admin_header.php';

// Fetch lists for dropdowns
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$instructors = $pdo->query("SELECT id, full_name, role FROM users WHERE role IN ('instructor', 'admin') ORDER BY full_name")->fetchAll(PDO::FETCH_ASSOC);
?>

<div style="margin-bottom: 25px;">
    <a href="courses.php" class="btn btn-secondary" style="margin-bottom: 15px;"><i class="fa fa-arrow-left"></i> Back to Courses</a>
    <h1 style="font-size: 28px; font-weight: 800; color: var(--dark-color); margin-bottom: 8px;">
        <?php echo $is_new ? 'Create New Course' : 'Course Editor'; ?>
    </h1>
    <p style="color: var(--gray-color); font-size: 15px;">
        <?php echo $is_new ? 'Fill in the basic info to create the course container.' : 'Manage course metadata and curriculum content.'; ?>
    </p>
</div>

<?php if ($msg): ?>
<div style="background: <?php echo $msg_type === 'error' ? 'rgba(231, 76, 60, 0.1)' : 'rgba(46, 204, 113, 0.1)'; ?>; border: 1px solid <?php echo $msg_type === 'error' ? '#e74c3c' : '#2ecc71'; ?>; color: <?php echo $msg_type === 'error' ? '#e74c3c' : '#2ecc71'; ?>; padding: 15px; border-radius: 8px; margin-bottom: 25px; font-weight: 700;">
    <?php echo $msg; ?>
</div>
<?php
endif; ?>

<div style="display: grid; grid-template-columns: 1fr <?php echo $is_new ? '' : '1fr'; ?>; gap: 30px;">
    
    <!-- Course Metadata Form -->
    <div style="background: var(--bg-card); border-radius: 12px; padding: 30px; border: 1px solid var(--border-color); box-shadow: var(--shadow);">
        <h3 style="font-size: 18px; font-weight: 800; color: var(--dark-color); margin-bottom: 20px;">Course Details</h3>
        
        <form method="POST">
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 8px;">Course Title *</label>
                <input type="text" name="title" required value="<?php echo htmlspecialchars($course['title']); ?>" style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-size: 15px; font-family: inherit;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 8px;">Description</label>
                <textarea name="description" rows="5" style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-size: 15px; font-family: inherit; resize: vertical;"><?php echo htmlspecialchars($course['description']); ?></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 8px;">Category *</label>
                    <select name="category_id" required style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-size: 15px;">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $course['category_id'] == $cat['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php
endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 8px;">Price (USD) *</label>
                    <input type="number" step="0.01" name="price" required value="<?php echo htmlspecialchars($course['price']); ?>" style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-size: 15px;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <div>
                    <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 8px;">Status</label>
                    <select name="status" style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-size: 15px;">
                        <option value="draft" <?php echo $course['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                        <option value="published" <?php echo $course['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                        <option value="archived" <?php echo $course['status'] === 'archived' ? 'selected' : ''; ?>>Archived</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 8px;">Assign Instructor *</label>
                    <select name="instructor_id" required style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-size: 15px;">
                        <option value="">Select Instructor</option>
                        <?php foreach ($instructors as $inst): ?>
                            <option value="<?php echo $inst['id']; ?>" <?php echo $course['instructor_id'] == $inst['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($inst['full_name']); ?> (<?php echo ucfirst($inst['role']); ?>)
                            </option>
                        <?php
endforeach; ?>
                    </select>
                </div>
            </div>

            <button type="submit" name="save_course" class="btn btn-primary" style="width: 100%; font-size: 16px; padding: 15px;">
                <?php echo $is_new ? 'Create Course Container' : 'Save Changes'; ?>
            </button>
        </form>
    </div>

    <!-- Curriculum Editor (Only shown if editing existing course) -->
    <?php if (!$is_new): ?>
    <div style="background: var(--bg-card); border-radius: 12px; padding: 30px; border: 1px solid var(--border-color); box-shadow: var(--shadow);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-size: 18px; font-weight: 800; color: var(--dark-color); margin: 0;">Curriculum (<?php echo count($lessons); ?> Lessons)</h3>
            <button class="btn btn-primary" style="font-size: 13px; padding: 8px 15px;" onclick="openLessonModal(0, '', '', '', '')"><i class="fa fa-plus"></i> Add Lesson</button>
        </div>

        <?php if (empty($lessons)): ?>
            <div style="text-align: center; padding: 40px; border: 1px dashed var(--border-color); border-radius: 8px;">
                <i class="fa fa-film" style="font-size: 30px; color: var(--gray-color); margin-bottom: 10px;"></i>
                <p style="color: var(--gray-color); font-weight: 700;">No lessons added yet.</p>
                <button class="btn btn-secondary" style="margin-top: 10px;" onclick="openLessonModal(0, '', '', '', '')">Add First Lesson</button>
            </div>
        <?php
    else: ?>
            <div id="lessons-list" style="display: flex; flex-direction: column; gap: 10px;">
                <?php foreach ($lessons as $index => $lesson): ?>
                    <div id="lesson-row-<?php echo $lesson['id']; ?>" style="display: flex; align-items: center; justify-content: space-between; padding: 15px; background: var(--light-gray); border: 1px solid var(--border-color); border-radius: 8px;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--bg-card); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; font-weight: 800; color: var(--gray-color); font-size: 12px;">
                                <?php echo $index + 1; ?>
                            </div>
                            <div>
                                <h4 style="font-size: 15px; font-weight: 800; color: var(--dark-color); margin: 0 0 4px 0;"><?php echo htmlspecialchars($lesson['title']); ?></h4>
                                <div style="font-size: 12px; color: var(--gray-color);">
                                    <i class="far fa-clock"></i> <?php echo $lesson['duration_mins']; ?> mins
                                    <?php if ($lesson['video_url']): ?>
                                        <span style="margin-left: 10px; color: #2ecc71;"><i class="fa fa-check-circle"></i> Video Attached</span>
                                    <?php
            else: ?>
                                        <span style="margin-left: 10px; color: #e74c3c;"><i class="fa fa-exclamation-circle"></i> No Video</span>
                                    <?php
            endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="action-btns">
                            <button class="btn-icon" title="Edit Lesson" onclick="openLessonModal(<?php echo $lesson['id']; ?>, '<?php echo htmlspecialchars(addslashes($lesson['title'])); ?>', '<?php echo htmlspecialchars(addslashes($lesson['video_url'])); ?>', <?php echo $lesson['duration_mins']; ?>, <?php echo $lesson['order_num']; ?>)"><i class="fa fa-edit"></i></button>
                            <button class="btn-icon" title="Delete Lesson" style="color: #e74c3c; border-color: rgba(231,76,60,0.3);" onclick="deleteLesson(<?php echo $lesson['id']; ?>)"><i class="fa fa-trash-alt"></i></button>
                        </div>
                    </div>
                <?php
        endforeach; ?>
            </div>
        <?php
    endif; ?>
    </div>
    <?php
endif; ?>

</div>

<!-- Add/Edit Lesson Modal -->
<div id="lesson-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center;">
    <div style="background: var(--bg-card); width: 100%; max-width: 500px; border-radius: 12px; padding: 30px; border: 1px solid var(--border-color); box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
        <h2 id="lesson-modal-title" style="font-size: 20px; font-weight: 800; color: var(--dark-color); margin-bottom: 20px;">Add Lesson</h2>
        
        <input type="hidden" id="modal-lesson-id" value="0">
        <input type="hidden" id="modal-course-id" value="<?php echo $course_id; ?>">
        
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 5px;">Lesson Title *</label>
            <input type="text" id="modal-lesson-title" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color);">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 5px;">Video URL (YouTube/Vimeo)</label>
            <input type="text" id="modal-lesson-video" placeholder="e.g. https://youtube.com/watch?v=..." style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color);">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 5px;">Duration (mins)</label>
                <input type="number" id="modal-lesson-duration" value="0" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color);">
            </div>
            <div>
                <label style="display: block; font-weight: 700; color: var(--dark-color); margin-bottom: 5px;">Order Index</label>
                <input type="number" id="modal-lesson-order" value="0" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color);">
            </div>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button class="btn btn-secondary" style="flex: 1;" onclick="closeLessonModal()">Cancel</button>
            <button class="btn btn-primary" style="flex: 1;" onclick="saveLesson()">Save Lesson</button>
        </div>
    </div>
</div>

<script>
    function openLessonModal(id, title, video, duration, order) {
        document.getElementById('modal-lesson-id').value = id;
        document.getElementById('modal-lesson-title').value = title;
        document.getElementById('modal-lesson-video').value = video;
        document.getElementById('modal-lesson-duration').value = duration || 0;
        document.getElementById('modal-lesson-order').value = order || 0;
        
        document.getElementById('lesson-modal-title').innerText = id === 0 ? 'Add New Lesson' : 'Edit Lesson';
        document.getElementById('lesson-modal').style.display = 'flex';
    }

    function closeLessonModal() {
        document.getElementById('lesson-modal').style.display = 'none';
    }

    function saveLesson() {
        const payload = {
            action: 'save_lesson',
            lesson_id: document.getElementById('modal-lesson-id').value,
            course_id: document.getElementById('modal-course-id').value,
            title: document.getElementById('modal-lesson-title').value,
            video_url: document.getElementById('modal-lesson-video').value,
            duration_mins: document.getElementById('modal-lesson-duration').value,
            order_num: document.getElementById('modal-lesson-order').value
        };

        if(!payload.title.trim()) {
            alert("Lesson title is required.");
            return;
        }

        fetch('../../api/admin_lessons.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        }).then(res => res.json()).then(data => {
            if(data.success) {
                location.reload(); // Refresh to show new list
            } else {
                alert('Error: ' + data.message);
            }
        }).catch(e => {
            alert('Network error occurred.');
        });
    }

    function deleteLesson(id) {
        if(confirm("Are you sure you want to delete this lesson? This cannot be undone.")) {
            fetch('../../api/admin_lessons.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete_lesson', lesson_id: id })
            }).then(res => res.json()).then(data => {
                if(data.success) {
                    const row = document.getElementById('lesson-row-' + id);
                    if(row) {
                        row.style.transition = "all 0.3s";
                        row.style.opacity = '0';
                        setTimeout(() => row.remove(), 300);
                    } else {
                        location.reload();
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            }).catch(e => {
                alert('Network error occurred.');
            });
        }
    }
</script>

<?php include '../../includes/admin/admin_footer.php'; ?>
