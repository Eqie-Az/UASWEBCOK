<?php

require_once '../config/database.php';

if (!$koneksi) {
    die("Database connection failed: " . mysqli_connect_error());
}

$nik = $_POST['nik']; 
$password = $_POST['password'];

<<<<<<< HEAD
// Join with warga table to get nama_lengkap
$sql = "SELECT u.*, w.nama_lengkap 
        FROM warga u 
        LEFT JOIN warga w ON u.warga_id = w.warga_id 
        WHERE u.nik = '$username'";
=======
$sql = "SELECT * FROM warga WHERE nik = '$nik'";
>>>>>>> merge-source-code
$result = mysqli_query($koneksi, $sql);

if (mysqli_num_rows($result) > 0) {
    $warga = mysqli_fetch_assoc($result);

    if ($password === $warga['password']) {
        session_start();
<<<<<<< HEAD
        $_SESSION['nik'] = $user['nik'];
        $_SESSION['alamat'] = $user['alamat'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['warga_id'] = $user['warga_id'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $role = trim($user['role']);
=======
        $_SESSION['warga_id'] = $warga['warga_id'];
        $_SESSION['nik'] = $warga['nik'];
        $_SESSION['nama_lengkap'] = $warga['nama_lengkap'];

        $role = trim($warga['role']);
        $_SESSION['role'] = $role;

>>>>>>> merge-source-code
        switch ($role) {
            case 'admin':
                header("Location: ../views/admin/admin_dashboard.php");
                break;
            case 'panitia':
                header("Location: ../views/panitia/panitia_dashboard.php");
                break;
            case 'berqurban':
                header("Location: ../views/berqurban/berqurban_dashboard.php");
                break;
            case 'warga':
                header("Location: ../views/warga/warga_dashboard.php");
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
