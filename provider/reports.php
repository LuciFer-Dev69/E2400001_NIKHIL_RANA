<?php
// provider/reports.php - REFACTORED TO PDO
include '../includes/db_connect.php';
include '../includes/functions.php';

check_role(['Provider']);
$provider_id = $_SESSION['provider_id'];

// Fetch Course Specific Analytics (PDO)
$sql = "SELECT c.title, c.category, 
               COUNT(e.id) as enrollments, 
               SUM(e.amount_paid) as revenue
        FROM courses c 
        LEFT JOIN enrollments e ON c.id = e.course_id AND e.payment_status = 'PAID'
        WHERE c.provider_id = ?
        GROUP BY c.id";
$stmt = $pdo->prepare($sql);
$stmt->execute([$provider_id]);
$reports = $stmt->fetchAll();

include '../includes/header.php';
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item active">Course Analytics</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4 text-dark">
    <h2 class="fw-bold">Your Performance Insights</h2>
    <button onclick="window.print()" class="btn btn-outline-secondary border-2 shadow-sm"><i class="bi bi-printer me-2"></i> Export Data</button>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card ems-card border-0 shadow-sm p-4 text-dark h-100">
            <h5 class="fw-bold mb-3">Course Analytics Summary</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-dark">
                    <thead class="bg-theme-header">
                        <tr>
                            <th>Course Title</th>
                            <th>Total Students</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $row): ?>
                            <tr>
                                <td class="fw-bold"><?php echo $row['title']; ?></td>
                                <td><?php echo $row['enrollments']; ?></td>
                                <td class="text-success fw-bold"><?php echo format_price($row['revenue'] ?: 0); ?></td>
                            </tr>
                        <?php
endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card ems-card border-0 shadow-sm p-4 text-dark h-100 text-center">
            <h5 class="fw-bold mb-4">Students per Course</h5>
            <canvas id="enrollmentChart"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('enrollmentChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($reports, 'title')); ?>,
            datasets: [{
                label: 'Enrollments',
                data: <?php echo json_encode(array_column($reports, 'enrollments')); ?>,
                backgroundColor: 'rgba(16, 185, 129, 0.7)',
                borderColor: '#10B981',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            scales: { y: { beginAtZero: true, grid: { display: false } } },
            plugins: { legend: { display: false } }
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>
