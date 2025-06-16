<?php

require_once('C:\laragon\www\UASWEBCOK\config\database.php');
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

/**
 * Get dashboard data for panitia
 * Similar to admin but with limited scope
 */
function getPanitiaDashboardData()
{
    $wargaTotal = getAllWarga();
    $berqurbanCount = getUserCountByRole('berqurban');
    $panitiaCount = getUserCountByRole('panitia');
    $regularWargaCount = $wargaTotal - $berqurbanCount - $panitiaCount;

    $data = [
        'user_stats' => [
            'warga' => $wargaTotal,
            'berqurban' => $berqurbanCount,
            'panitia' => $panitiaCount,
            'regular' => $regularWargaCount
        ],
        'meat_distribution' => getMeatDistribution()
    ];
    return $data;
}

/**
 * Get all warga count
 */
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

/**
 * Get user count by specific role
 */
function getUserCountByRole($role)
{
    global $koneksi;
    $role = mysqli_real_escape_string($koneksi, $role);

    $sql = "SELECT COUNT(*) AS total FROM warga WHERE role = '$role'";
    $result = mysqli_query($koneksi, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    } else {
        echo "Error: " . mysqli_error($koneksi);
        return 0;
    }
}

/**
 * Get meat distribution statistics 
 */
function getMeatDistribution()
{
    global $koneksi;

    $sqlTotal = "SELECT 
        SUM(total_daging_kambing + total_daging_sapi) AS total 
        FROM meat_allocation;";
    $totalResult = mysqli_query($koneksi, $sqlTotal);
    $total = 0;
    if ($totalResult && $row = mysqli_fetch_assoc($totalResult)) {
        $total = $row['total'] ?: 0;
    }

    $sqlBeef = "SELECT AVG(total_daging_sapi) AS total FROM meat_allocation";
    $beefResult = mysqli_query($koneksi, $sqlBeef);
    $beef = 0;
    if ($beefResult && $row = mysqli_fetch_assoc($beefResult)) {
        $beef = $row['total'] ?: 0;
    }

    $sqlGoat = "SELECT AVG(total_daging_kambing) AS total FROM meat_allocation";
    $goatResult = mysqli_query($koneksi, $sqlGoat);
    $goat = 0;
    if ($goatResult && $row = mysqli_fetch_assoc($goatResult)) {
        $goat = $row['total'] ?: 0;
    }

    return [
        'total_distributed' => $total,
        'beef' => $beef,
        'goat' => $goat,
        'total' => $beef + $goat
    ];
}

// User management functions have been removed as requested

/**
 * Get distribution data for panitia to manage
 */
function getPanitiaDistribusiData()
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

/**
 * Verify meat distribution by panitia
 */
function verifyDistribusiPanitia($distributionId)
{
    global $koneksi;

    // Validate input
    $distributionId = intval($distributionId);
    if ($distributionId <= 0) {
        return false;
    }

    // Use prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($koneksi, "UPDATE meat_distribution SET status = 'sudah_diambil', pickup_date = NOW() WHERE distribution_id = ?");

    if (!$stmt) {
        return false;
    }

    // Bind parameters
    mysqli_stmt_bind_param($stmt, "i", $distributionId);

    // Execute the statement
    $success = mysqli_stmt_execute($stmt);

    // Get affected rows
    $affectedRows = mysqli_stmt_affected_rows($stmt);

    // Close statement
    mysqli_stmt_close($stmt);

    return $success && $affectedRows > 0;
}


function getPanitiaDistribusiStats()
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
