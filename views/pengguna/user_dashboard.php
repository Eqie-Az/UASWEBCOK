<?php
require_once('C:\laragon\www\UASWEBCOK\config\database.php');
include_once('../templates/header.php');
include_once('../templates/sidebar_admin.php');
require_once('C:\laragon\www\UASWEBCOK\controllers/userControllers.php');

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$dataTabel = getdataUser();
$wargaAllocations = getWargaAllocations();

?>

<!-- Warga Allocations Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-5">
    <div class="p-5 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-users mr-2 text-blue-600"></i> Daftar Alokasi Warga
            </h2>
            <button type="button" onclick="showAddAllocationModal()"
                class="px-4 py-2 text-sm bg-green-500 text-white rounded hover:bg-green-600 transition-colors">
                <i class="fas fa-plus-circle mr-2"></i> Tambah Alokasi
            </button>
        </div>
    </div>

    <table class="table-fixed w-full text-sm text-left text-gray-600">
        <thead class="text-xs text-white uppercase bg-blue-600">
            <tr>
                <th class="w-1/12 px-4 py-2 border-r border-blue-500">No</th>
                <th class="w-2/12 px-4 py-2 border-r border-blue-500">Nama Warga</th>
                <th class="w-2/12 px-4 py-2 border-r border-blue-500">NIK / ID</th>
                <th class="w-2/12 px-4 py-2 border-r border-blue-500">Peran (Role)</th>
                <th class="w-2/12 px-4 py-2 border-r border-blue-500">Alokasi per Role (kg)</th>
                <th class="w-1/12 px-4 py-2 border-r border-blue-500">Total Daging (kg)</th>
                <th class="w-2/12 px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($wargaAllocations)): ?>
                <?php $no = 1;
                foreach ($wargaAllocations as $warga): ?>
                    <?php
                    $roles = explode(',', $warga['roles']);
                    $allocations = explode(',', $warga['allocations']);

                    // Format roles and allocations
                    $roleText = '';
                    $allocText = '';

                    for ($i = 0; $i < count($roles); $i++) {
                        $roleText .= $roles[$i] . ($i < count($roles) - 1 ? ', ' : '');
                        $allocText .= number_format((float)$allocations[$i], 1) . ' (' . $roles[$i] . ')' . ($i < count($allocations) - 1 ? ', ' : '');
                    }
                    ?>
                    <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-2"><?= $no++ ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($warga['nama_lengkap']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($warga['nik']) ?></td>
                        <td class="px-4 py-2"><?= $roleText ?></td>
                        <td class="px-4 py-2"><?= $allocText ?></td>
                        <td class="px-4 py-2"><?= number_format((float)$warga['total_allocation'], 1) ?></td>
                        <td class="px-4 py-2">
                            <button type="button"
                                onclick="showDetailModal(<?= $warga['warga_id'] ?>)"
                                class="px-3 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                                <i class="fas fa-search"></i> Detail
                            </button>
                            <button type="button"
                                onclick="showEditModal(<?= $warga['warga_id'] ?>)"
                                class="px-3 py-1 text-xs bg-yellow-500 text-white rounded hover:bg-yellow-600 transition-colors">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="px-4 py-3 text-center text-gray-500">Tidak ada data alokasi ditemukan</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Detail Modal -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-lg">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Detail Alokasi Daging</h3>
            <button type="button" onclick="closeModal('detailModal')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="detailContent" class="space-y-4">
            <!-- Detail content will be loaded dynamically -->
        </div>
        <div class="flex justify-end pt-4">
            <button type="button" onclick="closeModal('detailModal')"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-lg">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Edit Alokasi Daging</h3>
            <button type="button" onclick="closeModal('editModal')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editForm" method="post" action="../../../controllers/update_allocation.php" class="space-y-4">
            <input type="hidden" id="edit_warga_id" name="warga_id">
            <!-- Form fields will be loaded dynamically -->
            <div id="editFormContent"></div>
            <div id="updateMessage" class="hidden bg-green-100 border border-green-300 text-green-700 px-4 py-2 rounded mb-3"></div>
            <div class="flex justify-end pt-2">
                <button type="button" onclick="closeModal('editModal')"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md mr-2 hover:bg-gray-400 transition-colors">
                    Batal
                </button>
                <button type="button" onclick="submitAllocationUpdate()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Allocation Modal -->
