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
$pageTitle = 'Dashboard Qurban - Sistem Manajemen Qurban';
$activeMenu = 'qurban';

// Dummy data for demonstration - this would typically come from your database
$data = [
    'residents' => [
        'total' => 84,
        'committee' => 15,
        'participants' => 9,
        'general_public' => 60
    ],
    'funds' => [
        'total_collected' => 26550000,
        'admin_fees' => 150000
    ],
    'meat' => [
        'total_distributed' => 200,
        'beef' => 100,
        'goat' => 100
    ],
    'financial' => [
        'income' => 26550000,
        'expenditure' => 26400000,
        'balance' => 150000
    ],
    'transactions' => [
        [
            'date' => '2025-05-25',
            'description' => 'Dana partisipasi dari warga',
            'type' => 'income',
            'amount' => 2500000
        ],
        [
            'date' => '2025-05-26',
            'description' => 'Dana partisipasi dari donatur',
            'type' => 'income',
            'amount' => 15000000
        ],
        [
            'date' => '2025-05-27',
            'description' => 'Pembayaran perlengkapan',
            'type' => 'expense',
            'amount' => 1500000
        ],
        [
            'date' => '2025-05-28',
            'description' => 'Dana partisipasi tambahan',
            'type' => 'income',
            'amount' => 9050000
        ],
        [
            'date' => '2025-05-29',
            'description' => 'Pembayaran distribusi',
            'type' => 'expense',
            'amount' => 24900000
        ]
    ]
];

// Include header template
include_once('../templates/header.php');

// Include sidebar template
include_once('../templates/sidebar_admin.php');
?>

