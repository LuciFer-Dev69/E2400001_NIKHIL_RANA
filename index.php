<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="py-5 reveal-on-scroll">
    <div class="container py-lg-5">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-3 fw-bold mb-4" style="font-family: 'Outfit';">
                    <?php echo __('hero_title'); ?>
                </h1>
                <p class="lead  mb-4">
                    <?php echo __('hero_subtitle'); ?>
                </p>
                <div class="d-flex gap-3">
                    <a href="#" class="btn btn-red btn-lg px-5"><?php echo __('btn_explore'); ?></a>
                    <a href="#" class="btn btn-outline-dark btn-lg px-5"><?php echo __('btn_teach'); ?></a>
                </div>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0">
                <!-- Image Removed -->
                <div class="glass-panel rose-placeholder shadow-2xl d-flex align-items-center justify-content-center" style="height: 480px; border-radius: 30px;">
                    <span class="text-muted fw-bold opacity-25">SKILLSTACK</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trusted By Section (Text Only) -->
<div class="logo-slider border-top border-bottom py-4">
    <div class="container text-center">
        <div class=" mb-3 small ">Trusted by over 17,000 companies and millions of learners around the world</div>
        <div class="d-flex flex-wrap justify-content-center gap-4 fw-bold opacity-50">
            <span>SAMSUNG</span> <span>CISCO</span> <span>VIMEO</span> <span>HP</span> <span>ERICSSON</span>
        </div>
    </div>
</div>

<!-- Skills Showcase Section -->
<section class="py-5 bg-transparent reveal-on-scroll">
    <div class="container">
        <h2 class="fw-bold mb-2">Skills to transform your career and life</h2>
        <p class=" mb-4">From critical skills to technical topics, SkillStack supports your professional development.</p>
        
        <!-- Tabs -->
        <ul class="nav nav-tabs nav-tabs-eduskill mb-4 border-0">
            <li class="nav-item">
                <a class="nav-link active" href="#" data-target="#cat-ai">Artificial Intelligence (AI)</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-target="#cat-python">Python</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-target="#cat-excel">Microsoft Excel</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-target="#cat-marketing">Digital Marketing</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-target="#cat-aws">Amazon AWS</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-target="#cat-bootcamp">Bootcamps</a>
            </li>
        </ul>

        <!-- Course Grids for each Category -->
        <?php
$categories = [
    "cat-ai" => [
        ["title" => "The AI Engineer Course 2026: Complete Bootcamp", "inst" => "365 Careers", "price" => "$59.99", "badge" => "Bestseller"],
        ["title" => "The Complete AI Coding Course (2025) - Cursor", "inst" => "Brendan AI", "price" => "$19.99", "badge" => "Bestseller"],
        ["title" => "2026 Bootcamp: Generative AI, LLM Apps", "inst" => "Julio Colomer", "price" => "$54.99", "badge" => "Premium"],
        ["title" => "AI for Business Analysts - The Complete Course", "inst" => "George Smarts", "price" => "$27.99", "badge" => "Bestseller"],
    ],
    "cat-python" => [
        ["title" => "Python for Data Science & Machine Learning", "inst" => "Jose Portilla", "price" => "$89.99", "badge" => "Bestseller"],
        ["title" => "Learn Python Programming from Scratch", "inst" => "Colt Steele", "price" => "$14.99", "badge" => "Classic"],
        ["title" => "Automate the Boring Stuff with Python", "inst" => "Al Sweigart", "price" => "$19.99", "badge" => "Bestseller"],
        ["title" => "Advanced Python: Deep Dive", "inst" => "Fred Baptiste", "price" => "$49.99", "badge" => "Expert"],
    ],
    "cat-excel" => [
        ["title" => "Microsoft Excel: Zero to Hero in 10 Days", "inst" => "Kyle Pew", "price" => "$12.99", "badge" => "Bestseller"],
        ["title" => "Data Analysis with Excel Pivot Tables", "inst" => "Chris Dutton", "price" => "$19.99", "badge" => "Popular"],
        ["title" => "Advanced Excel Formulas & Functions", "inst" => "Leila Gharani", "price" => "$24.99", "badge" => "Expert"],
        ["title" => "Excel VBA & Macros Bootcamp", "inst" => "John Michaloudis", "price" => "$29.99", "badge" => "Premium"],
    ],
    "cat-marketing" => [
        ["title" => "The Ultimate Google Ads Training", "inst" => "Isaac Rudansky", "price" => "$19.99", "badge" => "Bestseller"],
        ["title" => "Social Media Marketing Mastery", "inst" => "COURSENVY", "price" => "$14.99", "badge" => "Bestseller"],
        ["title" => "SEO 2026: Complete SEO Training", "inst" => "Arun Nagarathanam", "price" => "$12.99", "badge" => "Classic"],
        ["title" => "Digital Marketing Strategy with AI", "inst" => "Anton Voroniuk", "price" => "$24.99", "badge" => "Popular"],
    ],
    "cat-aws" => [
        ["title" => "AWS Certified Solutions Architect Associate", "inst" => "Stephane Maarek", "price" => "$18.99", "badge" => "Bestseller"],
        ["title" => "Ultimate AWS Certified Developer Associate", "inst" => "Stephane Maarek", "price" => "$17.99", "badge" => "Premium"],
        ["title" => "AWS Certified Cloud Practitioner", "inst" => "Neal Davis", "price" => "$12.99", "badge" => "Bestseller"],
        ["title" => "Amazon AWS: Serverless Architecture", "inst" => "Ryan Kroonenburg", "price" => "$29.99", "badge" => "Expert"],
    ],
    "cat-bootcamp" => [
        ["title" => "Python for Data Science Bootcamp 2026", "inst" => "Nikhil Rana", "price" => "$99.99", "badge" => "Featured"],
        ["title" => "C++ Systems Programming Masterclass", "inst" => "Aditya Jaiwal", "price" => "$89.99", "badge" => "Expert"],
        ["title" => "Go Lang: Scalable Backend Services", "inst" => "Manav Rawal", "price" => "$79.99", "badge" => "New"],
        ["title" => "The Full Stack Web Bootcamp (MERN)", "inst" => "Colt Steele", "price" => "$19.99", "badge" => "Bestseller"],
    ],
];

