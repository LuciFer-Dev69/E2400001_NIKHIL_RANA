<?php
require_once 'config/db.php';
session_start();

$category_slug = $_GET['category'] ?? 'all';
$search_query = $_GET['search'] ?? '';

// Mocking category titles
$categories = [
    'all' => 'All Courses',
    'development' => 'Development',
    'java' => 'Java Development',
    'python' => 'Python',
    'ai' => 'Artificial Intelligence',
    'design' => 'Design',
    'marketing' => 'Marketing'
];

$page_title = $categories[$category_slug] ?? 'Courses';
if (!empty($search_query)) {
    $page_title = "Results for \"" . htmlspecialchars($search_query) . "\"";
}

include 'includes/header.php';
?>

<div class="container" style="padding: 40px 0;">
    <div style="margin-bottom: 40px;">
        <h1 style="font-size: 32px; margin-bottom: 10px;"><?php echo $page_title; ?></h1>
        <p style="color: var(--gray-color); font-size: 16px;">Explore the best courses in <?php echo strtolower($page_title); ?>.</p>
    </div>

    <div style="display: flex; gap: 40px;">
        <!-- Sidebar Filters -->
        <aside style="width: 260px; flex-shrink: 0;" class="hide-mobile">
            <div class="filter-section">
                <h4 style="margin-bottom: 15px; font-size: 16px;">Ratings</h4>
                <div class="filter-item"><input type="checkbox"> 4.5 & up <span style="color: var(--gray-color); font-size: 12px; margin-left: 5px;">(1,248)</span></div>
                <div class="filter-item"><input type="checkbox"> 4.0 & up <span style="color: var(--gray-color); font-size: 12px; margin-left: 5px;">(2,500)</span></div>
                <div class="filter-item"><input type="checkbox"> 3.5 & up <span style="color: var(--gray-color); font-size: 12px; margin-left: 5px;">(450)</span></div>
            </div>

            <div class="filter-section" style="margin-top: 30px; border-top: 1px solid var(--border-color); padding-top: 20px;">
                <h4 style="margin-bottom: 15px; font-size: 16px;">Video Duration</h4>
                <div class="filter-item"><input type="checkbox"> 0-1 Hours</div>
                <div class="filter-item"><input type="checkbox"> 1-3 Hours</div>
                <div class="filter-item"><input type="checkbox"> 3-6 Hours</div>
                <div class="filter-item"><input type="checkbox"> 6+ Hours</div>
            </div>

            <div class="filter-section" style="margin-top: 30px; border-top: 1px solid var(--border-color); padding-top: 20px;">
                <h4 style="margin-bottom: 15px; font-size: 16px;">Level</h4>
                <div class="filter-item"><input type="checkbox"> Beginner</div>
                <div class="filter-item"><input type="checkbox"> Intermediate</div>
                <div class="filter-item"><input type="checkbox"> Expert</div>
            </div>
        </aside>

        <!-- Course List -->
        <div style="flex: 1;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <button class="btn btn-secondary" style="padding: 10px 20px; font-size: 14px;"><i class="fa fa-filter"></i> Filter</button>
                <div style="font-weight: 700; font-size: 14px;">1,245 results</div>
            </div>

            <div class="course-list-stack">
                <!-- Course 1 (Java as requested) -->
                <div class="course-horizontal-card">
                    <div class="card-thumb" style="background: linear-gradient(45deg, #f89820, #5382a1);">
                        <i class="fab fa-java" style="color: white; font-size: 48px;"></i>
                    </div>
                    <div class="card-info">
                        <h3>Java Programming Masterclass for Software Developers</h3>
                        <p class="desc">Learn Java In This Course And Become a Computer Programmer. Core Java Learn Java Programming.</p>
                        <p class="instructor">Tim Buchalka, Learn Programming Academy</p>
                        <div class="rating">
                            <span style="font-weight: 700; color: #b4690e;">4.7</span>
                            <i class="fa fa-star" style="color: #b4690e; font-size: 12px;"></i>
                            <span style="color: var(--gray-color); font-size: 13px;">(182,450 ratings)</span>
                        </div>
                        <div style="font-size: 13px; color: var(--gray-color); margin-top: 5px;">
                            80.5 total hours &bull; 450 lectures &bull; All Levels
                        </div>
                    </div>
                    <div class="card-price">
                        <div style="font-weight: 700; font-size: 18px;">$12.99</div>
                        <div style="text-decoration: line-through; color: var(--gray-color); font-size: 14px;">$84.99</div>
                    </div>
                </div>

                <!-- Course 2 -->
                <div class="course-horizontal-card">
                    <div class="card-thumb" style="background: linear-gradient(45deg, #1fa2ff, #12d8fa);"></div>
                    <div class="card-info">
                        <h3>Complete Web Bootcamp 2026</h3>
                        <p class="desc">Become a Full-Stack Web Developer with just ONE course. HTML, CSS, Javascript, Node, React, MongoDB and more!</p>
                        <p class="instructor">Dr. Angela Yu</p>
                        <div class="rating">
                            <span style="font-weight: 700; color: #b4690e;">4.8</span>
                            <i class="fa fa-star" style="color: #b4690e; font-size: 12px;"></i>
                            <span style="color: var(--gray-color); font-size: 13px;">(245,000 ratings)</span>
                        </div>
                        <div style="font-size: 13px; color: var(--gray-color); margin-top: 5px;">
                            65 total hours &bull; 490 lectures &bull; All Levels
                        </div>
                    </div>
                    <div class="card-price">
                        <div style="font-weight: 700; font-size: 18px;">$9.99</div>
                    </div>
                </div>

                <!-- Course 3 -->
                <div class="course-horizontal-card">
                    <div class="card-thumb" style="background: linear-gradient(45deg, #f093fb, #f5576c);"></div>
                    <div class="card-info">
                        <h3>Python for Data Science and Machine Learning Bootcamp</h3>
                        <p class="desc">Learn how to use NumPy, Pandas, Seaborn, Matplotlib, Plotly, Scikit-Learn, Machine Learning, Tensorflow, and more!</p>
                        <p class="instructor">Jose Portilla</p>
                        <div class="rating">
                            <span style="font-weight: 700; color: #b4690e;">4.6</span>
                            <i class="fa fa-star" style="color: #b4690e; font-size: 12px;"></i>
                            <span style="color: var(--gray-color); font-size: 13px;">(150,230 ratings)</span>
                        </div>
                        <div style="font-size: 13px; color: var(--gray-color); margin-top: 5px;">
                            25 total hours &bull; 165 lectures &bull; All Levels
                        </div>
                    </div>
                    <div class="card-price">
                        <div style="font-weight: 700; font-size: 18px;">$14.99</div>
                        <div style="text-decoration: line-through; color: var(--gray-color); font-size: 14px;">$94.99</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
