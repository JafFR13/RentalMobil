<?php
session_start();
include "../config/koneksi.php";

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


?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Rent A Car</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 -->
  <link rel="stylesheet" href="../css/bootstrap.css">
  <!-- Icons -->
  <link href="../css/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

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
    .card {
      border: none;
      border-radius: 12px;
      overflow: hidden;
      transition: transform 0.2s, opacity 0.5s;
      opacity: 0;
      transform: scale(0.95);
    }
    .card.show {
      opacity: 1;
      transform: scale(1);
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .card img {
      border-radius: 12px;
    }
    .navbar, .offcanvas-header {
      background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    }
    .navbar-brand, .nav-link {
      color: white !important;
    }
    .profile-card {
      max-width: 700px;
      margin: auto;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      padding: 30px;
    }
    .btn-primary {
      border-radius: 8px;
    }

    main {
            flex: 1; /* isi konten dorong footer ke bawah */
        }
  </style>
</head>
<body>
<div class="header">
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="../index.php"><i class="bi bi-car-front-fill"></i> Rent A Car</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link active" href="../index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="kontak.php">Kontak</a></li>
          <li class="nav-item"><a class="nav-link" href=" daftar_mobil.php">Mobil</a></li>
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

