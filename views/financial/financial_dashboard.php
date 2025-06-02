<?php
session_start();
require_once('C:\laragon\www\UASWEBCOK\config\config.php');
include_once('../templates/header.php');
include_once('../templates/sidebar_admin.php');
require_once('C:\laragon\www\UASWEBCOK\controllers\financialControllers.php');

$data = getFinancial();

?>
<div class="ml-64 p-4">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
        <div class="p-6">
            <div class="grid gap-4">
                <div class="relative overflow-x-auto w-full">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">No</th>
                                <th scope="col" class="px-6 py-3">Tipe Transaksi</th>
                                <th scope="col" class="px-6 py-3">Jumlah</th>
                                <th scope="col" class="px-6 py-3">Deskripsi</th>
                                <th scope="col" class="px-6 py-3">Kategori</th>
                                <th scope="col" class="px-6 py-3">Tanggal Transaksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $row): ?>
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                    <td class="px-6 py-4"><?= htmlspecialchars($row['transaction_id']) ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($row['transaction_type']) ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($row['amount']) ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($row['description']) ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($row['category']) ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($row['transaction_date']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>