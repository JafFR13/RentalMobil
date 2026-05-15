<?php
session_start();
include "../config/koneksi.php";

// cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'pengguna') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: daftar_sewa.php");
    exit;
}

$isLoggedIn = isset($_SESSION['login']);
$username = $isLoggedIn ? $_SESSION['username'] : 'User';
$role = $isLoggedIn ? $_SESSION['role'] : null;

// Ambil nama lengkap jika login
$namaLengkap = $username;
if ($isLoggedIn) {
    $id_user = $_SESSION['id'];
    $qUser = mysqli_query($koneksi, "SELECT nama_lengkap FROM users WHERE id='$id_user' LIMIT 1");
    $user = mysqli_fetch_assoc($qUser);
    if ($user) {
        $namaLengkap = $user['nama_lengkap'];
    }
}

// Format tanggal Indonesia
setlocale(LC_TIME, 'id_ID.UTF-8', 'Indonesian', 'id_ID');
$tanggalHariIni = strftime("%A, %d %B %Y");
;

$id_sewa = mysqli_real_escape_string($koneksi, $_GET['id']);

// ambil detail sewa
$query = mysqli_query($koneksi, "
    SELECT s.*, m.nama_mobil, m.foto, m.jenis, m.kursi 
    FROM tbl_sewa s
    JOIN tbl_mobil m ON s.id_mobil = m.id_mobil
    WHERE s.id_sewa = '$id_sewa'
    LIMIT 1
");

$sewa = mysqli_fetch_assoc($query);

if (!$sewa) {
    echo "<div class='alert alert-danger'>Data sewa tidak ditemukan.</div>";
    exit;
}

// hitung total bayar
$total_bayar = $sewa['total_harga'] + $sewa['denda'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Sewa <?= $sewa['id_sewa']; ?></title>
<link rel="stylesheet" href="../css/bootstrap.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    html, body {
            height: 100%;
            
        }
    body {
      background-color: #f8f9fa;
      font-family: 'Poppins', sans-serif;
      display: flex;
      flex-direction: column;
    }
  
    .navbar {
      font-weight: 500;
      letter-spacing: 0.5px;
    }
    .navbar, .offcanvas-header {
      background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    }
    .navbar-brand, .nav-link {
      color: white !important;
    }

    main {
            flex: 1; /* isi konten dorong footer ke bawah */
        }
    .card {
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .card img {
      border-radius: 12px;
    }
  </style>
</head>
<body>
<div class="header">
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-car-front-fill"></i> Rent A Car</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link active" href="../index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Features</a></li>
          <li class="nav-item"><a class="nav-link" href="daftar_mobil.php">Mobil</a></li>
        </ul>

        <ul class="navbar-nav ms-auto align-items-center">
          <li class="nav-item me-3 text-white">
            <i class="bi bi-calendar-date"></i>
            <span><?= ucfirst($tanggalHariIni); ?></span>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#" data-bs-toggle="offcanvas" data-bs-target="#sidebarUser">
              <i class="bi bi-person-circle"></i>
              <?= htmlspecialchars($namaLengkap); ?>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</div>

<!-- Sidebar User -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="sidebarUser">
  <div class="offcanvas-header bg-primary text-light">
    <h3 class="offcanvas-title">
      <?php if($isLoggedIn): ?>
        Halo, <?= htmlspecialchars($namaLengkap); ?>
      <?php else: ?>
        Selamat Datang
      <?php endif; ?>
    </h3>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body bg-light">
    <?php if(!$isLoggedIn): ?>
      <a href="../auth/login.php" class="btn btn-primary w-100 mb-2">Login</a>
      <a href="../auth/register.php" class="btn btn-outline-secondary w-100">Register</a>
    <?php else: ?>
      <?php if($role === 'pengguna'): ?>
        <a href="profil.php" class="btn btn-outline-primary w-100 mb-2">Profil</a>
        <a href="daftar_sewa.php" class="btn btn-outline-primary w-100 mb-2">Daftar Sewa</a>
      <?php endif; ?>
      <a href="../auth/logout.php" class="btn btn-danger w-100">Logout</a>
    <?php endif; ?>
  </div>
</div>



<main class="flex-shrink-0">
<div class="container py-5">
  <a href="daftar_sewa.php" class="btn btn-secondary mb-3">&larr; Kembali</a>
  
  <div class="card p-4">
    <div class="row">
      <div class="col-md-5">
        <img src="../uploads/<?= $sewa['foto']; ?>" class="img-fluid" alt="<?= $sewa['nama_mobil']; ?>">
      </div>
      <div class="col-md-7">
        <h3 class="fw-bold mb-3"><?= $sewa['nama_mobil']; ?></h3>
        <ul class="list-group mb-3">
          <li class="list-group-item"><strong>ID Sewa:</strong> <?= $sewa['id_sewa']; ?></li>
          <li class="list-group-item"><strong>Nama Penyewa:</strong> <?= $sewa['nama_penyewa']; ?></li>
          <li class="list-group-item"><strong>Email:</strong> <?= $sewa['email']; ?></li>
          <li class="list-group-item"><strong>No. Telp:</strong> <?= $sewa['no_telp']; ?></li>
          <li class="list-group-item"><strong>Alamat Awal:</strong> <?= $sewa['alamat_awal']; ?></li>
          <li class="list-group-item"><strong>Alamat Tujuan:</strong> <?= $sewa['alamat_tujuan']; ?></li>
          <li class="list-group-item"><strong>Tanggal Mulai:</strong> <?= date("d-m-Y", strtotime($sewa['tanggal_mulai'])); ?></li>
          <li class="list-group-item"><strong>Tanggal Selesai:</strong> <?= date("d-m-Y", strtotime($sewa['tanggal_selesai'])); ?></li>
          <li class="list-group-item"><strong>Status:</strong>
            <?php if($sewa['status'] == "Disewa"): ?>
              <span class="badge bg-warning text-dark">Sedang Disewa</span>
            <?php else: ?>
              <span class="badge bg-success">Selesai</span>
            <?php endif; ?>
          </li>
        </ul>
        
        <div class="alert alert-info">
          <strong>Total Harga:</strong> Rp <?= number_format($sewa['total_harga'],0,',','.'); ?><br>
          <strong>Denda:</strong> Rp <?= number_format($sewa['denda'],0,',','.'); ?><br>
          <hr>
          <strong>Total Bayar:</strong> Rp <?= number_format($total_bayar,0,',','.'); ?>
        </div>
      </div>
    </div>
<a href="invoice.php?id=<?= $sewa['id_sewa']; ?>" target="_blank" class="btn btn-primary mb-3">
  🧾 Cetak Invoice
</a>

<?php if ($sewa['status'] === 'Pending' && $sewa['metode_bayar'] === 'Transfer Bank'): ?>
  <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalBayar">
    💳 Konfirmasi Pembayaran
  </button>
<?php elseif ($sewa['status'] === 'Disewa'): ?>
  <div class="alert alert-success mt-3">
    <i class="bi bi-check-circle-fill"></i> Pembayaran sudah dikonfirmasi. Mobil sedang disewa.
  </div>
<?php elseif ($sewa['metode_bayar'] === 'Tunai' && $sewa['status'] === 'Pending'): ?>
  <div class="alert alert-warning mt-3">
    <i class="bi bi-cash-stack"></i> Pembayaran tunai akan dikonfirmasi oleh admin saat pengambilan mobil.
  </div>
<?php endif; ?>

  </div>



</div>
</main>

<!-- Modal Konfirmasi Pembayaran -->
<div class="modal fade" id="modalBayar" tabindex="-1" aria-labelledby="modalBayarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="konfirmasi_bayar.php" method="POST" enctype="multipart/form-data">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="modalBayarLabel"><i class="bi bi-credit-card-2-front"></i> Konfirmasi Pembayaran</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id_sewa" value="<?= $sewa['id_sewa']; ?>">

          <p>Silakan upload bukti transfer ke rekening resmi kami.</p>

          <div class="mb-3">
            <label class="form-label">Upload Bukti Transfer (JPG/PNG/PDF)</label>
            <input type="file" name="bukti_transfer" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Metode Pembayaran</label>
            <select name="metode" class="form-select" required>
              <option value="Transfer Bank" selected>Transfer Bank</option>
              <option value="Tunai">Tunai</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Jumlah Bayar (Rp)</label>
            <input type="number" name="jumlah_bayar" class="form-control"
                  value="<?= $sewa['total_harga']; ?>" readonly required>
          </div>

          <div class="alert alert-info">
            <strong>Rekening Bank Kami:</strong><br>
            Bank BCA – 1234567890 a/n PT Rent A Car
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="konfirmasi" class="btn btn-success">Kirim Konfirmasi</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Footer -->
<footer class="bg-dark text-white text-center py-3">
  <small>&copy; <?= date("Y"); ?> Rent A Car. All Rights Reserved.</small>
</footer>
<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
