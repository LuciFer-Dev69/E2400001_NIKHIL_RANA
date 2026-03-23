<!-- Sidebar component for portals when using global header -->
<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="portal-sidebar">
    <nav>
        <ul class="sidebar-nav">
            <?php if ($portal_type === 'student'): ?>
                <li style="margin-bottom: 5px;">
                    <a href="index.php" class="sidebar-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
                        <i class="fa fa-th-large"></i> Dashboard
                    </a>
                </li>
                <li style="margin-bottom: 5px;">
                    <a href="courses.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'courses.php' ? 'active' : ''; ?>">
                        <i class="fa fa-play-circle"></i> Courses
                    </a>
                </li>
                <li style="margin-bottom: 5px;">
                    <a href="#" class="sidebar-link">
                        <i class="fa fa-heart"></i> Wishlist
                    </a>
                </li>
                <li style="margin-bottom: 5px;">
                    <a href="#" class="sidebar-link">
                        <i class="fa fa-certificate"></i> Certificates
                    </a>
                </li>
            <?php
elseif ($portal_type === 'instructor'): ?>
                <li style="margin-bottom: 5px;">
                    <a href="index.php" class="sidebar-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
                        <i class="fa fa-chart-line"></i> Instructor Hub
                    </a>
                </li>
                <li style="margin-bottom: 5px;">
                    <a href="#" class="sidebar-link">
                        <i class="fa fa-plus-square"></i> Create Course
                    </a>
                </li>
            <?php
elseif ($portal_type === 'admin'): ?>
                <li style="margin-bottom: 5px;">
                    <a href="index.php" class="sidebar-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
                        <i class="fa fa-users-cog"></i> Admin Dashboard
                    </a>
                </li>
                <li style="margin-bottom: 5px;">
                    <a href="#" class="sidebar-link">
                        <i class="fa fa-users"></i> Users
                    </a>
                </li>
                <li style="margin-bottom: 5px;">
                    <a href="#" class="sidebar-link">
                        <i class="fa fa-play-circle"></i> Courses
                    </a>
                </li>
                <li style="margin-bottom: 5px;">
                    <a href="#" class="sidebar-link">
                        <i class="fa fa-chart-line"></i> Analytics
                    </a>
                </li>
            <?php
endif; ?>
            <li style="margin-bottom: 5px;">
                <a href="<?php echo $base_url; ?>settings.php" class="sidebar-link <?php echo $current_page === 'settings.php' ? 'active' : ''; ?>">
                    <i class="fa fa-cog"></i> Settings
                </a>
            </li>
        </ul>
    </nav>
</aside>
