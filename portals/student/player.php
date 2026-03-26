<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$lesson_id = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : 0;

if (!$course_id) {
    header("Location: courses.php");
    exit();
}

// Check Subscription/Enrollment
$is_authorized = false;
$user_id = $_SESSION['user_id'];

// Check for direct enrollment or active subscription
$stmt = $pdo->prepare("SELECT id FROM enrollments WHERE student_id = ? AND course_id = ?");
$stmt->execute([$user_id, $course_id]);
if ($stmt->fetch())
    $is_authorized = true;

if (!$is_authorized) {
    $stmt = $pdo->prepare("SELECT id FROM user_subscriptions WHERE user_id = ? AND status = 'active' AND (expires_at IS NULL OR expires_at > NOW())");
    $stmt->execute([$user_id]);
    if ($stmt->fetch())
        $is_authorized = true;
}

if (!$is_authorized) {
    header("Location: ../../course_details.php?id=$course_id&error=not_authorized");
    exit();
}

// Fetch Course & Lessons
$stmt = $pdo->prepare("SELECT title FROM courses WHERE id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM sections WHERE course_id = ? ORDER BY order_num ASC");
$stmt->execute([$course_id]);
$sections = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY order_num ASC");
$stmt->execute([$course_id]);
$lessons = $stmt->fetchAll();

// Group lessons
$grouped_lessons = [];
foreach ($lessons as $lesson) {
    $sid = $lesson['section_id'] ?: 0;
    $grouped_lessons[$sid][] = $lesson;
}

if (empty($sections)) {
    $sections = [['id' => 0, 'title' => 'Course Content']];
}

// Current Lesson
$current_lesson = null;
if ($lesson_id) {
    foreach ($lessons as $l) {
        if ($l['id'] == $lesson_id) {
            $current_lesson = $l;
            break;
        }
    }
}
if (!$current_lesson && !empty($lessons)) {
    $current_lesson = $lessons[0];
}

