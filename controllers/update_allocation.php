<?php
require_once('C:\laragon\www\UASWEBCOK\config\database.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $wargaId = $_POST['warga_id'];

    // Collect allocation updates
    $updates = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'allocation_') === 0) {
            $role = substr($key, strlen('allocation_'));
            $amount = (float)$value;

            $updates[] = [
                'role' => $role,
                'amount' => $amount
            ];
        }
    }

    // Process updates
    if (!empty($updates)) {
        global $koneksi;

        try {
            // Start transaction
            mysqli_begin_transaction($koneksi);

            foreach ($updates as $update) {
                $role = mysqli_real_escape_string($koneksi, $update['role']);
                $amount = $update['amount'];

                // Get allocation_id for this warga and role
                $query = "SELECT map.allocation_id 
                    FROM meat_allocation_peserta map 
                    JOIN meat_allocation a ON map.allocation_id = a.allocation_id
                    WHERE map.warga_id = '$wargaId' AND a.role = '$role'";

                $result = mysqli_query($koneksi, $query);

                if ($row = mysqli_fetch_assoc($result)) {
                    $allocationId = $row['allocation_id'];

                    // Update the allocation amount
                    $updateQuery = "UPDATE meat_allocation 
                                   SET amount_per_person = $amount 
                                   WHERE allocation_id = '$allocationId'";

                    if (!mysqli_query($koneksi, $updateQuery)) {
                        throw new Exception("Error updating allocation: " . mysqli_error($koneksi));
                    }
                }
            }

            // Commit transaction
            mysqli_commit($koneksi);

            echo json_encode([
                'status' => 'success',
                'message' => 'Alokasi daging berhasil diperbarui'
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
            'message' => 'Tidak ada data alokasi yang diperbarui'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Metode tidak didukung'
    ]);
}
