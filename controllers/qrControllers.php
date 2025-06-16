<?php
// filepath: controllers/qrControllers.php
require_once __DIR__ . '/../config/database.php';

function getQRDataByWargaId($warga_id)
{
    global $koneksi;

    // Get meat allocation data
    $sql = "SELECT 
            w.nik,
            w.nama_lengkap,
            COALESCE(SUM(ma.sapi), 0) as total_sapi,
            COALESCE(SUM(ma.kambing), 0) as total_kambing,
            COALESCE(SUM(ma.sapi + ma.kambing), 0) as total_daging
        FROM warga w
        LEFT JOIN meat_allocation_peserta map ON w.warga_id = map.warga_id
        LEFT JOIN meat_allocation ma ON map.allocation_id = ma.allocation_id
        WHERE w.warga_id = ?
        GROUP BY w.warga_id, w.nik, w.nama_lengkap";

    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "s", $warga_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    if ($data) {
        return json_encode([
            'nik' => $data['nik'],
            'nama_lengkap' => $data['nama_lengkap'],
            'sapi' => number_format($data['total_sapi'], 1),
            'kambing' => number_format($data['total_kambing'], 1),
            'total_daging' => number_format($data['total_daging'], 1)
        ]);
    }

    return json_encode([
        'nik' => '',
        'nama_lengkap' => '',
        'sapi' => '0.0',
        'kambing' => '0.0',
        'total_daging' => '0.0'
    ]);
}

function generateQRCodeUrl($data)
{
    if (!$data) return '';
    return "https://quickchart.io/qr?text=" . urlencode($data);
}
