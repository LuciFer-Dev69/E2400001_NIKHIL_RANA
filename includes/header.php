<?php

$base_url = isset($base_url) ? $base_url : "";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillEdu - Learn anything, anywhere</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
    <?php if (isset($portal_context)): ?>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/portal.css">
    <?php
endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="<?php echo isset($portal_context) ? 'portal-active' : ''; ?>">
    <header>
    <script>window.SkillEduConfig = { baseUrl: '<?php echo $base_url; ?>' };</script>
        <div class="nav-container">
            <?php
$logo_link = $base_url . "index.php";
if (isset($portal_context)) {
    if ($portal_context === 'student')
        $logo_link = $base_url . "portals/student/index.php";
    if ($portal_context === 'instructor')
        $logo_link = $base_url . "portals/instructor/index.php";
    if ($portal_context === 'admin')
        $logo_link = $base_url . "portals/admin/index.php";
}
?>
            <a href="<?php echo $logo_link; ?>" class="logo">Skill<span>Edu</span></a>
            
            <div class="nav-item-dropdown">
                <a href="<?php echo $base_url; ?>courses.php" class="nav-link">Explore</a>
                <div class="explore-dropdown">
                    <div class="dropdown-section">
                        <div class="dropdown-section-title">New & Featured</div>
                        <a href="<?php echo $base_url; ?>courses.php?search=google+ai" class="dropdown-item">
                            <span><img src="https://upload.wikimedia.org/wikipedia/commons/2/2f/Google_2015_logo.svg" style="height: 12px; margin-right: 8px;"> Learn AI with Google</span>
                            <i class="fa fa-chevron-right"></i>
                        </a>
                    </div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-section">
                        <div class="dropdown-section-title">Explore by Goal</div>
                        <a href="<?php echo $base_url; ?>courses.php?category=ai" class="dropdown-item"><span>Learn AI</span> <i class="fa fa-chevron-right"></i></a>
                        <a href="<?php echo $base_url; ?>courses.php?search=career" class="dropdown-item"><span>Launch a new career</span> <i class="fa fa-chevron-right"></i></a>
                        <a href="<?php echo $base_url; ?>courses.php?search=certification" class="dropdown-item"><span>Prepare for a certification</span> <i class="fa fa-chevron-right"></i></a>
                    </div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-section">
                        <a href="<?php echo $base_url; ?>courses.php?category=development" class="dropdown-item"><span>Development</span> <i class="fa fa-chevron-right"></i></a>
                        <a href="<?php echo $base_url; ?>courses.php?category=business" class="dropdown-item"><span>Business</span> <i class="fa fa-chevron-right"></i></a>
                        <a href="<?php echo $base_url; ?>courses.php?category=design" class="dropdown-item"><span>Design</span> <i class="fa fa-chevron-right"></i></a>
                        <a href="<?php echo $base_url; ?>courses.php?category=marketing" class="dropdown-item"><span>Marketing</span> <i class="fa fa-chevron-right"></i></a>
                        <a href="<?php echo $base_url; ?>courses.php?category=it-software" class="dropdown-item"><span>IT & Software</span> <i class="fa fa-chevron-right"></i></a>
                        <a href="<?php echo $base_url; ?>courses.php?category=lifestyle" class="dropdown-item"><span>Lifestyle</span> <i class="fa fa-chevron-right"></i></a>
                        <a href="<?php echo $base_url; ?>courses.php?category=photography" class="dropdown-item"><span>Photography & Video</span> <i class="fa fa-chevron-right"></i></a>
                        <a href="<?php echo $base_url; ?>courses.php?category=music" class="dropdown-item"><span>Music</span> <i class="fa fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>
            
            <a href="<?php echo $base_url; ?>subscribe.php" class="btn-subscribe hide-mobile">Subscribe</a>

            <form action="<?php echo $base_url; ?>courses.php" method="GET" class="search-bar">
                <i class="fa fa-search search-icon"></i>
                <input type="text" name="search" placeholder="Search for anything">
            </form>

            <nav class="nav-links">
                <?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])):
    $portal_link = $base_url . 'portals/student/index.php';
    if ($_SESSION['user_role'] === 'instructor')
        $portal_link = $base_url . 'portals/instructor/index.php';
    if ($_SESSION['user_role'] === 'admin')
        $portal_link = $base_url . 'portals/admin/index.php';
