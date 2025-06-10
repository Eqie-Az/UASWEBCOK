<?php
require_once('C:\laragon\www\UASWEBCOK\config\database.php');
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
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

function insertRole($warga_id, $role, $sapi_amount, $kambing_amount)
{
    global $koneksi;

    // Check if the warga exists
    $checkWargaQuery = "SELECT warga_id FROM warga WHERE warga_id = ?";
    $stmt = mysqli_prepare($koneksi, $checkWargaQuery);
    mysqli_stmt_bind_param($stmt, "i", $warga_id);
    mysqli_stmt_execute($stmt);
    $wargaResult = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($wargaResult) === 0) {
        return ['success' => false, 'message' => 'Warga tidak ditemukan'];
    }

    // Check if the role exists in meat_allocation table
    $checkRoleQuery = "SELECT allocation_id FROM meat_allocation WHERE role = ?";
    $stmt = mysqli_prepare($koneksi, $checkRoleQuery);
    mysqli_stmt_bind_param($stmt, "s", $role);
    mysqli_stmt_execute($stmt);
    $roleResult = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($roleResult) === 0) {
        // Role doesn't exist, create it
        $insertRoleQuery = "INSERT INTO meat_allocation (role, sapi, kambing) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $insertRoleQuery);
        mysqli_stmt_bind_param($stmt, "sdd", $role, $sapi_amount, $kambing_amount);

        if (!mysqli_stmt_execute($stmt)) {
            return ['success' => false, 'message' => 'Gagal menambahkan role: ' . mysqli_error($koneksi)];
        }

        $allocation_id = mysqli_insert_id($koneksi);
    } else {
        // Role exists, get allocation_id and update sapi/kambing amounts
        $roleRow = mysqli_fetch_assoc($roleResult);
        $allocation_id = $roleRow['allocation_id'];

        $updateRoleQuery = "UPDATE meat_allocation SET sapi = ?, kambing = ? WHERE allocation_id = ?";
        $stmt = mysqli_prepare($koneksi, $updateRoleQuery);
        mysqli_stmt_bind_param($stmt, "ddi", $sapi_amount, $kambing_amount, $allocation_id);

        if (!mysqli_stmt_execute($stmt)) {
            return ['success' => false, 'message' => 'Gagal mengupdate role: ' . mysqli_error($koneksi)];
        }
    }

    // Check if the warga already has this role
    $checkWargaRoleQuery = "SELECT * FROM meat_allocation_peserta 
                           WHERE warga_id = ? AND allocation_id = ?";
    $stmt = mysqli_prepare($koneksi, $checkWargaRoleQuery);
    mysqli_stmt_bind_param($stmt, "ii", $warga_id, $allocation_id);
    mysqli_stmt_execute($stmt);
    $wargaRoleResult = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($wargaRoleResult) > 0) {
        return ['success' => true, 'message' => 'Warga sudah memiliki role ini'];
    }

    // Assign the role to the warga
    $assignRoleQuery = "INSERT INTO meat_allocation_peserta (warga_id, allocation_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($koneksi, $assignRoleQuery);
    mysqli_stmt_bind_param($stmt, "ii", $warga_id, $allocation_id);

    if (mysqli_stmt_execute($stmt)) {
        return ['success' => true, 'message' => 'Role berhasil ditambahkan'];
    } else {
        return ['success' => false, 'message' => 'Gagal menambahkan role: ' . mysqli_error($koneksi)];
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
            a.sapi,
            a.kambing,
            (a.sapi + a.kambing) AS total_amount
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

    $sql = "SELECT 
    w.warga_id,
    w.nama_lengkap,
    w.nik,
    COUNT(DISTINCT ma.allocation_id) as jumlah_role,
    GROUP_CONCAT(DISTINCT ma.role ORDER BY ma.role SEPARATOR ', ') AS roles,
    SUM(ma.sapi) AS total_sapi,
    SUM(ma.kambing) AS total_kambing,
    SUM(ma.sapi + ma.kambing) AS total_allocation
FROM warga w
LEFT JOIN meat_allocation_peserta map ON w.warga_id = map.warga_id
LEFT JOIN meat_allocation ma ON map.allocation_id = ma.allocation_id
WHERE map.allocation_id IS NOT NULL  -- Only show people with allocations
GROUP BY w.warga_id, w.nama_lengkap, w.nik
ORDER BY w.nama_lengkap";

    $result = mysqli_query($koneksi, $sql);

    if (!$result) {
        error_log("GetWargaAllocations - SQL Error: " . mysqli_error($koneksi));
        return [];
    }

    $allocations = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $allocations[] = $row;
    }

    return $allocations;
}

