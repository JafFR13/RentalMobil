<?php
include "includes/header.php";
include "includes/sidebar.php";

// ====== PROSES SIMPAN PENGEMBALIAN ======
if (isset($_POST['simpan_pengembalian'])) {
  $id_sewa = $_POST['id_sewa'];
  $tanggal_kembali = $_POST['tanggal_kembali'];
  $kondisi_mobil = $_POST['kondisi_mobil']; 
  $denda = $_POST['denda'];
  $catatan = $_POST['catatan'];

  // Ambil total harga sewa dari tbl_sewa
$qSewa = mysqli_query($koneksi, "
  SELECT s.*, u.id AS id_user
  FROM tbl_sewa s
  JOIN users u ON s.id_user = u.id
  WHERE s.id_sewa='$id_sewa' 
");
  $dataSewa = mysqli_fetch_assoc($qSewa);
  $total_harga = $dataSewa['total_harga'];
  $id_mobil = $dataSewa['id_mobil'];

  $total_bayar = $total_harga + $denda;

  // Insert ke tbl_pengembalian
  mysqli_query($koneksi, "INSERT INTO tbl_pengembalian 
    (id_sewa, tanggal_kembali, kondisi_mobil, denda, total_bayar, status_pengembalian, catatan)
    VALUES ('$id_sewa', '$tanggal_kembali', '$kondisi_mobil', '$denda', '$total_bayar', 'Selesai', '$catatan')
  ");
// Ambil data tambahan untuk laporan
$qData = mysqli_query($koneksi, "SELECT * FROM tbl_sewa WHERE id_sewa='$id_sewa'");
$d = mysqli_fetch_assoc($qData);

$id_user = $d['id_user'];
$tanggal_sewa = $d['tanggal_mulai'];
$tanggal_selesai = $d['tanggal_selesai'];
$keterangan = "Pengembalian selesai. Kondisi: $kondisi_mobil";

$total_pendapatan = $total_harga + $denda;

// Simpan ke tbl_laporan
mysqli_query($koneksi, "INSERT INTO tbl_laporan 
  (id_sewa, id_mobil, id_user, tanggal_sewa, tanggal_kembali, total_bayar, denda, total_pendapatan, keterangan, tanggal_dibuat)
  VALUES (
    '$id_sewa',
    '$id_mobil',
    '$id_user',
    '$tanggal_sewa',
    '$tanggal_kembali',
    '$total_bayar',
    '$denda',
    '$total_pendapatan',
    '$keterangan',
    NOW()
  )
");


  // Update status sewa dan mobil
  mysqli_query($koneksi, "UPDATE tbl_sewa SET status='Selesai' WHERE id_sewa='$id_sewa'");
  mysqli_query($koneksi, "UPDATE tbl_mobil SET status='Tersedia' WHERE id_mobil='$id_mobil'");

  echo "
    <script>
    Swal.fire({
      icon: 'success',
      title: 'Berhasil!',
      text: 'Mobil telah dikembalikan.',
      confirmButtonColor: '#3085d6',
      confirmButtonText: 'OK'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location = 'pengembalian.php';
      }
    });
    </script>
  ";
  exit;
}

// ====== AMBIL DATA SEWA YANG BELUM DIKEMBALIKAN ======
$query = "
  SELECT s.*, m.nama_mobil, m.jenis, u.nama_lengkap AS nama_user, u.email, u.no_hp
  FROM tbl_sewa s
  JOIN tbl_mobil m ON s.id_mobil = m.id_mobil
  JOIN users u ON s.id_user = u.id
  WHERE s.status = 'Disewa'
  ORDER BY s.tanggal_mulai DESC
";

$result = mysqli_query($koneksi, $query);
?>

<div class="content">
  <h2 class="mb-4">Daftar Pengembalian Mobil</h2>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead class="table-dark text-center">
            <tr>
              <th>ID Sewa</th>
              <th>Penyewa</th>
              <th>Mobil</th>
              <th>Tanggal Sewa</th>
              <th>Tanggal Selesai</th>
              <th>Total Harga</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if(mysqli_num_rows($result) == 0): ?>
              <tr><td colspan="7" class="text-center text-muted">Tidak ada mobil yang sedang disewa.</td></tr>
            <?php else: ?>
              <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                  <td><?= $row['id_sewa']; ?></td>
                  <td><?= htmlspecialchars($row['nama_user']); ?><br><small><?= $row['email']; ?> / <?= $row['no_hp']; ?></small></td>
                  <td><?= $row['nama_mobil']; ?> (<?= $row['jenis']; ?>)</td>
                  <td><?= date("d-m-Y", strtotime($row['tanggal_mulai'])); ?></td>
                  <td><?= date("d-m-Y", strtotime($row['tanggal_selesai'])); ?></td>
                  <td>Rp <?= number_format($row['total_harga'],0,',','.'); ?></td>
                  <td class="text-center">
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalPengembalian<?= $row['id_sewa']; ?>">
                      <i class="bi bi-box-arrow-in-left"></i> Proses
                    </button>
                  </td>
                </tr>

                <!-- Modal Proses Pengembalian -->
                <div class="modal fade" id="modalPengembalian<?= $row['id_sewa']; ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg">
                      <form method="post">
                        <div class="modal-header bg-success text-white">
                          <h5 class="modal-title"><i class="bi bi-arrow-repeat me-2"></i> Proses Pengembalian - <?= $row['nama_mobil']; ?></h5>
                          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="id_sewa" value="<?= $row['id_sewa']; ?>">

                          <div class="row g-3">
                            <div class="col-md-6">
                              <label class="form-label fw-semibold">Tanggal Pengembalian</label>
                              <input type="date" name="tanggal_kembali" value="<?= date('Y-m-d'); ?>" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                              <label class="form-label fw-semibold">Kondisi Mobil</label>
                              <select name="kondisi_mobil" class="form-select" required>
                                <option value="Baik">Baik</option>
                                <option value="Ada Lecet">Ada Lecet</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                              </select>
                            </div>
                          </div>

                          <div class="mt-3">
                            <label class="form-label fw-semibold">Denda (Rp)</label>
                            <input type="number" name="denda" value="0" class="form-control" min="0">
                          </div>

                          <div class="mt-3">
                            <label class="form-label fw-semibold">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan tambahan (opsional)"></textarea>
                          </div>

                          <hr>
                          <p><strong>Total Sewa:</strong> Rp <?= number_format($row['total_harga'],0,',','.'); ?></p>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Batal
                          </button>
                          <button type="submit" name="simpan_pengembalian" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Simpan Pengembalian
                          </button>
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