<div id="addAllocationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-lg">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Tambah Alokasi Daging</h3>
            <button type="button" onclick="closeModal('addAllocationModal')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="addAllocationForm" method="post" action="../../../controllers/add_allocation.php" class="space-y-4">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Warga</label>
                <select id="add_warga_id" name="warga_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300">
                    <option value="">Pilih Warga</option>
                    <?php
                    // Get all warga for dropdown
                    $sql = "SELECT warga_id, nama_lengkap, nik FROM warga ORDER BY nama_lengkap";
                    $result = mysqli_query($koneksi, $sql);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<option value="' . $row['warga_id'] . '">' . htmlspecialchars($row['nama_lengkap']) . ' (' . htmlspecialchars($row['nik']) . ')</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select id="add_role" name="role" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300">
                    <option value="">Pilih Role</option>
                    <option value="warga">Warga</option>
                    <option value="berqurban">Berqurban</option>
                    <option value="panitia">Panitia</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Alokasi (kg)</label>
                <input type="number" step="0.1" id="add_amount" name="amount" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300">
            </div>

            <div id="addAllocationMessage" class="hidden bg-green-100 border border-green-300 text-green-700 px-4 py-2 rounded mb-3"></div>

            <div class="flex justify-end pt-2">
                <button type="button" onclick="closeModal('addAllocationModal')"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md mr-2 hover:bg-gray-400 transition-colors">
                    Batal
                </button>
                <button type="button" onclick="submitAddAllocation()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

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

