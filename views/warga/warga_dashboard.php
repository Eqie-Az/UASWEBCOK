<?php
session_start();
require_once '../../controllers/wargaControllers.php';
include_once('../templates/header.php');
include_once('../templates/sidebar_warga.php');

// Ambil data user dari session
$warga_id = $_SESSION['warga_id'] ?? '';
$nama_lengkap = $_SESSION['nama_lengkap'] ?? '-';
$nik = $_SESSION['nik'] ?? '-';
$alamat = $_SESSION['alamat'] ?? '-';

require_once '../../controllers/qrControllers.php';

// Ambil jatah daging
$jatahDaging = $warga_id ? number_format(getJumlahDagingByWargaId($warga_id), 1, '.', '') : '0.0';

// Generate QR data with complete information
$qrValue = getQRDataByWargaId($warga_id);
?>

<div class="min-h-screen bg-gray-50 p-6 md:ml-64">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Dashboard Warga</h1>
    <p class="text-gray-500 mb-8">Panel informasi warga</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Data Pengqurban -->
        <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6 flex flex-col items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full mb-4">
                <i class="fas fa-user text-2xl text-white"></i>
            </div>
            <div class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($nama_lengkap) ?></div>
            <div class="text-gray-500">NIK: <?= htmlspecialchars($nik) ?></div><br>
            <div class="text-gray-500">Alamat: <?= htmlspecialchars($alamat) ?></div>
        </div>
        <!-- Jatah Daging -->
        <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6 flex flex-col items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-r from-red-400 to-pink-500 rounded-full mb-4">
                <i class="fas fa-drumstick-bite text-2xl text-white"></i>
            </div>
            <div class="text-2xl font-bold text-red-600 mb-1"><?= $jatahDaging ?> kg</div>
            <div class="text-gray-500">Jatah Daging Anda</div>
        </div>
        <!-- QR Code -->
        <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6 flex flex-col items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-r from-green-400 to-green-600 rounded-full mb-4">
                <i class="fas fa-qrcode text-2xl text-white"></i>
            </div> <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=<?= urlencode($qrValue) ?>" alt="QR Code" class="mb-2">
            <div class="text-gray-500">QR Code Anda</div>
        </div>
    </div>
</div>

<?php include_once('../templates/footer.php'); ?>