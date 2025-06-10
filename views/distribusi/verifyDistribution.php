<?php
require_once 'C:\laragon\www\UASWEBCOK\config\database.php';
require_once 'C:\laragon\www\UASWEBCOK\controllers\distribusiController.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['distribution_id'])) {
    $distributionId = $_POST['distribution_id'];

    // Call the controller function to update status
    $success = verifiedDistribusi($distributionId);

    if ($success) {
        // Set success message
        $_SESSION['message'] = "Distribusi berhasil diverifikasi!";
        $_SESSION['alert_type'] = "success";
    } else {
        // Set error message
        $_SESSION['message'] = "Gagal memverifikasi distribusi!";
        $_SESSION['alert_type'] = "danger";
    }
} else {
    // Invalid request
    $_SESSION['message'] = "Permintaan tidak valid!";
    $_SESSION['alert_type'] = "warning";
}

// Redirect back to the dashboard
header('Location: distribusiDashboard.php');
exit();