/**
 * Remove a role from a warga
 * @param int $warga_id The ID of the warga
 * @param string $role The role to remove (warga, berqurban, panitia)
 * @return array Result of the operation
 */
function removeRole($warga_id, $role)
{
    global $koneksi;

    // Get the allocation_id for the role
    $getRoleIdQuery = "SELECT allocation_id FROM meat_allocation WHERE role = ?";
    $stmt = mysqli_prepare($koneksi, $getRoleIdQuery);
    mysqli_stmt_bind_param($stmt, "s", $role);
    mysqli_stmt_execute($stmt);
    $roleResult = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($roleResult) === 0) {
        return ['success' => false, 'message' => 'Role tidak ditemukan'];
    }

    $roleRow = mysqli_fetch_assoc($roleResult);
    $allocation_id = $roleRow['allocation_id'];

    // Remove the role from the warga
    $removeRoleQuery = "DELETE FROM meat_allocation_peserta 
                        WHERE warga_id = ? AND allocation_id = ?";
    $stmt = mysqli_prepare($koneksi, $removeRoleQuery);
    mysqli_stmt_bind_param($stmt, "ii", $warga_id, $allocation_id);

    if (mysqli_stmt_execute($stmt)) {
        return [
            'success' => true,
            'message' => 'Role berhasil dihapus',
            'affected_rows' => mysqli_stmt_affected_rows($stmt)
        ];
    } else {
        return ['success' => false, 'message' => 'Gagal menghapus role: ' . mysqli_error($koneksi)];
    }
}

function getAllRoles()
{
    global $koneksi;
    $sql = "SELECT * FROM meat_allocation";
    $result = mysqli_query($koneksi, $sql);
    $roles = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $roles[] = $row;
        }
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }

    return $roles;
}

/**
 * Add multiple selected roles to a warga after removing existing ones
 * @param int $warga_id The ID of the warga
 * @param array $roles Array of role names to add to the warga
 * @return array Result of the operation
 */
