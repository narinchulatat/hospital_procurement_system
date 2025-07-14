<?php
require_once 'includes/functions.php';
checkLogin();

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get statistics
$procurement_request = new ProcurementRequest($db);
$statistics = $procurement_request->getStatistics();

// Get department statistics if user is department head
$department_stats = null;
if ($_SESSION['user_role'] == 'department_head' && $_SESSION['department_id']) {
    $department_stats = $procurement_request->getStatisticsByDepartment($_SESSION['department_id']);
}

// Get recent requests
$recent_requests = null;
if ($_SESSION['user_role'] == 'admin') {
    $recent_requests = $procurement_request->readAll();
} elseif ($_SESSION['user_role'] == 'department_head') {
    $recent_requests = $procurement_request->readByDepartment($_SESSION['department_id']);
} else {
    $recent_requests = $procurement_request->readByUser($_SESSION['user_id']);
}

// Get current budget year
$budget_year = new BudgetYear($db);
$current_budget_year = $budget_year->getCurrentBudgetYear();

$page_title = 'Dashboard';
$base_url = '';
include 'includes/header.php';
?>
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                    <p class="text-gray-600">ภาพรวมระบบจัดซื้อครุภัณฑ์คอมพิวเตอร์</p>
                </div>

                <!-- Current Budget Year Info -->
                <?php if ($current_budget_year): ?>
                <div class="mb-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt text-blue-600 text-xl mr-3"></i>
                            <div>
                                <h5 class="font-semibold text-blue-900">ปีงบประมาณ <?php echo $current_budget_year->year_be; ?></h5>
                                <p class="text-blue-700 text-sm">
                                    ช่วงการขอ: <?php echo formatThaiDate($current_budget_year->request_start_date); ?> - <?php echo formatThaiDate($current_budget_year->request_end_date); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <div class="flex items-center justify-center mb-4">
                            <div class="p-3 rounded-full bg-blue-100">
                                <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                            </div>
                        </div>
                        <h6 class="text-sm text-gray-600 mb-2">คำขอทั้งหมด</h6>
                        <h2 class="text-2xl font-bold text-gray-900"><?php echo number_format($statistics['total_requests']); ?></h2>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <div class="flex items-center justify-center mb-4">
                            <div class="p-3 rounded-full bg-yellow-100">
                                        <i class="fas fa-clock text-warning fs-4"></i>
                                    </div>
                                </div>
                                <h6 class="card-title text-muted">รอการอนุมัติ</h6>
                                <h2 class="card-text text-dark"><?php echo number_format($statistics['pending_requests']); ?></h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-3">
                                    <div class="p-3 rounded-circle bg-success bg-opacity-10">
                                        <i class="fas fa-check-circle text-success fs-4"></i>
                                    </div>
                                </div>
                                <h6 class="card-title text-muted">อนุมัติแล้ว</h6>
                                <h2 class="card-text text-dark"><?php echo number_format($statistics['approved_requests']); ?></h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-3">
                                    <div class="p-3 rounded-circle bg-danger bg-opacity-10">
                                        <i class="fas fa-times-circle text-danger fs-4"></i>
                                    </div>
                                </div>
                                <h6 class="card-title text-muted">ปฏิเสธ</h6>
                                <h2 class="card-text text-dark"><?php echo number_format($statistics['rejected_requests']); ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Requests -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">คำขอล่าสุด</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>เลขที่คำขอ</th>
                                        <th>รายการ</th>
                                        <th>แผนก</th>
                                        <th>จำนวนเงิน</th>
                                        <th>สถานะ</th>
                                        <th>วันที่</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $count = 0;
                                    while ($row = $recent_requests->fetch(PDO::FETCH_ASSOC)) : 
                                        if ($count >= 10) break;
                                        $count++;
                                        
                                        $status = new Status($db);
                                        $status_class = $status->getStatusBadgeClass($row['status_name']);
                                        $status_text = $status->getStatusTranslation($row['status_name']);
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="fw-semibold"><?php echo htmlspecialchars($row['request_number']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['department_name']); ?></td>
                                        <td>฿<?php echo number_format($row['total_price'], 2); ?></td>
                                        <td>
                                            <span class="badge <?php echo $status_class; ?>">
                                                <?php echo $status_text; ?>
                                            </span>
                                        </td>
                                        <td><?php echo formatThaiDate($row['created_at']); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

<?php include 'includes/footer.php'; ?>