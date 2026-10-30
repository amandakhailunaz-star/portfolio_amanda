<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "portfolio_amanda";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

mysqli_set_charset($koneksi, "utf8mb4");
?>
