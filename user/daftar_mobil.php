<?php
include "includes/header.php";
// Tentukan jumlah mobil per halaman
$limit = 6; // tampilkan 6 mobil per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Ambil parameter pencarian & filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$harga = isset($_GET['harga']) ? $_GET['harga'] : '';
$kursi = isset($_GET['kursi']) ? $_GET['kursi'] : '';

// Query dasar
$query = "SELECT * FROM tbl_mobil WHERE 1=1";

// Filter pencarian
if (!empty($search)) {
    $query .= " AND nama_mobil LIKE '%$search%'";
}

// Filter harga
if ($harga == 'low') {
    $query .= " AND harga_per_hari < 300000";
} elseif ($harga == 'mid') {
    $query .= " AND harga_per_hari BETWEEN 300000 AND 600000";
} elseif ($harga == 'high') {
    $query .= " AND harga_per_hari > 600000";
}

// Filter kursi
if (!empty($kursi)) {
    $query .= " AND kursi = '$kursi'";
}

// Pagination
$limit = 6; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$totalQuery = mysqli_query($koneksi, $query);
$totalData = mysqli_num_rows($totalQuery);
$totalPages = ceil($totalData / $limit);

$query .= " LIMIT $limit OFFSET $offset";
$result = mysqli_query($koneksi, $query);

?>

<main class="flex-shrink-0">
<div class="container py-4">
  <!-- Form Pencarian & Filter -->
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Cari mobil..."
                   value="<?= htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-3">
            <select name="harga" class="form-select">
                <option value="">Filter Harga</option>
                <option value="low" <?= $harga == 'low' ? 'selected' : ''; ?>>&lt; Rp 300.000</option>
                <option value="mid" <?= $harga == 'mid' ? 'selected' : ''; ?>>Rp 300.000 - Rp 600.000</option>
                <option value="high" <?= $harga == 'high' ? 'selected' : ''; ?>>&gt; Rp 600.000</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="kursi" class="form-select">
                <option value="">Filter Kursi</option>
                <option value="4" <?= $kursi == '4' ? 'selected' : ''; ?>>4 Kursi</option>
                <option value="6" <?= $kursi == '6' ? 'selected' : ''; ?>>6 Kursi</option>
                <option value="8" <?= $kursi == '8' ? 'selected' : ''; ?>>8 Kursi</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100">Cari</button>
            <a href="daftar_mobil.php" class="btn btn-secondary w-100">Reset</a>
        </div>
    </form>

  <!-- Daftar Mobil -->
  <h2 class="text-center mb-5 fw-bold">Semua Mobil</h2>
  <div class="row">
    <?php if(mysqli_num_rows($result) > 0): ?>
      <?php while($mobil = mysqli_fetch_assoc($result)) { ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100 shadow-sm">
            <img src="../uploads/<?php echo $mobil['foto']; ?>" 
                 class="card-img-top" 
                 alt="<?php echo $mobil['nama_mobil']; ?>" 
                 style="height:220px;object-fit:cover;">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?php echo $mobil['nama_mobil']; ?></h5>
              <p class="card-text mb-1">Rp <?php echo number_format($mobil['harga_per_hari'],0,',','.'); ?>/hari</p>
              <p class="card-text">
                Status: 
                <?php if($mobil['status']=="Tersedia"){ ?>
                  <span class="badge bg-success">Tersedia</span>
                <?php } else { ?>
                  <span class="badge bg-danger">Disewa</span>
                <?php } ?>
              </p>
              <div class="mt-auto d-flex justify-content-between">
                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailMobil<?= $mobil['id_mobil']; ?>">
                  <i class="bi bi-info-circle"></i> Detail
                </button>
                <?php if($mobil['status']=="Tersedia"){ ?>
                  <a href="sewa.php?id=<?= $mobil['id_mobil']; ?>" class="btn btn-success btn-sm">
                    <i class="bi bi-cart-fill"></i> Sewa Sekarang
                  </a>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Detail -->
        <div class="modal fade" id="detailMobil<?= $mobil['id_mobil']; ?>" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Detail Mobil: <?php echo $mobil['nama_mobil']; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6">
                    <img src="../uploads/<?php echo $mobil['foto']; ?>" class="img-fluid rounded" alt="">
                  </div>
                  <div class="col-md-6">
                    <ul class="list-group">
                      <li class="list-group-item"><strong>Jenis:</strong> <?php echo $mobil['jenis']; ?></li>
                      <li class="list-group-item"><strong>Kursi:</strong> <?php echo $mobil['kursi']; ?> Penumpang</li>
                      <li class="list-group-item"><strong>Harga:</strong> Rp <?php echo number_format($mobil['harga_per_hari'],0,',','.'); ?>/hari</li>
                      <li class="list-group-item"><strong>Status:</strong> 
                        <?php if($mobil['status']=="Tersedia"){ ?>
                          <span class="badge bg-success">Tersedia</span>
                        <?php } else { ?>
                          <span class="badge bg-danger">Disewa</span>
                        <?php } ?>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <?php if($mobil['status']=="Tersedia"){ ?>
                  <a href="../sewa.php?id=<?= $mobil['id_mobil']; ?>" class="btn btn-success">Sewa Sekarang</a>
                <?php } ?>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
    <?php else: ?>
      <p class="text-center">Mobil tidak ditemukan.</p>
    <?php endif; ?>
  </div>

  <!-- Pagination -->
  <?php if($totalPages > 1): ?>
  <nav>
    <ul class="pagination justify-content-center">
      <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
        <a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$page-1])); ?>">Previous</a>
      </li>
      <?php for($i=1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= ($i==$page) ? 'active' : '' ?>">
          <a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$i])); ?>"><?= $i; ?></a>
        </li>
      <?php endfor; ?>
      <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
        <a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$page+1])); ?>">Next</a>
      </li>
    </ul>
  </nav>
  <?php endif; ?>
</div>
</main>

<?php include "includes/footer.php"; ?>