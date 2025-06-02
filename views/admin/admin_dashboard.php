<?php
session_start();
require_once('C:\laragon\www\UASWEBCOK\config\config.php');
include_once('../templates/header.php');
include_once('../templates/sidebar_admin.php');
require_once('C:\laragon\www\UASWEBCOK\controllers\adminControllers.php');
$data = getDashboardData();
?>

<!-- Main content -->
<main class="flex-1 overflow-y-auto">
    <div class="p-6">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-tachometer-alt mr-3 text-blue-600"></i>
                Admin Dashboard
            </h1>
            <p class="text-gray-600 mt-2">Panel kontrol dan ringkasan sistem manajemen qurban</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- iki card warga -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-users text-2xl text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Statistik Pengguna</p>
                        <p class="text-xl font-bold text-blue-600">Total: <?= $data['user_stats']['warga'] ?? 0 ?></p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between mb-1 text-sm">
                            <span class="text-gray-600 font-bold">Warga</span>
                            <span class="text-gray-600 font-bold"><?= $data['user_stats']['warga'] - $data['user_stats']['berqurban'] - $data['user_stats']['panitia'] ?></span>
                        </div>

                    </div>
                    <div>
                        <div class="flex justify-between mb-1 text-sm">
                            <span class="text-green-600 font-bold">Berqurban</span>
                            <span class="text-green-600 font-bold"><?= $data['user_stats']['berqurban'] ?? 0 ?></span>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1 text-sm">
                            <span class="text-blue-600 font-bold">Panitia</span>
                            <span class="text-purple-600 font-bold"><?= $data['user_stats']['panitia'] ?? 0 ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- iki Card daging -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="fas fa-drumstick-bite text-2xl text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Distribusi Daging</p>
                        <p class="text-xl font-bold text-red-600"><?= ($data['meat_distribution']['beef'] + $data['meat_distribution']['goat']) ?? 0 ?> kg</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between mb-1 text-sm">
                            <span class="text-red-600 font-bold">Daging Sapi</span>
                            <span class="text-red-600 font-bold"><?= $data['meat_distribution']['beef'] ?? 0 ?> kg</span>
                        </div>

                    </div>
                    <div>
                        <div class="flex justify-between mb-1 text-sm">
                            <span class="text-blue-600 font-bold">Daging Kambing</span>
                            <span class="text-blue-600 font-bold"><?= $data['meat_distribution']['goat'] ?? 0 ?> kg</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Verify Meat Pickup -->
            <a href="qr_scanner.php" class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm flex items-center hover:shadow-md transition-all duration-200">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="ml-3">
                    <h4 class="font-medium">Verifikasi Pengambilan</h4>
                    <p class="text-xs text-gray-500 mt-1">Validasi pengambilan daging</p>
                </div>
            </a>

            <!-- Financial Reports -->
            <a href="financial.php" class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm flex items-center hover:shadow-md transition-all duration-200">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="ml-3">
                    <h4 class="font-medium">Laporan Keuangan</h4>
                    <p class="text-xs text-gray-500 mt-1">Lihat & unduh laporan keuangan</p>
                </div>
            </a>

            <!-- Meat Allocation -->
            <a href="meat_allocation.php" class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm flex items-center hover:shadow-md transition-all duration-200">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <div class="ml-3">
                    <h4 class="font-medium">Alokasi Daging</h4>
                    <p class="text-xs text-gray-500 mt-1">Atur pembagian daging sapi/kambing</p>
                </div>
            </a>

            <!-- //user management -->
            <a href="users.php" class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm flex items-center hover:shadow-md transition-all duration-200">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div class="ml-3">
                    <h4 class="font-medium">Manajemen Pengguna</h4>
                    <p class="text-xs text-gray-500 mt-1">Kelola role warga</p>
                </div>
            </a>
        </div>


        <!-- Financial Summary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="card-gradient text-white p-6 rounded-t-xl">
                <h3 class="text-xl font-semibold flex items-center">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Ringkasan Keuangan
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div class="p-4 bg-green-50 rounded-lg">
                        <p class="text-sm font-medium text-green-600">Pemasukan</p>
                        <p class="text-2xl font-bold text-green-700">Rp <?= number_format($data['financial_summary']['pemasukan'] ?? 0, 0, ',', '.') ?></p>
                    </div>
                    <div class="p-4 bg-red-50 rounded-lg">
                        <p class="text-sm font-medium text-red-600">Pengeluaran</p>
                        <p class="text-2xl font-bold text-red-700">Rp <?= number_format($data['financial_summary']['pengeluaran'] ?? 0, 0, ',', '.') ?></p>
                    </div>
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <p class="text-sm font-medium text-blue-600">Saldo</p>
                        <p class="text-2xl font-bold text-blue-700">Rp <?= number_format($data['financial_summary']['saldo'] ?? 0, 0, ',', '.') ?></p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<?php
// Include footer template - no extra scripts needed
include_once('../templates/footer.php');
?>