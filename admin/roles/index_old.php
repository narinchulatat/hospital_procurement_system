<?php
require_once '../../includes/functions.php';
checkLogin();
checkRole(['admin']);

// Initialize variables
$page_title = 'จัดการบทบาท';
$search = $_GET['search'] ?? '';
$page = $_GET['page'] ?? 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get roles
$role = new Role($db);
$roles_query = $role->readAllWithPagination($search, $per_page, $offset);
$roles = $roles_query['data'];
$total_roles = $roles_query['total'];
$total_pages = ceil($total_roles / $per_page);

// Set base URL for admin pages
$base_url = '../../';

include '../../includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">จัดการบทบาท</h1>
    <a href="create.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>เพิ่มบทบาท
    </a>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-8">
                <label for="search" class="form-label">ค้นหา</label>
                <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="ชื่อบทบาท, คำอธิบาย">
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search me-1"></i>ค้นหา
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Roles Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">รายการบทบาท (<?php echo number_format($total_roles); ?> บทบาท)</h5>
    </div>
    <div class="card-body">
        <?php if ($total_roles > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ชื่อบทบาท</th>
                            <th>คำอธิบาย</th>
                            <th>วันที่สร้าง</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($role_data = $roles->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $role_data['id']; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center me-2">
                                            <i class="fas fa-user-tag"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($role_data['name']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($role_data['description']): ?>
                                        <span class="text-truncate max-w-xs" title="<?php echo htmlspecialchars($role_data['description']); ?>">
                                            <?php echo htmlspecialchars($role_data['description']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">ไม่มีคำอธิบาย</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo formatThaiDate($role_data['created_at']); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="edit.php?id=<?php echo $role_data['id']; ?>" class="btn btn-outline-primary" title="แก้ไข">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($role_data['id'] > 3): // Don't allow deletion of default roles ?>
                                            <a href="delete.php?id=<?php echo $role_data['id']; ?>" class="btn btn-outline-danger delete-btn" title="ลบ" data-name="<?php echo htmlspecialchars($role_data['name']); ?>">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-user-tag fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">ไม่พบบทบาท</h5>
                <p class="text-muted">ลองค้นหาด้วยคำอื่น หรือ <a href="create.php">เพิ่มบทบาทใหม่</a></p>
            </div>
        <?php endif; ?>
    </div>
</div>

