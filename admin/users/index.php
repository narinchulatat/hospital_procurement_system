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

function getRoleBadgeColor($role) {
    switch ($role) {
        case 'admin':
            return 'bg-red-100 text-red-800';
        case 'department_head':
            return 'bg-yellow-100 text-yellow-800';
        case 'staff':
            return 'bg-blue-100 text-blue-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}
?>
<?php include '../../includes/header.php'; ?>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">จัดการผู้ใช้งาน</h1>
            <a href="create.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                <i class="fas fa-plus mr-2"></i>เพิ่มผู้ใช้งาน
            </a>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">ค้นหา</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="ชื่อ, นามสกุล, อีเมล">
                    </div>
                    <div>
                        <label for="department" class="block text-sm font-medium text-gray-700 mb-1">แผนก</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                id="department" name="department">
                            <option value="">ทั้งหมด</option>
                            <?php $departments->execute(); while ($dept = $departments->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?php echo $dept['id']; ?>" <?php echo ($_GET['department'] ?? '') == $dept['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dept['department_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">บทบาท</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                id="role" name="role">
                            <option value="">ทั้งหมด</option>
                            <?php $roles->execute(); while ($role_data = $roles->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?php echo $role_data['id']; ?>" <?php echo ($_GET['role'] ?? '') == $role_data['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($role_data['role_name']); ?>
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

        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h5 class="text-lg font-semibold text-gray-900">รายการผู้ใช้งาน (<?php echo number_format($total_users); ?> คน)</h5>
            </div>
            <div class="overflow-x-auto">
                <?php if ($total_users > 0): ?>
                    <table id="users-table" class="data-table min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อ-นามสกุล</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">อีเมล</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">แผนก</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">บทบาท</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">วันที่สร้าง</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider no-sort">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while ($user_data = $users->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $user_data['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center mr-3 text-sm font-medium">
                                                <?php echo strtoupper(substr($user_data['first_name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($user_data['first_name'] . ' ' . $user_data['last_name']); ?></div>
                                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($user_data['username']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($user_data['email']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($user_data['department_name'] ?? 'ไม่ระบุ'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo getRoleBadgeColor($user_data['role_name']); ?>">
                                            <?php echo htmlspecialchars($user_data['role_name']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $user_data['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo $user_data['is_active'] ? 'เปิดใช้งาน' : 'ปิดใช้งาน'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo formatThaiDate($user_data['created_at']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="edit.php?id=<?php echo $user_data['id']; ?>" 
                                               class="text-blue-600 hover:text-blue-900" title="แก้ไข">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($user_data['id'] != $_SESSION['user_id']): ?>
                                                <a href="delete.php?id=<?php echo $user_data['id']; ?>" 
                                                   class="text-red-600 hover:text-red-900 delete-btn" title="ลบ" 
                                                   data-name="<?php echo htmlspecialchars($user_data['first_name'] . ' ' . $user_data['last_name']); ?>">
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
                        <i class="fas fa-users text-gray-300 text-4xl mb-4"></i>
                        <h5 class="text-gray-500 text-lg font-medium">ไม่พบผู้ใช้งาน</h5>
                        <p class="text-gray-500 mt-2">ลองค้นหาด้วยคำอื่น หรือ <a href="create.php" class="text-blue-600 hover:text-blue-800">เพิ่มผู้ใช้งานใหม่</a></p>
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