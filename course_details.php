<?php
require_once 'config/db.php';
session_start();

$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$course_id) {
    header("Location: courses.php");
    exit();
}

// Fetch Course Data
$stmt = $pdo->prepare("
    SELECT c.*, u.full_name as instructor_name, cat.name as category_name, cat.slug as category_slug
    FROM courses c
    JOIN users u ON c.instructor_id = u.id
    LEFT JOIN categories cat ON c.category_id = cat.id
    WHERE c.id = ?
");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

if (!$course) {
    die("Course not found.");
}

// Fetch Sections
$stmt = $pdo->prepare("SELECT * FROM sections WHERE course_id = ? ORDER BY order_num ASC");
$stmt->execute([$course_id]);
$sections = $stmt->fetchAll();

// Fetch Lessons
$stmt = $pdo->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY order_num ASC");
$stmt->execute([$course_id]);
$lessons = $stmt->fetchAll();

// Group lessons by section_id
$grouped_lessons = [];
foreach ($lessons as $lesson) {
    $sid = $lesson['section_id'] ?: 0;
    $grouped_lessons[$sid][] = $lesson;
}

// If no sections, create a dummy one for the view
if (empty($sections)) {
    $sections = [['id' => 0, 'title' => 'Course Content']];
}

// Check for active subscription
$is_subscribed = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT id FROM user_subscriptions WHERE user_id = ? AND status = 'active' AND (expires_at IS NULL OR expires_at > NOW())");
    $stmt->execute([$_SESSION['user_id']]);
    if ($stmt->fetch()) {
        $is_subscribed = true;
    }
}

$total_lessons = count($lessons);
$total_mins = array_sum(array_column($lessons, 'duration_mins'));
$total_hours = round($total_mins / 60, 1);

// Meta for Premium Feel
$rating = 4.5;
$rating_count = 290;
$student_count = 15200;

$page_title = $course['title'];
include 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/course_premium.css">

<main class="premium-course-page">
    <!-- Dark Hero Section -->
    <section class="premium-hero">
        <div class="premium-container">
            <div class="hero-main">
                <nav class="breadcrumb-nav">
                    <a href="courses.php" style="color: inherit; text-decoration: none;">Courses</a> <i class="fa fa-chevron-right"></i>
                    <?php if ($course['category_name']): ?>
                        <a href="courses.php?category=<?php echo urlencode($course['category_slug']); ?>" style="color: inherit; text-decoration: none;"><?php echo htmlspecialchars($course['category_name']); ?></a> <i class="fa fa-chevron-right"></i>
                    <?php
endif; ?>
                    <span><?php echo htmlspecialchars($course['title']); ?></span>
                </nav>

                <div style="margin-bottom: 24px;">
                    <!-- Branded Logo Placeholder based on instructor or category -->
                    <img src="assets/img/logo.png" alt="SkillEdu" style="height: 32px; filter: brightness(0) invert(1);">
                </div>

                <h1 class="premium-title"><?php echo htmlspecialchars($course['title']); ?></h1>

                <div class="course-badge-premium">
                    <i class="fa fa-sparkles"></i> Part of SkillEdu Professional Certificate Path
                </div>

                <p class="premium-subtitle"><?php echo htmlspecialchars(substr($course['description'], 0, 200)); ?>...</p>

                <div class="premium-meta">
                    <div class="premium-meta-item">
                        <img src="https://www.gstatic.com/images/branding/product/1x/googleg_32dp.png" alt="G" style="height: 16px; margin-right: 8px;">
                        Created by <a href="#" style="color: #fff; text-decoration: underline; margin-left: 5px;"><?php echo htmlspecialchars($course['instructor_name']); ?></a>
                    </div>
                </div>

                <div class="premium-meta" style="margin-top: 15px; opacity: 0.9;">
                    <div class="premium-meta-item"><i class="fa fa-exclamation-circle"></i> Last updated <?php echo date('n/Y', strtotime($course['created_at'])); ?></div>
                    <div class="premium-meta-item"><i class="fa fa-globe"></i> English</div>
                    <div class="premium-meta-item"><i class="fa fa-closed-captioning"></i> English, <a href="#" style="color: #fff;">12 more</a></div>
                </div>

                <!-- Premium Info Box -->
                <div class="premium-info-grid">
                    <div class="premium-tab">
                        <i class="fa fa-check-circle"></i>
                        <span>Premium</span>
                    </div>
                    <div class="premium-info-content">
                        <div class="premium-info-text">
                            Access 26,000+ top-rated courses with SkillEdu Personal Plan.
                        </div>
                        <div class="premium-stats">
                            <div class="stat-item">
                                <span class="stat-value"><?php echo $rating; ?></span>
                                <div class="stars" style="justify-content: center; margin-bottom: 4px;">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-alt"></i>
                                </div>
                                <a href="#reviews" class="stat-label" style="text-decoration: underline;"><?php echo number_format($rating_count); ?> ratings</a>
                            </div>
                            <div class="stat-item">
                                <span class="stat-value"><?php echo number_format($student_count); ?></span>
                                <span class="stat-label">learners</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky Sidebar -->
            <div class="premium-sidebar-wrapper">
                <aside class="premium-sidebar">
                    <div class="sidebar-preview" onclick="openVideoModal()">
                        <img src="<?php echo htmlspecialchars($course['thumbnail']); ?>" alt="Preview" onerror="this.src='https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&q=80&w=600'">
                        <div class="play-overlay">
                            <div style="position: absolute; top: 20px; right: 20px; background: #fff; padding: 4px; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-play" style="color: var(--udemy-accent); font-size: 14px;"></i>
                            </div>
                            <div style="position: absolute; top: 100px; left: 0; right: 0; text-align: center; color: rgba(255,255,255,0.7); font-size: 13px; font-weight: 700;">Preview this course</div>
                            <i class="fa fa-play-circle"></i>
                            <span style="font-weight: 700; font-size: 15px;">Preview this course</span>
                        </div>
                    </div>
                    <div class="sidebar-content">
                        <div class="plan-info">
                            <i class="fa fa-info-circle" style="font-size: 20px;"></i>
                            <p style="margin: 0; font-weight: 700;">Part of the SkillEdu Personal Plan</p>
                            <p style="margin: 5px 0 0; color: var(--text-muted); font-size: 13px;">Subscribe to access this and 26,000+ top-rated courses. <a href="#" style="color: var(--udemy-accent); text-decoration: underline;">Learn more</a></p>
                        </div>

                        <div class="sidebar-price">
                            <?php if ($is_subscribed): ?>
                                <span style="font-size: 16px; color: var(--udemy-accent);">Included in your plan</span>
                            <?php
