<?php
/**
 * admin_header.php
 * Admin portal shared layout and security enforcement.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. STRICT ROLE VERIFICATION
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die("Access denied. You do not have administrator privileges.");
}

if (!isset($root))
    $root = "../../";
if (!isset($page_title))
    $page_title = 'Admin Dashboard';

$current_page = basename($_SERVER['PHP_SELF']);

// Initial user profile details
$initials = '';
$fullName = $_SESSION['full_name'] ?? 'Admin User';
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
    <title><?php echo htmlspecialchars($page_title); ?> — SkillEdu Admin</title>
    
    <link rel="stylesheet" href="<?php echo $root; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $root; ?>assets/css/portal.css">
    <link rel="stylesheet" href="<?php echo $root; ?>assets/css/admin.css">
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
                if (window.usersChart) window.usersChart.destroy();
                if (window.revenueChart) window.revenueChart.destroy();
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
            <a href="<?php echo $root; ?>portals/admin/index.php" class="logo">Skill<span>Edu</span> <span style="font-size: 14px; background: rgba(229, 57, 53, 0.1); color: var(--primary-color); padding: 4px 8px; border-radius: 4px; margin-left: 10px; font-weight: 800; text-transform: uppercase;">Admin</span></a>
        </div>

        <!-- Global Quick Search -->
        <form action="<?php echo $root; ?>portals/admin/users.php" method="GET" class="search-bar" style="margin: 0 40px; max-width: 500px;">
            <i class="fa fa-search search-icon"></i>
            <input type="text" name="search" placeholder="Search users, courses, or settings...">
        </form>

        <div class="auth-icons" style="display: flex; align-items: center; gap: 20px;">
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
                        <a href="<?php echo $root; ?>portals/admin/notifications.php" style="font-size: 12px; color: var(--primary-color); font-weight: 800; text-decoration: none;">View all announcements</a>
                    </div>
                </div>
            </div>

            <!-- Theme Toggle -->
            <button id="theme-toggle" onclick="toggleTheme(event)" title="Toggle Dark Mode" style="background: var(--light-gray); border: 1px solid var(--border-color); cursor: pointer; font-size: 16px; color: var(--dark-color); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                <i class="far fa-moon" id="theme-icon"></i>
            </button>

            <!-- Profile Account / Logout -->
            <div style="position: relative;">
                <a href="<?php echo $root; ?>logout.php" id="user-menu-toggle" style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 5px 12px; border-radius: 50px; border: 1px solid rgba(231,76,60,0.3); background: rgba(231,76,60,0.05); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='rgba(231,76,60,0.1)'" onmouseout="this.style.background='rgba(231,76,60,0.05)'" title="Logout">
                    <div class="user-avatar" style="width: 32px; height: 32px; font-size: 12px; background: #e74c3c; color: white;">
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
    <!-- ADMIN SIDEBAR NAVIGATION -->
    <aside class="admin-sidebar hide-mobile">
        <div style="padding: 0 32px 15px 32px; font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--gray-color); letter-spacing: 1px;">Main Menu</div>
        
        <a href="index.php" class="admin-nav-item <?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
            <i class="fa fa-chart-pie"></i> Dashboard
        </a>
        <a href="users.php" class="admin-nav-item <?php echo $current_page === 'users.php' || $current_page === 'user_details.php' ? 'active' : ''; ?>">
            <i class="fa fa-users"></i> Users
        </a>
        <a href="courses.php" class="admin-nav-item <?php echo $current_page === 'courses.php' || strpos($current_page, 'course_') !== false ? 'active' : ''; ?>">
            <i class="fa fa-video"></i> Courses
        </a>
        <a href="categories.php" class="admin-nav-item <?php echo $current_page === 'categories.php' ? 'active' : ''; ?>">
            <i class="fa fa-tags"></i> Categories
        </a>
        
        <div style="padding: 25px 32px 15px 32px; font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--gray-color); letter-spacing: 1px;">Engagement</div>
        
        <a href="reviews.php" class="admin-nav-item <?php echo $current_page === 'reviews.php' ? 'active' : ''; ?>">
            <i class="fa fa-star"></i> Reviews
        </a>
        <a href="notifications.php" class="admin-nav-item <?php echo $current_page === 'notifications.php' ? 'active' : ''; ?>">
            <i class="fa fa-bullhorn"></i> Announcements
        </a>
        
        <div style="padding: 25px 32px 15px 32px; font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--gray-color); letter-spacing: 1px;">System</div>
        
        <a href="settings.php" class="admin-nav-item <?php echo $current_page === 'settings.php' ? 'active' : ''; ?>">
            <i class="fa fa-cog"></i> CMS Settings
        </a>
    </aside>

    <!-- ADMIN MAIN CONTENT -->
    </main>
</div>

<!-- Toast Container -->
<div id="toast-container"></div>

<script src="<?php echo $root; ?>assets/js/notifications.js" defer></script>
</body>
</html>
