<?php
require_once 'C:\laragon\www\UASWEBCOK\config\database.php';
require_once 'C:\laragon\www\UASWEBCOK\config\config.php';
include_once '../templates/header.php';
include_once '../templates/sidebar_admin.php';
require_once 'C:\laragon\www\UASWEBCOK\controllers\distribusiController.php';

if (!$koneksi) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get data from existing controller function
$distribusiData = getDistribusiData();
?>

<!-- Main content -->
<div class="w-full p-4">
    <!-- Display alerts if any -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="mb-4 p-4 rounded-lg border 
            <?php
            if ($_SESSION['alert_type'] == 'success') echo 'bg-green-100 border-green-400 text-green-700';
            else if ($_SESSION['alert_type'] == 'danger') echo 'bg-red-100 border-red-400 text-red-700';
            else echo 'bg-yellow-100 border-yellow-400 text-yellow-700';
            ?>">
            <?= $_SESSION['message'] ?>
            <button type="button" class="float-right close-alert" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php
        // Clear the message after displaying
        unset($_SESSION['message']);
        unset($_SESSION['alert_type']);
        ?>
    <?php endif; ?>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Dashboard Distribusi Daging Kurban</h1>
        <p class="text-gray-600">Manajemen distribusi daging kurban kepada warga</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <!-- Total Records -->
        <div class="bg-blue-100 p-4 rounded-lg shadow-sm border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-800 text-sm font-medium">Total Distribusi</p>
                    <h3 class="text-2xl font-bold text-blue-900"><?= array_sum(array_map('count', $distribusiData)) ?></h3>
                </div>
                <div class="bg-blue-200 p-2 rounded-full">
                    <i class="fas fa-list text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Collected Records -->
        <div class="bg-green-100 p-4 rounded-lg shadow-sm border border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-800 text-sm font-medium">Sudah Diambil</p>
                    <h3 class="text-2xl font-bold text-green-900">
                        <?php
                        $collected = 0;
                        foreach ($distribusiData as $roleItems) {
                            $collected += count(array_filter($roleItems, function ($item) {
                                return $item['status'] === 'sudah_diambil';
                            }));
                        }
                        echo $collected;
                        ?>
                    </h3>
                </div>
                <div class="bg-green-200 p-2 rounded-full">
                    <i class="fas fa-check text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Pending Records -->
        <div class="bg-yellow-100 p-4 rounded-lg shadow-sm border border-yellow-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-800 text-sm font-medium">Belum Diambil</p>
                    <h3 class="text-2xl font-bold text-yellow-900">
                        <?php
                        $pending = 0;
                        foreach ($distribusiData as $roleItems) {
                            $pending += count(array_filter($roleItems, function ($item) {
                                return $item['status'] === 'belum_diambil';
                            }));
                        }
                        echo $pending;
                        ?>
                    </h3>
                </div>
                <div class="bg-yellow-200 p-2 rounded-full">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribution Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-list mr-2 text-blue-600"></i> Daftar Distribusi Daging Kurban
                </h2>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-white uppercase bg-blue-600">
                    <tr>
                        <th class="px-4 py-2 border-r border-blue-500">No</th>
                        <th class="px-4 py-2 border-r border-blue-500">Nama Warga</th>
                        <th class="px-4 py-2 border-r border-blue-500">Role</th>
                        <th class="px-4 py-2 border-r border-blue-500">Daging Sapi (kg)</th>
                        <th class="px-4 py-2 border-r border-blue-500">Daging Kambing (kg)</th>
                        <th class="px-4 py-2 border-r border-blue-500">Total (kg)</th>
                        <th class="px-4 py-2 border-r border-blue-500">Status</th>
                        <th class="px-4 py-2 border-r border-blue-500">Tanggal Ambil</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $hasData = false;

                    foreach ($distribusiData as $role => $items):
                        foreach ($items as $item):
                            $hasData = true;
                    ?>
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-4 py-2"><?= $no++ ?></td>
                                <td class="px-4 py-2 font-medium"><?= htmlspecialchars($item['nama_lengkap']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($item['role']) ?></td>
                                <td class="px-4 py-2"><?= number_format((float)$item['sapi'], 1) ?> kg</td>
                                <td class="px-4 py-2"><?= number_format((float)$item['kambing'], 1) ?> kg</td>
                                <td class="px-4 py-2"><?= number_format((float)$item['total_daging'], 1) ?> kg</td>
                                <td class="px-4 py-2">
                                    <?php if ($item['status'] === 'sudah_diambil'): ?>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                            <i class="fas fa-check mr-1"></i> Sudah Diambil
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">
                                            <i class="fas fa-clock mr-1"></i> Belum Diambil
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2">
                                    <?= $item['pickup_date'] ? date('d/m/Y', strtotime($item['pickup_date'])) : '-' ?>
                                </td>
                                <td class="px-4 py-2">
                                    <?php if ($item['status'] !== 'sudah_diambil'): ?>
                                        <form action="verifyDistribution.php" method="post" class="inline">
                                            <input type="hidden" name="distribution_id" value="<?= $item['distribution_id'] ?>">
                                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">
                                                <i class="fas fa-check-circle mr-1"></i> Verifikasi
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs"><i class="fas fa-check-double"></i> Terverifikasi</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php
                        endforeach;
                    endforeach;

                    if (!$hasData):
                        ?>
                        <tr>
                            <td colspan="9" class="px-4 py-2 text-center text-gray-500">Tidak ada data distribusi ditemukan</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once '../templates/footer.php'; ?>