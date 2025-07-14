<?php
// Simple test dashboard without complex features for now
$page_title = 'Dashboard';
$base_url = '';

// For testing purposes, let's create some mock data
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['user_name'] = 'Admin User';
$_SESSION['user_role'] = 'admin';
$_SESSION['department_id'] = 1;
$_SESSION['department_name'] = 'IT Department';

include 'includes/sidebar.php';
?>
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                    <p class="text-gray-600">ภาพรวมระบบจัดซื้อครุภัณฑ์คอมพิวเตอร์</p>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100">
                                <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">คำขอทั้งหมด</p>
                                <p class="text-2xl font-bold text-gray-900">0</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100">
                                <i class="fas fa-clock text-yellow-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">รออนุมัติ</p>
                                <p class="text-2xl font-bold text-gray-900">0</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">อนุมัติแล้ว</p>
                                <p class="text-2xl font-bold text-gray-900">0</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100">
                                <i class="fas fa-times-circle text-red-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">ปฏิเสธ</p>
                                <p class="text-2xl font-bold text-gray-900">0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">ยื่นคำขอใหม่</h3>
                                <p class="text-gray-600">สร้างคำขอจัดซื้อครุภัณฑ์</p>
                            </div>
                            <div class="text-blue-600">
                                <i class="fas fa-plus-circle text-2xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="request_form.php" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                                เริ่มต้น
                            </a>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">ดูคำขอทั้งหมด</h3>
                                <p class="text-gray-600">ติดตามสถานะคำขอ</p>
                            </div>
                            <div class="text-green-600">
                                <i class="fas fa-list text-2xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="requests.php" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition duration-200">
                                ดูรายการ
                            </a>
                        </div>
                    </div>

                    <?php if ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'department_head'): ?>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">อนุมัติคำขอ</h3>
                                <p class="text-gray-600">อนุมัติคำขอจัดซื้อ</p>
                            </div>
                            <div class="text-purple-600">
                                <i class="fas fa-check-circle text-2xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="approve_request.php" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition duration-200">
                                อนุมัติ
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">กิจกรรมล่าสุด</h3>
                    </div>
                    <div class="p-6">
                        <div class="text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-4"></i>
                            <p>ยังไม่มีกิจกรรมล่าสุด</p>
                            <p class="text-sm">เมื่อมีการยื่นคำขอหรือกิจกรรมอื่นๆ จะแสดงที่นี่</p>
                        </div>
                    </div>
                </div>

