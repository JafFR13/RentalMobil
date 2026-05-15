<?php
include "includes/header.php";
include "includes/sidebar.php";

// Hitung data
$totalMobil = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM tbl_mobil"))['jml'];
$mobilTersedia = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM tbl_mobil WHERE status='Tersedia'"))['jml'];
$mobilDisewa = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM tbl_mobil WHERE status='Disewa'"))['jml'];
$totalSewa = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM tbl_sewa"))['jml'];
?>

<h3 class="fw-bold mb-4">Dashboard</h3>
<p class="text-muted">Gunakan menu di sebelah kiri untuk mengelola sistem rental mobil.</p>

<div class="row g-4">
  <div class="col-md-3">
    <div class="card dashboard-card bg-primary text-white">
      <div class="card-body">
        <h5 class="card-title">Total Mobil</h5>
        <h2><?= $totalMobil; ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card dashboard-card bg-success text-white">
      <div class="card-body">
        <h5 class="card-title">Mobil Tersedia</h5>
        <h2><?= $mobilTersedia; ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card dashboard-card bg-warning text-dark">
      <div class="card-body">
        <h5 class="card-title">Sedang Disewa</h5>
        <h2><?= $mobilDisewa; ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card dashboard-card bg-dark text-white">
      <div class="card-body">
        <h5 class="card-title">Total Transaksi Sewa</h5>
        <h2><?= $totalSewa; ?></h2>
      </div>
    </div>
  </div>
</div>

<?php include "includes/footer.php"; ?>
