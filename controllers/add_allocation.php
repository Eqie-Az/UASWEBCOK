<?php
require_once('C:\laragon\www\UASWEBCOK\config\database.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $wargaId = $_POST['warga_id'];
    $role = $_POST['role'];
    $amount = (float)$_POST['amount'];

    if (empty($wargaId) || empty($role) || $amount <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Data tidak lengkap atau tidak valid'
        ]);
        exit;
    }

    global $koneksi;

    try {
        // Start transaction
        mysqli_begin_transaction($koneksi);

        // Check if allocation already exists
        $checkQuery = "SELECT a.allocation_id 
                      FROM meat_allocation a
                      JOIN meat_allocation_peserta map ON a.allocation_id = map.allocation_id
                      WHERE map.warga_id = '$wargaId' AND a.role = '$role'";

        $checkResult = mysqli_query($koneksi, $checkQuery);

        if (mysqli_num_rows($checkResult) > 0) {
            // Allocation exists, update it
            $row = mysqli_fetch_assoc($checkResult);
            $allocationId = $row['allocation_id'];

            $updateQuery = "UPDATE meat_allocation 
                          SET amount_per_person = $amount 
                          WHERE allocation_id = '$allocationId'";

            if (!mysqli_query($koneksi, $updateQuery)) {
                throw new Exception("Error updating allocation: " . mysqli_error($koneksi));
            }
        } else {
            // Create new allocation
            // First create allocation entry
            $insertAllocationQuery = "INSERT INTO meat_allocation (role, amount_per_person) 
                                    VALUES ('$role', $amount)";

            if (!mysqli_query($koneksi, $insertAllocationQuery)) {
                throw new Exception("Error creating allocation: " . mysqli_error($koneksi));
            }

            $allocationId = mysqli_insert_id($koneksi);

            // Then link to warga
            $insertLinkQuery = "INSERT INTO meat_allocation_peserta (warga_id, allocation_id, amount) 
                              VALUES ('$wargaId', '$allocationId', 1)";

            if (!mysqli_query($koneksi, $insertLinkQuery)) {
                throw new Exception("Error linking allocation to warga: " . mysqli_error($koneksi));
            }
        }

        // Commit transaction
        mysqli_commit($koneksi);

        echo json_encode([
            'status' => 'success',
            'message' => 'Alokasi daging berhasil disimpan'
        ]);
    } catch (Exception $e) {
        mysqli_rollback($koneksi);

        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Metode tidak didukung'
    ]);
}