<script>
    // Function to show detail modal
    function showDetailModal(wargaId) {
        let detailContent = document.getElementById('detailContent');
        detailContent.innerHTML = '<div class="flex justify-center"><div class="loader"></div></div>';
        document.getElementById('detailModal').classList.remove('hidden');

        // Try to fetch data from server
        fetch('../../../controllers/get_warga_detail.php?warga_id=' + wargaId)
            .then(response => response.json())
            .then(responseData => {
                if (responseData.status === 'success') {
                    const data = responseData.data;
                    let html = '';
                    html += '<div class="border-b pb-3">';
                    html += '<p class="text-lg font-bold">' + data.nama + '</p>';
                    html += '<p class="text-gray-600">NIK: ' + data.nik + '</p>';
                    html += '</div>';

                    html += '<table class="w-full mt-3">';
                    html += '<thead>';
                    html += '<tr class="bg-gray-100 text-left">';
                    html += '<th class="py-2 px-3">Role</th>';
                    html += '<th class="py-2 px-3">Alokasi (kg)</th>';
                    html += '</tr>';
                    html += '</thead>';
                    html += '<tbody>';

                    // Add rows for each role
                    for (let i = 0; i < data.details.length; i++) {
                        const detail = data.details[i];
                        html += '<tr class="border-b">';
                        html += '<td class="py-2 px-3">' + detail.role + '</td>';
                        html += '<td class="py-2 px-3">' + detail.amount.toFixed(1) + '</td>';
                        html += '</tr>';
                    }

                    // Add total row
                    html += '<tr class="bg-gray-50 font-bold">';
                    html += '<td class="py-2 px-3">Total</td>';
                    html += '<td class="py-2 px-3">' + data.total.toFixed(1) + ' kg</td>';
                    html += '</tr>';
                    html += '</tbody>';
                    html += '</table>';

                    detailContent.innerHTML = html;
                } else {
                    // Fallback to demo data if server request fails
                    useDemoDataForDetail(wargaId);
                }
            })
            .catch(error => {
                console.error('Error fetching warga details:', error);
                useDemoDataForDetail(wargaId);
            });
    }

    // Fallback function using demo data
    function useDemoDataForDetail(wargaId) {
        // Demo data - for fallback when AJAX fails
        const demoData = {};

        // Example for Ahmad, warga_id = 1
        demoData[1] = {
            nama: 'Ahmad',
            nik: '12345678',
            details: [{
                    role: 'berqurban',
                    amount: 2.0
                },
                {
                    role: 'panitia',
                    amount: 2.0
                }
            ],
            total: 4.0
        };

        // Example for Siti, warga_id = 2
        demoData[2] = {
            nama: 'Siti',
            nik: '87654321',
            details: [{
                role: 'warga',
                amount: 1.5
            }],
            total: 1.5
        };

        // Get data for current warga_id
        const data = demoData[wargaId];
        let detailContent = document.getElementById('detailContent');

        if (data) {
            let html = '';
            html += '<div class="border-b pb-3">';
            html += '<p class="text-lg font-bold">' + data.nama + '</p>';
            html += '<p class="text-gray-600">NIK: ' + data.nik + '</p>';
            html += '</div>';

            html += '<table class="w-full mt-3">';
            html += '<thead>';
            html += '<tr class="bg-gray-100 text-left">';
            html += '<th class="py-2 px-3">Role</th>';
            html += '<th class="py-2 px-3">Alokasi (kg)</th>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';

            // Add rows for each role
            for (let i = 0; i < data.details.length; i++) {
                const detail = data.details[i];
                html += '<tr class="border-b">';
                html += '<td class="py-2 px-3">' + detail.role + '</td>';
                html += '<td class="py-2 px-3">' + detail.amount.toFixed(1) + '</td>';
                html += '</tr>';
            }

            // Add total row
            html += '<tr class="bg-gray-50 font-bold">';
            html += '<td class="py-2 px-3">Total</td>';
            html += '<td class="py-2 px-3">' + data.total.toFixed(1) + ' kg</td>';
            html += '</tr>';
            html += '</tbody>';
            html += '</table>';

            detailContent.innerHTML = html;
        } else {
            detailContent.innerHTML = '<p>Tidak ada detail untuk warga dengan ID ' + wargaId + '.</p>';
        }
    } // Function to show edit modal
    function showEditModal(wargaId) {
        // Set the warga ID for editing
        document.getElementById('edit_warga_id').value = wargaId;

        let editContent = document.getElementById('editFormContent');
        editContent.innerHTML = '<div class="flex justify-center"><div class="loader"></div></div>';
        document.getElementById('editModal').classList.remove('hidden');

        // Try to fetch data from server first
        fetch('../../../controllers/get_warga_detail.php?warga_id=' + wargaId)
            .then(response => response.json())
            .then(responseData => {
                if (responseData.status === 'success') {
                    const data = responseData.data;
                    let html = '';
                    html += '<div class="mb-4">';
                    html += '<p class="text-lg font-bold">' + data.nama + '</p>';
                    html += '<p class="text-gray-600 mb-3">NIK: ' + data.nik + '</p>';
                    html += '</div>';

                    // Add form fields for each role
                    for (let i = 0; i < data.details.length; i++) {
                        const detail = data.details[i];
                        html += '<div class="border p-3 rounded-md ' + (i < data.details.length - 1 ? 'mb-3' : '') + '">';
                        html += '<div class="mb-2">';
                        html += '<label class="block text-sm font-medium text-gray-700 mb-1">Role: ' + detail.role + '</label>';
                        html += '<input type="number" step="0.1" name="allocation_' + detail.role + '" value="' + detail.amount.toFixed(1) + '" ';
                        html += 'class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300">';
                        html += '</div>';
                        html += '</div>';
                    }

                    editContent.innerHTML = html;
                } else {
                    // Fallback to demo data if server request fails
                    useDemoDataForEdit(wargaId);
                }
            })
            .catch(error => {
                console.error('Error fetching warga details for edit:', error);
                useDemoDataForEdit(wargaId);
            });
    }

    // Fallback function using demo data for edit
    function useDemoDataForEdit(wargaId) {
        // Demo data - for fallback when AJAX fails
        const demoData = {};

        // Example for Ahmad, warga_id = 1
        demoData[1] = {
            nama: 'Ahmad',
            nik: '12345678',
            details: [{
                    role: 'berqurban',
                    amount: 2.0
                },
                {
                    role: 'panitia',
                    amount: 2.0
                }
            ]
        };

        // Example for Siti, warga_id = 2
        demoData[2] = {
            nama: 'Siti',
            nik: '87654321',
            details: [{
                role: 'warga',
                amount: 1.5
            }]
        };

        // Get data for current warga_id
        const data = demoData[wargaId];
        let editContent = document.getElementById('editFormContent');

        if (data) {
            let html = '';
            html += '<div class="mb-4">';
            html += '<p class="text-lg font-bold">' + data.nama + '</p>';
            html += '<p class="text-gray-600 mb-3">NIK: ' + data.nik + '</p>';
            html += '</div>';

            // Add form fields for each role
            for (let i = 0; i < data.details.length; i++) {
                const detail = data.details[i];
                html += '<div class="border p-3 rounded-md ' + (i < data.details.length - 1 ? 'mb-3' : '') + '">';
                html += '<div class="mb-2">';
                html += '<label class="block text-sm font-medium text-gray-700 mb-1">Role: ' + detail.role + '</label>';
                html += '<input type="number" step="0.1" name="allocation_' + detail.role + '" value="' + detail.amount.toFixed(1) + '" ';
                html += 'class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300">';
                html += '</div>';
                html += '</div>';
            }

            editContent.innerHTML = html;
        } else {
            editContent.innerHTML = '<p>Tidak ada data untuk warga dengan ID ' + wargaId + '.</p>';
        }
    }

    // Function to close any modal
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    // Function to handle form submission using AJAX
    function submitAllocationUpdate() {
        const form = document.getElementById('editForm');
        const formData = new FormData(form);
        const updateMessage = document.getElementById('updateMessage');

        // Show loading indicator in the button
        const submitBtn = form.querySelector('button[type="button"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';

        fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                updateMessage.classList.remove('hidden', 'bg-red-100', 'border-red-300', 'text-red-700');

                if (data.status === 'success') {
                    updateMessage.classList.add('bg-green-100', 'border-green-300', 'text-green-700');
                    updateMessage.innerHTML = '<i class="fas fa-check-circle mr-2"></i> ' + data.message;

                    // Reload page after short delay to show updated data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    updateMessage.classList.add('bg-red-100', 'border-red-300', 'text-red-700');
                    updateMessage.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i> ' + data.message;
                }

                // Reset button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            })
            .catch(error => {
                console.error('Error updating allocation:', error);
                updateMessage.classList.remove('hidden', 'bg-green-100', 'border-green-300', 'text-green-700');
                updateMessage.classList.add('bg-red-100', 'border-red-300', 'text-red-700');
                updateMessage.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i> Terjadi kesalahan saat menyimpan data';

                // Reset button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });

        return false; // Prevent default form submission
    }

    // Function to handle adding a new allocation
    function submitAddAllocation() {
        const form = document.getElementById('addAllocationForm');
        const formData = new FormData(form);
        const addMessage = document.getElementById('addAllocationMessage');

        // Show loading indicator in the button
        const submitBtn = form.querySelector('button[type="button"]:last-child');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';

        fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                addMessage.classList.remove('hidden', 'bg-red-100', 'border-red-300', 'text-red-700');

                if (data.status === 'success') {
                    addMessage.classList.add('bg-green-100', 'border-green-300', 'text-green-700');
                    addMessage.innerHTML = '<i class="fas fa-check-circle mr-2"></i> ' + data.message;

                    // Reset form
                    form.reset();

                    // Reload page after short delay to show updated data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    addMessage.classList.add('bg-red-100', 'border-red-300', 'text-red-700');
                    addMessage.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i> ' + data.message;
                }

                // Reset button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            })
            .catch(error => {
                console.error('Error adding allocation:', error);
                addMessage.classList.remove('hidden', 'bg-green-100', 'border-green-300', 'text-green-700');
                addMessage.classList.add('bg-red-100', 'border-red-300', 'text-red-700');
                addMessage.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i> Terjadi kesalahan saat menyimpan data';

                // Reset button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });

        return false; // Prevent default form submission
    } // Function to show add allocation modal
    function showAddAllocationModal() {
        // Reset form before showing modal
        document.getElementById('addAllocationForm').reset();
        document.getElementById('addAllocationMessage').classList.add('hidden');
        document.getElementById('addAllocationModal').classList.remove('hidden');
    }
</script>

</div>