function addSelectedRolesToWarga($warga_id, $roles)
{
    global $koneksi;

    // Check if the warga exists
    $checkWargaQuery = "SELECT warga_id FROM warga WHERE warga_id = ?";
    $stmt = mysqli_prepare($koneksi, $checkWargaQuery);
    mysqli_stmt_bind_param($stmt, "i", $warga_id);
    mysqli_stmt_execute($stmt);
    $wargaResult = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($wargaResult) === 0) {
        return [
            'status' => 'error',
            'message' => 'Warga tidak ditemukan'
        ];
    }

    // Begin transaction
    mysqli_begin_transaction($koneksi);

    try {
        // First get the peserta_ids associated with this warga
        $getPesertaIds = "SELECT peserta_id FROM meat_allocation_peserta WHERE warga_id = ?";
        $stmt = mysqli_prepare($koneksi, $getPesertaIds);
        mysqli_stmt_bind_param($stmt, "i", $warga_id);
        mysqli_stmt_execute($stmt);
        $pesertaResult = mysqli_stmt_get_result($stmt);

        $peserta_ids = [];
        while ($row = mysqli_fetch_assoc($pesertaResult)) {
            $peserta_ids[] = $row['peserta_id'];
        }

        // Delete from meat_distribution where peserta_id is in the list
        if (!empty($peserta_ids)) {
            $placeholders = implode(',', array_fill(0, count($peserta_ids), '?'));
            $deleteDistQuery = "DELETE FROM meat_distribution WHERE peserta_id IN ($placeholders)";
            $stmt = mysqli_prepare($koneksi, $deleteDistQuery);

            $types = str_repeat('i', count($peserta_ids));
            $stmt->bind_param($types, ...$peserta_ids);

            mysqli_stmt_execute($stmt);
        }

        // Now delete all existing roles for this warga
        $deleteQuery = "DELETE FROM meat_allocation_peserta WHERE warga_id = ?";
        $stmt = mysqli_prepare($koneksi, $deleteQuery);
        mysqli_stmt_bind_param($stmt, "i", $warga_id);
        mysqli_stmt_execute($stmt);

        // Initialize totals
        $totalSapi = 0;
        $totalKambing = 0;
        $successCount = 0;
        $roleDetails = [];

        // Add each selected role
        foreach ($roles as $role) {
            // Get role details
            $roleQuery = "SELECT * FROM meat_allocation WHERE role = ?";
            $stmt = mysqli_prepare($koneksi, $roleQuery);
            mysqli_stmt_bind_param($stmt, "s", $role);
            mysqli_stmt_execute($stmt);
            $roleResult = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($roleResult) > 0) {
                $roleData = mysqli_fetch_assoc($roleResult);
                $allocation_id = $roleData['allocation_id'];
                $sapi = $roleData['sapi'];
                $kambing = $roleData['kambing'];
                $total = $sapi + $kambing;

                // Insert role to warga
                $insertQuery = "INSERT INTO meat_allocation_peserta (warga_id, allocation_id) VALUES (?, ?)";
                $stmt = mysqli_prepare($koneksi, $insertQuery);
                mysqli_stmt_bind_param($stmt, "ii", $warga_id, $allocation_id);

                if (mysqli_stmt_execute($stmt)) {
                    $successCount++;
                    $totalSapi += $sapi;
                    $totalKambing += $kambing;
                    $roleDetails[] = [
                        'role' => $role,
                        'sapi' => $sapi,
                        'kambing' => $kambing,
                        'total' => $total
                    ];
                }
            }
        }

        // Commit transaction
        mysqli_commit($koneksi);

        return [
            'status' => 'success',
            'message' => 'Roles berhasil ditambahkan',
            'total_sapi' => $totalSapi,
            'total_kambing' => $totalKambing,
            'roles' => $roleDetails,
            'success_count' => $successCount
        ];
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($koneksi);
        return [
            'status' => 'error',
            'message' => 'Gagal menambahkan roles: ' . $e->getMessage()
        ];
    }
}

/**
 * Get all roles for a specific warga
 * 
 * @param int $warga_id The ID of the warga
 * @return array List of roles for the warga
 */
function getWargaRoles($warga_id)
{
    global $koneksi;

    $roles = [];
    $query = mysqli_query($koneksi, "SELECT map.*, ma.role, ma.sapi, ma.kambing 
                                    FROM meat_allocation_peserta map
                                    JOIN meat_allocation ma ON map.allocation_id = ma.allocation_id
                                    WHERE map.warga_id = '$warga_id'");

    while ($row = mysqli_fetch_assoc($query)) {
        $roles[] = [
            'allocation_id' => $row['allocation_id'],
            'role' => $row['role'],
            'sapi' => $row['sapi'],
            'kambing' => $row['kambing']
            // Removed reference to amount_per_person that doesn't exist
        ];
    }

    return $roles;
}

/**
 * Remove a role from a specific warga
 * 
 * @param int $warga_id The ID of the warga
 * @param int $allocation_id The allocation ID to remove
 * @return array Response with status and message
 */
function removeRoleFromWarga($warga_id, $allocation_id)
{
    global $koneksi;

    // Validate inputs
    if (empty($warga_id) || empty($allocation_id)) {
        return [
            'status' => 'error',
            'message' => 'Warga ID dan allocation ID harus diisi'
        ];
    }

    // Get allocation details before deletion
    $getAllocation = mysqli_query($koneksi, "SELECT map.*, ma.sapi, ma.kambing, ma.role 
                                            FROM meat_allocation_peserta map
                                            JOIN meat_allocation ma ON map.allocation_id = ma.allocation_id
                                            WHERE map.warga_id = '$warga_id' 
                                            AND map.allocation_id = '$allocation_id'");

    if (mysqli_num_rows($getAllocation) == 0) {
        return [
            'status' => 'error',
            'message' => 'Alokasi tidak ditemukan'
        ];
    }

    $allocation = mysqli_fetch_assoc($getAllocation);
    $peserta_id = $allocation['peserta_id'];
    $roleName = $allocation['role'];

    // Begin transaction
    mysqli_begin_transaction($koneksi);

    try {
        // First delete from meat_distribution if exists
        $deleteDistQuery = "DELETE FROM meat_distribution WHERE peserta_id = ?";
        $stmt = mysqli_prepare($koneksi, $deleteDistQuery);
        mysqli_stmt_bind_param($stmt, "i", $peserta_id);
        mysqli_stmt_execute($stmt);

        // Then remove role from warga
        $deleteRoleQuery = "DELETE FROM meat_allocation_peserta WHERE peserta_id = ?";
        $stmt = mysqli_prepare($koneksi, $deleteRoleQuery);
        mysqli_stmt_bind_param($stmt, "i", $peserta_id);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_error($koneksi));
        }

        // Commit transaction
        mysqli_commit($koneksi);

        return [
            'status' => 'success',
            'message' => "Role $roleName berhasil dihapus"
        ];
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($koneksi);
        return [
            'status' => 'error',
            'message' => 'Gagal menghapus role: ' . $e->getMessage()
        ];
    }
}

