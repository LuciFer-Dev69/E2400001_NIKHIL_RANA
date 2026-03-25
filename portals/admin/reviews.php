<?php
require_once '../../config/db.php';
session_start();

$root = "../../";
$page_title = 'Review Moderation';
include '../../includes/admin/admin_header.php';

// Pagination setup
$limit = 15;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Filter Setup
$status_filter = $_GET['status'] ?? 'pending';

$sql = "
    SELECT r.id, r.rating, r.comment, r.status, r.created_at,
           u.full_name as student_name, c.title as course_title
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN courses c ON r.course_id = c.id
    WHERE 1=1
";
$params = [];

if (!empty($status_filter) && in_array($status_filter, ['pending', 'approved', 'rejected', 'all'])) {
    if ($status_filter !== 'all') {
        $sql .= " AND r.status = ?";
        $params[] = $status_filter;
    }
}

// Get Total
$count_sql = "SELECT COUNT(*) FROM ($sql) as sub";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_reviews = $stmt->fetchColumn();
$total_pages = ceil($total_reviews / $limit);

// Fetch Paginated List
$sql .= " ORDER BY r.created_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $val) {
    if (is_int($val))
        $stmt->bindValue($key + 1, $val, PDO::PARAM_INT);
    else
        $stmt->bindValue($key + 1, $val, PDO::PARAM_STR);
}
$stmt->execute();
$reviews = $stmt->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px;">
    <div>
        <h1 style="font-size: 28px; font-weight: 800; color: var(--dark-color); margin-bottom: 8px;">Review Moderation</h1>
        <p style="color: var(--gray-color); font-size: 15px;">Monitor and manage student feedback on courses.</p>
    </div>
</div>

<div style="background: var(--bg-card); padding: 20px; border-radius: 8px; border: 1px solid var(--border-color); box-shadow: var(--shadow); margin-bottom: 25px; display: flex; gap: 15px; align-items: center;">
    <form method="GET" style="display: flex; gap: 15px; flex: 1;">
        <select name="status" style="padding: 10px 15px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-family: inherit;">
            <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending Approval</option>
            <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Approved</option>
            <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
            <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Reviews</option>
        </select>
        <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">Filter</button>
    </form>
</div>

<div style="display: flex; flex-direction: column; gap: 15px;">
    <?php if (empty($reviews)): ?>
        <div style="text-align: center; padding: 40px; background: var(--bg-card); border-radius: 12px; border: 1px dashed var(--border-color);">
            <i class="fa fa-star" style="font-size: 40px; color: var(--gray-color); margin-bottom: 15px;"></i>
            <p style="color: var(--gray-color); font-weight: 700;">No reviews found matching the selected filter.</p>
        </div>
    <?php
else: ?>
        <?php foreach ($reviews as $review): ?>
        <div id="review-row-<?php echo $review['id']; ?>" style="background: var(--bg-card); border-radius: 12px; padding: 20px; border: 1px solid var(--border-color); box-shadow: var(--shadow);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                <div style="display: flex; gap: 15px; align-items: center;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--light-gray); display: flex; align-items: center; justify-content: center; font-weight: 800; color: var(--primary-color);">
                        <?php echo substr($review['student_name'], 0, 1); ?>
                    </div>
                    <div>
                        <div style="font-weight: 800; color: var(--dark-color); font-size: 15px;"><?php echo htmlspecialchars($review['student_name']); ?></div>
                        <div style="font-size: 12px; color: var(--gray-color);"><i class="fa fa-book-open"></i> <?php echo htmlspecialchars($review['course_title']); ?></div>
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="color: #f1c40f; font-size: 14px; margin-bottom: 5px;">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="<?php echo $i <= $review['rating'] ? 'fas' : 'far'; ?> fa-star"></i>
                        <?php
        endfor; ?>
                    </div>
                    <div style="font-size: 11px; color: var(--gray-color);"><?php echo date('M j, Y', strtotime($review['created_at'])); ?></div>
                </div>
            </div>

            <p style="background: var(--light-gray); padding: 15px; border-radius: 8px; color: var(--dark-color); font-size: 14px; line-height: 1.5; margin-bottom: 15px; font-style: italic;">
                "<?php echo nl2br(htmlspecialchars($review['comment'] ?: 'No written feedback.')); ?>"
            </p>

            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 15px;">
                <div>
                    <?php if ($review['status'] === 'pending'): ?>
                        <span class="badge badge-warning">Pending</span>
                    <?php
        elseif ($review['status'] === 'approved'): ?>
                        <span class="badge badge-success">Approved</span>
                    <?php
        else: ?>
                        <span class="badge badge-danger">Rejected</span>
                    <?php
        endif; ?>
                </div>

                <div class="action-btns">
                    <?php if ($review['status'] !== 'approved'): ?>
                        <button class="btn btn-secondary" style="border-color: #2ecc71; color: #2ecc71; font-weight: 700; font-size: 12px; padding: 6px 15px;" onclick="updateReview(<?php echo $review['id']; ?>, 'approved')"><i class="fa fa-check"></i> Approve</button>
                    <?php
        endif; ?>
                    
                    <?php if ($review['status'] !== 'rejected'): ?>
                        <button class="btn btn-secondary" style="border-color: #f39c12; color: #f39c12; font-weight: 700; font-size: 12px; padding: 6px 15px;" onclick="updateReview(<?php echo $review['id']; ?>, 'rejected')"><i class="fa fa-times"></i> Reject</button>
                    <?php
        endif; ?>
                    
                    <button class="btn-icon" title="Delete Permanently" style="color: #e74c3c; border-color: rgba(231,76,60,0.3); width: auto; padding: 0 15px;" onclick="deleteReview(<?php echo $review['id']; ?>)"><i class="fa fa-trash-alt"></i></button>
                </div>
            </div>
        </div>
        <?php
    endforeach; ?>
    <?php
endif; ?>
</div>

<script>
    function updateReview(id, newStatus) {
        fetch('../../api/admin_reviews.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'change_status', review_id: id, status: newStatus })
        }).then(res => res.json()).then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        }).catch(e => {
            alert('Network error occurred.');
        });
    }

    function deleteReview(id) {
        if(confirm("Permanently delete this review?")) {
            fetch('../../api/admin_reviews.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete_review', review_id: id })
            }).then(res => res.json()).then(data => {
                if(data.success) {
                    const row = document.getElementById('review-row-' + id);
                    if(row) {
                        row.style.transition = "all 0.3s";
                        row.style.opacity = '0';
                        setTimeout(() => row.remove(), 300);
                    } else location.reload();
                } else alert('Error: ' + data.message);
            }).catch(e => alert('Network error occurred.'));
        }
    }
</script>

<?php include '../../includes/admin/admin_footer.php'; ?>
