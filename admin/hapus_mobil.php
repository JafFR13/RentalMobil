<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$id = $_GET['id'];

// ambil data untuk hapus file foto
$q = mysqli_query($koneksi, "SELECT foto FROM tbl_mobil WHERE id_mobil='$id'");
$data = mysqli_fetch_assoc($q);
if ($data && $data['foto'] && file_exists("../uploads/".$data['foto'])) {
    unlink("../uploads/".$data['foto']);
}

mysqli_query($koneksi, "DELETE FROM tbl_mobil WHERE id_mobil='$id'");

header("Location: daftar_mobil.php");
exit;
