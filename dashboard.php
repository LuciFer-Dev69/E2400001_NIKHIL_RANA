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
                    <p class="text-muted">You've completed 45% of your goal this week. Keep it up!</p>
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
                    <div class="col-md-4 bg-light d-none d-md-flex align-items-center justify-content-center border-end" style="min-height: 200px;">
                        <span class="text-muted fw-bold small text-uppercase ls-1">Course Preview</span>
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
                        <?php for ($i = 0; $i < 2; $i++): ?>
                        <div class="col-md-6">
                            <div class="card h-100 course-card p-3">
                                <h6 class="fw-bold mb-1 small text-truncate">Web Design Systems with Figma</h6>
                                <p class="small text-muted mb-2">Aditya Jaiwal</p>
                                <div class="progress mt-auto" style="height: 4px;">
                                    <div class="progress-bar bg-red" role="progressbar" style="width: 75%;"></div>
                                </div>
                            </div>
                        </div>
                        <?php
endfor; ?>
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
