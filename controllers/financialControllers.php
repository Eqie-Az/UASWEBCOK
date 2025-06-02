<?php
require_once('C:\laragon\www\UASWEBCOK\config\database.php');
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transactionType = $_POST['transaction_type'];
    $jumlah = $_POST['amount'];
    $description = $_POST['description'];
    $kategori = $_POST['category'];
}

function getFinancial()
{
    global $koneksi;
    $sql = "SELECT * FROM financial_transactions";
    $result = mysqli_query($koneksi, $sql);
    $transaksi = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $transaksi[] = $row;
    }
    return $transaksi;
}
