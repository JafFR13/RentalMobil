<?php
session_start();
include "config/koneksi.php";

// cek login
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

$sql = "SELECT * FROM tbl_mobil WHERE status = 'Tersedia' ORDER BY id_mobil DESC LIMIT 3";
$result = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rent A Car</title>
  <link rel="stylesheet" href="css/bootstrap.css">
  <link href="css/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <!-- AOS Animation -->
  <link href="css/aos/dist/aos.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f8f9fa;
      overflow-x: hidden;
      padding-top: 50px;
    }
    .navbar {
      font-weight: 500;
      letter-spacing: 0.5px;
    }
    .header {
      background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    }
    .card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
      transform: translateY(-6px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .hero {
      background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
      color: white;
      border-radius: 1rem;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      transform: scale(0.97);
      opacity: 0;
      animation: fadeInScale 1s forwards;
    }
    @keyframes fadeInScale {
      to {
        opacity: 1;
        transform: scale(1);
      }
    }
    .hero-image img {
  filter: brightness(0.9) contrast(1.1);
  transition: transform 0.6s ease;
}

.hero-image:hover img {
  transform: scale(1.03);
}
  </style>
</head>
<body>
<div class="header">
  
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-car-front-fill"></i> Rent A Car</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="#kontak">Kontak</a></li>
          <li class="nav-item"><a class="nav-link" href="user/daftar_mobil.php">Mobil</a></li>
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
  <div class="offcanvas-header header text-light">
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
      <a href="auth/login.php" class="btn btn-primary w-100 mb-2">Login</a>
      <a href="auth/register.php" class="btn btn-outline-secondary w-100">Register</a>
    <?php else: ?>
      <?php if($role === 'pengguna'): ?>
        <a href="user/profil.php" class="btn btn-outline-primary w-100 mb-2">Profil</a>
        <a href="user/daftar_sewa.php" class="btn btn-outline-primary w-100 mb-2">Daftar Sewa</a>
      <?php endif; ?>
      <a href="auth/logout.php" class="btn btn-danger w-100">Logout</a>
    <?php endif; ?>
  </div>
</div>

<!-- Hero Image Section -->
<section class="hero-image position-relative">
  <img src="uploads/img/hero.jpg" alt="Hero Car" class="w-100" style="height: 400px; object-fit: cover;">
  <div class="overlay position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center text-white text-center" 
       style="background: rgba(0, 0, 0, 0.45);">
    <h1 class="display-5 fw-bold mb-3" data-aos="fade-down">Sewa Mobil Impianmu Sekarang!</h1>
    <p class="lead mb-3" data-aos="fade-up" data-aos-delay="200">
      Pilihan mobil lengkap, harga bersahabat, pelayanan cepat dan terpercaya.
    </p>
    <a href="user/daftar_mobil.php" class="btn btn-light btn-lg shadow-sm" data-aos="zoom-in" data-aos-delay="300">
      Lihat Semua Mobil
    </a>
  </div>
</section>

<!-- Main -->
<main class="container my-5">

  <!-- Daftar Mobil -->
  <h2 class="text-center mb-4" data-aos="fade-up" data-aos-delay="100">🚘 Mobil Tersedia</h2>
  <div class="row">
    <?php while($mobil = mysqli_fetch_assoc($result)) { ?>
      <div class="col-md-4 mb-4" data-aos="zoom-in" data-aos-delay="200">
        <div class="card shadow-sm h-100">
          <img src="uploads/<?php echo $mobil['foto']; ?>" class="card-img-top" alt="<?= $mobil['nama_mobil']; ?>" style="height:200px;object-fit:cover;">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title"><?= $mobil['nama_mobil']; ?></h5>
            <p class="card-text">Rp <?= number_format($mobil['harga_per_hari'],0,',','.'); ?>/hari</p>
            <div class="mt-auto d-flex justify-content-between">
              <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#detailMobil<?= $mobil['id_mobil']; ?>">Detail</button>
              <?php if($mobil['status'] === "Tersedia"): ?>
                <a href="user/sewa.php?id=<?= $mobil['id_mobil']; ?>" class="btn btn-success">Sewa</a>
              <?php else: ?>
                <button class="btn btn-secondary" disabled>Disewa</button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>

<br><br><br>

  <!-- Kontak -->
    <div class="text-center mb-5" id="kontak">
      <h2 class="fw-bold text-primary">Hubungi Kami</h2>
      <p class="text-muted">Kami siap membantu Anda kapan saja. Silakan hubungi kami melalui informasi di bawah ini.</p>
      <hr class="w-25 mx-auto">
    </div>

    <div class="row g-4">
      <!-- Informasi Kontak -->
      <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-body">
            <h4 class="fw-bold mb-3 text-primary"><i class="bi bi-telephone-forward me-2"></i>Informasi Kontak</h4>
            <p><i class="bi bi-geo-alt-fill text-primary me-2"></i><strong>Alamat:</strong> Jl. Merdeka No. 45, Jakarta, Indonesia</p>
            <p><i class="bi bi-envelope-fill text-primary me-2"></i><strong>Email:</strong> support@rentalmobilpro.com</p>
            <p><i class="bi bi-telephone-fill text-primary me-2"></i><strong>Telepon:</strong> +62 812-3456-7890</p>
            <p><i class="bi bi-clock-fill text-primary me-2"></i><strong>Jam Operasional:</strong> Senin - Minggu, 08.00 - 20.00 WIB</p>

            <hr>
            <h5 class="fw-bold text-primary mt-4"><i class="bi bi-share-fill me-2"></i>Media Sosial</h5>
            <div class="d-flex gap-3 mt-2">
              <a href="#" class="text-primary fs-4"><i class="bi bi-facebook"></i></a>
              <a href="#" class="text-info fs-4"><i class="bi bi-twitter-x"></i></a>
              <a href="#" class="text-danger fs-4"><i class="bi bi-instagram"></i></a>
              <a href="#" class="text-success fs-4"><i class="bi bi-whatsapp"></i></a>
            </div>
          </div>
        </div>
      </div>

      <!-- Formulir Kontak -->
      <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-body">
            <h4 class="fw-bold mb-3 text-primary"><i class="bi bi-envelope-paper me-2"></i>Kirim Pesan</h4>
            <form action="kirim_pesan.php" method="POST">
              <div class="mb-3">
                <label for="nama" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama Anda" required>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Alamat Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email aktif" required>
              </div>
              <div class="mb-3">
                <label for="subjek" class="form-label">Subjek</label>
                <input type="text" class="form-control" id="subjek" name="subjek" placeholder="Judul pesan" required>
              </div>
              <div class="mb-3">
                <label for="pesan" class="form-label">Pesan</label>
                <textarea class="form-control" id="pesan" name="pesan" rows="5" placeholder="Tulis pesan Anda..." required></textarea>
              </div>
              <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-send-fill me-2"></i>Kirim Pesan
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Peta Lokasi -->
    <div class="mt-5">
      <h4 class="fw-bold text-center text-primary mb-3"><i class="bi bi-geo-alt-fill me-2"></i>Lokasi Kami</h4>
      <div class="ratio ratio-16x9 shadow-sm">
        <iframe 
          src="https://www.google.com/maps?q=Jakarta&output=embed" 
          style="border:0;" 
          allowfullscreen 
          loading="lazy">
        </iframe>
      </div>
    </div>
</main>

<footer class="footer bg-dark text-white text-center py-3">
  <small>&copy; <?= date("Y"); ?> RentalMobil - Semua Hak Dilindungi</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({
    once: true,
    duration: 800,
    offset: 100,
  });
</script>
</body>
</html>
