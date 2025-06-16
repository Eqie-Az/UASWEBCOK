<?php
require_once('C:\laragon\www\UASWEBCOK\config\database.php');

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

/**
 * Generate QR Code for distribution data
 * 
 * @param int $distribution_id The ID of the distribution
 * @return array Response with QR code data
 */
function generateDistributionQRCode($distribution_id)
{
    global $koneksi;

    // Validate input
    $distribution_id = intval($distribution_id);
    if ($distribution_id <= 0) {
        return [
            'status' => 'error',
            'message' => 'ID distribusi tidak valid'
        ];
    }
    $sql = "SELECT 
        md.distribution_id,
        md.status,
        w.nama_lengkap,
        w.nik,
        ma.kambing,
        ma.sapi,
        (ma.sapi + ma.kambing) AS total_daging
    FROM meat_distribution md
    JOIN meat_allocation_peserta map ON md.peserta_id = map.peserta_id
    JOIN warga w ON map.warga_id = w.warga_id
    JOIN meat_allocation ma ON map.allocation_id = ma.allocation_id
    WHERE md.distribution_id = ?";

    // Get distribution data
    $stmt = mysqli_prepare($koneksi, $sql);

    if (!$stmt) {
        return [
            'status' => 'error',
            'message' => 'Gagal mempersiapkan query: ' . mysqli_error($koneksi)
        ];
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$row = mysqli_fetch_assoc($result)) {
        return [
            'status' => 'error',
            'message' => 'Data distribusi tidak ditemukan'
        ];
    }

    // Prepare QR code data
    $qrData = [
        'distribution_id' => $row['distribution_id'],
        'nik' => $row['nik'],
        'nama_lengkap' => $row['nama_lengkap'],
        'kambing' => (float)$row['kambing'],
        'sapi' => (float)$row['sapi'],
        'total_daging' => (float)$row['total_daging'],
        'status' => $row['status']
    ];

    // Convert to JSON
    $qrText = json_encode($qrData);

    // Generate QR code URL
    $qrCodeUrl = "https://quickchart.io/qr?text=" . urlencode($qrText);

    // Return the data with QR code URL
    return [
        'status' => 'success',
        'data' => $row,
        'qr_data' => $qrData,
        'qr_code_url' => $qrCodeUrl
    ];
}


function displayQRCode($distribution_id)
{
    // Get QR code data
    $result = generateDistributionQRCode($distribution_id);

    if ($result['status'] === 'error') {
        header('Content-Type: text/plain');
        echo $result['message'];
        return;
    }

    // Get QR code image from API
    $qrCodeContent = file_get_contents($result['qr_code_url']);

    if ($qrCodeContent === false) {
        header('Content-Type: text/plain');
        echo "Failed to fetch QR code from API";
        return;
    }

    // Output image directly
    header('Content-Type: image/png');
    echo $qrCodeContent;
}


function saveQRCode($distribution_id)
{
    global $koneksi;

    $result = generateDistributionQRCode($distribution_id);

    if ($result['status'] === 'error') {
        return $result;
    }

    $qrCodeUrl = $result['qr_code_url'];

    $stmt = mysqli_prepare($koneksi, "UPDATE meat_distribution SET kode_qr = ? WHERE distribution_id = ?");

    if (!$stmt) {
        return [
            'status' => 'error',
            'message' => 'Gagal mempersiapkan query: ' . mysqli_error($koneksi)
        ];
    }

    mysqli_stmt_bind_param($stmt, "si", $qrCodeUrl, $distribution_id);
    $success = mysqli_stmt_execute($stmt);

    if (!$success) {
        return [
            'status' => 'error',
            'message' => 'Gagal menyimpan kode QR: ' . mysqli_error($koneksi)
        ];
    }

    return [
        'status' => 'success',
        'message' => 'Kode QR berhasil disimpan',
        'qr_code_url' => $qrCodeUrl
    ];
}


