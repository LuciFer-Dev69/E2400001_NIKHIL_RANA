<?php
/**
 * portal_header.php
 * 
 * THE ONE TRUE portal layout header. This replaces the public header.php for
 * ALL portal pages (student, instructor, admin). 
 * 
 * Required variables before including:
 *   $portal_type  = 'student' | 'instructor' | 'admin'
 *   $page_title   = (optional) browser tab title
 *   $root         = path back to EMS1 root, e.g. "../../"
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($root))
    $root = "../../";
if (!isset($portal_type))
    $portal_type = 'student';
if (!isset($page_title))
    $page_title = 'Dashboard';

$current_page = basename($_SERVER['PHP_SELF']);

// Build initials for avatar
$initials = '';
$fullName = $_SESSION['full_name'] ?? ($_SESSION['user_name'] ?? 'User');
foreach (explode(' ', $fullName) as $p) {
    if (!empty($p))
        $initials .= strtoupper($p[0]);
}
$initials = substr($initials, 0, 2);

$portal_dashboard_link = $root . 'portals/student/index.php';
if ($portal_type === 'instructor')
    $portal_dashboard_link = $root . 'portals/instructor/index.php';
if ($portal_type === 'admin')
    $portal_dashboard_link = $root . 'portals/admin/index.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> — SkillEdu</title>
    <link rel="stylesheet" href="<?php echo $root; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $root; ?>assets/css/portal.css">
    <script src="<?php echo $root; ?>assets/js/responsive.js" defer></script>
    <link rel="stylesheet" href="<?php echo $root; ?>assets/css/dark-mode.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        const savedTheme = localStorage.getItem('skilledu_theme');
        if (savedTheme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
        
        // Global Theme Toggle
        window.toggleTheme = function(e) {
            if (e) e.preventDefault();
            const root = document.documentElement;
            const currentTheme = root.getAttribute('data-theme');
            const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', nextTheme);
            localStorage.setItem('skilledu_theme', nextTheme);
            
            // Update icons
            const icon = document.getElementById('theme-icon');
            if (icon) icon.className = nextTheme === 'dark' ? 'fas fa-sun' : 'far fa-moon';

            // Refresh charts if any (e.g. Dashboard)
            if (typeof initCharts === 'function') {
                if (typeof learningChart !== 'undefined' && learningChart) learningChart.destroy();
                if (typeof goalChart !== 'undefined' && goalChart) goalChart.destroy();
                initCharts();
            }
        };
    </script>
</head>
<body class="portal-active">

<script>window.SkillEduConfig = { baseUrl: '<?php echo $root; ?>' };</script>

<!-- 1. FULL-WIDTH HEADER AT THE TOP LEVEL -->
<header>
    <div class="nav-container">
        <!-- LEFT: Logo & Brand links -->
        <div style="display: flex; align-items: center; gap: 20px;">
            <button id="sidebar-toggle" class="mobile-toggle-btn" style="background: none; border: none; font-size: 22px; color: var(--dark-color); cursor: pointer; display: none;">
                <i class="fa fa-bars"></i>
            </button>
            <a href="<?php echo $root; ?>index.php" class="logo">Skill<span>Edu</span></a>
            
            <div class="nav-links hide-mobile" style="margin-left: 10px;">
                <a href="<?php echo $root; ?>courses.php" class="nav-link">Explore</a>
                <a href="#" class="nav-link">Subscribe</a>
            </div>
        </div>

        <!-- CENTER: Search bar (Pill style) -->
        <form action="<?php echo $root; ?>courses.php" method="GET" class="search-bar" style="margin: 0 40px;">
            <i class="fa fa-search search-icon"></i>
            <input type="text" name="search" placeholder="Search for anything...">
        </form>

        <div class="auth-icons" style="display: flex; align-items: center; gap: 20px;">
            <div class="nav-links hide-mobile">
                <a href="<?php echo $root; ?>courses.php" class="nav-link">Browse Courses</a>
                <a href="<?php echo $root; ?>portals/student/courses.php" class="nav-link">Learning</a>
            </div>

            <!-- Theme Toggles & Notifs -->
            <button id="theme-toggle" onclick="toggleTheme(event)" title="Toggle Dark Mode" style="background: none; border: none; cursor: pointer; font-size: 18px; color: var(--dark-color); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="far fa-moon" id="theme-icon"></i>
            </button>

            <div style="position: relative;">
                <button id="bell-toggle" title="Notifications" style="background: none; border: none; cursor: pointer; font-size: 18px; color: var(--dark-color); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="far fa-bell"></i>
                    <span id="notification-badge" style="display: none; position: absolute; top: 4px; right: 4px; background: #e74c3c; width: 10px; height: 10px; border-radius: 50%; border: 2px solid white;"></span>
                </button>
                <div id="notifications-popover" class="nav-popover" style="opacity: 0; visibility: hidden; right: 0; width: 320px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <h4 style="margin: 0; font-size: 15px;">Notifications</h4>
                        <button id="mark-read-btn" style="background: none; border: none; font-size: 12px; color: var(--primary-color); cursor: pointer; font-weight: 700; display: none;">Mark all read</button>
                    </div>
                    <div id="notifications-list">
                        <div style="text-align: center; padding: 20px 0; color: #6a6f73; font-size: 13px;">Loading...</div>
                    </div>
                </div>
            </div>

            <!-- Profile Account -->
            <div style="position: relative;">
                <div id="user-menu-toggle" class="user-avatar" style="cursor: pointer;" title="Account">
                    <?php echo $initials; ?>
                </div>
                <div id="user-menu-popover" class="nav-popover" style="opacity: 0; visibility: hidden; right: 0; width: 220px; padding: 0;">
                    <div style="padding: 15px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px;">
                        <div class="user-avatar" style="width: 40px; height: 40px; font-size: 14px; flex-shrink: 0;"><?php echo $initials; ?></div>
                        <div>
                            <div style="font-weight: 800; font-size: 14px;"><?php echo htmlspecialchars($fullName); ?></div>
                            <div style="font-size: 12px; color: #6a6f73;"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></div>
                        </div>
                    </div>
                    <div style="padding: 8px 0;">
                        <a href="settings.php" class="dropdown-item">Profile &amp; Settings</a>
                        <a href="<?php echo $root; ?>courses.php" class="dropdown-item">Browse Courses</a>
                    </div>
                    <div style="border-top: 1px solid var(--border-color); padding: 8px 16px 12px;">
                        <a href="<?php echo $root; ?>logout.php" style="display: block; text-align: center; background: #e74c3c; color: white; padding: 10px; border-radius: 4px; font-weight: 700; font-size: 14px; text-decoration: none;">
                            <i class="fa fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- 2. SHELL WRAPS SIDEBAR AND MAIN CONTENT -->
<div class="portal-shell">
    <aside class="portal-sidebar">
        <nav>
            <ul class="sidebar-nav">
                <?php if ($portal_type === 'student'): ?>
                    <li><a href="index.php" class="sidebar-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>"><i class="fa fa-th-large"></i> Dashboard</a></li>
                    <li><a href="courses.php" class="sidebar-link <?php echo $current_page === 'courses.php' ? 'active' : ''; ?>"><i class="fa fa-play-circle"></i> My Courses</a></li>
                    <li><a href="planner.php" class="sidebar-link <?php echo $current_page === 'planner.php' ? 'active' : ''; ?>"><i class="fa fa-calendar-alt"></i> Planner</a></li>
                    <li><a href="leaderboard.php" class="sidebar-link <?php echo $current_page === 'leaderboard.php' ? 'active' : ''; ?>"><i class="fa fa-trophy"></i> Leaderboard</a></li>
                    <li><a href="certificates.php" class="sidebar-link <?php echo $current_page === 'certificates.php' ? 'active' : ''; ?>"><i class="fa fa-certificate"></i> Certificates</a></li>
                    <li><a href="settings.php" class="sidebar-link <?php echo $current_page === 'settings.php' ? 'active' : ''; ?>"><i class="fa fa-cog"></i> Settings</a></li>
                <?php
endif; ?>
                <li style="margin-top: 30px; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 15px;">
                    <a href="<?php echo $root; ?>logout.php" class="sidebar-link" style="color: #e74c3c;"><i class="fa fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </nav>
    </aside>

    <main class="portal-main">
        <div class="portal-content">
<?php
// Page content follows...
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Utility elements
    const bellToggle = document.getElementById('bell-toggle');
    const notifPopover = document.getElementById('notifications-popover');
    const userMenuToggle = document.getElementById('user-menu-toggle');
    const userMenuPopover = document.getElementById('user-menu-popover');

    function closeAllPopovers() {
        if (notifPopover) { notifPopover.style.opacity = '0'; notifPopover.style.visibility = 'hidden'; }
        if (userMenuPopover) { userMenuPopover.style.opacity = '0'; userMenuPopover.style.visibility = 'hidden'; }
    }

    if (bellToggle) {
        bellToggle.addEventListener('click', e => {
            e.stopPropagation();
            const isVisible = notifPopover.style.visibility === 'visible';
            closeAllPopovers();
            if (!isVisible) { notifPopover.style.opacity = '1'; notifPopover.style.visibility = 'visible'; }
        });
    }

    if (userMenuToggle) {
        userMenuToggle.addEventListener('click', e => {
            e.stopPropagation();
            const isVisible = userMenuPopover.style.visibility === 'visible';
            closeAllPopovers();
            if (!isVisible) { userMenuPopover.style.opacity = '1'; userMenuPopover.style.visibility = 'visible'; }
        });
    }

    document.addEventListener('click', closeAllPopovers);

    // Initial Icon Sync
    const icon = document.getElementById('theme-icon');
    if (icon && localStorage.getItem('skilledu_theme') === 'dark') {
        icon.className = 'fas fa-sun';
    }

    // Load Notifications Logic
    const baseUrl = '<?php echo $root; ?>';
    function loadNotifications() {
        fetch(baseUrl + 'api/notifications.php?action=get')
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;
            const badge = document.getElementById('notification-badge');
            const markBtn = document.getElementById('mark-read-btn');
            badge.style.display = data.unread_count > 0 ? 'block' : 'none';
            if (markBtn) markBtn.style.display = data.unread_count > 0 ? 'block' : 'none';
            const list = document.getElementById('notifications-list');
            if (data.notifications.length === 0) {
                list.innerHTML = '<div style="text-align:center;padding:20px 0;color:#6a6f73;font-size:13px;">No alerts.</div>';
            } else {
                list.innerHTML = data.notifications.map(n => `
                    <div style="display:flex;gap:10px;padding:10px 0;border-bottom:1px solid #f7f9fa;${n.is_read?'opacity:0.6':''}">
                        <div style="width:30px;height:30px;background:#ebf6ff;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#0a84ff;flex-shrink:0;">
                            <i class="fa fa-bell" style="font-size:12px;"></i>
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:700;">${n.title}</div>
                            <div style="font-size:12px;color:#6a6f73;">${n.message}</div>
                        </div>
                    </div>`).join('');
            }
        });
    }
    loadNotifications();
    setInterval(loadNotifications, 60000);

    document.getElementById('mark-read-btn')?.addEventListener('click', e => {
        e.stopPropagation();
        fetch(baseUrl + 'api/notifications.php?action=mark_read').then(() => loadNotifications());
    });
});
</script>
