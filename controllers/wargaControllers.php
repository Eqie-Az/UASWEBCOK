<?php
require_once __DIR__ . '/../config/database.php';

function getWargaByNik($nik)
{
    global $koneksi;
    $sql = "SELECT * FROM warga WHERE nik = '$nik' AND role = 'warga'";
    $result = mysqli_query($koneksi, $sql);
    return mysqli_fetch_assoc($result);
}

function getJumlahDagingByWargaId($warga_id)
{
    global $koneksi;
    $sql = "SELECT 
                SUM(ma.kambing + ma.sapi) AS total_daging
            FROM meat_allocation_peserta map
            JOIN meat_allocation ma ON map.allocation_id = ma.allocation_id
            WHERE map.warga_id = '$warga_id'";
    $result = mysqli_query($koneksi, $sql);
    if ($row = mysqli_fetch_assoc($result)) {
        return $row['total_daging'] ?? 0;
    }
    return 0;
}