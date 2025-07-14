<?php
require_once '../../includes/functions.php';
checkLogin();
checkRole(['admin']);

// Initialize variables
$page_title = 'จัดการผู้ใช้งาน';
$search = $_GET['search'] ?? '';
$page = $_GET['page'] ?? 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get users
$user = new User($db);
$users_query = $user->readAllWithPagination($search, $per_page, $offset);
$users = $users_query['data'];
$total_users = $users_query['total'];
$total_pages = ceil($total_users / $per_page);

// Get departments for filter
$department = new Department($db);
$departments = $department->readAll();

// Get roles for filter
$role = new Role($db);
$roles = $role->readAll();

// Set base URL for admin pages
$base_url = '../../';

include '../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">จัดการผู้ใช้งาน</h1>
    <a href="create.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>เพิ่มผู้ใช้งาน
    </a>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">ค้นหา</label>
                <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="ชื่อ, นามสกุล, อีเมล">
            </div>
            <div class="col-md-3">
                <label for="department" class="form-label">แผนก</label>
                <select class="form-select" id="department" name="department">
                    <option value="">ทั้งหมด</option>
                    <?php while ($dept = $departments->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?php echo $dept['id']; ?>" <?php echo ($_GET['department'] ?? '') == $dept['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dept['department_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="role" class="form-label">บทบาท</label>
                <select class="form-select" id="role" name="role">
                    <option value="">ทั้งหมด</option>
                    <?php while ($role_data = $roles->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?php echo $role_data['id']; ?>" <?php echo ($_GET['role'] ?? '') == $role_data['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($role_data['role_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-2">
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

<!-- Users Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">รายการผู้ใช้งาน (<?php echo number_format($total_users); ?> คน)</h5>
    </div>
    <div class="card-body">
        <?php if ($total_users > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th>อีเมล</th>
                            <th>แผนก</th>
                            <th>บทบาท</th>
                            <th>สถานะ</th>
                            <th>วันที่สร้าง</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user_data = $users->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $user_data['id']; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2">
                                            <?php echo strtoupper(substr($user_data['first_name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($user_data['first_name'] . ' ' . $user_data['last_name']); ?></div>
                                            <div class="small text-muted"><?php echo htmlspecialchars($user_data['username']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user_data['email']); ?></td>
                                <td><?php echo htmlspecialchars($user_data['department_name'] ?? 'ไม่ระบุ'); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo getRoleBadgeColor($user_data['role_name']); ?>">
                                        <?php echo htmlspecialchars($user_data['role_name']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $user_data['is_active'] ? 'success' : 'danger'; ?>">
                                        <?php echo $user_data['is_active'] ? 'เปิดใช้งาน' : 'ปิดใช้งาน'; ?>
                                    </span>
                                </td>
                                <td><?php echo formatThaiDate($user_data['created_at']); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="edit.php?id=<?php echo $user_data['id']; ?>" class="btn btn-outline-primary" title="แก้ไข">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($user_data['id'] != $_SESSION['user_id']): ?>
                                            <a href="delete.php?id=<?php echo $user_data['id']; ?>" class="btn btn-outline-danger delete-btn" title="ลบ" data-name="<?php echo htmlspecialchars($user_data['first_name'] . ' ' . $user_data['last_name']); ?>">
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
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&department=<?php echo $_GET['department'] ?? ''; ?>&role=<?php echo $_GET['role'] ?? ''; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&department=<?php echo $_GET['department'] ?? ''; ?>&role=<?php echo $_GET['role'] ?? ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&department=<?php echo $_GET['department'] ?? ''; ?>&role=<?php echo $_GET['role'] ?? ''; ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">ไม่พบผู้ใช้งาน</h5>
                <p class="text-muted">ลองค้นหาด้วยคำอื่น หรือ <a href="create.php">เพิ่มผู้ใช้งานใหม่</a></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
function getRoleBadgeColor($role) {
    switch ($role) {
        case 'admin':
            return 'danger';
        case 'department_head':
            return 'warning';
        case 'staff':
            return 'info';
        default:
            return 'secondary';
    }
}

include '../../includes/footer.php';
?>