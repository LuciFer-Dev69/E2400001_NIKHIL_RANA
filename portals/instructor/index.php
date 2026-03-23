<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$portal_type = 'instructor';
$portal_context = 'instructor';
$base_url = "../../";
include '../../includes/header.php';
?>

<div class="portal-shell">
    <?php include '../../includes/portal_header_sidebar.php'; ?>
    <div class="portal-content">


<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h2 style="font-size: 28px; margin-bottom: 5px;">Instructor Dashboard</h2>
        <p style="color: var(--gray-color);">Manage your courses, track performance, and grow your audience.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="#" class="btn btn-gradient" style="font-size: 14px; padding: 10px 25px;"><i class="fa fa-plus"></i> Create New Course</a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon bg-red"><i class="fa fa-book"></i></div>
        <div class="stat-info">
            <h4>Total Courses</h4>
            <div class="value">12</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-blue"><i class="fa fa-users"></i></div>
        <div class="stat-info">
            <h4>Total Students</h4>
            <div class="value">1,248</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-green"><i class="fa fa-star"></i></div>
        <div class="stat-info">
            <h4>Avg Rating</h4>
            <div class="value">4.8</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-purple"><i class="fa fa-wallet"></i></div>
        <div class="stat-info">
            <h4>Total Earnings</h4>
            <div class="value">$1,450.00</div>
        </div>
    </div>
</div>

<div class="card-table">
    <div class="table-title">
        <span>Active Courses</span>
        <span style="font-size: 14px; color: var(--gray-color); font-weight: 400;">Current Month: March 2026</span>
    </div>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Category</th>
                    <th>Published</th>
                    <th>Price</th>
                    <th>Enrollment</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Prompt Engineering 101</strong></td>
                    <td>AI & Data Science</td>
                    <td>Mar 15, 2026</td>
                    <td>$12.99</td>
                    <td>542</td>
                    <td><span class="badge badge-success">Published</span></td>
                    <td>
                        <div class="action-btns">
                            <a href="#" class="action-btn" title="Edit"><i class="fa fa-edit"></i></a>
                            <a href="#" class="action-btn" title="Analytics"><i class="fa fa-chart-bar"></i></a>
                            <button class="action-btn btn-delete" title="Delete"><i class="fa fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><strong>Microsoft Excel Masterclass</strong></td>
                    <td>Business</td>
                    <td>Mar 20, 2026</td>
                    <td>$9.99</td>
                    <td>706</td>
                    <td><span class="badge badge-success">Published</span></td>
                    <td>
                        <div class="action-btns">
                            <a href="#" class="action-btn" title="Edit"><i class="fa fa-edit"></i></a>
                            <a href="#" class="action-btn" title="Analytics"><i class="fa fa-chart-bar"></i></a>
                            <button class="action-btn btn-delete" title="Delete"><i class="fa fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><strong>Introduction to LLMs</strong></td>
                    <td>Programming</td>
                    <td>Mar 22, 2026</td>
                    <td>$14.99</td>
                    <td>0</td>
                    <td><span class="badge badge-pending">Pending</span></td>
                    <td>
                        <div class="action-btns">
                            <a href="#" class="action-btn" title="Edit"><i class="fa fa-edit"></i></a>
                            <button class="action-btn btn-delete" title="Delete"><i class="fa fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

    </div>
</div>
<?php include '../../includes/footer.php'; ?>
