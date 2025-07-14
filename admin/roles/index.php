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
?>
<?php include '../../includes/header.php'; ?>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">จัดการบทบาท</h1>
            <a href="create.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                <i class="fas fa-plus mr-2"></i>เพิ่มบทบาท
            </a>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">ค้นหา</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="ชื่อบทบาท, คำอธิบาย">
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

        <!-- Roles Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h5 class="text-lg font-semibold text-gray-900">รายการบทบาท (<?php echo number_format($total_roles); ?> บทบาท)</h5>
            </div>
            <div class="overflow-x-auto">
                <?php if ($total_roles > 0): ?>
                    <table id="roles-table" class="data-table min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อบทบาท</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">คำอธิบาย</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">วันที่สร้าง</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider no-sort">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while ($role_data = $roles->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $role_data['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center mr-3">
                                                <i class="fas fa-user-tag"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($role_data['name']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <?php if ($role_data['description']): ?>
                                            <span class="max-w-xs truncate" title="<?php echo htmlspecialchars($role_data['description']); ?>">
                                                <?php echo htmlspecialchars($role_data['description']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-gray-500">ไม่มีคำอธิบาย</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo formatThaiDate($role_data['created_at']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="edit.php?id=<?php echo $role_data['id']; ?>" 
                                               class="text-blue-600 hover:text-blue-900" title="แก้ไข">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($role_data['id'] > 3): // Don't allow deletion of default roles ?>
                                                <a href="delete.php?id=<?php echo $role_data['id']; ?>" 
                                                   class="text-red-600 hover:text-red-900 delete-btn" title="ลบ" 
                                                   data-name="<?php echo htmlspecialchars($role_data['name']); ?>">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="text-center py-8">
                        <i class="fas fa-user-tag text-gray-300 text-4xl mb-4"></i>
                        <h5 class="text-gray-500 text-lg font-medium">ไม่พบบทบาท</h5>
                        <p class="text-gray-500 mt-2">ลองค้นหาด้วยคำอื่น หรือ <a href="create.php" class="text-blue-600 hover:text-blue-800">เพิ่มบทบาทใหม่</a></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.tailwindcss.min.js"></script>
    <script src="../../js/main.js"></script>

<?php include '../../includes/footer.php'; ?>