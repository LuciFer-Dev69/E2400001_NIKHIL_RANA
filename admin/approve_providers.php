<?php
// admin/approve_providers.php - REFACTORED TO PDO
include '../includes/db_connect.php';
include '../includes/functions.php';

check_role(['Admin']);

// Handle Approval / Rejection (PDO)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    $status = ($action == 'approve') ? 'APPROVED' : 'REJECTED';

    $stmt = $pdo->prepare("UPDATE providers SET status = ?, approved_at = NOW() WHERE id = ?");
    if ($stmt->execute([$status, $id])) {
        // Log action
        log_action($pdo, "PROVIDER_MODERATION", "Provider $id status set to $status");
        header("Location: approve_providers.php?msg=success");
        exit();
    }
}

// Fetch pending (PDO)
$sql = "SELECT p.*, u.full_name, u.email 
        FROM providers p 
        JOIN users u ON p.user_id = u.id 
        WHERE p.status = 'PENDING'";
$stmt = $pdo->query($sql);
$pending_list = $stmt->fetchAll();

include '../includes/header.php';
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item active">Provider Applications</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4 text-dark">
    <h2 class="fw-bold">Pending Applications</h2>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success border-success">Action processed successfully.</div>
<?php
endif; ?>

<div class="card ems-card border-0 shadow-sm overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 text-dark">
            <thead class="bg-theme-header">
                <tr>
                    <th>Organization</th>
                    <th>Contact Person</th>
                    <th>Email</th>
                    <th>Document</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($pending_list) > 0): ?>
                    <?php foreach ($pending_list as $row): ?>
                        <tr>
                            <td class="fw-bold"><?php echo $row['organization_name']; ?></td>
                            <td><?php echo $row['full_name']; ?></td>
                            <td><a href="mailto:<?php echo $row['email']; ?>" class="text-decoration-none"><?php echo $row['email']; ?></a></td>
                            <td>
                                <a href="../assets/uploads/<?php echo $row['document_path']; ?>" target="_blank" class="btn btn-sm btn-outline-info text-dark">
                                    <i class="bi bi-file-earmark-pdf"></i> View Doc
                                </a>
                            </td>
                            <td class="text-end">
                                <a href="?action=approve&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success px-3 me-2">Approve</a>
                                <a href="?action=reject&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Reject this application?')">Reject</a>
                            </td>
                        </tr>
                    <?php
    endforeach; ?>
                <?php
else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">No pending applications found.</td>
                    </tr>
                <?php
endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