/**
 * Get information about a specific warga by ID
 * @param int $warga_id The ID of the warga
 * @return array|null The warga data or null if not found
 */
function getWargaById($warga_id)
{
    global $koneksi;

    $query = "SELECT * FROM warga WHERE warga_id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $warga_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    } else {
        return null;
    }
}

/**
 * Remove a role from a warga
 * @param int $warga_id The ID of the warga
 * @param int $allocation_id The allocation ID to remove
 * @return array Response with status and message
 */
function removeWargaRole($warga_id, $allocation_id)
{
    return removeRoleFromWarga($warga_id, $allocation_id);
}

/**
 * Handle dashboard data and form processing
 * @return array Data required for the dashboard
 */
function processDashboardData()
{
    $data = [
        'message' => '',
        'messageType' => '',
        'editingWarga' => null,
        'currentRoles' => [],
        'wargaAllocations' => getWargaAllocations(),
        'dataTabel' => getdataUser()
    ];

    // Handle edit warga roles button click
    if (isset($_POST['edit_warga_roles'])) {
        $wargaId = $_POST['warga_id'];
        $data['editingWarga'] = getWargaById($wargaId);
        $data['currentRoles'] = getWargaRoles($wargaId);
    }

    // Handle role removal
    if (isset($_POST['remove_role'])) {
        $wargaId = $_POST['warga_id'];
        $allocationId = $_POST['allocation_id'];

        $result = removeWargaRole($wargaId, $allocationId);

        if ($result['status'] === 'success') {
            $data['message'] = $result['message'];
            $data['messageType'] = 'success';
        } else {
            $data['message'] = $result['message'];
            $data['messageType'] = 'error';
        }

        $data['editingWarga'] = getWargaById($wargaId);
        $data['currentRoles'] = getWargaRoles($wargaId);
    }

    // Handle adding selected roles
    if (isset($_POST['add_selected_roles'])) {
        $wargaId = $_POST['warga_id'];

        if (isset($_POST['roles']) && is_array($_POST['roles']) && !empty($_POST['roles'])) {
            $roles = $_POST['roles'];
            $result = addSelectedRolesToWarga($wargaId, $roles);

            if ($result['status'] === 'success') {
                $data['message'] = $result['message'] . "<br>Sapi: " . number_format((float)$result['total_sapi'], 1) .
                    "kg, Kambing: " . number_format((float)$result['total_kambing'], 1) . "kg";
                $data['messageType'] = 'success';
            } else {
                $data['message'] = $result['message'];
                $data['messageType'] = 'error';
            }
        } else {
            $data['message'] = "Pilih minimal satu role terlebih dahulu";
            $data['messageType'] = 'error';
        }

        $data['editingWarga'] = getWargaById($wargaId);
        $data['currentRoles'] = getWargaRoles($wargaId);
    }

    // Handle closing modal
    if (isset($_POST['close_modal'])) {
        // Redirect handled in view
    }

    return $data;
}
