<?php
require_once('C:\laragon\www\UASWEBCOK\config\database.php');
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transactionType = $_POST['transaction_type'];
    $jumlah = $_POST['amount'];
    $description = $_POST['description'];
    $kategori = $_POST['category'];
}

function getAllWarga()
{
    global $koneksi;
    $sql = "SELECT COUNT(*) AS total FROM warga";
    $result = mysqli_query($koneksi, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    } else {
        echo "Error: " . mysqli_error($koneksi);
        return 0;
    }
}

function getUserCountByRole($role)
{
    global $koneksi;
    $sql = "SELECT COUNT(*) AS total FROM users WHERE role = '$role'";
    $result = mysqli_query($koneksi, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    } else {
        echo "Error: " . mysqli_error($koneksi);
        return 0;
    }
}

function getTabelData()
{
    global $koneksi;

    $sql = "SELECT 
            w.warga_id,
            w.nama_lengkap,
            w.nik,
            w.alamat,
            a.role,
            a.amount_per_person,
            map.amount AS amount_qurban,
            (a.amount_per_person * map.amount) AS total_amount
            FROM warga w
            JOIN meat_allocation_peserta map ON w.warga_id = map.warga_id
            JOIN meat_allocation a ON map.allocation_id = a.allocation_id
            ORDER BY w.nama_lengkap, a.role";

    $result = mysqli_query($koneksi, $sql);
    $data = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }

    return $data;
}

function getdataUser()
{
    $totalWarga = getAllWarga();
    $berqurban = getUserCountByRole('berqurban');
    $panitia = getUserCountByRole('panitia');
    $warga = getUserCountByRole('warga');

    return [
        'warga' => $totalWarga,
        'regular' => $warga,
        'berqurban' => $berqurban,
        'panitia' => $panitia
    ];
}

function getWargaAllocations()
{
    global $koneksi;

    // Query to get all allocation data grouped by warga
    $sql = "SELECT 
            w.warga_id,
            w.nama_lengkap,
            w.nik,
            GROUP_CONCAT(a.role) AS roles,
            GROUP_CONCAT(a.amount_per_person) AS allocations,
            SUM(a.amount_per_person) AS total_allocation
            FROM warga w
            JOIN meat_allocation_peserta map ON w.warga_id = map.warga_id
            JOIN meat_allocation a ON map.allocation_id = a.allocation_id
            GROUP BY w.warga_id, w.nama_lengkap, w.nik
            ORDER BY w.nama_lengkap";

    $result = mysqli_query($koneksi, $sql);
    $data = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }

    return $data;
}

function showDetailModal($wargaId)
{
    global $koneksi;

    // Query to get detailed allocation data for a specific warga
    $sql = "SELECT 
            w.nama_lengkap,
            w.nik,
            a.role,
            a.amount_per_person
            FROM warga w
                JOIN meat_allocation_peserta map ON w.warga_id = map.warga_id
                JOIN meat_allocation a ON map.allocation_id = a.allocation_id
            WHERE w.warga_id = '$wargaId'";

    $result = mysqli_query($koneksi, $sql);
    $details = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $details[] = $row;
        }
    }
    return $details;
}
