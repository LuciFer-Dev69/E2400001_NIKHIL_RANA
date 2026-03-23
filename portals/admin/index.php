<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}
$portal_type = 'admin';
$portal_context = 'admin';
$base_url = "../../";
include '../../includes/header.php';
?>

<div class="portal-shell">
    <?php include '../../includes/portal_header_sidebar.php'; ?>
    <div class="portal-content">
        <h1 style="font-size: 28px; font-weight: 800; color: #1c1d1f; margin-bottom: 24px;">Admin Dashboard</h1>


<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon bg-red"><i class="fa fa-users"></i></div>
        <div class="stat-info"><h4>Total Users</h4><div class="value">4,500</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-blue"><i class="fa fa-graduation-cap"></i></div>
        <div class="stat-info"><h4>Instructors</h4><div class="value">320</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-green"><i class="fa fa-check-circle"></i></div>
        <div class="stat-info"><h4>Pending Approval</h4><div class="value">14</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-purple"><i class="fa fa-dollar-sign"></i></div>
        <div class="stat-info"><h4>Total Revenue</h4><div class="value">$142,500</div></div>
    </div>
</div>

<div class="card-table">
    <div class="table-title">
        <span>User Management</span>
        <a href="#" class="btn btn-primary btn-icon"><i class="fa fa-plus"></i> New User</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Join Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Jane Doe</strong></td>
                <td>jane@example.com</td>
                <td>Instructor</td>
                <td>Jan 15, 2026</td>
                <td><span class="badge badge-success">Active</span></td>
                <td>
                    <div class="action-btns">
                        <a href="#" class="action-btn btn-edit"><i class="fa fa-edit"></i></a>
                        <button class="action-btn btn-delete"><i class="fa fa-trash"></i></button>
                    </div>
                </td>
            </tr>
            <tr>
                <td><strong>Bob Smith</strong></td>
                <td>bob@example.com</td>
                <td>Student</td>
                <td>Feb 20, 2026</td>
                <td><span class="badge badge-success">Active</span></td>
                <td>
                    <div class="action-btns">
                        <a href="#" class="action-btn btn-edit"><i class="fa fa-edit"></i></a>
                        <button class="action-btn btn-delete"><i class="fa fa-trash"></i></button>
                    </div>
                </td>
            </tr>
            <tr>
                <td><strong>Alice Green</strong></td>
                <td>alice@example.com</td>
                <td>Instructor</td>
                <td>Mar 22, 2026</td>
                <td><span class="badge badge-pending">Suspended</span></td>
                <td>
                    <div class="action-btns">
                        <a href="#" class="action-btn btn-edit"><i class="fa fa-edit"></i></a>
                        <button class="action-btn btn-delete"><i class="fa fa-trash"></i></button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
