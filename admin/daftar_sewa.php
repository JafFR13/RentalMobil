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
$whereSQL = "";
if (count($where) > 0) {
  $whereSQL = "WHERE " . implode(" AND ", $where);
}

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

// ====== HAPUS DATA ======
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  mysqli_query($koneksi, "DELETE FROM tbl_sewa WHERE id_sewa='$id'");
  header("Location: daftar_sewa.php");
  exit;
}

// ====== UPDATE DATA ======
if (isset($_POST['update'])) {
  $id_sewa = $_POST['id_sewa'];
  $tgl_mulai = $_POST['tanggal_mulai'];
  $tgl_selesai = $_POST['tanggal_selesai'];
  $total_harga = $_POST['total_harga'];
  $status = $_POST['status'];

  $qMobil = mysqli_query($koneksi, "SELECT id_mobil FROM tbl_sewa WHERE id_sewa='$id_sewa'");
  $dataMobil = mysqli_fetch_assoc($qMobil);
  $id_mobil = $dataMobil['id_mobil'];

  mysqli_query($koneksi, "UPDATE tbl_sewa SET 
      tanggal_mulai='$tgl_mulai',
      tanggal_selesai='$tgl_selesai',
      total_harga='$total_harga',
      status='$status'
      WHERE id_sewa='$id_sewa'
  ");

if ($status == 'Disewa') {
    mysqli_query($koneksi, "UPDATE tbl_mobil SET status='Disewa' WHERE id_mobil='$id_mobil'");
} elseif ($status == 'Selesai' || $status == 'Dibatalkan') {
    mysqli_query($koneksi, "UPDATE tbl_mobil SET status='Tersedia' WHERE id_mobil='$id_mobil'");
}


  header("Location: daftar_sewa.php");
  exit;
}
?>

<div class="content">
  <h2 class="mb-4">Daftar Penyewaan</h2>

  <!-- ====== FILTER SECTION ====== -->
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
            <option value="Batal" <?= @$_GET['status']=="Batal"?"selected":""; ?>>Batal</option>
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
          <a href="daftar_sewa.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-repeat"></i> Reset</a>
        </div>
      </form>
    </div>
  </div>
  <!-- ====== END FILTER ====== -->

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead class="table-dark text-center">
            <tr>
              <th>ID Sewa</th>
              <th>Nama Penyewa</th>
              <th>Mobil</th>
              <th>Tanggal Sewa</th>
              <th>Tanggal Kembali</th>
              <th>Total Harga</th>
              <th>Metode Bayar</th>
              <th>Status Pembayaran</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if(mysqli_num_rows($result) == 0): ?>
              <tr><td colspan="10" class="text-center text-muted">Tidak ada data ditemukan.</td></tr>
            <?php else: ?>
              <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                  <td><?= $row['id_sewa']; ?></td>
                  <td><?= htmlspecialchars($row['nama_user']); ?><br><small><?= $row['email']; ?> / <?= $row['no_telp']; ?></small></td>
                  <td><?= $row['nama_mobil']; ?> (<?= $row['jenis']; ?>)</td>
                  <td><?= date("d-m-Y", strtotime($row['tanggal_mulai'])); ?></td>
                  <td><?= date("d-m-Y", strtotime($row['tanggal_selesai'])); ?></td>
                  <td>Rp <?= number_format($row['total_harga'],0,',','.'); ?></td>
                  <td><?= ucfirst($row['metode_bayar']); ?></td>
                  <td>
                    <?php if ($row['status_pembayaran'] == 'Sudah Dibayar'): ?>
                      <span class="badge bg-success">Sudah Dibayar</span>
                    <?php elseif ($row['status_pembayaran'] == 'Belum Dibayar'): ?>
                      <span class="badge bg-danger">Belum Dibayar</span>
                    <?php else: ?>
                      <span class="badge bg-secondary"><?= htmlspecialchars($row['status_pembayaran']); ?></span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php
                    if ($row['status'] == "Pending") {
                          echo '<span class="badge bg-secondary">Pending</span>';
                      } elseif ($row['status'] == "Disewa") {
                          echo '<span class="badge bg-warning text-dark">Disewa</span>';
                      } elseif ($row['status'] == "Selesai") {
                          echo '<span class="badge bg-success">Selesai</span>';
                      } elseif ($row['status'] == "Dibatalkan") {
                          echo '<span class="badge bg-danger">Dibatalkan</span>';
                      }

                    ?>
                  </td>
                  <td>
                    <!-- Tombol Detail -->
                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal<?= $row['id_sewa']; ?>">Detail</button>
                    <!-- Tombol Edit -->
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id_sewa']; ?>">Edit</button>
                    <!-- Tombol Delete -->
                    <a href="daftar_sewa.php?hapus=<?= $row['id_sewa']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                    <!-- Tombol Invoice -->
                    <a href="invoice.php?id=<?= $row['id_sewa']; ?>" target="_blank" class="btn btn-primary btn-sm">Invoice</a>
                  </td>
                </tr>

              <!-- Modal Detail -->
                <div class="modal fade" id="detailModal<?= $row['id_sewa']; ?>" tabindex="-1" aria-labelledby="detailModalLabel<?= $row['id_sewa']; ?>" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- ✅ modal-dialog-centered untuk posisi tengah -->
                    <div class="modal-content border-0 shadow-lg">
                      <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="detailModalLabel<?= $row['id_sewa']; ?>"><i class="bi bi-card-text me-2"></i> Detail Sewa #<?= $row['id_sewa']; ?></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                      </div>
                      <div class="modal-body">
                        <div class="row g-3">
                          <div class="col-md-6">
                            <p><strong>Nama Penyewa:</strong><br><?= htmlspecialchars($row['nama_user']); ?></p>
                            <p><strong>Email:</strong><br><?= htmlspecialchars($row['email']); ?></p>
                            <p><strong>No. Telp:</strong><br><?= htmlspecialchars($row['no_hp']); ?></p>
                          </div>
                          <div class="col-md-6">
                            <p><strong>Mobil:</strong><br><?= $row['nama_mobil']; ?> (<?= $row['jenis']; ?>)</p>
                            <p><strong>Tanggal Sewa:</strong><br><?= date("d-m-Y", strtotime($row['tanggal_mulai'])); ?></p>
                            <p><strong>Tanggal Selesai:</strong><br><?= date("d-m-Y", strtotime($row['tanggal_selesai'])); ?></p>
                          </div>
                        </div>
                        <hr>
                        <p class="fs-5"><strong>Total Harga:</strong> Rp <?= number_format($row['total_harga'],0,',','.'); ?></p>
                        <p><strong>Status:</strong> 
                          <?php if ($row['status'] == "Disewa"): ?>
                            <span class="badge bg-warning text-dark">Disewa</span>
                          <?php elseif ($row['status'] == "Selesai"): ?>
                            <span class="badge bg-success">Selesai</span>
                          <?php else: ?>
                            <span class="badge bg-secondary"><?= htmlspecialchars($row['status']); ?></span>
                          <?php endif; ?>
                        </p>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Tutup</button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Modal Edit -->
                <div class="modal fade" id="editModal<?= $row['id_sewa']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $row['id_sewa']; ?>" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered"> <!-- ✅ modal-dialog-centered -->
                    <div class="modal-content border-0 shadow-lg">
                      <form method="post">
                        <div class="modal-header bg-warning text-dark">
                          <h5 class="modal-title" id="editModalLabel<?= $row['id_sewa']; ?>"><i class="bi bi-pencil-square me-2"></i> Edit Sewa #<?= $row['id_sewa']; ?></h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="id_sewa" value="<?= $row['id_sewa']; ?>">
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" value="<?= $row['tanggal_mulai']; ?>" class="form-control" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" value="<?= $row['tanggal_selesai']; ?>" class="form-control" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Total Harga (Rp)</label>
                            <input type="number" name="total_harga" value="<?= $row['total_harga']; ?>" class="form-control" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                              <option value="Pending" <?= $row['status']=="Pending"?"selected":""; ?>>Pending</option>
                              <option value="Disewa" <?= $row['status']=="Disewa"?"selected":""; ?>>Disewa</option>
                              <option value="Selesai" <?= $row['status']=="Selesai"?"selected":""; ?>>Selesai</option>
                              <option value="Dibatalkan" <?= $row['status']=="Dibatalkan"?"selected":""; ?>>Dibatalkan</option>
                            </select>

                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Batal</button>
                          <button type="submit" name="update" class="btn btn-success"><i class="bi bi-check-circle"></i> Simpan</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              <?php endwhile; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include "includes/footer.php"; ?>



            