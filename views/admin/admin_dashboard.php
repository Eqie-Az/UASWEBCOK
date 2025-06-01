<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include configuration file
require_once($_SERVER['DOCUMENT_ROOT'] . '/UASWEBCOK/config/config.php');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../loginPage.php?error=unauthorized");
    exit();
}

// Set page title and active menu for templates
$pageTitle = 'Admin Dashboard - Sistem Manajemen Qurban';
$activeMenu = 'dashboard';

// Include header template
include_once('../templates/header.php');

// Include sidebar template
include_once('../templates/sidebar_admin.php');
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


                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Warga</p>
                                <p class="text-3xl font-bold text-blue-600"><?= $data['user_stats']['warga'] ?? 0 ?></p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fas fa-users text-2xl text-blue-600"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Yang Berqurban</p>
                                <p class="text-3xl font-bold text-green-600"><?= $data['user_stats']['berqurban'] ?? 0 ?></p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <i class="fas fa-hand-holding-heart text-2xl text-green-600"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Panitia</p>
                                <p class="text-3xl font-bold text-purple-600"><?= $data['user_stats']['panitia'] ?? 0 ?></p>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-full">
                                <i class="fas fa-user-tie text-2xl text-purple-600"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Saldo</p>
                                <p class="text-2xl font-bold text-yellow-600">Rp <?= number_format($data['financial_summary']['saldo'] ?? 0, 0, ',', '.') ?></p>
                            </div>
                            <div class="bg-yellow-100 p-3 rounded-full">
                                <i class="fas fa-wallet text-2xl text-yellow-600"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features Overview -->
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-star mr-3 text-yellow-500"></i>
                    Fitur Sistem
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <!-- User Management -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 transition-all duration-300 feature-card">
                        <div class="flex items-center mb-4">
                            <div class="bg-blue-100 p-3 rounded-lg">
                                <i class="fas fa-users text-xl text-blue-600"></i>
                            </div>
                            <h3 class="ml-4 text-lg font-semibold text-gray-800">Manajemen Pengguna</h3>
                        </div>
                        <p class="text-gray-600 mb-4">Kelola pengguna sistem seperti admin, panitia, peserta, dan warga. Tambah, edit, atau hapus data pengguna.</p>
                        <a href="users.php" class="text-blue-600 hover:text-blue-700 flex items-center font-medium">
                            <span>Kelola Pengguna</span>
                            <i class="fas fa-arrow-right ml-2 text-sm"></i>
                        </a>
                    </div>

                    <!-- Financial Management -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 transition-all duration-300 feature-card">
                        <div class="flex items-center mb-4">
                            <div class="bg-green-100 p-3 rounded-lg">
                                <i class="fas fa-money-bill-wave text-xl text-green-600"></i>
                            </div>
                            <h3 class="ml-4 text-lg font-semibold text-gray-800">Manajemen Keuangan</h3>
                        </div>
                        <p class="text-gray-600 mb-4">Pantau pemasukan, pengeluaran, dan saldo kegiatan qurban. Catat transaksi dan buat laporan keuangan.</p>
                        <a href="financial.php" class="text-green-600 hover:text-green-700 flex items-center font-medium">
                            <span>Kelola Keuangan</span>
                            <i class="fas fa-arrow-right ml-2 text-sm"></i>
                        </a>
                    </div>

                    <!-- Meat Distribution -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 transition-all duration-300 feature-card">
                        <div class="flex items-center mb-4">
                            <div class="bg-red-100 p-3 rounded-lg">
                                <i class="fas fa-cut text-xl text-red-600"></i>
                            </div>
                            <h3 class="ml-4 text-lg font-semibold text-gray-800">Distribusi Daging</h3>
                        </div>
                        <p class="text-gray-600 mb-4">Atur pembagian daging qurban kepada warga. Lacak penerima dan pastikan distribusi merata.</p>
                        <a href="meat.php" class="text-red-600 hover:text-red-700 flex items-center font-medium">
                            <span>Kelola Distribusi</span>
                            <i class="fas fa-arrow-right ml-2 text-sm"></i>
                        </a>
                    </div>

                    <!-- QR Code System -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 transition-all duration-300 feature-card">
                        <div class="flex items-center mb-4">
                            <div class="bg-purple-100 p-3 rounded-lg">
                                <i class="fas fa-qrcode text-xl text-purple-600"></i>
                            </div>
                            <h3 class="ml-4 text-lg font-semibold text-gray-800">Sistem QR Code</h3>
                        </div>
                        <p class="text-gray-600 mb-4">Gunakan QR Code untuk memvalidasi penerima daging qurban. Scan QR untuk pengecekan dan pencatatan otomatis.</p>
                        <a href="qr_scanner.php" class="text-purple-600 hover:text-purple-700 flex items-center font-medium">
                            <span>Buka Scanner QR</span>
                            <i class="fas fa-arrow-right ml-2 text-sm"></i>
                        </a>
                    </div>

                    <!-- Reports -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 transition-all duration-300 feature-card">
                        <div class="flex items-center mb-4">
                            <div class="bg-yellow-100 p-3 rounded-lg">
                                <i class="fas fa-chart-bar text-xl text-yellow-600"></i>
                            </div>
                            <h3 class="ml-4 text-lg font-semibold text-gray-800">Laporan</h3>
                        </div>
                        <p class="text-gray-600 mb-4">Buat dan unduh laporan berdasarkan berbagai kategori seperti keuangan, distribusi, dan partisipasi warga.</p>
                        <a href="reports.php" class="text-yellow-600 hover:text-yellow-700 flex items-center font-medium">
                            <span>Lihat Laporan</span>
                            <i class="fas fa-arrow-right ml-2 text-sm"></i>
                        </a>
                    </div>

                    <!-- System Settings -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 transition-all duration-300 feature-card">
                        <div class="flex items-center mb-4">
                            <div class="bg-gray-100 p-3 rounded-lg">
                                <i class="fas fa-cog text-xl text-gray-600"></i>
                            </div>
                            <h3 class="ml-4 text-lg font-semibold text-gray-800">Pengaturan Sistem</h3>
                        </div>
                        <p class="text-gray-600 mb-4">Konfigurasi sistem qurban, atur parameter, dan sesuaikan tampilan aplikasi sesuai kebutuhan.</p>
                        <a href="settings.php" class="text-gray-600 hover:text-gray-700 flex items-center font-medium">
                            <span>Ubah Pengaturan</span>
                            <i class="fas fa-arrow-right ml-2 text-sm"></i>
                        </a>
                    </div>
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
                    </div>                </div>
            </div>
        </main>

<?php
// Include footer template
include_once('../templates/footer.php');
?>