else: ?>
                                From $12.00 <span>/month</span>
                            <?php
endif; ?>
                        </div>

                        <ul class="sidebar-features">
                            <li><i class="fa fa-infinity"></i> Full lifetime access</li>
                            <li><i class="fa fa-mobile-alt"></i> Access on mobile and TV</li>
                            <li><i class="fa fa-award"></i> Certificate of completion</li>
                        </ul>

                        <?php if ($is_subscribed): ?>
                            <button class="btn-get-started" onclick="location.href='portals/student/player.php?course_id=<?php echo $course['id']; ?>'">Start learning <i class="fa fa-arrow-right"></i></button>
                        <?php
else: ?>
                            <button class="btn-get-started" onclick="location.href='checkout_subscription.php'">Get started <i class="fa fa-arrow-right"></i></button>
                            
                            <div style="text-align: center; margin-top: 15px;">
                                <a href="checkout.php?id=<?php echo $course['id']; ?>" style="font-size: 14px; font-weight: 700; color: var(--udemy-dark); text-decoration: underline;">Buy this course for $<?php echo number_format($course['price'], 2); ?></a>
                            </div>
                        <?php
endif; ?>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <!-- Content Sections -->
    <section class="premium-container content-section">
        <!-- Mobile Sidebar Placeholder -->
        <div id="mobile-sidebar-placeholder" class="mobile-sidebar-container"></div>

        <div class="content-main-wrapper">
            <!-- Achievement Section -->
            <div class="certificate-section">
                <i class="fa fa-certificate" style="font-size: 64px; color: var(--udemy-accent);"></i>
                <div class="cert-content">
                    <h3>Earn a certificate of completion</h3>
                    <p>After finishing all lessons, earn a professional certificate that you can share on social media, LinkedIn, or your resume to showcase your new skills.</p>
                </div>
                <i class="fa fa-arrow-right cert-arrow"></i>
            </div>

            <!-- What you'll learn -->
            <div class="learning-box">
                <h2>What you'll learn</h2>
                <ul class="learning-list">
                    <li><i class="fa fa-check"></i> Master the core concepts from scratch.</li>
                    <li><i class="fa fa-check"></i> Build real-world projects to add to your portfolio.</li>
                    <li><i class="fa fa-check"></i> Learn industry best practices directly from an expert.</li>
                    <li><i class="fa fa-check"></i> Gain the confidence to ace interviews and pass exams.</li>
                </ul>
            </div>

            <!-- Course Content -->
            <div class="curriculum-section">
                <h2 style="font-size: 24px; margin-bottom: 16px;">Course content</h2>
                <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 10px;">
                    <span><?php echo $total_lessons; ?> lectures &bull; <?php echo $total_hours; ?> total length</span>
                    <a href="#" style="color: var(--udemy-accent); font-weight: 700;">Expand all sections</a>
                </div>

                <div class="accordion-container">
                    <?php foreach ($sections as $idx => $section): ?>
                        <div class="accordion-item">
                            <div class="accordion-header" onclick="toggleAccordion(this)">
                                <span><?php echo htmlspecialchars($section['title']); ?></span>
                                <i class="fa fa-chevron-<?php echo $idx === 0 ? 'up' : 'down'; ?>"></i>
                            </div>
                            <div class="accordion-body" style="display: <?php echo $idx === 0 ? 'block' : 'none'; ?>;">
                                <?php
    $sec_lessons = $grouped_lessons[$section['id']] ?? [];
    if (empty($sec_lessons)):
