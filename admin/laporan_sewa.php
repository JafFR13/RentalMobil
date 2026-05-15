<?php
include "includes/header.php";
include "includes/sidebar.php";

// ====== PROSES FILTER ======
$where = [];
if (!empty($_GET['status'])) {
  $status = mysqli_real_escape_string($koneksi, $_GET['status']);
  $where[] = "s.status = '$status'";
}
if (!empty($_GET['status_pembayaran'])) {
  $status_pembayaran = mysqli_real_escape_string($koneksi, $_GET['status_pembayaran']);
  $where[] = "s.status_pembayaran = '$status_pembayaran'";
}
if (!empty($_GET['metode_bayar'])) {
  $metode_bayar = mysqli_real_escape_string($koneksi, $_GET['metode_bayar']);
  $where[] = "s.metode_bayar = '$metode_bayar'";
}
if (!empty($_GET['tanggal_mulai']) && !empty($_GET['tanggal_selesai'])) {
  $tgl1 = mysqli_real_escape_string($koneksi, $_GET['tanggal_mulai']);
  $tgl2 = mysqli_real_escape_string($koneksi, $_GET['tanggal_selesai']);
  $where[] = "(s.tanggal_mulai BETWEEN '$tgl1' AND '$tgl2')";
}

// Gabungkan kondisi WHERE jika ada
$whereSQL = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

// ====== AMBIL DATA SEWA ======
$query = "
  SELECT s.*, m.nama_mobil, m.jenis, u.nama_lengkap as nama_user, u.email, u.no_hp
  FROM tbl_sewa s
  JOIN tbl_mobil m ON s.id_mobil = m.id_mobil
  JOIN users u ON s.nama_penyewa = u.nama_lengkap
  $whereSQL
  ORDER BY s.tanggal_mulai DESC
";
$result = mysqli_query($koneksi, $query);
?>

<div class="content">
  <h2 class="mb-4">Laporan Penyewaan Mobil</h2>

  <!-- FILTER -->
  <div class="card mb-4 shadow-sm">
    <div class="card-body">
      <form method="get" class="row g-3 align-items-end">
        <div class="col-md-2">
          <label class="form-label">Status Sewa</label>
          <select name="status" class="form-select">
            <option value="">Semua</option>
            <option value="Pending" <?= @$_GET['status']=="Pending"?"selected":""; ?>>Pending</option>
            <option value="Disewa" <?= @$_GET['status']=="Disewa"?"selected":""; ?>>Disewa</option>
            <option value="Selesai" <?= @$_GET['status']=="Selesai"?"selected":""; ?>>Selesai</option>
            <option value="Dibatalkan" <?= @$_GET['status']=="Dibatalkan"?"selected":""; ?>>Dibatalkan</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Status Pembayaran</label>
          <select name="status_pembayaran" class="form-select">
            <option value="">Semua</option>
            <option value="Belum Dibayar" <?= @$_GET['status_pembayaran']=="Belum Dibayar"?"selected":""; ?>>Belum Dibayar</option>
            <option value="Sudah Dibayar" <?= @$_GET['status_pembayaran']=="Sudah Dibayar"?"selected":""; ?>>Sudah Dibayar</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Metode Bayar</label>
          <select name="metode_bayar" class="form-select">
            <option value="">Semua</option>
            <option value="tunai" <?= @$_GET['metode_bayar']=="tunai"?"selected":""; ?>>Tunai</option>
            <option value="transfer" <?= @$_GET['metode_bayar']=="transfer"?"selected":""; ?>>Transfer</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Dari Tanggal</label>
          <input type="date" name="tanggal_mulai" class="form-control" value="<?= @$_GET['tanggal_mulai']; ?>">
        </div>
        <div class="col-md-2">
          <label class="form-label">Sampai Tanggal</label>
          <input type="date" name="tanggal_selesai" class="form-control" value="<?= @$_GET['tanggal_selesai']; ?>">
        </div>
        <div class="col-md-2 text-end">
          <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
          <a href="laporan_sewa.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-repeat"></i> Reset</a>
        </div>
      </form>
    </div>
  </div>

  <!-- TABEL -->
  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Hasil Laporan</h5>
        <a href="laporan_sewa_cetak.php?<?= http_build_query($_GET); ?>" target="_blank" class="btn btn-danger">
          <i class="bi bi-file-earmark-pdf-fill"></i> Cetak PDF
        </a>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead class="table-dark text-center">
            <tr>
              <th>ID</th>
              <th>Nama Penyewa</th>
              <th>Mobil</th>
              <th>Tanggal Sewa</th>
              <th>Tanggal Kembali</th>
              <th>Total Harga</th>
              <th>Metode</th>
              <th>Status Bayar</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if(mysqli_num_rows($result) == 0): ?>
              <tr><td colspan="9" class="text-center text-muted">Tidak ada data ditemukan.</td></tr>
            <?php else: ?>
              <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                  <td><?= $row['id_sewa']; ?></td>
                  <td><?= htmlspecialchars($row['nama_user']); ?><br><small><?= $row['email']; ?> / <?= $row['no_hp']; ?></small></td>
                  <td><?= $row['nama_mobil']; ?> (<?= $row['jenis']; ?>)</td>
                  <td><?= date("d-m-Y", strtotime($row['tanggal_mulai'])); ?></td>
                  <td><?= date("d-m-Y", strtotime($row['tanggal_selesai'])); ?></td>
                  <td>Rp <?= number_format($row['total_harga'],0,',','.'); ?></td>
                  <td><?= ucfirst($row['metode_bayar']); ?></td>
                  <td><?= $row['status_pembayaran'] == "Sudah Dibayar" ? "<span class='badge bg-success'>Sudah</span>" : "<span class='badge bg-danger'>Belum</span>"; ?></td>
                  <td><?= $row['status']; ?></td>
                </tr>
              <?php endwhile; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include "includes/footer.php"; ?>