foreach ($categories as $id => $courses):
    $hiddenClass = ($id === "cat-ai") ? "" : "d-none";
?>
        <div id="<?php echo $id; ?>" class="course-category-grid row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mb-4 <?php echo $hiddenClass; ?>">
            <?php foreach ($courses as $c): ?>
            <div class="col">
                <div class="card h-100 course-card p-0 border shadow-sm glass-panel">
                    <div class="course-thumb rose-placeholder d-flex align-items-center justify-content-center" style="height: 160px;">
                        <i class="bi bi-journal-text display-6 opacity-25"></i>
                    </div>
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-1 text-truncate-2" style="height: 2.8rem; overflow: hidden;"><?php echo $c['title']; ?></h6>
                        <p class="x-small  mb-2"><?php echo $c['inst']; ?></p>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge badge-bestseller"><?php echo $c['badge']; ?></span>
                            <span class="fw-bold x-small text-dark">4.8</span>
                            <div class="text-warning x-small">
                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="fw-bold text-dark"><?php echo $c['price']; ?></span>
                            <span class=" x-small text-decoration-line-through">$84.99</span>
                        </div>
                    </div>
                </div>
            </div>
            <?php
    endforeach; ?>
        </div>
        <?php
endforeach; ?>
        
        <a href="#" class="text-red text-decoration-none fw-bold small">Show all <span id="current-category-name">Artificial Intelligence (AI)</span> courses &rarr;</a>
    </div>
</section>

<!-- Featured Topic Section (Learn AI) -->
<section class="py-5 reveal-on-scroll">
    <div class="container">
        <h3 class="fw-bold mb-4">Learn AI with <span class="text-red">SkillStack</span></h3>
        <div class="featured-banner p-4 p-lg-5 text-white" style="border-radius: 20px;">
            <div class="row align-items-center g-4">
                <!-- Main Certificate Card -->
                <div class="col-lg-4">
                    <div class="card p-4 h-100 text-dark border-0 shadow-lg glass-panel" style="border-radius: 15px;">
                        <div class="mb-3 px-1">
                            <h4 class="fw-bold"><span class="text-red">SkillStack</span> AI Professional <br>Certificate</h4>
                            <p class="small ">Build your AI fluency and get more done, faster with our professional track.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <span class="badge border text-dark fw-normal">★ 4.8</span>
                            <span class="badge border text-dark fw-normal">1,200 ratings</span>
                            <span class="badge border text-dark fw-normal">15 total hours</span>
                        </div>
                        <a href="#" class="btn btn-outline-dark rounded-pill py-2 fw-bold">Learn more</a>
                    </div>
                </div>
                
                <!-- Side Courses Slider -->
                <div class="col-lg-8">
                    <div class="slider-container-wrapper">
                        <button class="slider-nav prev" onclick="scrollSlider('ai-slider', -1)"><i class="bi bi-chevron-left"></i></button>
                        
                        <div id="ai-slider" class="d-flex gap-3 overflow-auto pb-3 no-scrollbar scroll-smooth">
                            <?php
