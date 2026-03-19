<?php
// admin/audit_logs.php - NEW [GOVERNANCE]
include '../includes/db_connect.php';
include '../includes/functions.php';

check_role(['Admin']);

// Fetch logs with user names (PDO)
$sql = "SELECT a.*, u.full_name, u.role 
        FROM audit_logs a 
        LEFT JOIN users u ON a.user_id = u.id 
        ORDER BY a.created_at DESC LIMIT 100";
$stmt = $pdo->query($sql);
$logs = $stmt->fetchAll();

include '../includes/header.php';
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item active">System Audit Logs</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4 text-dark">
    <h2 class="fw-bold">Governance & Audit Logs</h2>
    <span class="badge bg-theme-header text-dark p-2 border border-success">Recent 100 Actions</span>
</div>

<div class="card ems-card border-0 shadow-sm overflow-hidden text-dark">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 text-dark">
            <thead class="bg-theme-header">
                <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="small text-muted"><?php echo date('d M Y, H:i', strtotime($log['created_at'])); ?></td>
                        <td>
                            <div class="fw-bold"><?php echo $log['full_name'] ?: 'System/Guest'; ?></div>
                            <small class="text-muted"><?php echo $log['role'] ?: 'Public'; ?></small>
                        </td>
                        <td>
                            <span class="badge <?php echo strpos($log['action'], 'DELETE') !== false ? 'bg-danger' : 'bg-primary'; ?> px-2 py-1">
                                <?php echo $log['action']; ?>
                            </span>
                        </td>
                        <td class="small"><?php echo htmlspecialchars($log['details']); ?></td>
                        <td class="text-muted small"><?php echo $log['ip_address']; ?></td>
                    </tr>
                <?php
endforeach; ?>
                
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">No audit logs found.</td>
                    </tr>
                <?php
endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