?>
                    <?php if (isset($portal_context) && $portal_context === 'student'): ?>
                        <a href="<?php echo $base_url; ?>courses.php" class="nav-link hide-mobile">Browse Courses</a>
                        <a href="<?php echo $portal_link; ?>" class="nav-link">Learning</a>
                        <?php if ($_SESSION['user_role'] === 'instructor'): ?>
                            <a href="<?php echo $base_url; ?>portals/instructor/index.php" class="nav-link hide-mobile" style="color: var(--primary-color); font-weight: 700;">Instructor View</a>
                        <?php
        endif; ?>
                    <?php
    else: ?>
                        <a href="<?php echo $base_url; ?>courses.php" class="nav-link hide-mobile">Browse Courses</a>
                        <a href="<?php echo $portal_link; ?>" class="nav-link">Learning</a>
                    <?php
    endif; ?>
                    
                    <div class="auth-icons">
                        <div style="position: relative;">
                            <a href="#" class="icon-btn" title="Wishlist" id="wishlist-toggle"><i class="far fa-heart"></i></a>
                            <div class="nav-popover" id="wishlist-popover">
                                <h4 style="margin-bottom: 15px; font-size: 16px;">Wishlist</h4>
                                <p style="font-size: 13px; color: #6a6f73; text-align: center; padding: 20px 0;">Your wishlist is empty.</p>
                                <a href="<?php echo $base_url; ?>courses.php" style="display: block; text-align: center; font-weight: 700; color: var(--primary-color); text-decoration: none; font-size: 14px;">Explore courses</a>
                            </div>
                        </div>

                        <div style="position: relative;">
                            <a href="#" class="icon-btn" title="Cart" id="cart-toggle"><i class="fa fa-shopping-cart"></i></a>
                            <div class="nav-popover" id="cart-popover">
                                <h4 style="margin-bottom: 15px; font-size: 16px;">Shopping Cart</h4>
                                <p style="font-size: 13px; color: #6a6f73; text-align: center; padding: 20px 0;">Your cart is empty.</p>
                                <a href="<?php echo $base_url; ?>courses.php" style="display: block; text-align: center; font-weight: 700; color: var(--primary-color); text-decoration: none; font-size: 14px;">Keep shopping</a>
                            </div>
                        </div>

                        <div style="position: relative;">
                            <a href="#" class="icon-btn" title="Notifications" id="bell-toggle"><i class="far fa-bell"></i></a>
                            <div class="nav-popover" id="notifications-popover">
                                <h4 style="margin-bottom: 15px; font-size: 16px;">Notifications</h4>
                                <div style="display: flex; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f7f9fa;">
                                    <div style="width: 40px; height: 40px; background: #ebf6ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #0a84ff;">
                                        <i class="fa fa-info-circle"></i>
                                    </div>
                                    <div>
                                        <div style="font-size: 13px; font-weight: 700;">Welcome to SkillEdu!</div>
                                        <div style="font-size: 12px; color: #6a6f73;">Start your first lesson today.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="user-menu-container" style="position: relative;">
                            <div class="user-avatar" id="user-menu-toggle" style="cursor: pointer;" title="Account Menu">
                                <?php
    $initials = '';
    $fullName = $_SESSION['full_name'] ?? 'User';
    if (!empty($fullName)) {
        $parts = explode(' ', $fullName);
        foreach ($parts as $p) {
            if (!empty($p))
                $initials .= strtoupper($p[0]);
        }
    }
    echo substr($initials, 0, 2);
