<?php

require_once '../config/database.php';
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

//function to get all of data from database table warga
function getAllWarga() {
    global $koneksi;
    $sql = "SELECT * FROM warga";
    $result = mysqli_query($koneksi, $sql);
    $wargaList = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $wargaList[] = $row;
        }
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
    
    return $wargaList;
}