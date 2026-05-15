<!-- Sidebar -->
<nav id="sidebar" class="bg-dark text-white sidebar">
  <div class="p-3">
    <h4 class="text-center mb-3"><i class="bi bi-car-front-fill"></i> Rent A Car</h4>
    <hr class="border-light">
    <ul class="nav flex-column">
      <li><a href="index.php" class="nav-link text-white"><i class="bi bi-house-door me-2"></i> Dashboard</a></li>
      <li><a href="daftar_mobil.php" class="nav-link text-white"><i class="bi bi-car-front me-2"></i> Kelola Mobil</a></li>
      <li><a href="daftar_user.php" class="nav-link text-white"><i class="bi bi-people me-2"></i> Kelola User</a></li>
      <li><a href="daftar_sewa.php" class="nav-link text-white"><i class="bi bi-receipt me-2"></i> Kelola Sewa</a></li>
      <li><a href="konfirmasi_bayar.php" class="nav-link text-white"><i class="bi bi-wallet me-2"></i> Konfirmasi Bayar</a></li>
      <li><a href="pengembalian.php" class="nav-link text-white"><i class="bi bi-car-front-fill me-2"></i> Pengembalian</a></li>
      <li><a href="laporan_sewa.php" class="nav-link text-white"><i class="bi bi-bar-chart me-2"></i> Laporan Penyewaan</a></li>
      <li><a href="laporan.php" class="nav-link text-white"><i class="bi bi-graph-down-arrow me-2"></i> Laporan Transaksi</a></li>
      <li><a href="daftar_pesan.php" class="nav-link text-white"><i class="bi bi-chat-left-text me-2"></i> Daftar Pesan</a></li>
      <hr class="border-light">
      <li><a href="../auth/logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
    </ul>
  </div>
</nav>

<!-- Main Content -->
<div id="page-content-wrapper" class="flex-grow-1">
  <nav class="navbar navbar-light bg-white shadow-sm px-4 py-3">
    <button class="btn btn-outline-secondary me-3" id="toggleSidebar"><i class="bi bi-list"></i></button>
    <h5 class="mb-0">Selamat Datang, <?= htmlspecialchars($namaLengkap); ?></h5>
  </nav>

  <div class="container-fluid px-4 py-4">
