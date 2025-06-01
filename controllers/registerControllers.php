<?php
require_once '../config/database.php';
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Start transaction to ensure both inserts succeed or both fail
mysqli_begin_transaction($koneksi);

try {
    $nik = $_POST['nik'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $phone = $_POST['phone'];
    $alamat = $_POST['alamat'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = ($_POST['role']);

    $check_username = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($koneksi, $check_username);
    if (mysqli_num_rows($result) > 0) {
        echo "Username sudah digunakan. Silakan pilih username lain.";
        exit();
    }

    $sql_warga = "INSERT INTO warga (nik, nama_lengkap, jenis_kelamin, phone, alamat) 
                 VALUES ('$nik', '$nama_lengkap', '$jenis_kelamin', '$phone', '$alamat')";

    if (mysqli_query($koneksi, $sql_warga)) {
        $warga_id = mysqli_insert_id($koneksi);

        $sql_users = "INSERT INTO users (warga_id, username, password, role) 
                    VALUES ('$warga_id', '$username', '$password', '$role')";

        if (mysqli_query($koneksi, $sql_users)) {
            mysqli_commit($koneksi);

            header("Location: ../views/loginPage.php?success=1");
            exit();
        } else {
            throw new Exception("Error inserting user: " . mysqli_error($koneksi));
        }
    } else {
        throw new Exception("Error inserting warga: " . mysqli_error($koneksi));
    }
} catch (Exception $e) {
    mysqli_rollback($koneksi);
    echo "Registration failed: " . $e->getMessage();
}
