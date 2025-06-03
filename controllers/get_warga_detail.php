<?php
require_once('C:\laragon\www\UASWEBCOK\config\database.php');
require_once('C:\laragon\www\UASWEBCOK\controllers\userControllers.php');

header('Content-Type: application/json');

if (isset($_GET['warga_id'])) {
    $wargaId = $_GET['warga_id'];
    $details = showDetailModal($wargaId);

    if (!empty($details)) {
        // Get the first row for warga info
        $nama = $details[0]['nama_lengkap'];
        $nik = $details[0]['nik'];

        $total = 0;
        $roleDetails = [];

        foreach ($details as $detail) {
            $roleDetails[] = [
                'role' => $detail['role'],
                'amount' => (float)$detail['amount_per_person']
            ];
            $total += (float)$detail['amount_per_person'];
        }

        echo json_encode([
            'status' => 'success',
            'data' => [
                'nama' => $nama,
                'nik' => $nik,
                'details' => $roleDetails,
                'total' => $total
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Tidak ada data ditemukan untuk warga ini'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'ID warga tidak diberikan'
    ]);
}
