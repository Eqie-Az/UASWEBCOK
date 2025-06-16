<?php
session_start();
require_once('C:\laragon\www\UASWEBCOK\config\config.php');
include_once('../templates/header.php');
include_once('../templates/sidebar_panitia.php');
require_once('C:\laragon\www\UASWEBCOK\controllers\panitiaControllers.php');
require_once('C:\laragon\www\UASWEBCOK\controllers\qrCodeControllers.php');

$data = getPanitiaDashboardData();

$user_id = $_SESSION['user_id'] ?? 0;
$user_name = $_SESSION['nama_lengkap'] ?? '';
$user_role = $_SESSION['role'] ?? '';


$userQRResult = getUserQRCode($user_id);
?>

<!-- Main content -->
<main class="pl-0 sm:pl-64 transition-all duration-300">
    <div class="p-4 md:p-6">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-tachometer-alt mr-3 text-green-600"></i>
                Panitia Dashboard
            </h1>
            <p class="text-gray-600 mt-2">Panel kontrol dan pengelolaan sistem distribusi qurban</p>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">

            <!-- Daftar Distribusi -->
            <a href="daftar_distribusi.php" class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm flex items-center hover:shadow-md transition-all duration-200 hover:border-teal-300">
                <div class="p-3 rounded-full bg-teal-100 text-teal-600">
                    <i class="fas fa-list-alt"></i>
                </div>
                <div class="ml-3">
                    <h4 class="font-medium">Daftar Distribusi</h4>
                    <p class="text-xs text-gray-500 mt-1">Lihat data distribusi daging</p>
                </div>
            </a>
        </div>

        <!-- Distribusi Daging Summary -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Status Distribusi Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="card-gradient-panitia text-white p-6 rounded-t-xl">
                    <h3 class="text-xl font-semibold flex items-center">
                        <i class="fas fa-user-check mr-2"></i>
                        Status Pengambilan
                    </h3>
                </div>
                <div class="p-6 flex justify-between">
                    <div class="p-4 bg-emerald-50 rounded-lg text-center">
                        <p class="text-sm font-medium text-emerald-600">Sudah Diambil</p>
                        <p class="text-2xl font-bold text-emerald-700">2</p>
                    </div>
                    <div class="p-4 bg-amber-50 rounded-lg text-center">
                        <p class="text-sm font-medium text-amber-600">Belum Diambil</p>
                        <p class="text-2xl font-bold text-amber-700">77</p>
                    </div>
                </div>
            </div>

            <!-- Status Distribusi Daging -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="card-gradient-panitia text-white p-6 rounded-t-xl">
                    <h3 class="text-xl font-semibold flex items-center">
                        <i class="fas fa-drumstick-bite mr-2"></i>
                        Status Distribusi Daging
                    </h3>
                </div>
                <div class="p-6 text-center">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm font-medium text-gray-600">Total Distribusi</p>
                        <p class="text-2xl font-bold text-gray-700">79</p>
                    </div>
                </div>
            </div>

            <!-- QR Code Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 md:col-span-2">
                <div class="card-gradient-panitia text-white p-6 rounded-t-xl">
                    <h3 class="text-xl font-semibold flex items-center">
                        <i class="fas fa-qr-code mr-2"></i>
                        QR Code - <?php echo htmlspecialchars($user_name); ?>
                    </h3>
                </div>
                <div class="p-6">
                    <?php if ($userQRResult['status'] === 'success'): ?>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- QR Code Display -->
                            <div class="text-center">
                                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                    <img src="<?php echo htmlspecialchars($userQRResult['qr_code_url']); ?>"
                                        alt="QR Code" class="mx-auto max-w-full h-auto" style="max-width: 200px;">
                                </div>
                            </div>

                            <!-- User Details -->
                            <div class="lg:col-span-2">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4">
                                    <?php echo ($user_role === 'panitia') ? 'Detail Panitia' : 'Detail Distribusi Anda'; ?>
                                </h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="text-sm text-gray-600 mb-1">ID Distribusi</p>
                                        <p class="text-lg font-semibold text-green-600"><?php echo htmlspecialchars($userQRResult['data']['distribution_id']); ?></p>
                                    </div>

                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="text-sm text-gray-600 mb-1">Status</p>
                                        <?php if ($userQRResult['data']['status'] == 'diambil'): ?>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i> Sudah Diambil
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>
                                                <?php echo $user_role === 'panitia' ? 'Aktif' : 'Belum Diambil'; ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="text-sm text-gray-600 mb-1">Nama Lengkap</p>
                                        <p class="text-lg font-semibold"><?php echo htmlspecialchars($userQRResult['data']['nama_lengkap']); ?></p>
                                        <p class="text-xs text-gray-500">NIK: <?php echo htmlspecialchars($userQRResult['data']['nik']); ?></p>
                                    </div>

                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="text-sm text-gray-600 mb-1">Total Daging</p>
                                        <p class="text-lg font-semibold text-blue-600"><?php echo $userQRResult['data']['total_daging']; ?> kg</p>
                                        <p class="text-xs text-gray-500">
                                            Sapi: <?php echo $userQRResult['data']['sapi']; ?> kg |
                                            Kambing: <?php echo $userQRResult['data']['kambing']; ?> kg
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Informasi QR Code</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <?php echo htmlspecialchars($userQRResult['message']); ?>
                                    </div>
                                    <div class="mt-2 text-xs text-yellow-600">
                                        User ID: <?php echo $user_id; ?> | Nama: <?php echo htmlspecialchars($user_name); ?> | Role: <?php echo htmlspecialchars($user_role); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-200 mb-8 overflow-hidden">
            <div class="card-gradient-panitia text-white p-6 rounded-t-xl">
                <h3 class="text-xl font-semibold flex items-center">
                    <i class="fas fa-list-alt mr-2"></i>
                    Distribusi Terbaru
                </h3>
            </div>
            <div class="p-4 md:p-6">
                <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Daging</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Ambil</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php
                            $distribusiData = getPanitiaDistribusiData();
                            $count = 0;

                            // Flatten the array for displaying the most recent distributions
                            $recentDistributions = [];
                            foreach ($distribusiData as $role => $distributions) {
                                foreach ($distributions as $distribution) {
                                    $recentDistributions[] = $distribution;
                                }
                            }
                            foreach (array_slice($recentDistributions, 0, 5) as $distribution) {
                                $statusClass = $distribution['status'] == 'sudah_diambil' ? 'text-green-600' : 'text-red-600';
                                $statusDisplay = $distribution['status'] == 'sudah_diambil' ? 'Sudah Diambil' : 'Belum Diambil';
                            ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($distribution['nama_lengkap']) ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm <?= $statusClass ?> font-medium">
                                        <?= $statusDisplay ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        Sapi: <?= $distribution['sapi'] ?> kg, Kambing: <?= $distribution['kambing'] ?> kg
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        <?= $distribution['pickup_date'] ? date('d-m-Y H:i', strtotime($distribution['pickup_date'])) : '-' ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-right">
                    <a href="../distribusi/distribusiDashboard.php" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
                        Lihat Semua
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
// Include footer template
include_once('../templates/footer.php');
?>

<style>
    .card-gradient-panitia {
        background: linear-gradient(135deg, #047857 0%, #10b981 100%);
    }

    /* Sidebar styles */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 16rem;
        /* 64 in Tailwind = 16rem */
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        z-index: 1000;
        box-shadow: 4px 0 10px rgba(0, 0, 0, 0.05);
    }

    /* Main content area */
    main {
        width: 100%;
        min-height: 100vh;
        background-color: #f9fafb;
    }

    /* Custom scrollbar for tables */
    .scrollbar-thin::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }

    .scrollbar-thin::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }

    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }

    /* Table responsive adjustments */
    @media (max-width: 768px) {
        table {
            display: block;
            overflow-x: auto;
        }
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        main {
            margin-left: 0;
        }
    }
</style>