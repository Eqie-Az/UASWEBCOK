<?php
// filepath: views/templates/sidebar_berqurban.php
?>
<aside class="fixed top-0 left-0 h-full w-64 bg-gradient-to-b from-blue-600 to-purple-600 shadow-lg flex flex-col z-30">
    <div class="flex flex-col items-center py-8">
        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow mb-3">
            <i class="fas fa-user text-3xl text-blue-600"></i>
        </div>
        <span class="text-white font-bold text-lg mb-1">
            Berqurban Panel
        </span>
        <?php if (isset($_SESSION['nama_lengkap'])): ?>
            <span class="text-white text-sm mb-1"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></span>
        <?php endif; ?>
        <span class="bg-white text-blue-600 text-xs px-3 py-1 rounded-full font-semibold">Berqurban</span>
    </div>
    <nav class="flex-1 px-4">
        <ul class="space-y-2">
            <li>
                <a href="../berqurban/berqurban_dashboard.php" class="flex items-center px-4 py-2 rounded-lg text-white hover:bg-blue-700 transition">
                    <i class="fas fa-home mr-3"></i>
                    Dashboard
                </a>
            </li>
        </ul>
    </nav>
    <div class="px-4 pb-6 mt-auto">
        <a href="../../controllers/logoutControllers.php" class="flex items-center px-4 py-2 rounded-lg text-white hover:bg-red-600 transition">
            <i class="fas fa-sign-out-alt mr-3"></i>
            Logout
        </a>
    </div>
</aside>