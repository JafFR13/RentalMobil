<?php
session_start();
include "../config/koneksi.php";

// ✅ Cegah akses tanpa login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    echo "<script>alert('Silakan login terlebih dahulu untuk menyewa mobil!'); window.location='../auth/login.php';</script>";
    exit;
}

// Fungsi generate ID otomatis
function generateId($koneksi, $prefix, $table, $column) {
    $query = mysqli_query($koneksi, "SELECT MAX($column) as max_id FROM $table");
    $data = mysqli_fetch_assoc($query);
    $maxId = $data['max_id'];

    if ($maxId) {
        $num = (int) substr($maxId, strlen($prefix));
        $num++;
        $newId = $prefix . str_pad($num, 5, "0", STR_PAD_LEFT);
    } else {
        $newId = $prefix . "00001";
    }
    return $newId;
}

// Pastikan ada id mobil
if (!isset($_GET['id'])) die("ID mobil tidak ditemukan!");
$id_mobil = $_GET['id'];

// Format tanggal Indonesia
setlocale(LC_TIME, 'id_ID.UTF-8', 'Indonesian', 'id_ID');
$tanggalHariIni = strftime("%A, %d %B %Y");

// Ambil detail mobil
$qMobil = mysqli_query($koneksi, "SELECT * FROM tbl_mobil WHERE id_mobil='$id_mobil'");
$mobil = mysqli_fetch_assoc($qMobil);
if (!$mobil) die("Mobil tidak ditemukan!");

// 🚨 Cek apakah mobil sudah disewa
if ($mobil['status'] === "Disewa") {
    die("<script>alert('Mobil ini sedang disewa dan tidak tersedia!'); window.location='user/daftar_mobil.php';</script>");
}

// Ambil data user
$userData = null;
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    $username = $_SESSION['username'];
    $qUser = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' LIMIT 1");
    $userData = mysqli_fetch_assoc($qUser);
}

