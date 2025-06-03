<?php
session_start();
require_once('C:\laragon\www\UASWEBCOK\config\config.php');
include_once('../templates/header.php');
include_once('../templates/sidebar_admin.php');
require_once('C:\laragon\www\UASWEBCOK\controllers\financialControllers.php');
require_once('C:\laragon\www\UASWEBCOK\controllers\adminControllers.php');

$dataTabel = getFinancial();
$data = getFinancialSummary();

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_transaction'])) {
        $transactionType = "'" . $_POST['transaction_type'] . "'";
        $amount = $_POST['amount'];
        $description = "'" . $_POST['description'] . "'";
        $category = "'" . $_POST['category'] . "'";
        $transactionDate = $_POST['transaction_date'];

        $result = addFinancialTransaction($transactionType, $jumlah, $description, $category, $transactionDate);

        if ($result['status'] === 'success') {
            echo '<div id="notification" class="max-w-sm w-full bg-white border border-gray-200 rounded-lg shadow-lg p-4 flex items-start space-x-3 fixed top-4 right-4 z-50">
                <div class="text-green-500 mt-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-sm text-gray-800">' . $result['message'] . '</p>
                </div>
            </div>';
            // Reload page data after adding
            $dataTabel = getFinancial();
            $data = getFinancialSummary();
        } else {
            echo '<div id="notification-failed" class="max-w-sm w-full bg-white border border-red-200 rounded-lg shadow-lg p-4 flex items-start space-x-3 fixed top-4 right-4 z-50">
                    <div class="text-red-500 mt-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-sm text-gray-800">' . $result['message'] . '</p>
                </div>';
        }
    }

    if (isset($_POST['delete_transaction'])) {
        $transactionId = $_POST['transaction_id'];
        $result = deleteFinancialTransaction($transactionId);

        if ($result['status'] === 'success') {
            echo '<div id="notification" class="max-w-sm w-full bg-white border border-gray-200 rounded-lg shadow-lg p-4 flex items-start space-x-3 fixed top-4 right-4 z-50">
                <div class="text-green-500 mt-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-sm text-gray-800">' . $result['message'] . '</p>
                </div>
            </div>';
            // Reload page data after deleting
            $dataTabel = getFinancial();
            $data = getFinancialSummary();
        } else {
            echo '<div id="notification-failed" class="max-w-sm w-full bg-white border border-red-200 rounded-lg shadow-lg p-4 flex items-start space-x-3 fixed top-4 right-4 z-50">
                    <div class="text-red-500 mt-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-sm text-gray-800">' . $result['message'] . '</p>
                </div>';
        }
    }

    if (isset($_POST['update_transaction'])) {
        global $koneksi;
        $transactionId = $_POST['transaction_id'];
        $transactionType = $_POST['transaction_type'];
        $amount = $_POST['amount'];
        $description = $_POST['description'];
        $category = $_POST['category'];
        $transactionDate = $_POST['transaction_date'];
        
        $result = updateFinancialTransaction($transactionId, $transactionType, $amount, $category, $transactionDate, $description);

        if ($result['status'] === 'success') {
            echo '<div id="notification" class="max-w-sm w-full bg-white border border-gray-200 rounded-lg shadow-lg p-4 flex items-start space-x-3 fixed top-4 right-4 z-50">
                <div class="text-green-500 mt-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-sm text-gray-800">' . $result['message'] . '</p>
                </div>
            </div>';
            // Reload page data after updating
            $dataTabel = getFinancial();
            $data = getFinancialSummary();
        } else {
            echo '<div id="notification-failed" class="max-w-sm w-full bg-white border border-red-200 rounded-lg shadow-lg p-4 flex items-start space-x-3 fixed top-4 right-4 z-50">
                    <div class="text-red-500 mt-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-sm text-gray-800">' . $result['message'] . '</p>
                </div>';
        }
    }
}

