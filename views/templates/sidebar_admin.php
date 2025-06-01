        <!-- Sidebar -->
        <nav class="w-64 sidebar-gradient shadow-lg hidden md:block">
            <div class="p-6">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-white flex items-center justify-center">
                        <i class="fas fa-mosque mr-2"></i> Admin Panel
                    </h2>
                    <p class="text-white/70 text-sm mt-2">Selamat datang, <?= $_SESSION['nama_lengkap'] ?? 'Admin' ?></p>
                    <span class="inline-block bg-white/20 text-white text-xs px-3 py-1 rounded-full mt-2">
                        <?= ucfirst($_SESSION['role'] ?? '') ?>
                    </span>
                </div>

                <ul class="space-y-2">
                    <li>
                        <a href="<?= $basePath ?>admin/admin_dashboard.php"
                            class="flex items-center px-4 py-3 <?= $activeMenu === 'dashboard' ? 'text-white bg-white/20' : 'text-white/80 hover:text-white hover:bg-white/10' ?> rounded-lg transition-all duration-200">
                            <i class="fas fa-tachometer-alt mr-3"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="<?= $basePath ?>admin/qurban_dashboard.php"
                            class="flex items-center px-4 py-3 <?= $activeMenu === 'qurban' ? 'text-white bg-white/20' : 'text-white/80 hover:text-white hover:bg-white/10' ?> rounded-lg transition-all duration-200">
                            <i class="fas fa-hand-holding-heart mr-3"></i>
                            Dashboard Qurban
                        </a>
                    </li>
                    <li>
                        <a href="<?= $basePath ?>admin/financial.php"
                            class="flex items-center px-4 py-3 <?= $activeMenu === 'financial' ? 'text-white bg-white/20' : 'text-white/80 hover:text-white hover:bg-white/10' ?> rounded-lg transition-all duration-200">
                            <i class="fas fa-money-bill-wave mr-3"></i>
                            Keuangan
                        </a>
                    </li>
                    <li>
                        <a href="<?= $basePath ?>admin/users.php"
                            class="flex items-center px-4 py-3 <?= $activeMenu === 'users' ? 'text-white bg-white/20' : 'text-white/80 hover:text-white hover:bg-white/10' ?> rounded-lg transition-all duration-200">
                            <i class="fas fa-users mr-3"></i>
                            Manajemen Pengguna
                        </a>
                    </li>
                    <li>
                        <a href="<?= $basePath ?>admin/meat.php"
                            class="flex items-center px-4 py-3 <?= $activeMenu === 'meat' ? 'text-white bg-white/20' : 'text-white/80 hover:text-white hover:bg-white/10' ?> rounded-lg transition-all duration-200">
                            <i class="fas fa-cut mr-3"></i>
                            Distribusi Daging
                        </a>
                    </li>
                    <li>
                        <a href="<?= $basePath ?>admin/qr_scanner.php"
                            class="flex items-center px-4 py-3 <?= $activeMenu === 'qr' ? 'text-white bg-white/20' : 'text-white/80 hover:text-white hover:bg-white/10' ?> rounded-lg transition-all duration-200">
                            <i class="fas fa-camera mr-3"></i>
                            Scanner QR
                        </a>
                    </li>
                    <li>
                        <a href="<?= $basePath ?>admin/reports.php"
                            class="flex items-center px-4 py-3 <?= $activeMenu === 'reports' ? 'text-white bg-white/20' : 'text-white/80 hover:text-white hover:bg-white/10' ?> rounded-lg transition-all duration-200">
                            <i class="fas fa-chart-bar mr-3"></i>
                            Laporan
                        </a>
                    </li>
                </ul>

                <div class="mt-8 pt-8 border-t border-white/20">
                    <a href="<?= $basePath ?>controllers/logout.php"
                        class="flex items-center px-4 py-3 text-red-200 hover:text-red-100 hover:bg-red-500/20 rounded-lg transition-all duration-200">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        Logout
                    </a>
                </div>
            </div>
        </nav>

        <!-- Mobile menu button -->
        <div class="fixed top-0 left-0 z-40 md:hidden p-4">
            <button id="menu-toggle" class="bg-primary-600 text-white p-2 rounded-md">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <!-- Mobile sidebar overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden md:hidden"></div>

        <!-- Mobile sidebar -->
        <nav id="mobile-sidebar" class="fixed left-0 top-0 bottom-0 w-64 sidebar-gradient shadow-lg z-40 transform -translate-x-full transition-transform duration-300 md:hidden">
            <div class="p-6">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-white flex items-center justify-center">
                        <i class="fas fa-mosque mr-2"></i> Admin Panel
                    </h2>
                    <p class="text-white/70 text-sm mt-2">Selamat datang, <?= $_SESSION['nama_lengkap'] ?? 'Admin' ?></p>
                    <span class="inline-block bg-white/20 text-white text-xs px-3 py-1 rounded-full mt-2">
                        <?= ucfirst($_SESSION['role'] ?? '') ?>
                    </span>
                </div>

                <ul class="space-y-2">
                    <li>
                        <a href="<?= $basePath ?>admin/admin_dashboard.php"
                            class="flex items-center px-4 py-3 <?= $activeMenu === 'dashboard' ? 'text-white bg-white/20' : 'text-white/80 hover:text-white hover:bg-white/10' ?> rounded-lg transition-all duration-200">
                            <i class="fas fa-tachometer-alt mr-3"></i>
                            Dashboard
                        </a>
                    </li>
                    <!-- Copy other menu items from desktop sidebar with the same active menu logic -->
                </ul>

                <div class="mt-8 pt-8 border-t border-white/20">
                    <a href="<?= $basePath ?>controllers/logout.php"
                        class="flex items-center px-4 py-3 text-red-200 hover:text-red-100 hover:bg-red-500/20 rounded-lg transition-all duration-200">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        Logout
                    </a>
                </div>
            </div>
        </nav>