$page_title = $course['title'] . " | Player";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --player-bg: #1c1d1f;
            --sidebar-bg: #fff;
            --accent: #d6293e; /* Consistent Red Theme */
        }
        body {
            margin: 0;
            padding: 0;
            background: #000;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .player-header {
            height: 56px;
            background: var(--player-bg);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            padding: 0 20px;
            color: #fff;
            justify-content: space-between;
            z-index: 100;
        }
        .player-main {
            display: flex;
            flex: 1;
            overflow: hidden;
        }
        .video-content {
            flex: 1;
            background: #000;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }
        .video-wrapper {
            width: 100%;
            aspect-ratio: 16/9;
            background: #000;
            position: sticky;
            top: 0;
        }
        .video-wrapper video {
            width: 100%;
            height: 100%;
        }
        .lesson-info {
            padding: 30px 40px;
            color: #fff;
            background: #1c1d1f;
        }
        .player-sidebar {
            width: 350px;
            background: var(--sidebar-bg);
            border-left: 1px solid #d1d7dc;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }
        .sidebar-header {
            padding: 15px 20px;
            border-bottom: 1px solid #d1d7dc;
            font-weight: 700;
            color: #1c1d1f;
        }
        .accordion-item {
            border-bottom: 1px solid #d1d7dc;
        }
        .accordion-header {
            padding: 15px 20px;
            background: #f7f9fa;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            font-weight: 700;
        }
        .accordion-body {
            display: none;
        }
        .lesson-link {
            padding: 12px 20px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            text-decoration: none;
            color: #1c1d1f;
            font-size: 13px;
            transition: background 0.2s;
        }
        .lesson-link:hover {
            background: #f7f9fa;
        }
        .lesson-link.active {
            background: #e4e6e7;
        }
        .lesson-link i {
            margin-top: 3px;
            color: #6a6f73;
        }
        .lesson-link.active i {
            color: var(--accent);
        }
        @media (max-width: 991px) {
            .player-main {
                flex-direction: column;
            }
            .player-sidebar {
                width: 100%;
                border-left: none;
                border-top: 1px solid #d1d7dc;
                height: 400px;
                flex: none;
            }
            body {
                overflow: auto;
            }
        }
    </style>
</head>
<body>
    <header class="player-header">
        <div style="display: flex; align-items: center; gap: 20px;">
            <a href="index.php" style="color: #fff; text-decoration: none; font-size: 20px;"><i class="fa fa-times"></i></a>
            <div style="border-left: 1px solid rgba(255,255,255,0.2); padding-left: 20px;">
                <h4 style="margin: 0; font-size: 14px;"><?php echo htmlspecialchars($course['title']); ?></h4>
            </div>
        </div>
        <div>
            <button style="background: var(--accent); color: white; border: none; padding: 8px 16px; border-radius: 4px; font-weight: 700; cursor: pointer;">Share <i class="fa fa-share"></i></button>
        </div>
    </header>

    <div class="player-main">
        <div class="video-content">
            <div class="video-wrapper">
                <?php if ($current_lesson): ?>
                    <video id="mainPlayer" controls poster="https://pagedone.io/asset/uploads/1705386154.png">
                        <source src="<?php echo htmlspecialchars($current_lesson['video_url'] ?: 'https://www.w3schools.com/html/mov_bbb.mp4'); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                <?php
else: ?>
                    <div style="height: 100%; display: flex; align-items: center; justify-content: center; color: #fff;">
                        No video found for this lesson.
                    </div>
                <?php
endif; ?>
            </div>
            <div class="lesson-info">
                <h1 style="font-size: 24px; margin-bottom: 20px;"><?php echo htmlspecialchars($current_lesson['title']); ?></h1>
                <div style="display: flex; gap: 30px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; margin-bottom: 20px;">
                    <a href="#" style="color: #fff; text-decoration: none; font-weight: 700; border-bottom: 3px solid #fff; padding-bottom: 8px;">Overview</a>
                    <a href="#" style="color: #d1d7dc; text-decoration: none;">Notes</a>
                    <a href="#" style="color: #d1d7dc; text-decoration: none;">Announcements</a>
                    <a href="#" style="color: #d1d7dc; text-decoration: none;">Reviews</a>
                </div>
                <p style="color: #d1d7dc; line-height: 1.6;">Welcome to this lesson on <?php echo htmlspecialchars($current_lesson['title']); ?>. In this module, we will explore the core concepts and practical applications of the subject.</p>
            </div>
        </div>

        <aside class="player-sidebar">
            <div class="sidebar-header">Course content</div>
            <?php foreach ($sections as $idx => $section): ?>
                <div class="accordion-item">
                    <div class="accordion-header" onclick="toggleAccordion(this)">
                        <div style="display: flex; flex-direction: column;">
                            <span>Section <?php echo $idx + 1; ?>: <?php echo htmlspecialchars($section['title']); ?></span>
                            <span style="font-size: 11px; color: #6a6f73; margin-top: 4px;">0 / <?php echo count($grouped_lessons[$section['id']] ?? []); ?> | <?php
    $sec_mins = array_sum(array_column($grouped_lessons[$section['id']] ?? [], 'duration_mins'));
    echo $sec_mins; ?>min
                            </span>
                        </div>
                        <i class="fa fa-chevron-up"></i>
                    </div>
                    <div class="accordion-body" style="display: block;">
                        <?php
    $sec_lessons = $grouped_lessons[$section['id']] ?? [];
    foreach ($sec_lessons as $lesson):
        $active = ($lesson['id'] == $current_lesson['id']) ? 'active' : '';
?>
                            <a href="?course_id=<?php echo $course_id; ?>&lesson_id=<?php echo $lesson['id']; ?>" class="lesson-link <?php echo $active; ?>">
                                <input type="checkbox" style="margin-top: 4px;">
                                <div style="display: flex; flex-direction: column;">
                                    <span style="margin-bottom: 5px;"><?php echo htmlspecialchars($lesson['title']); ?></span>
                                    <span style="font-size: 11px; color: #6a6f73;"><i class="fa fa-play-circle"></i> <?php echo $lesson['duration_mins']; ?>min</span>
                                </div>
                            </a>
                        <?php
    endforeach; ?>
                    </div>
                </div>
            <?php
endforeach; ?>
        </aside>
    </div>

    <script>
        function toggleAccordion(header) {
            const body = header.nextElementSibling;
            const icon = header.querySelector('i');
            
            if (body.style.display === 'block') {
                body.style.display = 'none';
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            } else {
                body.style.display = 'block';
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            }
        }
    </script>
</body>
</html>
