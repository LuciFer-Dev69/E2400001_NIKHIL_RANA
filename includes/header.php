<?php
include_once __DIR__ . '/lang_helper.php';

// Simple path logic for global consistency
$current_path = $_SERVER['PHP_SELF'];
$base_path = ''; // Default 

if (strpos($current_path, '/views/') !== false) {
    $base_path = '../';
}
if (strpos($current_path, '/views/auth/') !== false) {
    $base_path = '../../';
}
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillStack - Modern Marketplace</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Custom CSS (Member Specific) -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/Nikhil.css?v=1.1">
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/Aditya.css?v=1.1">
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/Manav.css?v=1.1">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-skillstack sticky-top">
    <div class="container-fluid px-lg-5">
        <a class="navbar-brand fw-bold me-4" href="<?php echo $base_path; ?>index.php">
            <span class="text-red">Skill</span>Stack
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item mega-menu-trigger">
                    <a class="nav-link px-3" href="#"><?php echo __('explore'); ?></a>
                    <!-- Mega Menu Content -->
                    <div class="mega-menu shadow-lg border">
                        <div class="mega-menu-flex">
                            <!-- Column 1: Categories -->
                            <div class="col-menu py-3">
                                <ul class="list-unstyled mb-0" id="main-cat-list">
                                    <li class="menu-cat px-3 py-2 d-flex justify-content-between align-items-center active" data-target="#sub-dev">
                                        <span>Development</span> <i class="bi bi-chevron-right small"></i>
                                    </li>
                                    <li class="menu-cat px-3 py-2 d-flex justify-content-between align-items-center" data-target="#sub-bus">
                                        <span>Business</span> <i class="bi bi-chevron-right small"></i>
                                    </li>
                                    <li class="menu-cat px-3 py-2 d-flex justify-content-between align-items-center" data-target="#sub-it">
                                        <span>IT & Software</span> <i class="bi bi-chevron-right small"></i>
                                    </li>
                                    <li class="menu-cat px-3 py-2 d-flex justify-content-between align-items-center" data-target="#sub-des">
                                        <span>Design</span> <i class="bi bi-chevron-right small"></i>
                                    </li>
                                    <li class="px-1"><hr class="dropdown-divider"></li>
                                    <li class="menu-cat px-3 py-2 d-flex justify-content-between align-items-center" data-target="#sub-mark">
                                        <span>Marketing</span> <i class="bi bi-chevron-right small"></i>
                                    </li>
                                </ul>
                            </div>
                            
                            <!-- Column 2 & 3: Dynamic Sub-menus -->
                            <div class="flex-grow-1 h-100" id="sub-menu-container">
                                <!-- Sub: Development -->
                                <div class="sub-menu-content p-4 h-100" id="sub-dev">
                                    <div class="row">
                                        <div class="col-6 border-end">
                                            <h6 class="small fw-bold text-muted text-uppercase mb-3">Popular Topics</h6>
                                            <ul class="list-unstyled sub-links">
                                                <li><a href="#">Web Development <span class="badge rounded-pill bg-danger x-small ms-1">HOT</span></a></li>
                                                <li><a href="#">JavaScript</a></li>
                                                <li><a href="#">Python</a></li>
                                                <li><a href="#">React JS</a></li>
                                                <li><a href="#">PHP/MySQL</a></li>
                                            </ul>
                                        </div>
                                        <div class="col-6">
                                            <h6 class="small fw-bold text-muted text-uppercase mb-3">Professional Paths</h6>
                                            <ul class="list-unstyled sub-links">
                                                <li><a href="#">Full Stack Developer</a></li>
                                                <li><a href="#">Data Science <span class="badge rounded-pill bg-dark x-small ms-1">New</span></a></li>
                                                <li><a href="#">Mobile Development</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <!-- Sub: Business -->
                                <div class="sub-menu-content p-4 h-100 d-none" id="sub-bus">
                                    <div class="row">
                                        <div class="col-6 border-end">
                                            <h6 class="small fw-bold text-muted text-uppercase mb-3">Management</h6>
                                            <ul class="list-unstyled sub-links">
                                                <li><a href="#">Leadership</a></li>
                                                <li><a href="#">Project Management</a></li>
                                                <li><a href="#">Communication</a></li>
                                            </ul>
                                        </div>
                                        <div class="col-6">
                                            <h6 class="small fw-bold text-muted text-uppercase mb-3">Strategy</h6>
                                            <ul class="list-unstyled sub-links">
                                                <li><a href="#">Entrepreneurship</a></li>
                                                <li><a href="#">Business Strategy</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <!-- Sub: IT -->
                                <div class="sub-menu-content p-4 h-100 d-none" id="sub-it">
                                    <h6 class="small fw-bold text-muted text-uppercase mb-3">Certifications</h6>
                                    <ul class="list-unstyled sub-links">
                                        <li><a href="#">AWS Certification</a></li>
                                        <li><a href="#">Cyber Security</a></li>
                                        <li><a href="#">Network & Security</a></li>
                                    </ul>
                                </div>
                                <!-- Sub: Design -->
                                <div class="sub-menu-content p-4 h-100 d-none" id="sub-des">
                                    <div class="row">
                                        <div class="col-6 border-end">
                                            <h6 class="small fw-bold text-muted text-uppercase mb-3">Graphic & UI/UX</h6>
                                            <ul class="list-unstyled sub-links">
                                                <li><a href="#">UI/UX Design <span class="badge rounded-pill bg-danger x-small ms-1">HOT</span></a></li>
                                                <li><a href="#">Graphic Design</a></li>
                                                <li><a href="#">Photoshop</a></li>
                                                <li><a href="#">Illustrator</a></li>
                                            </ul>
                                        </div>
                                        <div class="col-6">
                                            <h6 class="small fw-bold text-muted text-uppercase mb-3">Visual Arts</h6>
                                            <ul class="list-unstyled sub-links">
                                                <li><a href="#">Drawing</a></li>
                                                <li><a href="#">Character Design</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <!-- Sub: Marketing -->
                                <div class="sub-menu-content p-4 h-100 d-none" id="sub-mark">
                                    <div class="row">
                                        <div class="col-6 border-end">
                                            <h6 class="small fw-bold text-muted text-uppercase mb-3">Digital Marketing</h6>
                                            <ul class="list-unstyled sub-links">
                                                <li><a href="#">Digital Marketing Masterclass <span class="badge rounded-pill bg-danger x-small ms-1">HOT</span></a></li>
                                                <li><a href="#">Social Media Marketing</a></li>
                                                <li><a href="#">SEO Training</a></li>
                                            </ul>
                                        </div>
                                        <div class="col-6">
                                            <h6 class="small fw-bold text-muted text-uppercase mb-3">Advertising</h6>
                                            <ul class="list-unstyled sub-links">
                                                <li><a href="#">Google Ads (PPC)</a></li>
                                                <li><a href="#">Facebook Ads</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link px-3 <?php echo(strpos($current_path, 'business.php') !== false) ? 'active' : ''; ?>" href="<?php echo $base_path; ?>views/business.php"><?php echo __('business'); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 <?php echo(strpos($current_path, 'teach.php') !== false) ? 'active' : ''; ?>" href="<?php echo $base_path; ?>views/teach.php"><?php echo __('teach'); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 <?php echo(strpos($current_path, 'subscribe.php') !== false) ? 'active' : ''; ?>" href="<?php echo $base_path; ?>views/subscribe.php"><?php echo __('subscribe'); ?></a>
                </li>
                <li class="nav-item px-2 border-start ms-2">
                    <a class="nav-link lang-switcher-trigger" href="#" data-bs-toggle="modal" data-bs-target="#languageModal">
                        <i class="bi bi-globe"></i>
                    </a>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link" href="#"><i class="bi bi-cart3"></i></a>
                </li>
                <li class="nav-item ms-lg-3">
                    <a href="<?php echo $base_path; ?>views/auth/login.php" class="btn btn-outline-dark rounded-pill px-4 <?php echo(strpos($current_path, 'login.php') !== false) ? 'active-btn' : ''; ?>"><?php echo __('login'); ?></a>
                </li>
                <li class="nav-item ms-lg-2">
                    <a href="<?php echo $base_path; ?>views/auth/register.php" class="btn btn-dark rounded-pill px-4 <?php echo(strpos($current_path, 'register.php') !== false) ? 'active-btn shadow-none' : ''; ?>"><?php echo __('signup'); ?></a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Language Selection Modal -->
<div class="modal fade" id="languageModal" tabindex="-1" aria-labelledby="languageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold fs-6" id="languageModalLabel"><?php echo __('choose_lang'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 pt-2">
                <ul class="list-unstyled lang-list mb-0">
                    <li class="<?php echo($_SESSION['lang'] == 'en') ? 'active' : ''; ?>"><a href="?lang=en">English</a></li>
                    <li class="<?php echo($_SESSION['lang'] == 'hi') ? 'active' : ''; ?>"><a href="?lang=hi">Hindi (हिन्दी)</a></li>
                    <li class="<?php echo($_SESSION['lang'] == 'es') ? 'active' : ''; ?>"><a href="?lang=es">Español</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="<?php echo $base_path; ?>assets/js/Nikhil.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
