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
?>
<?php include '../../includes/sidebar.php'; ?>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">จัดการรายการครุภัณฑ์</h1>
            <a href="create.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                <i class="fas fa-plus mr-2"></i>เพิ่มรายการครุภัณฑ์
            </a>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">ค้นหา</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="ชื่อรายการ, รหัส, คำอธิบาย">
                    </div>
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">หมวดหมู่</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                id="category" name="category">
                            <option value="">ทั้งหมด</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category === $cat ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="budget_year_id" class="block text-sm font-medium text-gray-700 mb-1">ปีงบประมาณ</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                id="budget_year_id" name="budget_year_id">
                            <option value="">ทั้งหมด</option>
                            <?php while ($year = $budget_years->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?php echo $year['id']; ?>" <?php echo $budget_year_id == $year['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($year['year_be']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                            <i class="fas fa-search mr-2"></i>ค้นหา
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Items Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h5 class="text-lg font-semibold text-gray-900">รายการครุภัณฑ์ (<?php echo number_format($total_items); ?> รายการ)</h5>
            </div>
            <div class="overflow-x-auto">
                <?php if ($total_items > 0): ?>
                    <table id="items-table" class="data-table min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อรายการ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รหัส</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">หมวดหมู่</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ราคาต่อหน่วย</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">หน่วย</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ปีงบประมาณ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider no-sort">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while ($item_data = $items->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $item_data['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center mr-3">
                                                <i class="fas fa-boxes"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($item_data['name']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php if ($item_data['code']): ?>
                                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-md text-xs font-mono">
                                                <?php echo htmlspecialchars($item_data['code']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-gray-500">ไม่มีรหัส</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php if ($item_data['category']): ?>
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-md text-xs">
                                                <?php echo htmlspecialchars($item_data['category']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-gray-500">ไม่ระบุ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php if ($item_data['price_per_unit']): ?>
                                            <span class="text-green-600 font-medium">
                                                <?php echo number_format($item_data['price_per_unit'], 2); ?> บาท
                                            </span>
                                        <?php else: ?>
                                            <span class="text-gray-500">ไม่ระบุ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($item_data['unit'] ?? 'หน่วย'); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($item_data['year_be'] ?? 'ไม่ระบุ'); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="edit.php?id=<?php echo $item_data['id']; ?>" 
                                               class="text-blue-600 hover:text-blue-900" title="แก้ไข">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete.php?id=<?php echo $item_data['id']; ?>" 
                                               class="text-red-600 hover:text-red-900 delete-btn" title="ลบ" 
                                               data-name="<?php echo htmlspecialchars($item_data['name']); ?>">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="text-center py-8">
                        <i class="fas fa-boxes text-gray-300 text-4xl mb-4"></i>
                        <h5 class="text-gray-500 text-lg font-medium">ไม่พบรายการครุภัณฑ์</h5>
                        <p class="text-gray-500 mt-2">ลองค้นหาด้วยคำอื่น หรือ <a href="create.php" class="text-blue-600 hover:text-blue-800">เพิ่มรายการใหม่</a></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.tailwindcss.min.js"></script>
    <script src="../../js/main.js"></script>

