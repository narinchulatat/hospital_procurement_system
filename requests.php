<?php
require_once 'includes/functions.php';
checkLogin();

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get filter parameters
$status_filter = isset($_GET['status']) ? (int)$_GET['status'] : 0;
$department_filter = isset($_GET['department']) ? (int)$_GET['department'] : 0;
$budget_year_filter = isset($_GET['budget_year']) ? (int)$_GET['budget_year'] : 0;
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';

// Build query based on user role
$procurement_request = new ProcurementRequest($db);
$query = "SELECT pr.*, u.first_name, u.last_name, d.name as department_name, 
                 sd.name as sub_department_name, i.name as item_name, i.code as item_code,
                 s.name as status_name, b.year_be, approver.first_name as approver_first_name,
                 approver.last_name as approver_last_name
          FROM procurement_requests pr
          LEFT JOIN users u ON pr.user_id = u.id
          LEFT JOIN departments d ON pr.department_id = d.id
          LEFT JOIN sub_departments sd ON pr.sub_department_id = sd.id
          LEFT JOIN items i ON pr.item_id = i.id
          LEFT JOIN statuses s ON pr.status_id = s.id
          LEFT JOIN budget_years b ON pr.budget_year_id = b.id
          LEFT JOIN users approver ON pr.approved_by = approver.id
          WHERE 1=1";

$params = [];

// Add role-based filters
if ($_SESSION['user_role'] == 'staff') {
    $query .= " AND pr.user_id = ?";
    $params[] = $_SESSION['user_id'];
} elseif ($_SESSION['user_role'] == 'department_head') {
    $query .= " AND pr.department_id = ?";
    $params[] = $_SESSION['department_id'];
} elseif ($_SESSION['user_role'] == 'procurement_staff') {
    $query .= " AND pr.status_id >= 2"; // Only approved requests
}

// Add filters
if ($status_filter > 0) {
    $query .= " AND pr.status_id = ?";
    $params[] = $status_filter;
}

if ($department_filter > 0) {
    $query .= " AND pr.department_id = ?";
    $params[] = $department_filter;
}

if ($budget_year_filter > 0) {
    $query .= " AND pr.budget_year_id = ?";
    $params[] = $budget_year_filter;
}

if (!empty($search)) {
    $query .= " AND (pr.request_number LIKE ? OR i.name LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$query .= " ORDER BY pr.created_at DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);

// Get filter options
$status = new Status($db);
$statuses = $status->readAll();

$department = new Department($db);
$departments = $department->readAll();

$budget_year = new BudgetYear($db);
$budget_years = $budget_year->readAll();

$page_title = 'รายการคำขอ';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - ระบบจัดซื้อครุภัณฑ์คอมพิวเตอร์</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">รายการคำขอ</h1>
                    <p class="text-gray-600">รายการคำขอซื้อครุภัณฑ์คอมพิวเตอร์</p>
                </div>
                <?php if ($_SESSION['user_role'] == 'staff' || $_SESSION['user_role'] == 'department_head'): ?>
                <a href="request_form.php" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>ยื่นคำขอใหม่
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">สถานะ</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">ทั้งหมด</option>
                            <?php while ($status_row = $statuses->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $status_row['id']; ?>" 
                                    <?php echo ($status_filter == $status_row['id']) ? 'selected' : ''; ?>>
                                <?php echo $status->getStatusTranslation($status_row['name']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <?php if ($_SESSION['user_role'] == 'admin'): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">แผนก</label>
                        <select name="department" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">ทั้งหมด</option>
                            <?php while ($dept_row = $departments->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $dept_row['id']; ?>" 
                                    <?php echo ($department_filter == $dept_row['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dept_row['name']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ปีงบประมาณ</label>
                        <select name="budget_year" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">ทั้งหมด</option>
                            <?php while ($year_row = $budget_years->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $year_row['id']; ?>" 
                                    <?php echo ($budget_year_filter == $year_row['id']) ? 'selected' : ''; ?>>
                                <?php echo $year_row['year_be']; ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ค้นหา</label>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="เลขที่คำขอ, รายการ, ชื่อผู้ขอ" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="flex items-end space-x-2">
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            <i class="fas fa-search mr-2"></i>ค้นหา
                        </button>
                        <a href="requests.php" 
                           class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                            <i class="fas fa-times mr-2"></i>ล้าง
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        รายการคำขอ (<?php echo $stmt->rowCount(); ?> รายการ)
                    </h3>
                    <?php if ($_SESSION['user_role'] == 'admin'): ?>
                    <div class="flex items-center space-x-2">
                        <a href="export_requests.php?type=excel" 
                           class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700">
                            <i class="fas fa-file-excel mr-1"></i>Excel
                        </a>
                        <a href="export_requests.php?type=pdf" 
                           class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                            <i class="fas fa-file-pdf mr-1"></i>PDF
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">เลขที่คำขอ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ผู้ขอ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รายการ</th>
                            <?php if ($_SESSION['user_role'] == 'admin'): ?>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">แผนก</th>
                            <?php endif; ?>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">จำนวน</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">จำนวนเงิน</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">วันที่</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($row['request_number']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($row['item_name']); ?>
                            </td>
                            <?php if ($_SESSION['user_role'] == 'admin'): ?>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($row['department_name']); ?>
                            </td>
                            <?php endif; ?>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo number_format($row['quantity']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ฿<?php echo number_format($row['total_price'], 2); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php 
                                $status_class = $status->getStatusBadgeClass($row['status_name']);
                                $status_text = $status->getStatusTranslation($row['status_name']);
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo formatThaiDate($row['created_at']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="request_detail.php?id=<?php echo $row['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye mr-1"></i>ดูรายละเอียด
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Display messages
        <?php if (isset($_SESSION['success'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: '<?php echo $_SESSION['success']; ?>',
                confirmButtonText: 'ตกลง'
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: '<?php echo $_SESSION['error']; ?>',
                confirmButtonText: 'ตกลง'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </script>
</body>
</html>