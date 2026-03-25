<?php
require_once '../../config/db.php';
session_start();

$root = "../../";
$page_title = 'Manage Categories';
include '../../includes/admin/admin_header.php';

// Fetch all categories with total courses count
$sql = "SELECT c.*, COUNT(co.id) as total_courses 
        FROM categories c
        LEFT JOIN courses co ON c.id = co.category_id
        GROUP BY c.id
        ORDER BY c.name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$categories = $stmt->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px;">
    <div>
        <h1 style="font-size: 28px; font-weight: 800; color: var(--dark-color); margin-bottom: 8px;">Course Categories</h1>
        <p style="color: var(--gray-color); font-size: 15px;">Manage the categorization structure for all platform courses.</p>
    </div>
    <div>
        <button class="btn btn-primary" onclick="openCategoryModal('add')" style="padding: 10px 20px; font-weight: 700;">
            <i class="fa fa-folder-plus" style="margin-right: 8px;"></i> Add Category
        </button>
    </div>
</div>

<div class="admin-table-container" style="background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow);">
    <table class="admin-table" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color);">
                <th style="padding: 15px 20px; text-align: left; font-weight: 800; color: var(--gray-color); text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">ID</th>
                <th style="padding: 15px 20px; text-align: left; font-weight: 800; color: var(--gray-color); text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Name</th>
                <th style="padding: 15px 20px; text-align: left; font-weight: 800; color: var(--gray-color); text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Icon</th>
                <th style="padding: 15px 20px; text-align: left; font-weight: 800; color: var(--gray-color); text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Slug</th>
                <th style="padding: 15px 20px; text-align: center; font-weight: 800; color: var(--gray-color); text-transform: uppercase; font-size: 12px; letter-spacing: 1px;"># Courses</th>
                <th style="padding: 15px 20px; text-align: center; font-weight: 800; color: var(--gray-color); text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($categories)): ?>
                <tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--gray-color);">No categories found.</td></tr>
            <?php
else: ?>
                <?php foreach ($categories as $cat): ?>
                <tr style="border-bottom: 1px solid var(--border-color); transition: background-color 0.2s;" id="cat-row-<?php echo $cat['id']; ?>" onmouseover="this.style.backgroundColor='var(--light-gray)'" onmouseout="this.style.backgroundColor='transparent'">
                    <td style="padding: 15px 20px; font-weight: 700; color: var(--gray-color);">#<?php echo $cat['id']; ?></td>
                    <td style="padding: 15px 20px; font-weight: 800; color: var(--dark-color);"><?php echo htmlspecialchars($cat['name']); ?></td>
                    <td style="padding: 15px 20px; color: var(--primary-color); font-size: 18px;"><i class="fa <?php echo htmlspecialchars($cat['icon']); ?>"></i></td>
                    <td style="padding: 15px 20px; color: var(--gray-color); font-size: 13px; font-family: monospace;"><?php echo htmlspecialchars($cat['slug']); ?></td>
                    <td style="padding: 15px 20px; text-align: center;">
                        <span style="background: rgba(46, 204, 113, 0.1); color: #2ecc71; padding: 5px 12px; border-radius: 20px; font-weight: 800; font-size: 12px;">
                            <?php echo $cat['total_courses']; ?>
                        </span>
                    </td>
                    <td style="padding: 15px 20px;">
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            <button class="btn-icon" title="Edit Category" onclick="openCategoryModal('edit', <?php echo $cat['id']; ?>, '<?php echo htmlspecialchars(addslashes($cat['name'])); ?>', '<?php echo htmlspecialchars(addslashes($cat['icon'])); ?>')" style="width: 32px; height: 32px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--dark-color); cursor: pointer;"><i class="fa fa-edit"></i></button>
                            <?php if ($cat['total_courses'] == 0): ?>
                            <button class="btn-icon" title="Delete Category" onclick="deleteCategory(<?php echo $cat['id']; ?>)" style="width: 32px; height: 32px; border-radius: 6px; border: 1px solid rgba(231,76,60,0.3); background: rgba(231,76,60,0.05); color: #e74c3c; cursor: pointer;"><i class="fa fa-trash-alt"></i></button>
                            <?php
        else: ?>
                            <button class="btn-icon" title="Cannot Delete: Courses Exist" style="width: 32px; height: 32px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--gray-color); cursor: not-allowed; opacity: 0.5;"><i class="fa fa-trash-alt"></i></button>
                            <?php
        endif; ?>
                        </div>
                    </td>
                </tr>
                <?php
    endforeach; ?>
            <?php
endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="category-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 2000; align-items: center; justify-content: center;">
    <div style="background: var(--bg-card); width: 100%; max-width: 450px; border-radius: 12px; padding: 30px; border: 1px solid var(--border-color); box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative;">
        <button onclick="closeCategoryModal()" style="position: absolute; top: 15px; right: 20px; background: none; border: none; font-size: 24px; cursor: pointer; color: var(--gray-color);">&times;</button>
        <h2 id="modal-title" style="font-size: 22px; font-weight: 800; color: var(--dark-color); margin-bottom: 25px;">Add Category</h2>
        
        <form id="category-form">
            <input type="hidden" id="cat-action" name="action" value="add_category">
            <input type="hidden" id="cat-id" name="id" value="0">
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 8px; color: var(--dark-color);">Category Name</label>
                <input type="text" id="cat-name" required placeholder="e.g. Graphic Design" style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-family: inherit;">
            </div>
            
            <div style="margin-bottom: 25px;">
                <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 8px; color: var(--dark-color);">FontAwesome Icon Class</label>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <div id="icon-preview" style="width: 45px; height: 45px; background: var(--light-gray); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 20px; color: var(--primary-color); border: 1px solid var(--border-color);">
                        <i class="fa fa-code"></i>
                    </div>
                    <input type="text" id="cat-icon" required value="fa-code" placeholder="e.g. fa-paint-brush" style="flex: 1; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-family: inherit;" oninput="document.getElementById('icon-preview').innerHTML = '<i class=\'fa ' + this.value + '\'></i>'">
                </div>
                <p style="font-size: 11px; color: var(--gray-color); margin-top: 8px;">Find icons at <a href="https://fontawesome.com/v5/search?m=free" target="_blank" style="color: var(--primary-color);">FontAwesome Free</a></p>
            </div>
            
            <div id="cat-msg" style="margin-bottom: 15px; font-weight: 700; font-size: 13px; text-align: center;"></div>

            <button type="submit" id="cat-submit-btn" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 16px;">Save Category</button>
        </form>
    </div>
</div>

<script>
    function openCategoryModal(mode, id = 0, name = '', icon = 'fa-code') {
        document.getElementById('category-modal').style.display = 'flex';
        document.getElementById('cat-msg').innerText = '';
        
        const form = document.getElementById('category-form');
        document.getElementById('cat-id').value = id;
        
        if(mode === 'add') {
            document.getElementById('modal-title').innerText = 'Add Category';
            document.getElementById('cat-action').value = 'add_category';
            document.getElementById('cat-name').value = '';
            document.getElementById('cat-icon').value = 'fa-code';
            document.getElementById('icon-preview').innerHTML = '<i class="fa fa-code"></i>';
            document.getElementById('cat-submit-btn').innerText = 'Create Category';
        } else {
            document.getElementById('modal-title').innerText = 'Edit Category';
            document.getElementById('cat-action').value = 'edit_category';
            document.getElementById('cat-name').value = name;
            document.getElementById('cat-icon').value = icon;
            document.getElementById('icon-preview').innerHTML = '<i class="fa ' + icon + '"></i>';
            document.getElementById('cat-submit-btn').innerText = 'Save Changes';
        }
    }

    function closeCategoryModal() {
        document.getElementById('category-modal').style.display = 'none';
        document.getElementById('category-form').reset();
    }

    document.getElementById('category-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('cat-submit-btn');
        const msg = document.getElementById('cat-msg');
        
        btn.disabled = true;
        btn.innerText = 'Saving...';
        msg.innerText = '';

        const data = {
            action: document.getElementById('cat-action').value,
            id: document.getElementById('cat-id').value,
            name: document.getElementById('cat-name').value,
            icon: document.getElementById('cat-icon').value
        };

        fetch('../../api/admin_categories.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
            msg.innerText = data.message;
            msg.style.color = data.success ? '#2ecc71' : '#e74c3c';
            if (data.success) {
                setTimeout(() => location.reload(), 800);
            } else {
                btn.disabled = false;
                btn.innerText = 'Save Category';
            }
        })
        .catch(err => {
            msg.innerText = 'Network error.';
            msg.style.color = '#e74c3c';
            btn.disabled = false;
            btn.innerText = 'Save Category';
        });
    });

    function deleteCategory(id) {
        if(confirm("Are you sure you want to delete this category? This action is permanent!")) {
            fetch('../../api/admin_categories.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete_category', id: id })
            }).then(res => res.json()).then(data => {
                if(data.success) {
                    const row = document.getElementById('cat-row-' + id);
                    if(row) {
                        row.style.background = '#ffebee';
                        setTimeout(() => row.remove(), 500);
                    } else {
                        location.reload();
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            }).catch(e => {
                alert('Network error occurred.');
            });
        }
    }
</script>

<?php include '../../includes/admin/admin_footer.php'; ?>
