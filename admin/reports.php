<?php
// admin/reports.php - REFACTORED TO PDO
include '../includes/db_connect.php';
include '../includes/functions.php';

check_role(['Admin']);

// Fetch Global Provider Performance (PDO)
$sql = "SELECT p.organization_name, 
               COUNT(DISTINCT c.id) as total_courses,
               COUNT(DISTINCT e.id) as total_enrollments,
               SUM(e.amount_paid) as total_revenue
        FROM providers p
        LEFT JOIN courses c ON p.id = c.provider_id
        LEFT JOIN enrollments e ON c.id = e.course_id AND e.payment_status = 'PAID'
        GROUP BY p.id";
$stmt = $pdo->query($sql);
$reports = $stmt->fetchAll();

include '../includes/header.php';
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item active">Global Reports</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4 text-dark">
    <h2 class="fw-bold">Platform Performance Analysis</h2>
    <button onclick="window.print()" class="btn btn-outline-secondary border-2 shadow-sm"><i class="bi bi-printer me-2"></i> Print Report</button>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card ems-card border-0 shadow-sm p-4 text-dark h-100">
            <h5 class="fw-bold mb-3">Provider Performance Summary</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-dark">
                    <thead class="bg-theme-header">
                        <tr>
                            <th>Provider Organization</th>
                            <th>Total Courses</th>
                            <th>Total Enrollments</th>
                            <th>Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $row): ?>
                            <tr>
                                <td class="fw-bold"><?php echo $row['organization_name']; ?></td>
                                <td><?php echo $row['total_courses']; ?></td>
                                <td><?php echo $row['total_enrollments']; ?></td>
                                <td class="text-success fw-bold"><?php echo format_price($row['total_revenue'] ?: 0); ?></td>
                            </tr>
                        <?php
endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card ems-card border-0 shadow-sm p-4 text-dark h-100 text-center">
            <h5 class="fw-bold mb-4">Revenue Distribution</h5>
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const chartData = {
        labels: <?php echo json_encode(array_column($reports, 'organization_name')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($reports, 'total_revenue')); ?>,
            backgroundColor: ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6'],
            borderWidth: 0
        }]
    };

    new Chart(ctx, {
        type: 'doughnut',
        data: chartData,
        options: {
            plugins: {
                legend: { position: 'bottom' }
            },
            cutout: '70%'
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>