// Proses form sewa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_sewa        = generateId($koneksi, "SW", "tbl_sewa", "id_sewa");
    $id_mobil       = $_POST['id_mobil'];
    $nama_penyewa   = mysqli_real_escape_string($koneksi, $_POST['nama_penyewa']);
    $email          = mysqli_real_escape_string($koneksi, $_POST['email']);
    $no_telp        = mysqli_real_escape_string($koneksi, $_POST['no_telp']);
    $alamat_awal    = mysqli_real_escape_string($koneksi, $_POST['alamat_awal']);
    $alamat_tujuan  = mysqli_real_escape_string($koneksi, $_POST['alamat_tujuan']);
    $tanggal_mulai  = $_POST['tanggal_mulai'];
    $tanggal_selesai= $_POST['tanggal_selesai'];
    $metode_bayar   = $_POST['metode_bayar'];

    // Hitung lama sewa
    $lama_sewa = (strtotime($tanggal_selesai) - strtotime($tanggal_mulai)) / (60*60*24);
    if ($lama_sewa < 1) $lama_sewa = 1;
    $total_harga = $mobil['harga_per_hari'] * $lama_sewa;

    $status_awal = "Pending"; // default pending dulu
    $id_user = $userData['id']; 

    $insert = mysqli_query($koneksi, "INSERT INTO tbl_sewa 
        (id_sewa, id_user, id_mobil, nama_penyewa, email, no_telp, alamat_awal, alamat_tujuan, tanggal_mulai, tanggal_selesai, total_harga, metode_bayar, status, status_pembayaran) 
        VALUES 
        ('$id_sewa', '$id_user', '$id_mobil', '$nama_penyewa', '$email', '$no_telp', '$alamat_awal', '$alamat_tujuan', '$tanggal_mulai', '$tanggal_selesai', '$total_harga', '$metode_bayar', '$status_awal', '$status_awal')");
    
    if ($insert) {
      echo "
      <script src='../css/sweetalert2/dist/sweetalert2.all.min.js'></script>
      <script>
      Swal.fire({
          icon: 'success',
          title: 'Penyewaan Berhasil!',
          text: 'Status penyewaan sekarang: PENDING. Silakan lakukan konfirmasi pembayaran.',
          confirmButtonText: 'OK',
          confirmButtonColor: '#3085d6'
      }).then((result) => {
          if (result.isConfirmed) {
              window.location = '../user/daftar_sewa.php';
          }
      });
      </script>";
      exit;
  } else {
      echo "
      <script src='../css/sweetalert2/dist/sweetalert2.all.min.js'></script>
      <script>
      Swal.fire({
          icon: 'error',
          title: 'Gagal!',
          text: 'Terjadi kesalahan saat memproses penyewaan.',
          confirmButtonText: 'OK',
          confirmButtonColor: '#d33'
      });
      </script>";
  }
  
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Sewa Mobil</title>
<link rel="stylesheet" href="../css/bootstrap.css">
<link href="../css/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
<style>
body { background-color: #f8f9fa; font-family: 'Poppins', sans-serif; }
.navbar, .offcanvas-header { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); }
.navbar-brand, .nav-link { color: white !important; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="../index.php"><i class="bi bi-car-front-fill"></i> Rent A Car</a>
    <ul class="navbar-nav ms-auto align-items-center">
      <li class="nav-item me-3 text-white">
        <i class="bi bi-calendar-date"></i> <span><?= ucfirst($tanggalHariIni); ?></span>
      </li>
    </ul>
  </div>
</nav>

<!-- Form Sewa -->
<div class="container mt-5">
  <h2>Sewa Mobil: <?= htmlspecialchars($mobil['nama_mobil']); ?></h2>
  <div class="card mt-3">
    <div class="card-body">
      <form id="formSewa" method="POST">
        <input type="hidden" name="id_mobil" value="<?= $id_mobil; ?>">

        <div class="mb-3">
          <label class="form-label">Nama Penyewa</label>
          <input type="text" name="nama_penyewa" class="form-control" 
                 value="<?= $userData ? htmlspecialchars($userData['nama_lengkap']) : '' ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" 
                 value="<?= $userData ? htmlspecialchars($userData['email']) : '' ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">No Telepon</label>
          <input type="text" name="no_telp" class="form-control" 
                 value="<?= $userData ? htmlspecialchars($userData['no_hp']) : '' ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Alamat Awal</label>
          <textarea name="alamat_awal" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Alamat Tujuan</label>
          <textarea name="alamat_tujuan" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Tanggal Mulai</label>
          <input type="date" name="tanggal_mulai" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Tanggal Selesai</label>
          <input type="date" name="tanggal_selesai" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Harga per Hari</label>
          <input type="text" class="form-control" value="Rp <?= number_format($mobil['harga_per_hari'],0,',','.') ?>" readonly>
        </div>
        <div class="mb-3">
          <label class="form-label">Total Harga</label>
          <input type="text" id="totalHarga" class="form-control" readonly>
        </div>

        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#konfirmasiModal">
          <i class="bi bi-credit-card"></i> Sewa Sekarang
        </button>
        <a href="../index.php" class="btn btn-secondary">Batal</a>
      </form>
    </div>
  </div>
</div>

<!-- Modal Konfirmasi Pembayaran -->
<div class="modal fade" id="konfirmasiModal" tabindex="-1" aria-labelledby="konfirmasiModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="konfirmasiModalLabel"><i class="bi bi-wallet2"></i> Konfirmasi Pembayaran</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Pilih metode pembayaran Anda:</p>
        <select name="metode_bayar" form="formSewa" class="form-select" required>
          <option value="">-- Pilih Metode --</option>
          <option value="Transfer Bank">Transfer Bank</option>
          <option value="Tunai Saat Pengambilan">Tunai Saat Pengambilan</option>
        </select>
        <hr>
        <p class="text-muted small mb-0">Pastikan semua data sudah benar sebelum melanjutkan.</p>
      </div>
      <div class="modal-footer">
        <button type="submit" form="formSewa" class="btn btn-success">
          <i class="bi bi-check-circle"></i> Konfirmasi & Sewa
        </button>
      </div>
    </div>
  </div>
</div>

<footer class="bg-dark text-white text-center py-3 mt-5">
  <small>&copy; <?= date("Y"); ?> Rent A Car. All Rights Reserved.</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const hargaPerHari = <?= $mobil['harga_per_hari']; ?>;
const tglMulai = document.querySelector('[name="tanggal_mulai"]');
const tglSelesai = document.querySelector('[name="tanggal_selesai"]');
const totalHarga = document.getElementById('totalHarga');

function hitungTotal() {
  if (tglMulai.value && tglSelesai.value) {
    let start = new Date(tglMulai.value);
    let end = new Date(tglSelesai.value);
    let diff = (end - start) / (1000*60*60*24);
    if (diff < 1) diff = 1;
    totalHarga.value = "Rp " + (diff * hargaPerHari).toLocaleString("id-ID");
  }
}
tglMulai.addEventListener("change", hitungTotal);
tglSelesai.addEventListener("change", hitungTotal);
</script>

</body>
</html>