?>
                            </div>
                            <div class="nav-popover user-dropdown" id="user-menu-popover" style="width: 250px; padding: 0; right: 0;">
                                <div style="padding: 20px; border-bottom: 1px solid #f1f3f5; display: flex; align-items: center; gap: 12px;">
                                    <div class="user-avatar" style="width: 44px; height: 44px; font-size: 16px; margin: 0; flex-shrink: 0;">
                                        <?php echo substr($initials, 0, 2); ?>
                                    </div>
                                    <div style="min-width: 0;">
                                        <div style="font-weight: 800; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #1c1d1f;">
                                            <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?>
                                        </div>
                                        <div style="font-size: 12px; color: #6a6f73; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <?php echo htmlspecialchars($_SESSION['email'] ?? 'student@skilledu.com'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div style="padding: 8px 0;">
                                    <a href="<?php echo $base_url; ?>portals/student/index.php" class="dropdown-item">My Dashboard</a>
                                    <a href="<?php echo $base_url; ?>portals/student/courses.php" class="dropdown-item">Learning</a>
                                    <a href="<?php echo $base_url; ?>cart.php" class="dropdown-item">My Cart</a>
                                    <a href="<?php echo $base_url; ?>wishlist.php" class="dropdown-item">Wishlist</a>
                                </div>
                                <div class="dropdown-divider" style="margin: 0;"></div>
                                <div style="padding: 8px 0;">
                                    <a href="<?php echo $base_url; ?>settings.php" class="dropdown-item">Account Settings</a>
                                    <a href="<?php echo $base_url; ?>purchase_history.php" class="dropdown-item">Purchase History</a>
                                </div>
                                <div class="dropdown-divider" style="margin: 0;"></div>
                                <div style="padding: 8px 16px 16px;">
                                    <a href="<?php echo $base_url; ?>logout.php" class="btn btn-secondary" style="width: 100%; text-align: center; border: 1px solid #d1d7dc; font-weight: 700;">Log Out</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
else: ?>
                    <a href="#" class="nav-link hide-mobile">SkillEdu Business</a>
                    <a href="#" class="nav-link hide-mobile">Teach on SkillEdu</a>
                    <a href="#" class="nav-link"><i class="fa fa-shopping-cart" style="font-size: 18px;"></i></a>
                    
                    <div style="display: flex; gap: 8px; align-items: center; margin-left: 10px;">
                        <a href="<?php echo $base_url; ?>login.php" class="btn btn-secondary">Log in</a>
                        <a href="<?php echo $base_url; ?>signup.php" class="btn btn-gradient" style="border: none;">Sign up</a>
                        <a href="#" class="btn btn-secondary btn-icon-square"><i class="fa fa-globe" style="font-size: 18px;"></i></a>
                    </div>
                <?php
endif; ?>
            </nav>
        </div>
    </header>

    <!-- Category Nav Bar -->
    <?php if (!isset($portal_context)): ?>
    <div class="category-nav hide-mobile">
        <div class="container category-nav-links">
            <a href="<?php echo $base_url; ?>courses.php?category=development" class="cat-nav-link">Development</a>
            <a href="<?php echo $base_url; ?>courses.php?category=business" class="cat-nav-link">Business</a>
            <a href="<?php echo $base_url; ?>courses.php?category=finance-accounting" class="cat-nav-link">Finance & Accounting</a>
            <a href="<?php echo $base_url; ?>courses.php?category=it-software" class="cat-nav-link">IT & Software</a>
            <a href="<?php echo $base_url; ?>courses.php?category=office-productivity" class="cat-nav-link">Office Productivity</a>
            <a href="<?php echo $base_url; ?>courses.php?category=personal-development" class="cat-nav-link">Personal Development</a>
            <a href="<?php echo $base_url; ?>courses.php?category=design" class="cat-nav-link">Design</a>
            <a href="<?php echo $base_url; ?>courses.php?category=marketing" class="cat-nav-link">Marketing</a>
            <a href="<?php echo $base_url; ?>courses.php?category=health-fitness" class="cat-nav-link">Health & Fitness</a>
            <a href="<?php echo $base_url; ?>courses.php?category=music" class="cat-nav-link">Music</a>
        </div>
    </div>
<?php
endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggles = [
            { btn: 'wishlist-toggle', pop: 'wishlist-popover' },
            { btn: 'cart-toggle', pop: 'cart-popover' },
            { btn: 'bell-toggle', pop: 'notifications-popover' },
            { btn: 'user-menu-toggle', pop: 'user-menu-popover' }
        ];

        toggles.forEach(t => {
            const btn = document.getElementById(t.btn);
            const pop = document.getElementById(t.pop);
            if (btn && pop) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    // Close others
                    toggles.forEach(other => {
                        const otherPop = document.getElementById(other.pop);
                        if (other.pop !== t.pop && otherPop) otherPop.style.display = 'none';
                    });
                    pop.style.display = pop.style.display === 'block' ? 'none' : 'block';
                });
            }
        });

        document.addEventListener('click', function() {
            toggles.forEach(t => {
                const pop = document.getElementById(t.pop);
                if (pop) pop.style.display = 'none';
            });
        });
    });
</script>
