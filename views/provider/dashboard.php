<?php include '../../includes/header.php'; ?>

<!-- Provider Dashboard Header -->
<div class="bg-dark text-white py-4">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-0">Instructor <span class="text-red">Dashboard</span></h2>
            <p class="text-white-50 small mb-0">Manage your courses and track your earnings.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-light btn-sm px-3">Performance</button>
            <button class="btn btn-red btn-sm px-3">Create New Course</button>
        </div>
    </div>
</div>

<main class="container py-5">
    <!-- Stats Row -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 h-100 stats-card">
                <div class="small text-muted mb-2 uppercase fw-bold ls-1">Total Revenue</div>
                <h3 class="fw-bold mb-1">$4,120.30</h3>
                <div class="small text-success"><i class="bi bi-arrow-up"></i> +12% this month</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 h-100 stats-card">
                <div class="small text-muted mb-2 uppercase fw-bold ls-1">Total Enrollments</div>
                <h3 class="fw-bold mb-1">842</h3>
                <div class="small text-success"><i class="bi bi-arrow-up"></i> +5% this month</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 h-100 stats-card">
                <div class="small text-muted mb-2 uppercase fw-bold ls-1">Instructor Rating</div>
                <h3 class="fw-bold mb-1">4.8 <i class="bi bi-star-fill text-warning fs-5"></i></h3>
                <div class="small text-muted">From 152 reviews</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 h-100 stats-card">
                <div class="small text-muted mb-2 uppercase fw-bold ls-1">Active Courses</div>
                <h3 class="fw-bold mb-1">12</h3>
                <div class="small text-muted">2 pending approval</div>
            </div>
        </div>
    </div>

    <!-- Revenue Chart & Tasks -->
    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Revenue Analytics</h5>
                    <select class="form-select form-select-sm w-auto">
                        <option>Last 30 Days</option>
                        <option>Last 6 Months</option>
                        <option>This Year</option>
                    </select>
                </div>
                <div id="revenueChart" style="height: 300px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 15px; border: 1px dashed #ddd;">
                    <div class="text-center text-muted">
                        <p class="small fw-bold text-uppercase ls-1">Revenue Data Visualization</p>
                        <p class="x-small">Analytics will be displayed here</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 h-100">
                <h5 class="fw-bold mb-4">Recent Notifications</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-3 pb-3 border-bottom">
                        <div class="d-flex gap-3">
                            <div class="stats-icon bg-light-red text-red"><i class="bi bi-cash"></i></div>
                            <div>
                                <p class="mb-0 small fw-bold">Payment Processed</p>
                                <p class="mb-0 x-small text-muted">Your earnings of $520.00 were sent to PayPal.</p>
                            </div>
                        </div>
                    </li>
                    <li class="mb-3 pb-3 border-bottom">
                        <div class="d-flex gap-3">
                            <div class="stats-icon bg-light-blue text-primary"><i class="bi bi-chat-left-text"></i></div>
                            <div>
                                <p class="mb-0 small fw-bold">New Review</p>
                                <p class="mb-0 x-small text-muted">A student left a 5-star review on "AI Course".</p>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="d-flex gap-3">
                            <div class="stats-icon bg-light-warning text-warning"><i class="bi bi-person-plus"></i></div>
                            <div>
                                <p class="mb-0 small fw-bold">New Enrollment</p>
                                <p class="mb-0 x-small text-muted">12 new students joined your Python Bootcamp.</p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Course Management Table -->
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Your Courses</h5>
            <input type="text" class="form-control form-control-sm w-auto" placeholder="Search courses...">
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Course Name</th>
                        <th>Enrollments</th>
                        <th>Revenue</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <div>
                                    <p class="mb-0 small fw-bold">AI Engineering 2026</p>
                                    <p class="mb-0 x-small text-muted">Published Jan 12, 2026</p>
                                </div>
                            </div>
                        </td>
                        <td>420</td>
                        <td class="fw-bold">$1,240.00</td>
                        <td><span class="text-warning">★</span> 4.9</td>
                        <td><span class="badge bg-success-subtle text-success px-3">Live</span></td>
                        <td class="pe-4 text-end">
                            <button class="btn btn-outline-dark btn-sm rounded-pill px-3">Edit</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <div>
                                    <p class="mb-0 small fw-bold">Python Bootcamp: Zero to Hero</p>
                                    <p class="mb-0 x-small text-muted">Published Dec 01, 2025</p>
                                </div>
                            </div>
                        </td>
                        <td>320</td>
                        <td class="fw-bold">$890.00</td>
                        <td><span class="text-warning">★</span> 4.7</td>
                        <td><span class="badge bg-success-subtle text-success px-3">Live</span></td>
                        <td class="pe-4 text-end">
                            <button class="btn btn-outline-dark btn-sm rounded-pill px-3">Edit</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="p-3 text-center bg-light">
            <a href="#" class="text-red text-decoration-none small fw-bold">View all courses &rarr;</a>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>
