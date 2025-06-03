<?php
require_once('C:\laragon\www\UASWEBCOK\config\database.php');
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $transactionType = $_POST['transaction_type'];
//     $jumlah = $_POST['amount'];
//     $description = $_POST['description'];
//     $kategori = $_POST['category'];
// }

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

function addFinancialTransaction($transactionType, $jumlah, $description, $category, $transactionDate)
{
    global $koneksi;
    $sql = "INSERT INTO financial_transactions (transaction_type, amount, description, category, transaction_date) VALUES ($transactionType, $jumlah, $description, $category, '$transactionDate')";
    $result = mysqli_query($koneksi, $sql);
    if ($result) {
        return [
            'status' => 'success',
            'message' => 'Transaksi berhasil ditambahkan'
        ];
    } else {
        return [
            'status' => 'error',
            'message' => 'Gagal menambahkan transaksi: ' . mysqli_error($koneksi)
        ];
    }
}

function deleteFinancialTransaction($transactionId)
{
    global $koneksi;
    $sql = "DELETE FROM financial_transactions WHERE transaction_id = '$transactionId'";
    $result = mysqli_query($koneksi, $sql);
    if ($result) {
        return [
            'status' => 'success',
            'message' => 'Transaksi berhasil dihapus'
        ];
    } else {
        return [
            'status' => 'error',
            'message' => 'Gagal menghapus transaksi: ' . mysqli_error($koneksi)
        ];
    }
}

function updateFinancialTransaction($transactionId, $transactionType, $amount, $category, $transactionDate, $description)
{
    global $koneksi;
    $sql = "UPDATE financial_transactions 
                SET transaction_type = '$transactionType',
                    amount = '$amount',
                    description = '$description',
                    category = '$category',
                    transaction_date = '$transactionDate'
                WHERE transaction_id = '$transactionId'";

    $result = mysqli_query($koneksi, $sql);
    if ($result) {
        return [
            'status' => 'success',
            'message' => 'Transaksi berhasil diperbarui'
        ];
    } else {
        return [
            'status' => 'error',
            'message' => 'Gagal memperbarui transaksi: ' . mysqli_error($koneksi)
        ];
    }
}
