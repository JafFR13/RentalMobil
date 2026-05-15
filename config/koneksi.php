<?php 
$host     = "localhost"; 
$user = "root"; 
$password = ""; 
$database = "dbrecar";

// Simpan koneksi ke variabel $koneksi
$koneksi = mysqli_connect($host, $user, $password, $database);

// Cek koneksi
if (!$koneksi) {
    die("Harap Periksa Koneksi Database Anda! " . mysqli_connect_error());
}
?>