?>
                                    <div class="lecture-item">No lessons in this section yet.</div>
                                <?php
    else: ?>
                                    <?php foreach ($sec_lessons as $lesson): ?>
                                        <div class="lecture-item">
                                            <span><i class="fa fa-play-circle" style="margin-right: 12px;"></i> <?php echo htmlspecialchars($lesson['title']); ?></span>
                                            <div>
                                                <a href="#" onclick="openVideoModal()" style="color: var(--udemy-accent); text-decoration: underline; margin-right: 15px;">Preview</a> 
                                                <?php echo $lesson['duration_mins']; ?>:00
                                            </div>
                                        </div>
                                    <?php
        endforeach; ?>
                                <?php
    endif; ?>
                            </div>
                        </div>
                    <?php
endforeach; ?>
                </div>
            </div>

            <!-- Description -->
            <div style="margin-top: 48px;">
                <h2 style="font-size: 24px; margin-bottom: 16px;">Description</h2>
                <div style="font-size: 16px; line-height: 1.6; color: var(--udemy-dark);">
                    <?php echo nl2br(htmlspecialchars($course['description'])); ?>
                </div>
            </div>

            <!-- Reviews Section -->
            <div id="reviews" style="margin-top: 64px; padding-top: 48px; border-top: 1px solid var(--border-soft);">
                <h2 style="font-size: 24px; margin-bottom: 24px;"><i class="fa fa-star" style="color: #b4690e;"></i> Course Rating &bull; <?php echo $rating; ?> (<?php echo number_format($rating_count); ?> ratings)</h2>
                
                <div class="review-card">
                    <div class="review-header">
                        <div class="review-avatar">JD</div>
                        <div class="review-info">
                            <h4>John Doe</h4>
                            <div class="stars">
                                <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <div class="review-body">
                        Great course! The explanations are very clear and easy to follow. Highly recommend.
                    </div>
                </div>

                <button class="btn-outline-premium">Show all reviews</button>
            </div>
        </div>
    </section>
</main>

<!-- Video Preview Modal -->
<div class="modal-overlay" id="videoModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Course Preview - <?php echo htmlspecialchars($course['title']); ?></h3>
            <button class="close-modal" onclick="closeVideoModal()">&times;</button>
        </div>
        <div class="video-player-container">
            <video id="previewPlayer" controls poster="<?php echo htmlspecialchars($course['thumbnail']); ?>">
                <source src="https://www.w3schools.com/html/mov_bbb.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
        <div class="modal-content-scroll">
            <div class="modal-section-title">Free Sample Videos:</div>
            <ul class="preview-video-list">
                <?php if (empty($lessons)): ?>
                    <li class="preview-item">No preview available</li>
                <?php
else: ?>
                    <?php foreach (array_slice($lessons, 0, 3) as $idx => $lesson): ?>
                        <li class="preview-item <?php echo $idx === 0 ? 'active' : ''; ?>" onclick="playPreview('https://www.w3schools.com/html/mov_bbb.mp4', this)">
                            <div class="preview-thumb">
                                <img src="<?php echo htmlspecialchars($course['thumbnail']); ?>" alt="Thumb">
                            </div>
                            <div class="preview-info">
                                <span class="preview-title-text"><?php echo htmlspecialchars($lesson['title']); ?></span>
                                <span class="preview-duration"><?php echo $lesson['duration_mins']; ?>:00</span>
                            </div>
                        </li>
                    <?php
    endforeach; ?>
                <?php
endif; ?>
            </ul>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('videoModal');
    const player = document.getElementById('previewPlayer');

    function openVideoModal() {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        player.play();
    }

    function closeVideoModal() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        player.pause();
    }

    function playPreview(src, element) {
        player.src = src;
        player.play();
        
        // Update active state
        document.querySelectorAll('.preview-item').forEach(item => item.classList.remove('active'));
        element.classList.add('active');
    }

    // Close on overlay click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeVideoModal();
    });

    // Sidebar Responsiveness Script
    const sidebar = document.querySelector('.premium-sidebar');
    const desktopWrapper = document.querySelector('.premium-sidebar-wrapper');
    const mobilePlaceholder = document.getElementById('mobile-sidebar-placeholder');

    function handleSidebarPosition() {
        if (!sidebar || !mobilePlaceholder || !desktopWrapper) return;
        if (window.innerWidth <= 991) {
            if (sidebar.parentElement !== mobilePlaceholder) {
                mobilePlaceholder.appendChild(sidebar);
            }
        } else {
            if (sidebar.parentElement !== desktopWrapper) {
                desktopWrapper.appendChild(sidebar);
            }
        }
    }

    window.addEventListener('resize', handleSidebarPosition);
    window.addEventListener('load', handleSidebarPosition);

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

<?php include 'includes/footer.php'; ?>