$ai_courses = ["AI Fundamentals", "Generative AI for Business", "AI Research & Insights", "Prompt Engineering"];
foreach ($ai_courses as $idx => $title):
?>
                            <div class="card bg-transparent text-dark border-0 glass-panel" style="min-width: 240px; border-radius: 12px; transition: transform 0.3s;">
                                <div class="p-3">
                                    <div class="course-banner-small d-flex align-items-center justify-content-center bg-dark" style="height: 140px; border-radius: 8px; margin-bottom: 15px; overflow: hidden;">
                                        <i class="bi bi-cpu text-white opacity-25 display-6"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1" style="font-size: 0.95rem;"><?php echo $title; ?></h6>
                                    <div class="mt-3 pt-2 border-top d-flex justify-content-between x-small ">
                                        <span>Course <?php echo $idx + 1; ?> of 4</span>
                                        <span>2.5 hours</span>
                                    </div>
                                </div>
                            </div>
                            <?php
endforeach; ?>
                        </div>

                        <button class="slider-nav next" onclick="scrollSlider('ai-slider', 1)"><i class="bi bi-chevron-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Courses Section -->
<section class="py-5 bg-transparent reveal-on-scroll">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="fw-bold">Most Popular Courses</h2>
                <p class="">Top picks based on student satisfaction.</p>
            </div>
            <a href="#" class="text-red text-decoration-none fw-bold">View More &rarr;</a>
        </div>
        
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            <!-- Sample Course Card -->
            <?php
$popular = [
    ["title" => "Python for Data Science Bootcamp 2026", "inst" => "Nikhil Rana", "price" => "$99.99", "orig" => "$199.99", "icon" => "bi-rocket-takeoff"],
    ["title" => "C++ Systems Programming Masterclass", "inst" => "Aditya Jaiwal", "price" => "$89.99", "orig" => "$179.99", "icon" => "bi-cpu"],
    ["title" => "Go Lang: Scalable Backend Services", "inst" => "Manav Rawal", "price" => "$79.99", "orig" => "$159.99", "icon" => "bi-code-square"],
    ["title" => "Full Stack Web Mastery (MERN Stack)", "inst" => "Nikhil Rana", "price" => "$109.99", "orig" => "$219.99", "icon" => "bi-globe"]
];
foreach ($popular as $p): ?>
            <div class="col">
                <div class="card h-100 course-card glass-panel">
                    <div class="rose-placeholder d-flex align-items-center justify-content-center" style="height: 160px; overflow: hidden;">
                         <i class="bi <?php echo $p['icon']; ?> opacity-25 display-6"></i>
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold mb-1 text-truncate"><?php echo $p['title']; ?></h6>
                        <p class="x-small mb-2"><?php echo $p['inst']; ?></p>
                        <div class="d-flex align-items-center mb-2">
                            <span class="text-warning small">★★★★★</span>
                            <span class="ms-2 small ">(4.9)</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="price-final"><?php echo $p['price']; ?></span>
                            <span class="price-original small text-decoration-line-through"><?php echo $p['orig']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php
endforeach; ?>
        </div>
    </div>
</section>

<!-- Plan Promotion Section -->
<section class="py-5 bg-transparent reveal-on-scroll">
    <div class="container">
        <div class="featured-banner p-4 p-lg-5" style=" border-radius: 20px;">
            <div class="row align-items-center g-5">
                <!-- Text Content -->
                <div class="col-lg-5">
                    <h2 class="display-5 fw-bold mb-3">Reimagine your career in the <span class="text-red">AI era</span></h2>
                    <p class="mb-4">Future-proof your skills with Personal Plan. Get access to a variety of fresh content from real-world experts.</p>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <i class="bi bi-stars text-red me-2"></i> Learn AI and more
                        </div>
                        <div class="col-6">
                            <i class="bi bi-trophy text-red me-2"></i> Prep for certification
                        </div>
                        <div class="col-6">
                            <i class="bi bi-laptop text-red me-2"></i> Practice with AI
                        </div>
                        <div class="col-6">
                            <i class="bi bi-graph-up-arrow text-red me-2"></i> Advance your career
                        </div>
                    </div>
                    
                    <a href="#" class="btn btn-red btn-lg rounded-pill px-5 mb-3">Learn more</a>
                    <div class="small ">Starting at $10.00/month</div>
                </div>
                
                <!-- Image Grid Placeholder -->
                <div class="col-lg-7">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="rose-placeholder d-flex align-items-center justify-content-center" style="height: 300px; border: 1px solid rgba(255,255,255,0.1);">
                                <span class="small opacity-50 fw-bold">CONTENT</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="rose-placeholder d-flex align-items-center justify-content-center" style="height: 146px; border: 1px solid rgba(255,255,255,0.1);">
                                        <span class="small opacity-50 fw-bold">MODULE</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="rose-placeholder d-flex align-items-center justify-content-center" style="height: 146px; border: 1px solid rgba(255,255,255,0.1);">
                                        <span class="small opacity-50 fw-bold">CERTIFICATE</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
