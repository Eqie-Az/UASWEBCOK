<?php
// filepath: views/berqurban/berqurban_dashboard.php
session_start();
require_once '../../controllers/berqurbanControllers.php';
include_once('../templates/header.php');
include_once('../templates/sidebar_berqurban.php');

require_once '../../controllers/qrControllers.php';

$warga_id = $_SESSION['warga_id'] ?? '';
$data = getBerqurbanDataByNik($warga_id);

// Ambil jumlah daging dari controller
$jumlahDaging = getJumlahDagingByWargaId($_SESSION['warga_id']) ?? 0;

// Iuran hewan
$iuran = 2000000;

// Generate QR data with complete information
$qrValue = getQRDataByWargaId($warga_id);
?>

<div class="p-6 ml-64 min-h-screen">
    <h1 class="text-2xl font-bold mb-6">Dashboard Berqurban</h1>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 justify-items-center">
        <!-- Data Pengqurban -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col items-center">
            <i class="fas fa-user text-3xl text-blue-600 mb-2"></i>
            <div class="text-lg font-semibold"><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? '-') ?></div>
            <div class="text-gray-500">NIK: <?= htmlspecialchars($_SESSION['nik'] ?? '-') ?></div>
            <div class="text-gray-500" style="text-align: center;">Alamat: <?= htmlspecialchars($_SESSION['alamat'] ?? '-') ?></div>
        </div>
        <!-- Iuran Hewan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col items-center">
            <i class="fas fa-money-bill-wave text-3xl text-yellow-600 mb-2"></i>
            <div class="text-lg font-semibold">Rp<?= number_format($iuran, 0, ',', '.') ?></div>
            <div class="text-gray-500">Iuran Hewan qurban </div>
        </div>
        <!-- Jumlah Daging -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col items-center">
            <i class="fas fa-drumstick-bite text-3xl text-red-500 mb-2"></i>
            <div class="text-lg font-semibold">
                <?= number_format($jumlahDaging, 1, '.', '') ?> kg
            </div>
            <div class="text-gray-500">Jumlah Daging Diperoleh</div>
        </div>
        <!-- QR Code -->
        <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6 flex flex-col items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-r from-green-400 to-green-600 rounded-full mb-4">
                <i class="fas fa-qrcode text-2xl text-white"></i>
            </div>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=<?= urlencode($qrValue) ?>" alt="QR Code" class="mb-2">
            <div class="text-gray-500">QR Code Anda</div>
        </div>
    </div>
</div>

<?php include_once('../templates/footer.php'); ?>