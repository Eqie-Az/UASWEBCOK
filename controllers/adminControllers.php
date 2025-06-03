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

function getUserCountByRole($role)
{
    global $koneksi;
    $role = mysqli_real_escape_string($koneksi, $role);

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

function getFinancialSummary()
{
    global $koneksi;

    $sqlIncome = "SELECT SUM(amount) AS total FROM financial_transactions WHERE transaction_type = 'pemasukan'";
    $incomeResult = mysqli_query($koneksi, $sqlIncome);
    $income = 0;
    if ($incomeResult) {
        $row = mysqli_fetch_assoc($incomeResult);
        $income = $row['total'] ?: 0;
    }

    $sqlExpense = "SELECT SUM(amount) AS total FROM financial_transactions WHERE transaction_type = 'pengeluaran'";
    $expenseResult = mysqli_query($koneksi, $sqlExpense);
    $expenses = 0;
    if ($expenseResult) {
        $row = mysqli_fetch_assoc($expenseResult);
        $expenses = $row['total'] ?: 0;
    }

    $balance = $income - $expenses;

    return [
        'pemasukan' => $income,
        'pengeluaran' => $expenses,
        'saldo' => $balance
    ];
}


function getMeatDistribution()
{
    global $koneksi;

    $sqlTotal = "SELECT SUM(total_daging) AS total FROM meat_distribution";
    $totalResult = mysqli_query($koneksi, $sqlTotal);
    $total = 0;
    if ($totalResult && $row = mysqli_fetch_assoc($totalResult)) {
        $total = $row['total'] ?: 0;
    }

    $sqlBeef = "SELECT SUM(daging_sapi) AS total FROM meat_distribution";
    $beefResult = mysqli_query($koneksi, $sqlBeef);
    $beef = 0;
    if ($beefResult && $row = mysqli_fetch_assoc($beefResult)) {
        $beef = $row['total'] ?: 0;
    }

    $sqlGoat = "SELECT SUM(daging_kambing) AS total FROM meat_distribution";
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

function getDashboardData()
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
        'financial_summary' => getFinancialSummary(),
        'meat_distribution' => getMeatDistribution()
    ];
    return $data;
}