function getUserDistributionId($user_id)
{
    global $koneksi;

    $sql = "SELECT md.distribution_id 
            FROM meat_distribution md
            JOIN meat_allocation_peserta map ON md.peserta_id = map.peserta_id
            JOIN warga w ON map.warga_id = w.warga_id
            WHERE w.warga_id = ?
            LIMIT 1";

    $stmt = mysqli_prepare($koneksi, $sql);
    if (!$stmt) return 0;

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        return $row['distribution_id'];
    }

    return 0;
}


function generateDistributionQRCodeForUser($distribution_id, $user_id, $user_role)
{
    global $koneksi;

    // Validate input
    $distribution_id = intval($distribution_id);
    $user_id = intval($user_id);

    if ($distribution_id <= 0) {
        return [
            'status' => 'error',
            'message' => 'ID distribusi tidak valid'
        ];
    }

    if ($user_role === 'panitia') {
        $sql = "SELECT 
            md.distribution_id,
            md.status,
            w.nama_lengkap,
            w.nik,
            ma.kambing,
            ma.sapi,
            (ma.sapi + ma.kambing) AS total_daging
        FROM meat_distribution md
        JOIN meat_allocation_peserta map ON md.peserta_id = map.peserta_id
        JOIN warga w ON map.warga_id = w.warga_id
        JOIN meat_allocation ma ON map.allocation_id = ma.allocation_id
        WHERE md.distribution_id = ?";

        $stmt = mysqli_prepare($koneksi, $sql);
    } else {
        $sql = "SELECT 
            md.distribution_id,
            md.status,
            w.nama_lengkap,
            w.nik,
            ma.kambing,
            ma.sapi,
            (ma.sapi + ma.kambing) AS total_daging
        FROM meat_distribution md
        JOIN meat_allocation_peserta map ON md.peserta_id = map.peserta_id
        JOIN warga w ON map.warga_id = w.warga_id
        JOIN meat_allocation ma ON map.allocation_id = ma.allocation_id
        WHERE md.distribution_id = ? AND w.warga_id = ?";

        $stmt = mysqli_prepare($koneksi, $sql);    }
    if (!$stmt) {
        return [
            'status' => 'error',
            'message' => 'Gagal mempersiapkan query: ' . mysqli_error($koneksi)
        ];
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$row = mysqli_fetch_assoc($result)) {
        $message = $user_role === 'warga' ?
            'Data distribusi Anda tidak ditemukan atau Anda tidak memiliki akses' :
            'Data distribusi tidak ditemukan';

        return [
            'status' => 'error',
            'message' => $message
        ];
    }

    $qrData = [
        'distribution_id' => $row['distribution_id'],
        'nik' => $row['nik'],
        'nama_lengkap' => $row['nama_lengkap'],
        'kambing' => (float)$row['kambing'],
        'sapi' => (float)$row['sapi'],
        'total_daging' => (float)$row['total_daging'],
        'status' => $row['status'],
        'generated_for' => $user_role,
        'timestamp' => date('Y-m-d H:i:s')
    ];

    $qrText = json_encode($qrData);

    $qrCodeUrl = "https://quickchart.io/qr?text=" . urlencode($qrText);

    return [
        'status' => 'success',
        'data' => $row,
        'qr_data' => $qrData,
        'qr_code_url' => $qrCodeUrl
    ];
}

