<?php
require_once('C:\laragon\www\UASWEBCOK\config\database.php');
include_once('../templates/header.php');
include_once('../templates/sidebar_admin.php');
require_once('C:\laragon\www\UASWEBCOK\controllers/userControllers.php');

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$dashboardData = processDashboardData();
extract($dashboardData);


?>
<div class="w-full p-4">
    <!-- Warga Allocations Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-5">
        <div class="p-5 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-users mr-2 text-blue-600"></i> Daftar Alokasi Warga
                </h2>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-white uppercase bg-blue-600">
                    <tr>
                        <th class="px-4 py-2 border-r border-blue-500">No</th>
                        <th class="px-4 py-2 border-r border-blue-500">Nama Warga</th>
                        <th class="px-4 py-2 border-r border-blue-500">NIK / ID</th>
                        <th class="px-4 py-2 border-r border-blue-500">Peran (Role)</th>
                        <th class="px-4 py-2 border-r border-blue-500">Daging Sapi (kg)</th>
                        <th class="px-4 py-2 border-r border-blue-500">Daging Kambing (kg)</th>
                        <th class="px-4 py-2 border-r border-blue-500">Total Daging (kg)</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($wargaAllocations)): ?>
                        <?php $no = 1;
                        foreach ($wargaAllocations as $warga): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($warga['nama_lengkap']) ?></td>
                                <td><?= htmlspecialchars($warga['nik']) ?></td>
                                <td><?= htmlspecialchars($warga['roles']) ?></td>
                                <td><?= number_format((float)$warga['total_sapi'], 1) ?> kg</td>
                                <td><?= number_format((float)$warga['total_kambing'], 1) ?> kg</td>
                                <td><?= number_format((float)$warga['total_allocation'], 1) ?> kg</td>
                                <td class="px-4 py-2 text-center">
                                    <form method="post">
                                        <input type="hidden" name="warga_id" value="<?= $warga['warga_id'] ?>">
                                        <button type="submit" name="edit_warga_roles"
                                            class="px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition-colors">
                                            <i class="fas fa-edit mr-1"></i> Edit Role
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">Tidak ada data alokasi ditemukan</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- PHP-based modal implementation -->
<?php if ($editingWarga): ?>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Edit Role <span class="text-blue-600"><?= htmlspecialchars($editingWarga['nama_lengkap']) ?></span></h3>
                <form method="post" class="inline">
                    <button type="submit" name="close_modal" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </form>
            </div>

            <?php if (!empty($message)): ?>
                <div class="mb-4 px-4 py-2 rounded <?= $messageType === 'success' ? 'bg-green-100 border border-green-300 text-green-700' : 'bg-red-100 border border-red-300 text-red-700' ?>">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <!-- Current Roles List -->
            <div class="mb-4">
                <h4 class="font-medium text-gray-700 mb-2">Role Saat Ini:</h4>
                <div class="space-y-2">
                    <?php if (!empty($currentRoles)): ?>
                        <?php foreach ($currentRoles as $role): ?>
                            <div class="flex justify-between items-center p-2 bg-white rounded border">
                                <div>
                                    <span class="font-medium"><?= htmlspecialchars($role['role']) ?></span>
                                    <span class="text-gray-500 text-sm ml-2">
                                        (Sapi: <?= number_format((float)$role['sapi'], 1) ?> kg,
                                        Kambing: <?= number_format((float)$role['kambing'], 1) ?> kg)
                                    </span>
                                </div>
                                <form method="post" class="inline" onsubmit="return confirm('Anda yakin ingin menghapus role ini?');">
                                    <input type="hidden" name="warga_id" value="<?= $editingWarga['warga_id'] ?>">
                                    <input type="hidden" name="allocation_id" value="<?= $role['allocation_id'] ?>">
                                    <button type="submit" name="remove_role"
                                        class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500 text-center">Tidak ada role</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-4 border-t pt-4">
                <h4 class="font-medium text-gray-700 mb-2">Tambah Role:</h4>
                <form method="post" onsubmit="return confirm('PERHATIAN: Semua role yang ada sebelumnya akan dihapus dan diganti dengan role yang baru dipilih. Lanjutkan?');">
                    <input type="hidden" name="warga_id" value="<?= $editingWarga['warga_id'] ?>">

                    <div class="flex flex-col space-y-2">
                        <?php
                        $roles = getAllRoles();
                        if (!empty($roles)): ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                <?php foreach ($roles as $role): ?>
                                    <div class="flex items-center p-2 border rounded-md hover:bg-gray-50">
                                        <input
                                            type="checkbox"
                                            id="role_<?= htmlspecialchars($role['allocation_id']) ?>"
                                            name="roles[]"
                                            value="<?= htmlspecialchars($role['role']) ?>"
                                            class="w-4 h-4 text-blue-600 focus:ring-blue-500 mr-2">
                                        <label for="role_<?= htmlspecialchars($role['allocation_id']) ?>" class="text-sm text-gray-700 cursor-pointer flex-1">
                                            <?= htmlspecialchars($role['role']) ?>
                                            <span class="text-gray-500 text-xs">
                                                (Sapi: <?= number_format((float)$role['sapi'], 1) ?> kg,
                                                Kambing: <?= number_format((float)$role['kambing'], 1) ?> kg)
                                            </span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-gray-500 text-center py-2">Tidak ada role tersedia</div>
                        <?php endif; ?>

                        <button type="submit" name="add_selected_roles"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors w-full mt-3">
                            <i class="fas fa-plus-circle mr-1"></i> Tambah Role Terpilih
                        </button>
                    </div>
                </form>
            </div>

            <div class="flex justify-end pt-4 border-t">
                <form method="post" class="inline">
                    <button type="submit" name="close_modal"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Selesai
                    </button>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<style>
    .loader {
        border: 3px solid rgba(0, 0, 0, 0.1);
        border-top: 3px solid #3498db;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        animation: spin 1s linear infinite;
        margin: 20px auto;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>