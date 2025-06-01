<?php

require_once '../config/database.php';

if (!$koneksi) {
    die("Database connection failed: " . mysqli_connect_error());
}

$username = $_POST['username'];
$password = $_POST['password'];

// Join with warga table to get nama_lengkap
$sql = "SELECT u.*, w.nama_lengkap 
        FROM users u 
        LEFT JOIN warga w ON u.warga_id = w.warga_id 
        WHERE u.username = '$username'";
$result = mysqli_query($koneksi, $sql);

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);

    if ($password === $user['password']) {
        session_start();
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['warga_id'] = $user['warga_id'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $role = trim($user['role']);
        switch ($role) {
            case 'admin':
                header("Location: ../views/admin/admin_dashboard.php");
                break;
            case 'panitia':
                header("Location: ../views/panitia_dashboard.php");
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
        echo "Invalid username or password.";
    }
} else {
    // User not found
    echo "Invalid username or password.";
}
