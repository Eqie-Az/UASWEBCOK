<?php
$host = 'localhost';
$dbname = 'qurban_mvc';
$username = 'Root';
$password = '';
$koneksi = mysqli_connect($host, $username, $password, $dbname);
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}