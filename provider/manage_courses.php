<?php
// provider/manage_courses.php - REFACTORED TO PDO
include '../includes/db_connect.php';
include '../includes/functions.php';

check_role(['Provider']);
$provider_id = $_SESSION['provider_id'];

// Handle Deletion (PDO)
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ? AND provider_id = ?");
    if ($stmt->execute([$id, $provider_id])) {
        log_action($pdo, "COURSE_DELETE", "Course $id deleted by provider $provider_id");
        header("Location: manage_courses.php?msg=deleted");
        exit();
    }
}

$stmt = $pdo->prepare("SELECT * FROM courses WHERE provider_id = ? ORDER BY created_at DESC");
$stmt->execute([$provider_id]);
$courses = $stmt->fetchAll();

include '../includes/header.php';
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item active">Manage Courses</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4 text-dark">
    <h2 class="fw-bold">Your Course Inventory</h2>
    <a href="add_course.php" class="btn btn-primary-custom text-white shadow-sm">+ Create New Course</a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success shadow-sm">Operation completed successfully.</div>
<?php
endif; ?>

<div class="card ems-card border-0 shadow-sm overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 text-dark">
            <thead class="bg-theme-header">
                <tr>
                    <th>Thumbnail</th>
                    <th>Course Title</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($courses) > 0): ?>
                    <?php foreach ($courses as $row): ?>
                        <tr>
                            <td>
                                <img src="../assets/uploads/<?php echo $row['thumbnail'] ?: 'default_course.jpg'; ?>" width="60" class="rounded shadow-sm" onerror="this.src='https://placehold.co/100x100?text=C'">
                            </td>
                            <td class="fw-bold"><?php echo $row['title']; ?></td>
                            <td class="text-secondary fw-bold"><?php echo format_price($row['price']); ?></td>
                            <td>
                                <span class="badge <?php echo $row['status'] == 'ACTIVE' ? 'bg-success' : 'bg-warning'; ?> p-2">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="edit_course.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary px-3 me-2">Edit</a>
                                <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Data will be permanently removed. Continue?')">Delete</a>
                            </td>
                        </tr>
                    <?php
    endforeach; ?>
                <?php
else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">No courses found. Start by adding your first course!</td>
                    </tr>
                <?php
endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
