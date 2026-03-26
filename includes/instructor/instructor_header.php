<?php
/**
 * instructor_header.php
 * Instructor portal shared layout and security enforcement.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// STRICT ROLE VERIFICATION
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'instructor') {
    die("Access denied. You must be an approved Instructor to view this portal.");
}

if (!isset($root))
    $root = "../../";
if (!isset($page_title))
    $page_title = 'Instructor Dashboard';

$current_page = basename($_SERVER['PHP_SELF']);

// Initial user profile details
$initials = '';
$fullName = $_SESSION['full_name'] ?? 'Instructor';
foreach (explode(' ', $fullName) as $p) {
    if (!empty($p))
        $initials .= strtoupper($p[0]);
}
$initials = substr($initials, 0, 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> — SkillEdu Instructor</title>
    
    <link rel="stylesheet" href="<?php echo $root; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $root; ?>assets/css/portal.css">
    <link rel="stylesheet" href="<?php echo $root; ?>assets/css/admin.css"> <!-- Reusing admin structural css -->
    <link rel="stylesheet" href="<?php echo $root; ?>assets/css/dark-mode.css">
    <link rel="stylesheet" href="<?php echo $root; ?>assets/css/notifications.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="<?php echo $root; ?>assets/js/global-sync.js" defer></script>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        const savedTheme = localStorage.getItem('skilledu_theme');
        if (savedTheme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
        
        window.toggleTheme = function(e) {
            if (e) e.preventDefault();
            const root = document.documentElement;
            const currentTheme = root.getAttribute('data-theme');
            const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', nextTheme);
            localStorage.setItem('skilledu_theme', nextTheme);
            
            const icon = document.getElementById('theme-icon');
            if (icon) icon.className = nextTheme === 'dark' ? 'fas fa-sun' : 'far fa-moon';

            if (typeof initCharts === 'function') {
                if (window.analyticsChart) window.analyticsChart.destroy();
                initCharts();
            }
        };
    </script>
</head>
<body class="portal-active">

<header>
    <div class="nav-container">
        <!-- Brand -->
        <div style="display: flex; align-items: center; gap: 20px;">
            <a href="<?php echo $root; ?>portals/instructor/index.php" class="logo">Skill<span>Edu</span> <span style="font-size: 14px; background: rgba(155, 89, 182, 0.1); color: #9b59b6; padding: 4px 8px; border-radius: 4px; margin-left: 10px; font-weight: 800; text-transform: uppercase;">Creator</span></a>
        </div>

        <div class="auth-icons" style="margin-left: auto; display: flex; align-items: center; gap: 20px;">
            <a href="<?php echo $root; ?>index.php" target="_blank" class="btn" style="background: none; border: 1px solid var(--border-color); color: var(--dark-color); padding: 8px 15px;">View Main Site</a>
            
            <!-- Notification Bell -->
            <div class="notification-wrapper">
                <button id="notif-bell" style="background: var(--light-gray); border: 1px solid var(--border-color); cursor: pointer; font-size: 16px; color: var(--dark-color); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; position: relative; transition: all 0.2s;">
                    <i class="far fa-bell"></i>
                    <span id="notif-badge" class="notification-badge">0</span>
                </button>
                <div id="notif-dropdown" class="notification-dropdown">
                    <div class="notification-header">
                        <h4>Notifications</h4>
                        <button onclick="markAllRead()" style="background: none; border: none; font-size: 11px; color: var(--primary-color); font-weight: 800; cursor: pointer;">Mark all as read</button>
                    </div>
                    <div id="notif-list" class="notification-list">
                        <!-- Loaded via JS -->
                        <div style="padding: 30px; text-align: center; color: var(--gray-color); font-size: 13px;">Loading...</div>
                    </div>
                    <div class="notification-footer">
                        <a href="<?php echo $root; ?>portals/instructor/notifications.php" style="font-size: 12px; color: var(--primary-color); font-weight: 800; text-decoration: none;">View all updates</a>
                    </div>
                </div>
            </div>

            <!-- Theme Toggle -->
            <button id="theme-toggle" onclick="toggleTheme(event)" title="Toggle Dark Mode" style="background: var(--light-gray); border: 1px solid var(--border-color); cursor: pointer; font-size: 16px; color: var(--dark-color); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                <i class="far fa-moon" id="theme-icon"></i>
            </button>

            <!-- Logout Direct Link -->
            <div style="position: relative;">
                <a href="<?php echo $root; ?>logout.php" id="user-menu-toggle" style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 5px 12px; border-radius: 50px; border: 1px solid rgba(231,76,60,0.3); background: rgba(231,76,60,0.05); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='rgba(231,76,60,0.1)'" onmouseout="this.style.background='rgba(231,76,60,0.05)'" title="Logout">
                    <div class="user-avatar" style="width: 32px; height: 32px; font-size: 12px; background: #9b59b6; color: white;">
                        <span id="navbar-user-initials"><?php echo $initials; ?></span>
                    </div>
                    <span id="navbar-user-name" style="font-weight: 700; font-size: 14px; color: #e74c3c;" class="hide-mobile"><?php echo htmlspecialchars($fullName); ?></span>
                    <i class="fa fa-sign-out-alt" style="font-size: 14px; color: #e74c3c;"></i>
                </a>
            </div>
        </div>
    </div>
</header>

<div class="admin-shell">
    <!-- INSTRUCTOR SIDEBAR NAVIGATION -->
    <aside class="admin-sidebar hide-mobile">
        <div style="padding: 0 32px 15px 32px; font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--gray-color); letter-spacing: 1px;">Creator Studio</div>
        
        <a href="index.php" class="admin-nav-item <?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
            <i class="fa fa-chart-line"></i> Analytics
        </a>
        <a href="courses.php" class="admin-nav-item <?php echo $current_page === 'courses.php' ? 'active' : ''; ?>">
            <i class="fa fa-video"></i> My Courses
        </a>
        <a href="create_course.php" class="admin-nav-item <?php echo $current_page === 'create_course.php' || strpos($current_page, 'builder') !== false ? 'active' : ''; ?>">
            <i class="fa fa-plus-circle"></i> Create Course
        </a>
        
        <div style="padding: 25px 32px 15px 32px; font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--gray-color); letter-spacing: 1px;">Community</div>
        
        <a href="students.php" class="admin-nav-item <?php echo $current_page === 'students.php' ? 'active' : ''; ?>">
            <i class="fa fa-users"></i> My Students
        </a>
        <a href="qa.php" class="admin-nav-item <?php echo $current_page === 'qa.php' ? 'active' : ''; ?>">
            <i class="fa fa-comments"></i> Q&A Messages
        </a>
        
        <div style="padding: 25px 32px 15px 32px; font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--gray-color); letter-spacing: 1px;">Account</div>
        <a href="settings.php" class="admin-nav-item <?php echo $current_page === 'settings.php' ? 'active' : ''; ?>">
            <i class="fa fa-user-cog"></i> Profile Settings
        </a>
        <a href="subscription.php" class="admin-nav-item <?php echo $current_page === 'subscription.php' ? 'active' : ''; ?>">
            <i class="fa fa-credit-card"></i> Plan & Billing
        </a>
    </aside>

    <!-- MAIN CONTENT -->
    </main>
</div>

<!-- Toast Container -->
<div id="toast-container"></div>

<script src="<?php echo $root; ?>assets/js/notifications.js" defer></script>
</body>
</html>
