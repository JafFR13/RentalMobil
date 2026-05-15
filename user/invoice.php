<?php
session_start();
include "../config/koneksi.php";

// cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'pengguna') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("ID sewa tidak ditemukan.");
}

$id_sewa = mysqli_real_escape_string($koneksi, $_GET['id']);

// ambil data sewa
$query = mysqli_query($koneksi, "
    SELECT s.*, m.nama_mobil, m.jenis, m.kursi 
    FROM tbl_sewa s
    JOIN tbl_mobil m ON s.id_mobil = m.id_mobil
    WHERE s.id_sewa = '$id_sewa'
    LIMIT 1
");

$sewa = mysqli_fetch_assoc($query);

if (!$sewa) {
    die("Data sewa tidak ditemukan.");
}

$total_bayar = $sewa['total_harga'] + $sewa['denda'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Invoice <?= $sewa['id_sewa']; ?></title>
  <link rel="stylesheet" href="../css/bootstrap.css">
  <style>
    body { background: #fff; font-size: 14px; }
    .invoice-box {
      max-width: 800px;
      margin: auto;
      padding: 30px;
      border: 1px solid #eee;
      box-shadow: 0 0 10px rgba(0,0,0,.15);
    }
    .invoice-box table {
      width: 100%;
      line-height: inherit;
      text-align: left;
    }
    .invoice-box table td {
      padding: 5px;
      vertical-align: top;
    }
    .invoice-header {
      text-align: center;
      margin-bottom: 20px;
    }
    .invoice-header h2 {
      margin-bottom: 0;
    }
    @media print {
      .no-print { display: none; }
    }
  </style>
</head>
<body>

<div class="invoice-box">
  <div class="invoice-header">
    <h2>INVOICE PENYEWAAN MOBIL</h2>
    <p>Rental Mobil Sakpore</p>
    <hr>
  </div>

  <table>
    <tr>
      <td><strong>ID Sewa:</strong> <?= $sewa['id_sewa']; ?></td>
      <td><strong>Tanggal Cetak:</strong> <?= date("d-m-Y"); ?></td>
    </tr>
    <tr>
      <td><strong>Nama Penyewa:</strong> <?= $sewa['nama_penyewa']; ?></td>
      <td><strong>Email:</strong> <?= $sewa['email']; ?></td>
    </tr>
    <tr>
      <td><strong>No. Telp:</strong> <?= $sewa['no_telp']; ?></td>
      <td><strong>Status:</strong> <?= $sewa['status']; ?></td>
    </tr>
  </table>

  <hr>

  <h5>Detail Mobil</h5>
  <table class="table table-bordered">
    <tr>
      <th>Nama Mobil</th>
      <th>Jenis</th>
      <th>Kursi</th>
      <th>Tanggal Mulai</th>
      <th>Tanggal Selesai</th>
    </tr>
    <tr>
      <td><?= $sewa['nama_mobil']; ?></td>
      <td><?= $sewa['jenis']; ?></td>
      <td><?= $sewa['kursi']; ?></td>
      <td><?= date("d-m-Y", strtotime($sewa['tanggal_mulai'])); ?></td>
      <td><?= date("d-m-Y", strtotime($sewa['tanggal_selesai'])); ?></td>
    </tr>
  </table>

  <h5>Alamat</h5>
  <table class="table table-bordered">
    <tr>
      <td><strong>Alamat Awal:</strong><br><?= $sewa['alamat_awal']; ?></td>
      <td><strong>Alamat Tujuan:</strong><br><?= $sewa['alamat_tujuan']; ?></td>
    </tr>
  </table>

  <h5>Rincian Biaya</h5>
  <table class="table table-bordered">
    <tr>
      <th>Total Harga</th>
      <th>Denda</th>
      <th>Total Bayar</th>
    </tr>
    <tr>
      <td>Rp <?= number_format($sewa['total_harga'],0,',','.'); ?></td>
      <td>Rp <?= number_format($sewa['denda'],0,',','.'); ?></td>
      <td><strong>Rp <?= number_format($total_bayar,0,',','.'); ?></strong></td>
    </tr>
  </table>

  <p class="text-center mt-4">Terima kasih telah menggunakan layanan kami 🙏</p>

  <div class="text-center no-print">
    <button class="btn btn-primary" onclick="window.print()">🖨 Cetak</button>
    <a href="daftar_sewa.php" class="btn btn-secondary">Kembali</a>
  </div>
</div>

</body>
</html>
