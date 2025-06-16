<?php

require_once '../config/database.php';

if (!$koneksi) {
    die("Database connection failed: " . mysqli_connect_error());
}

$nik = $_POST['nik']; 
$password = $_POST['password'];

$sql = "SELECT * FROM warga WHERE nik = '$nik'";
$result = mysqli_query($koneksi, $sql);

if (mysqli_num_rows($result) > 0) {
    $warga = mysqli_fetch_assoc($result);

    if ($password === $warga['password']) {
        session_start();
        $_SESSION['warga_id'] = $warga['warga_id'];
        $_SESSION['nik'] = $warga['nik'];
        $_SESSION['nama_lengkap'] = $warga['nama_lengkap'];

        $role = trim($warga['role']);
        $_SESSION['role'] = $role;

        switch ($role) {
            case 'admin':
                header("Location: ../views/admin/admin_dashboard.php");
                break;
            case 'panitia':
                header("Location: ../views/panitia/panitia_dashboard.php");
                break;
            case 'berqurban':
                header("Location: ../views/berqurban_dashboard.php");
                break;
            case 'warga':
                header("Location: ../views/warga_dashboard.php");
                break;
            default:
                echo "Invalid role: '" . $role . "'";
                exit();
        }
        exit();
    } else {
        // Invalid password
        echo "Invalid NIK or password.";
    }
} else {
    // User not found
    echo "Invalid NIK or password.";
}
