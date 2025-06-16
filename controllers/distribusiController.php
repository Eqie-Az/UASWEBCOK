<?php
require_once('C:\laragon\www\UASWEBCOK\config\database.php');
if (!$koneksi) {
    die("Database connection failed: " . mysqli_connect_error());
}
function getDistribusiData()
{
    global $koneksi;
    $sql = "SELECT 
        md.distribution_id,
        md.kode_qr,
        md.status,
        md.pickup_date,
        w.warga_id,
        w.nama_lengkap,
        w.nik,
        map.peserta_id,
        ma.role,
        ma.sapi,
        ma.kambing,
        (ma.sapi + ma.kambing) AS total_daging
    FROM meat_distribution md
    JOIN meat_allocation_peserta map ON md.peserta_id = map.peserta_id
    JOIN warga w ON map.warga_id = w.warga_id
    JOIN meat_allocation ma ON map.allocation_id = ma.allocation_id
    ORDER BY ma.role, w.nama_lengkap";

    $result = mysqli_query($koneksi, $sql);
    $data = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $role = $row['role'];
            if (!isset($data[$role])) {
                $data[$role] = [];
            }
            $data[$role][] = $row;
        }
    }

    return $data;
}

function verifiedDistribusi($distributionId)
{
    global $koneksi;

    $distributionId = intval($distributionId);
    if ($distributionId <= 0) {
        return false;
    }
    $stmt = mysqli_prepare($koneksi, "UPDATE meat_distribution SET status = 'sudah_diambil', pickup_date = NOW() WHERE distribution_id = ?");

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $distributionId);
    $success = mysqli_stmt_execute($stmt);

    $affectedRows = mysqli_stmt_affected_rows($stmt);

    mysqli_stmt_close($stmt);

    return $success && $affectedRows > 0;
}


function getDistribusiStats()
{
    global $koneksi;
    $stats = [
        'total' => 0,
        'collected' => 0,
        'pending' => 0
    ];

    $sql = "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'sudah_diambil' THEN 1 ELSE 0 END) as collected,
        SUM(CASE WHEN status = 'belum_diambil' THEN 1 ELSE 0 END) as pending
    FROM meat_distribution";

    $result = mysqli_query($koneksi, $sql);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        $stats['total'] = intval($row['total']);
        $stats['collected'] = intval($row['collected']);
        $stats['pending'] = intval($row['pending']);
    }

    return $stats;
}

function getDistribusiById($id)
{
    global $koneksi;

    $id = intval($id);
    $stmt = mysqli_prepare($koneksi, "SELECT 
        md.distribution_id,
        md.kode_qr,
        md.status,
        md.pickup_date,
        w.warga_id,
        w.nama_lengkap,
        w.nik,
        map.peserta_id,
        ma.role,
        ma.sapi,
        ma.kambing,
        (ma.sapi + ma.kambing) AS total_daging
    FROM meat_distribution md
    JOIN meat_allocation_peserta map ON md.peserta_id = map.peserta_id
    JOIN warga w ON map.warga_id = w.warga_id
    JOIN meat_allocation ma ON map.allocation_id = ma.allocation_id
    WHERE md.distribution_id = ?");

    if (!$stmt) {
        return null;
    }

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        return $row;
    }

    return null;
}