?>
<div class="w-full px-4 py-3">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard Keuangan</h1>
        <p class="text-gray-600 mt-1">Kelola semua transaksi keuangan dalam satu tempat</p>
    </div>

    <!-- Financial Summary Cards -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 mt-6">
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
                    <p class="text-2xl font-bold text-green-700">Rp <?= number_format($data['pemasukan'] ?? 0, 0, ',', '.') ?></p>
                </div>
                <div class="p-4 bg-red-50 rounded-lg">
                    <p class="text-sm font-medium text-red-600">Pengeluaran</p>
                    <p class="text-2xl font-bold text-red-700">Rp <?= number_format($data['pengeluaran'] ?? 0, 0, ',', '.') ?></p>
                </div>
                <div class="p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm font-medium text-blue-600">Saldo</p>
                    <p class="text-2xl font-bold text-blue-700">Rp <?= number_format($data['saldo'] ?? 0, 0, ',', '.') ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Transactions Table Header -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-5">
        <div class="p-5 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-list-ul mr-2 text-blue-600"></i> Daftar Transaksi
                </h2>
                <button type="button"
                    onclick="openAddModal()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                    <i class="fas fa-plus mr-1.5"></i> Tambah Transaksi
                </button>
            </div>
        </div>

        <table class="table-fixed w-full text-sm text-left text-gray-600">
            <thead class="text-xs text-white uppercase bg-blue-600">
                <tr>
                    <th class="w-1/12 px-4 py-2 border-r border-blue-500">No</th>
                    <th class="w-2/12 px-4 py-2 border-r border-blue-500">Tipe Transaksi</th>
                    <th class="w-1/12 px-4 py-2 border-r border-blue-500">Jumlah</th>
                    <th class="w-2/12 px-4 py-2 border-r border-blue-500">Deskripsi</th>
                    <th class="w-2/12 px-4 py-2 border-r border-blue-500">Kategori</th>
                    <th class="w-2/12 px-4 py-2 border-r border-blue-500">Tanggal Transaksi</th>
                    <th class="w-2/12 px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dataTabel as $row): ?> <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-2"><?= htmlspecialchars($row['transaction_id']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($row['transaction_type']) ?></td>
                        <td class="px-4 py-2 <?= $row['transaction_type'] === 'Pemasukan' ? 'text-green-600' : 'text-red-600' ?>">
                            Rp <?= number_format(htmlspecialchars($row['amount']), 0, ',', '.') ?>
                        </td>
                        <td class="px-4 py-2 break-words"><?= htmlspecialchars($row['description']) ?></td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 whitespace-nowrap">
                                <?= htmlspecialchars($row['category']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-2"><?= date('d M Y', strtotime($row['transaction_date'])) ?></td>
                        <td class="px-4 py-2 flex gap-2"> <button
                                type="button"
                                onclick="openEditModal(<?= $row['transaction_id'] ?>, '<?= htmlspecialchars($row['transaction_type']) ?>', <?= htmlspecialchars($row['amount']) ?>, '<?= htmlspecialchars($row['category']) ?>', '<?= htmlspecialchars($row['transaction_date']) ?>')"
                                class="px-3 py-1 text-xs bg-yellow-500 text-white rounded hover:bg-yellow-600 transition-colors">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <form method="post" class="inline" onsubmit="return confirm('Yakin ingin menghapus transaksi ini?');">
                                <input type="hidden" name="transaction_id" value="<?= $row['transaction_id'] ?>">
                                <button
                                    type="submit"
                                    name="delete_transaction"
                                    class="px-3 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?> <?php if (empty($dataTabel)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada transaksi ditemukan</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>



        <!-- Add Transaction Modal -->
        <div id="addTransactionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Tambah Transaksi Baru</h3>
                    <button type="button" onclick="closeModal('addTransactionModal')" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form method="post" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Transaksi</label>
                        <select name="transaction_type" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <option value="pemasukan">Pemasukan</option>
                            <option value="pengeluaran">Pengeluaran</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Rp)</label>
                        <input type="number" name="amount" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select class="" name="category" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <option value="iuran_qurban">Iuran Qurban</option>
                            <option value="pembelian_hewan">Pembelian Hewan</option>
                            <option value="perlengkapan">Perlengkapan</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Transaksi</label>
                        <input type="date" name="transaction_date" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                    <div class="flex justify-end pt-2">
                        <button type="button" onclick="closeModal('addTransactionModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md mr-2 hover:bg-gray-400 transition-colors">
                            Batal
                        </button>
                        <button type="submit" name="add_transaction" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Transaction Modal -->
        <div id="editTransactionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Edit Transaksi</h3>
                    <button type="button" onclick="closeModal('editTransactionModal')" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form method="post" class="space-y-4">
                    <input type="hidden" id="edit_transaction_id" name="transaction_id">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Transaksi</label>
                        <select id="edit_transaction_type" name="transaction_type" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <option value="pemasukan">Pemasukan</option>
                            <option value="pengeluaran">Pengeluaran</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Rp)</label>
                        <input type="number" id="edit_amount" name="amount" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <input type="text" id="edit_category" name="category" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea id="edit_description" name="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Transaksi</label>
                        <input type="date" id="edit_transaction_date" name="transaction_date" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                    <div class="flex justify-end pt-2">
                        <button type="button" onclick="closeModal('editTransactionModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md mr-2 hover:bg-gray-400 transition-colors">
                            Batal
                        </button>
                        <button type="submit" name="update_transaction" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            Perbarui
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function openAddModal() {
                document.getElementById('addTransactionModal').classList.remove('hidden');
            }

            function openEditModal(id, type, amount, category, date) {
                document.getElementById('edit_transaction_id').value = id;
                document.getElementById('edit_transaction_type').value = type;
                document.getElementById('edit_amount').value = amount;
                document.getElementById('edit_category').value = category;
                document.getElementById('edit_transaction_date').value = formatDateForInput(date);
                document.getElementById('editTransactionModal').classList.remove('hidden');
            }

            function closeModal(modalId) {
                document.getElementById(modalId).classList.add('hidden');
            }

            function formatDateForInput(dateStr) {
                const date = new Date(dateStr);
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            function hideNotification() {
                document.getElementById("notification").style.display = "none";
            }

            // (Opsional) Tampilkan otomatis lalu sembunyikan setelah beberapa detik
            setTimeout(hideNotification, 2000); // sembunyikan setelah 4 detik
        </script>
    </div>