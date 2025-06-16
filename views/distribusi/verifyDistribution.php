<?php
session_start();
require_once 'C:\laragon\www\UASWEBCOK\config\database.php';

// Check user role and include appropriate controller
if (isset($_SESSION['role']) && $_SESSION['role'] === 'panitia') {
    require_once 'C:\laragon\www\UASWEBCOK\controllers\panitiaControllers.php';
} else {
    require_once 'C:\laragon\www\UASWEBCOK\controllers\distribusiController.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['distribution_id'])) {
    $distributionId = $_POST['distribution_id'];

    if (isset($_SESSION['role']) && $_SESSION['role'] === 'panitia') {
        $success = verifyDistribusiPanitia($distributionId);
    } else {
        $success = verifiedDistribusi($distributionId);
    }

    if ($success) {
        $_SESSION['message'] = "Distribusi berhasil diverifikasi!";
        $_SESSION['alert_type'] = "success";
    } else {
        $_SESSION['message'] = "Gagal memverifikasi distribusi!";
        $_SESSION['alert_type'] = "danger";
    }
} else {
    $_SESSION['message'] = "Permintaan tidak valid!";
    $_SESSION['alert_type'] = "warning";
}

header('Location: distribusiDashboard.php');
exit();