<!-- Main content -->
<main class="flex-1 overflow-y-auto pt-16 md:pt-0">
    <div class="p-4 md:p-6">
        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-hand-holding-heart mr-3 text-blue-600"></i>
                Dashboard Qurban
            </h1>
            <p class="text-gray-600 mt-2">Manajemen dana dan distribusi qurban</p>
        </div>


        <!-- Top Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-6">
            <!-- Total Residents Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 md:p-5 bg-blue-50 border-b border-blue-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                        <h3 class="ml-3 font-semibold text-lg text-gray-800">Jumlah Warga</h3>
                    </div>
                </div>
                <div class="p-4 md:p-5">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-gray-600">Total</span>
                        <span class="text-3xl font-bold text-blue-600"><?= $data['residents']['total'] ?></span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-sm">Panitia</span>
                            <span class="text-blue-600 font-medium"><?= $data['residents']['committee'] ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-sm">Peserta</span>
                            <span class="text-blue-600 font-medium"><?= $data['residents']['participants'] ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-sm">Warga umum</span>
                            <span class="text-blue-600 font-medium"><?= $data['residents']['general_public'] ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Funds Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 md:p-5 bg-green-50 border-b border-green-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-money-bill-wave text-xl"></i>
                        </div>
                        <h3 class="ml-3 font-semibold text-lg text-gray-800">Total Dana</h3>
                    </div>
                </div>
                <div class="p-4 md:p-5">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-gray-600">Dana Terkumpul</span>
                        <span class="text-2xl font-bold text-green-600">Rp <?= number_format($data['funds']['total_collected'], 0, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-sm">Termasuk biaya admin</span>
                        <span class="text-green-600 font-medium">Rp <?= number_format($data['funds']['admin_fees'], 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>

            <!-- Total Meat Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 md:p-5 bg-red-50 border-b border-red-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-600">
                            <i class="fas fa-drumstick-bite text-xl"></i>
                        </div>
                        <h3 class="ml-3 font-semibold text-lg text-gray-800">Distribusi Daging</h3>
                    </div>
                </div>
                <div class="p-4 md:p-5">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-gray-600">Total Daging</span>
                        <span class="text-3xl font-bold text-red-600"><?= $data['meat']['total_distributed'] ?> kg</span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-sm">Daging Sapi</span>
                            <span class="text-red-600 font-medium"><?= $data['meat']['beef'] ?> kg</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-sm">Daging Kambing</span>
                            <span class="text-red-600 font-medium"><?= $data['meat']['goat'] ?> kg</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Summary & Transactions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="card-gradient text-white p-4 md:p-5 flex justify-between items-center">
                <h3 class="text-lg md:text-xl font-semibold flex items-center">
                    <i class="fas fa-chart-line mr-2"></i>
                    Ringkasan Keuangan
                </h3>
                <div class="flex space-x-1">
                    <button class="tab-btn active px-3 py-1 rounded-md bg-white/20 hover:bg-white/30 transition-all text-sm"
                        onclick="switchTab('summary')">Ringkasan</button>
                    <button class="tab-btn px-3 py-1 rounded-md hover:bg-white/30 transition-all text-sm"
                        onclick="switchTab('transactions')">Transaksi</button>
                </div>
            </div>

            <!-- Summary Tab (Default) -->
            <div id="summary-tab" class="tab-content p-4 md:p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-green-50 rounded-lg p-4 flex flex-col">
                        <span class="text-green-600 text-sm font-medium">Total Pemasukan</span>
                        <span class="text-2xl font-bold text-green-700 mt-1">
                            Rp <?= number_format($data['financial']['income'], 0, ',', '.') ?>
                        </span>
                        <span class="text-green-600 text-xs mt-1">Termasuk biaya admin</span>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4 flex flex-col">
                        <span class="text-red-600 text-sm font-medium">Total Pengeluaran</span>
                        <span class="text-2xl font-bold text-red-700 mt-1">
                            Rp <?= number_format($data['financial']['expenditure'], 0, ',', '.') ?>
                        </span>
                        <span class="text-red-600 text-xs mt-1">Pembelian & peralatan</span>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-4 flex flex-col">
                        <span class="text-blue-600 text-sm font-medium">Sisa Saldo</span>
                        <span class="text-2xl font-bold text-blue-700 mt-1">
                            Rp <?= number_format($data['financial']['balance'], 0, ',', '.') ?>
                        </span>
                        <span class="text-blue-600 text-xs mt-1">Dana tersisa</span>
                    </div>
                </div>

                <div class="mt-6">
                    <canvas id="financialChart" class="w-full h-64"></canvas>
                </div>
            </div>

            <!-- Transactions Tab (Hidden by Default) -->
            <div id="transactions-tab" class="tab-content p-4 md:p-6 hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 font-medium text-gray-700">Tanggal</th>
                                <th class="px-4 py-3 font-medium text-gray-700">Deskripsi</th>
                                <th class="px-4 py-3 font-medium text-gray-700 text-right">Jumlah</th>
                                <th class="px-4 py-3 font-medium text-gray-700 text-center">Jenis</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <?php foreach ($data['transactions'] as $transaction): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3"><?= date('d M Y', strtotime($transaction['date'])) ?></td>
                                    <td class="px-4 py-3"><?= $transaction['description'] ?></td>
                                    <td class="px-4 py-3 text-right font-medium 
                                                <?= $transaction['type'] === 'income' ? 'text-green-600' : 'text-red-600' ?>">
                                        <?= $transaction['type'] === 'income' ? '+' : '-' ?>
                                        Rp <?= number_format($transaction['amount'], 0, ',', '.') ?>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <?php if ($transaction['type'] === 'income'): ?>
                                            <span class="inline-block px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Pemasukan</span>
                                        <?php else: ?>
                                            <span class="inline-block px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">Pengeluaran</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-bolt mr-2 text-yellow-500"></i>
            Aksi Cepat
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Add Resident Data -->
            <a href="add_resident.php" class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm flex items-center hover:shadow-md transition-all duration-200">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="ml-3">
                    <h4 class="font-medium">Tambah Data Warga</h4>
                    <p class="text-xs text-gray-500 mt-1">Tambahkan data warga baru</p>
                </div>
            </a>

            <!-- Generate QR Cards -->
            <a href="generate_qr.php" class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm flex items-center hover:shadow-md transition-all duration-200">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-qrcode"></i>
                </div>
                <div class="ml-3">
                    <h4 class="font-medium">Generate Kartu QR</h4>
                    <p class="text-xs text-gray-500 mt-1">Buat kartu QR untuk distribusi</p>
                </div>
            </a>

            <!-- Distribution Report -->
            <a href="distribution_report.php" class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm flex items-center hover:shadow-md transition-all duration-200">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="ml-3">
                    <h4 class="font-medium">Laporan Distribusi</h4>
                    <p class="text-xs text-gray-500 mt-1">Lihat laporan distribusi daging</p>
                </div>
            </a>

            <!-- Download Financial Report -->
            <a href="download_report.php" class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm flex items-center hover:shadow-md transition-all duration-200">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-download"></i>
                </div>
                <div class="ml-3">
                    <h4 class="font-medium">Unduh Laporan</h4>
                    <p class="text-xs text-gray-500 mt-1">Unduh laporan keuangan</p>
                </div>
            </a>
        </div>
    </div>
</main>

<?php
// Add any page-specific scripts for the footer
$extraScripts = '
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Tab switching functionality
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll(".tab-content").forEach(tab => {
                tab.classList.add("hidden");
            });

            // Show the selected tab
            document.getElementById(tabName + "-tab").classList.remove("hidden");

            // Update active tab button
            document.querySelectorAll(".tab-btn").forEach(btn => {
                btn.classList.remove("active", "bg-white/20");
            });

            // Find the clicked button and make it active
            event.target.classList.add("active", "bg-white/20");
        }

        // Financial Chart
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById("financialChart").getContext("2d");
            const financialChart = new Chart(ctx, {
                type: "doughnut",
                data: {
                    labels: ["Pemasukan", "Pengeluaran", "Sisa Saldo"],
                    datasets: [{
                        data: [' . $data['financial']['income'] . ', ' . $data['financial']['expenditure'] . ', ' . $data['financial']['balance'] . '],
                        backgroundColor: [
                            "rgba(74, 222, 128, 0.8)",
                            "rgba(248, 113, 113, 0.8)",
                            "rgba(96, 165, 250, 0.8)"
                        ],
                        borderColor: [
                            "rgba(74, 222, 128, 1)",
                            "rgba(248, 113, 113, 1)",
                            "rgba(96, 165, 250, 1)"
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: "bottom"
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw;
                                    return "Rp " + new Intl.NumberFormat("id-ID").format(value);
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
';

// Include footer template
include_once('../templates/footer.php');
?>