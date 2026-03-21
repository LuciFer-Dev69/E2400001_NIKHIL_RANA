<?php include 'includes/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar Column -->
        <div class="col-lg-2 d-none d-lg-block">
            <?php include 'includes/sidebar.php'; ?>
        </div>

        <!-- Main Dashboard Content -->
        <main class="col-lg-10 ps-lg-4">
            <!-- Welcome Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Welcome back, <span class="text-red">Nikhil!</span></h2>
                    <p class="text-muted fw-bold">You've completed 45% of your goal this week. Keep it up!</p>
                </div>
                <div class="d-none d-md-block">
                    <span class="text-muted small me-2">March 19, 2026</span>
                    <div class="btn btn-outline-dark btn-sm rounded-pill px-3">Sync Progress</div>
                </div>
            </div>

            <!-- Stats Widgets -->
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="glass-panel p-4 text-center">
                        <div class="display-6 fw-bold text-red mb-1">12</div>
                        <div class="small text-muted text-uppercase fw-bold">Active Courses</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-panel p-4 text-center">
                        <div class="display-6 fw-bold text-red mb-1">4</div>
                        <div class="small text-muted text-uppercase fw-bold">Completed</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-panel p-4 text-center">
                        <div class="display-6 fw-bold text-red mb-1">85%</div>
                        <div class="small text-muted text-uppercase fw-bold">Average Score</div>
                    </div>
                </div>
            </div>

            <!-- Continue Learning Large Widget -->
            <div class="glass-panel p-0 overflow-hidden mb-5 border-0">
                <div class="row g-0">
                    <div class="col-md-4 rose-placeholder d-none d-md-flex align-items-center justify-content-center border-end" style="min-height: 200px;">
                        <span class="text-muted fw-bold small text-uppercase ls-1 opacity-25">Course Preview</span>
                    </div>
                    <div class="col-md-8 p-4">
                        <div class="badge bg-red-subtle text-red mb-2 px-3">Last Viewed</div>
                        <h4 class="fw-bold mb-2">Mastering Advanced PHP & MySQL Integration</h4>
                        <p class="text-muted small mb-4">Instructor: Nikhil Rana • 12/24 Lessons watched</p>
                        
                        <div class="progress mb-4" style="height: 10px; border-radius: 5px;">
                            <div class="progress-bar bg-red" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted">50% Complete</span>
                            <a href="#" class="btn btn-red rounded-pill px-4">Resume Learning</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity / Recommended -->
            <div class="row g-4">
                <div class="col-lg-8">
                    <h5 class="fw-bold mb-4">Recommended for You</h5>
                    <div class="row g-3">
                        <?php
$bootcamps = [
    ["title" => "Python for Data Science Bootcamp", "inst" => "Nikhil Rana", "progress" => "0%", "color" => "#640D2B"],
    ["title" => "C++ Systems Programming Masterclass", "inst" => "Aditya Jaiwal", "progress" => "0%", "color" => "#880E4F"],
    ["title" => "Go Lang: Scalable Backend Services", "inst" => "Manav Rawal", "progress" => "0%", "color" => "#4A148C"],
    ["title" => "Full Stack Web Mastery (MERN Stack)", "inst" => "Nikhil Rana", "progress" => "0%", "color" => "#C2185B"]
];
foreach ($bootcamps as $bc): ?>
                        <div class="col-md-4">
                            <div class="card h-100 course-card p-3 border-0 shadow-sm" style="background: <?php echo $bc['color']; ?>; color: #ffffff;">
                                <div class="rose-placeholder mb-3 rounded d-flex align-items-center justify-content-center" style="height: 100px; background: rgba(255,255,255,0.1) !important;">
                                    <i class="bi bi-rocket-takeoff display-6 opacity-50"></i>
                                </div>
                                <h6 class="fw-bold mb-1 small text-truncate"><?php echo $bc['title']; ?></h6>
                                <p class="x-small opacity-75 mb-2"><?php echo $bc['inst']; ?></p>
                                <div class="progress mt-auto" style="height: 4px; background: rgba(255,255,255,0.2);">
                                    <div class="progress-bar bg-white" role="progressbar" style="width: <?php echo $bc['progress']; ?>;"></div>
                                </div>
                            </div>
                        </div>
                        <?php
endforeach; ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h5 class="fw-bold mb-4">Announcements</h5>
                    <div class="glass-panel p-3 small mb-2">
                        <div class="fw-bold text-red">New Lesson Added!</div>
                        <div class="text-muted text-truncate">Module 4: "AJAX with Fetch" is now live.</div>
                    </div>
                    <div class="glass-panel p-3 small">
                        <div class="fw-bold text-red">System Update</div>
                        <div class="text-muted text-truncate">The dashboard now supports dark mode preview.</div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
