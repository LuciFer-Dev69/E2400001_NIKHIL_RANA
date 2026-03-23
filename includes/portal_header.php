<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$root = "../../"; // Assuming being called from portals/[role]/index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SkillEdu</title>
    <link rel="stylesheet" href="<?php echo $root; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="portal-wrapper">
        <!-- Sidebar -->
        <aside class="portal-sidebar">
            <div class="sidebar-logo">
                <a href="<?php echo $root; ?>index.php" style="color: white; text-decoration: none;">Skill<span>Edu</span></a>
            </div>
            <nav>
                <ul class="sidebar-nav">
                    <?php if ($portal_type === 'student'): ?>
                        <li><a href="index.php" class="sidebar-link active"><i class="fa fa-th-large"></i> Dashboard</a></li>
                        <li><a href="#" class="sidebar-link"><i class="fa fa-play-circle"></i> My Courses</a></li>
                        <li><a href="#" class="sidebar-link"><i class="fa fa-heart"></i> Wishlist</a></li>
                        <li><a href="#" class="sidebar-link"><i class="fa fa-certificate"></i> Certificates</a></li>
                        <li><a href="#" class="sidebar-link"><i class="fa fa-message"></i> Messages</a></li>
                    <?php
elseif ($portal_type === 'instructor'): ?>
                        <li><a href="index.php" class="sidebar-link active"><i class="fa fa-chart-line"></i> Instructor Hub</a></li>
                        <li><a href="#" class="sidebar-link"><i class="fa fa-plus-square"></i> Create Course</a></li>
                        <li><a href="#" class="sidebar-link"><i class="fa fa-film"></i> My Content</a></li>
                        <li><a href="#" class="sidebar-link"><i class="fa fa-users"></i> Students</a></li>
                    <?php
endif; ?>
                    <li><a href="<?php echo $root; ?>settings.php" class="sidebar-link"><i class="fa fa-cog"></i> Settings</a></li>
                </ul>
            </nav>
        </aside>

        <main class="portal-main">
            <!-- Topbar -->
            <header class="portal-topbar">
                <div class="auth-icons">
                    <span style="font-weight: 600; font-size: 14px; color: var(--dark-color);">@<?php echo $_SESSION['username'] ?? 'user'; ?></span>
                    <a href="#" class="icon-btn"><i class="far fa-bell"></i></a>
                    <div class="user-avatar">
                        <?php
$initials = '';
if (isset($_SESSION['full_name'])) {
    $parts = explode(' ', $_SESSION['full_name']);
    foreach ($parts as $p) {
        if (!empty($p))
            $initials .= strtoupper($p[0]);
    }
}
echo substr($initials, 0, 2);
?>
                    </div>
                    <a href="<?php echo $root; ?>logout.php" class="icon-btn" title="Logout"><i class="fa fa-sign-out-alt"></i></a>
                </div>
            </header>

            <div class="portal-content">
