<!-- Sidebar -->
<?php
require_once('C:\laragon\www\UASWEBCOK\config\config.php');

?>
<button data-drawer-target="default-sidebar" data-drawer-toggle="default-sidebar" aria-controls="default-sidebar" type="button" class="fixed top-4 left-4 z-50 inline-flex items-center p-2 mt-2 ms-3 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
    <span class="sr-only">Open sidebar</span>
    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
        <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
    </svg>
</button>

<aside id="default-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0 shadow-xl" aria-label="Sidebar">
    <div class="h-full px-3 py-4 overflow-y-auto bg-gray-50 dark:bg-gray-800 sidebar-gradient">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-white flex items-center justify-center">
                <i class="fas fa-mosque mr-2"></i> Admin Panel
            </h2>
            <p class="text-white/70 text-sm mt-2">Selamat datang, <?= $_SESSION['nama_lengkap'] ?? 'Admin' ?></p>
            <span class="inline-block bg-white/20 text-white text-xs px-3 py-1 rounded-full mt-2">
                <?= ucfirst($_SESSION['role'] ?? '') ?>
            </span>
        </div>

        <ul class="space-y-2 font-medium">
            <li>
                <a href="../admin/admin_dashboard.php"
                    class="flex items-center p-2 text-white rounded-lg <?= $activeMenu === 'dashboard' ? 'bg-white/20' : 'hover:bg-white/10' ?> group">
                    <i class="fas fa-tachometer-alt w-5 h-5 text-white transition duration-75"></i>
                    <span class="ms-3 sidebar-text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="../financial/financial_dashboard.php"
                    class="flex items-center p-2 text-white rounded-lg <?= $activeMenu === 'financial' ? 'bg-white/20' : 'hover:bg-white/10' ?> group">
                    <i class="fas fa-money-bill-wave w-5 h-5 text-white transition duration-75"></i>
                    <span class="ms-3 sidebar-text">Keuangan</span>
                </a>
            </li>
            <li>
                <a href="../pengguna/user_dashboard.php"
                    class="flex items-center p-2 text-white rounded-lg <?= $activeMenu === 'users' ? 'bg-white/20' : 'hover:bg-white/10' ?> group">
                    <i class="fas fa-users w-5 h-5 text-white transition duration-75"></i>
                    <span class="ms-3 sidebar-text">Manajemen Pengguna</span>
                </a>
            </li>
            <li>
                <a href="../distribusi/distribusiDashboard.php"
                    class="flex items-center p-2 text-white rounded-lg <?= $activeMenu === 'meat' ? 'bg-white/20' : 'hover:bg-white/10' ?> group">
                    <i class="fas fa-cut w-5 h-5 text-white transition duration-75"></i>
                    <span class="ms-3 sidebar-text">Distribusi Daging</span>
                </a>
            </li>
        </ul>

        <div class="mt-8 pt-4 border-t border-white/20">
            <a href="<?= $basePath ?>/../../controllers/logoutControllers.php"
                class="flex items-center p-2 text-red-200 hover:text-red-100 hover:bg-red-500/20 rounded-lg transition-all duration-200">
                <i class="fas fa-sign-out-alt w-5 h-5 transition duration-75"></i>
                <span class="ms-3 sidebar-text">Logout</span>
            </a>
        </div>

    </div>
</aside>

<div class="pl-64 transition-all duration-300 w-full" id="main-content">
    <!-- Main content goes here -->



    <style>
        /* Additional styles for smooth transitions */
        #default-sidebar {
            transition: width 0.3s ease;
        }

        #default-sidebar.collapsed .sidebar-text {
            display: none;
        }

        #default-sidebar.expanded .sidebar-text {
            display: inline;
        }

        .sidebar-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        }
    </style>