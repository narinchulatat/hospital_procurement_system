<?php
require_once '../../includes/functions.php';
checkLogin();
checkRole(['admin']);

// Initialize variables
$page_title = 'จัดการรายการครุภัณฑ์';
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$budget_year_id = $_GET['budget_year_id'] ?? '';
$page = $_GET['page'] ?? 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get items
$item = new Item($db);
$items_query = $item->readAllWithPagination($search, $category, $budget_year_id, $per_page, $offset);
$items = $items_query['data'];
$total_items = $items_query['total'];
$total_pages = ceil($total_items / $per_page);

// Get categories
$categories = $item->getCategories();

// Get budget years
$budget_year = new BudgetYear($db);
$budget_years = $budget_year->readAll();

// Set base URL for admin pages
$base_url = '../../';

include '../../includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">จัดการรายการครุภัณฑ์</h1>
    <a href="create.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>เพิ่มรายการครุภัณฑ์
    </a>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">ค้นหา</label>
                <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="ชื่อ, รหัส, คำอธิบาย">
            </div>
            <div class="col-md-3">
                <label for="category" class="form-label">หมวดหมู่</label>
                <select class="form-select" id="category" name="category">
                    <option value="">ทั้งหมด</option>
                    <?php while ($cat = $categories->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?php echo $cat['category']; ?>" <?php echo $category == $cat['category'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['category']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="budget_year_id" class="form-label">ปีงบประมาณ</label>
                <select class="form-select" id="budget_year_id" name="budget_year_id">
                    <option value="">ทั้งหมด</option>
                    <?php while ($year = $budget_years->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?php echo $year['id']; ?>" <?php echo $budget_year_id == $year['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($year['year_be']); ?>
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

<!-- Items Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">รายการครุภัณฑ์ (<?php echo number_format($total_items); ?> รายการ)</h5>
    </div>
    <div class="card-body">
        <?php if ($total_items > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>รหัส</th>
                            <th>ชื่อรายการ</th>
                            <th>หมวดหมู่</th>
                            <th>หน่วย</th>
                            <th>ราคาต่อหน่วย</th>
                            <th>ปีงบประมาณ</th>
                            <th>สถานะ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item_data = $items->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($item_data['code']); ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-2">
                                            <i class="fas fa-box"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($item_data['name']); ?></div>
                                            <?php if ($item_data['description']): ?>
                                                <div class="small text-muted"><?php echo htmlspecialchars($item_data['description']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo htmlspecialchars($item_data['category']); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($item_data['unit']); ?></td>
                                <td>฿<?php echo number_format($item_data['unit_price'], 2); ?></td>
                                <td>
                                    <?php if ($item_data['year_be']): ?>
                                        <span class="badge bg-warning"><?php echo htmlspecialchars($item_data['year_be']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">ไม่ระบุ</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $item_data['is_available'] ? 'success' : 'danger'; ?>">
                                        <?php echo $item_data['is_available'] ? 'พร้อมใช้' : 'ไม่พร้อมใช้'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="edit.php?id=<?php echo $item_data['id']; ?>" class="btn btn-outline-primary" title="แก้ไข">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete.php?id=<?php echo $item_data['id']; ?>" class="btn btn-outline-danger delete-btn" title="ลบ" data-name="<?php echo htmlspecialchars($item_data['name']); ?>">
                                            <i class="fas fa-trash"></i>
                                        </a>
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
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&budget_year_id=<?php echo $budget_year_id; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&budget_year_id=<?php echo $budget_year_id; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&budget_year_id=<?php echo $budget_year_id; ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-box fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">ไม่พบรายการครุภัณฑ์</h5>
                <p class="text-muted">ลองค้นหาด้วยคำอื่น หรือ <a href="create.php">เพิ่มรายการครุภัณฑ์ใหม่</a></p>
            </div>
        <?php endif; ?>
    </div>
</div>