if (isset($_GET['action']) && $_GET['action'] === 'display' && isset($_GET['id'])) {
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    $user_role = isset($_GET['role']) ? $_GET['role'] : '';

    if ($user_role && $user_id) {
        $result = generateDistributionQRCodeForUser($_GET['id'], $user_id, $user_role);

        if ($result['status'] === 'error') {
            header('Content-Type: text/plain');
            echo $result['message'];
            exit;
        }

        $qrCodeContent = file_get_contents($result['qr_code_url']);

        if ($qrCodeContent === false) {
            header('Content-Type: text/plain');
            echo "Failed to fetch QR code from API";
            exit;
        }

        header('Content-Type: image/png');
        echo $qrCodeContent;
    } else {
        displayQRCode($_GET['id']);
    }
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'save' && isset($_GET['id'])) {
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    $user_role = isset($_GET['role']) ? $_GET['role'] : '';

    if ($user_role && $user_id) {
        $result = saveQRCodeForUser($_GET['id'], $user_id, $user_role);
    } else {
        $result = saveQRCode($_GET['id']);
    }

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

function saveQRCodeForUser($distribution_id, $user_id, $user_role)
{
    global $koneksi;

    $result = generateDistributionQRCodeForUser($distribution_id, $user_id, $user_role);

    if ($result['status'] === 'error') {
        return $result;
    }

    if ($user_role === 'warga') {
        $checkSql = "SELECT COUNT(*) as count FROM meat_distribution md
                     JOIN meat_allocation_peserta map ON md.peserta_id = map.peserta_id
                     JOIN warga w ON map.warga_id = w.warga_id
                     WHERE md.distribution_id = ? AND w.warga_id = ?";

        $stmt = mysqli_prepare($koneksi, $checkSql);
        mysqli_stmt_bind_param($stmt, "ii", $distribution_id, $user_id);
        mysqli_stmt_execute($stmt);
        $checkResult = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($checkResult);

        if ($row['count'] == 0) {
            return [
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk menyimpan QR code ini'
            ];
        }
    }

    $qrCodeUrl = $result['qr_code_url'];

    $stmt = mysqli_prepare($koneksi, "UPDATE meat_distribution SET kode_qr = ? WHERE distribution_id = ?");

    if (!$stmt) {
        return [
            'status' => 'error',
            'message' => 'Gagal mempersiapkan query: ' . mysqli_error($koneksi)
        ];
    }

    $success = mysqli_stmt_execute($stmt);

    if (!$success) {
        return [
            'status' => 'error',
            'message' => 'Gagal menyimpan kode QR: ' . mysqli_error($koneksi)
        ];
    }

    return [
        'status' => 'success',
        'message' => 'Kode QR berhasil disimpan',
        'qr_code_url' => $qrCodeUrl
    ];
}

function getUserQRCode($user_id)
{
    global $koneksi;

    $session_name = $_SESSION['nama_lengkap'] ?? '';
    $session_role = $_SESSION['role'] ?? '';

    if ($user_id <= 0 && !empty($session_name)) {
        $userSql = "SELECT warga_id, nama_lengkap FROM warga WHERE nama_lengkap = ?";
        $userStmt = mysqli_prepare($koneksi, $userSql);

        if ($userStmt) {
            mysqli_stmt_bind_param($userStmt, "s", $session_name);
            mysqli_stmt_execute($userStmt);
            $userResult = mysqli_stmt_get_result($userStmt);

            if ($userRow = mysqli_fetch_assoc($userResult)) {
                $user_id = $userRow['warga_id'];
                $user_name = $userRow['nama_lengkap'];
                $user_role = $session_role;
            } else {
                $user_name = $session_name;
                $user_role = $session_role;
            }
        } else {
            $user_name = $session_name;
            $user_role = $session_role;
        }
    } else if ($user_id > 0) {
        $userSql = "SELECT warga_id, nama_lengkap FROM warga WHERE warga_id = ?";
        $userStmt = mysqli_prepare($koneksi, $userSql);

        if ($userStmt) {
            mysqli_stmt_bind_param($userStmt, "i", $user_id);
            mysqli_stmt_execute($userStmt);
            $userResult = mysqli_stmt_get_result($userStmt);

            if ($userRow = mysqli_fetch_assoc($userResult)) {
                $user_name = $userRow['nama_lengkap'];
                $user_role = $session_role;
            } else {
                $userSql2 = "SELECT user_id, nama_lengkap, role FROM users WHERE user_id = ?";
                $userStmt2 = mysqli_prepare($koneksi, $userSql2);

                if ($userStmt2) {
                    mysqli_stmt_execute($userStmt2);
                    $userResult2 = mysqli_stmt_get_result($userStmt2);

                    if ($userRow2 = mysqli_fetch_assoc($userResult2)) {
                        $user_name = $userRow2['nama_lengkap'];
                        $user_role = $userRow2['role'];
                    } else {
                        $user_name = $session_name;
                        $user_role = $session_role;
                    }
                } else {
                    $user_name = $session_name;
                    $user_role = $session_role;
                }
            }
        } else {
            $user_name = $session_name;
            $user_role = $session_role;
        }
    } else {
        return [
            'status' => 'error',
            'message' => 'User ID tidak valid dan tidak ada data session yang cukup'
        ];
    }

    $distributionData = null;

    if ($user_id > 0) {
        $sql = "SELECT 
            md.distribution_id,
            md.status,
            w.nama_lengkap,
            w.nik,
            ma.kambing,
            ma.sapi,
            (ma.sapi + ma.kambing) AS total_daging
        FROM meat_distribution md
        JOIN meat_allocation_peserta map ON md.peserta_id = map.peserta_id
        JOIN warga w ON map.warga_id = w.warga_id
        JOIN meat_allocation ma ON map.allocation_id = ma.allocation_id
        WHERE w.warga_id = ?
        LIMIT 1";

        $stmt = mysqli_prepare($koneksi, $sql);
        if ($stmt) {
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                $distributionData = $row;
            }
        }
    }

    if (!$distributionData && !empty($user_name)) {
        $sql2 = "SELECT 
            md.distribution_id,
            md.status,
            w.nama_lengkap,
            w.nik,
            ma.kambing,
            ma.sapi,
            (ma.sapi + ma.kambing) AS total_daging
        FROM meat_distribution md
        JOIN meat_allocation_peserta map ON md.peserta_id = map.peserta_id
        JOIN warga w ON map.warga_id = w.warga_id
        JOIN meat_allocation ma ON map.allocation_id = ma.allocation_id
        WHERE w.nama_lengkap LIKE ?
        LIMIT 1";

        $stmt2 = mysqli_prepare($koneksi, $sql2);
        if ($stmt2) {
            $searchName = "%" . $user_name . "%";
            mysqli_stmt_bind_param($stmt2, "s", $searchName);
            mysqli_stmt_execute($stmt2);
            $result2 = mysqli_stmt_get_result($stmt2);

            if ($row2 = mysqli_fetch_assoc($result2)) {
                $distributionData = $row2;
            }
        }
    }

    if (!$distributionData) {
        if ($user_role === 'panitia') {
            $distributionData = [
                'distribution_id' => 'PANITIA-' . ($user_id > 0 ? $user_id : rand(1000, 9999)),
                'status' => 'aktif',
                'nama_lengkap' => $user_name,
                'nik' => 'PANITIA-NIK-' . ($user_id > 0 ? $user_id : rand(1000, 9999)),
                'kambing' => 0,
                'sapi' => 0,
                'total_daging' => 0
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Data distribusi tidak ditemukan untuk ' . $user_name . '. Anda mungkin belum terdaftar dalam sistem distribusi daging qurban.'
            ];
        }
    }

    $qrData = [
        'distribution_id' => $distributionData['distribution_id'],
        'nik' => $distributionData['nik'],
        'nama_lengkap' => $distributionData['nama_lengkap'],
        'kambing' => (float)$distributionData['kambing'],
        'sapi' => (float)$distributionData['sapi'],
        'total_daging' => (float)$distributionData['total_daging'],
        'status' => $distributionData['status'],
        'user_role' => $user_role,
        'warga_id_used' => $user_id,
        'timestamp' => date('Y-m-d H:i:s')
    ];

    $qrText = json_encode($qrData);

    $qrCodeUrl = "https://quickchart.io/qr?text=" . urlencode($qrText);

    return [
        'status' => 'success',
        'data' => $distributionData,
        'qr_data' => $qrData,
        'qr_code_url' => $qrCodeUrl
    ];
}
