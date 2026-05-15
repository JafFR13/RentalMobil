<?php
session_start();
include "../config/koneksi.php"; 
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil nama admin
$id_user = $_SESSION['id'];
$query = mysqli_query($koneksi, "SELECT nama_lengkap FROM users WHERE id='$id_user' LIMIT 1");
$user = mysqli_fetch_assoc($query);
$namaLengkap = $user ? $user['nama_lengkap'] : $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel - Rent A Car</title>
  <link href="../css/bootstrap.css" rel="stylesheet">
  <link href="../css/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/admin.css" rel="stylesheet">
  <!-- <script src="../css/sweetalert2/dist/sweetalert2.all.min.js"></script> -->
</head>
<body>
<div class="d-flex" id="wrapper">
    