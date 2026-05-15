<?php
include "includes/header.php";
include "includes/sidebar.php";

// ====== PROSES KONFIRMASI ======
if (isset($_POST['aksi']) && isset($_POST['id'])) {
  $id = mysqli_real_escape_string($koneksi, $_POST['id']);
  $aksi = $_POST['aksi'];

if ($aksi == 'terima') {
    $statusBaru = 'Sudah Dibayar';

    // Ambil id_mobil dari sewa
    $q = mysqli_query($koneksi, "SELECT id_mobil FROM tbl_sewa WHERE id_sewa='$id'");
    $d = mysqli_fetch_assoc($q);
    $id_mobil = $d['id_mobil'];

    // Update status mobil menjadi Disewa
    mysqli_query($koneksi, "UPDATE tbl_mobil SET status='Disewa' WHERE id_mobil='$id_mobil'");

} elseif ($aksi == 'tolak') { 
    $statusBaru = 'Dibatalkan';
}

  // Update di tbl_sewa
  mysqli_query($koneksi, "UPDATE tbl_sewa SET status='Disewa', status_pembayaran='$statusBaru' WHERE id_sewa='$id'");


  // Update juga di tbl_pembayaran
  mysqli_query($koneksi, "UPDATE tbl_pembayaran SET status_pembayaran='$statusBaru' WHERE id_sewa='$id'");

  // Kirim pesan ke frontend
  echo "<script src='../css/sweetalert2/dist/sweetalert2.all.min.js'></script>";
  echo "<script>
      Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: 'Status pembayaran berhasil diperbarui menjadi $statusBaru.',
          confirmButtonColor: '#3085d6'
      }).then(() => {
          window.location.href = 'konfirmasi_bayar.php';
      });
  </script>";
  exit;
}

// ====== FILTER ======
$where = [];
if (!empty($_GET['status_pembayaran'])) {
  $status = mysqli_real_escape_string($koneksi, $_GET['status_pembayaran']);
  $where[] = "s.status_pembayaran = '$status'";
}
$whereSQL = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

// ====== AMBIL DATA PEMBAYARAN ======
$query = "
    SELECT 
    s.id_sewa, s.total_harga, s.status_pembayaran, s.tanggal_mulai, s.tanggal_selesai,
    m.nama_mobil, m.jenis,
    u.nama_lengkap, u.email, u.no_hp,
    p.metode AS metode_bayar, 
    p.bukti_bayar AS bukti_transfer
    FROM tbl_sewa s
    JOIN tbl_mobil m ON s.id_mobil = m.id_mobil
    JOIN users u ON s.id_user = u.id
    LEFT JOIN (
      SELECT id_sewa, MAX(id_pembayaran) AS id_pembayaran
      FROM tbl_pembayaran 
      GROUP BY id_sewa
    ) lastpay ON s.id_sewa = lastpay.id_sewa
    LEFT JOIN tbl_pembayaran p ON lastpay.id_pembayaran = p.id_pembayaran
    $whereSQL
    ORDER BY s.id_sewa DESC

";
$result = mysqli_query($koneksi, $query);
?>

<div class="content">
  <h2 class="mb-4">Konfirmasi Pembayaran</h2>

  <!-- ====== FILTER ====== -->
  <div class="card mb-4 shadow-sm">
    <div class="card-body">
      <form method="get" class="row g-3 align-items-end">
        <div class="col-md-3">
          <label class="form-label">Status Pembayaran</label>
          <select name="status_pembayaran" class="form-select">
            <option value="">Semua</option>
            <option value="Pending" <?= @$_GET['status_pembayaran']=="Pending"?"selected":""; ?>>Pending</option>
            <option value="Sudah Dibayar" <?= @$_GET['status_pembayaran']=="Sudah Dibayar"?"selected":""; ?>>Sudah Dibayar</option>
            <option value="Dibatalkan" <?= @$_GET['status_pembayaran']=="Dibatalkan"?"selected":""; ?>>Dibatalkan</option>
          </select>
        </div>
        <div class="col-md-3 text-end">
          <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
          <a href="konfirmasi_bayar.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-repeat"></i> Reset</a>
        </div>
      </form>
    </div>
  </div>

  <!-- ====== TABEL ====== -->
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead class="table-dark text-center">
            <tr>
              <th>ID Sewa</th>
              <th>Nama Penyewa</th>
              <th>Mobil</th>
              <th>Total Harga</th>
              <th>Metode Bayar</th>
              <th>Status Pembayaran</th>
              <th>Bukti Transfer</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if(mysqli_num_rows($result) == 0): ?>
              <tr><td colspan="8" class="text-center text-muted">Tidak ada data ditemukan.</td></tr>
            <?php else: ?>
              <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                  <td><?= $row['id_sewa']; ?></td>
                  <td><?= htmlspecialchars($row['nama_lengkap']); ?><br><small><?= $row['email']; ?> / <?= $row['no_hp']; ?></small></td>
                  <td><?= $row['nama_mobil']; ?> (<?= $row['jenis']; ?>)</td>
                  <td>Rp <?= number_format($row['total_harga'],0,',','.'); ?></td>
                  <td><?= !empty($row['metode_bayar']) ? ucfirst($row['metode_bayar']) : '<span class="text-muted">-</span>'; ?></td>
                  <td class="text-center">
                    <?php
                      if ($row['status_pembayaran'] == 'Sudah Dibayar') {
                        echo '<span class="badge bg-success">Sudah Dibayar</span>';
                      } elseif ($row['status_pembayaran'] == 'Pending') {
                        echo '<span class="badge bg-warning text-dark">Pending</span>';
                      } elseif ($row['status_pembayaran'] == 'Dibatalkan') {
                        echo '<span class="badge bg-danger">Dibatalkan</span>';
                      } else {
                        echo '<span class="badge bg-secondary">'.htmlspecialchars($row['status_pembayaran']).'</span>';
                      }
                    ?>
                  </td>
                  <td class="text-center">
                    <?php if (!empty($row['bukti_transfer'])): ?>
                      <img src="../uploads/bukti/<?= $row['bukti_transfer']; ?>" alt="Bukti" width="60" height="60" class="rounded shadow-sm" style="object-fit: cover;">
                    <?php else: ?>
                      <span class="text-muted">Tidak ada</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-center">
                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal<?= $row['id_sewa']; ?>">Detail</button>
                    <?php if ($row['status_pembayaran'] == 'Pending'): ?>
                      <button class="btn btn-success btn-sm" onclick="konfirmasiPembayaran('terima', '<?= $row['id_sewa']; ?>')">Terima</button>
                      <button class="btn btn-danger btn-sm" onclick="konfirmasiPembayaran('tolak', '<?= $row['id_sewa']; ?>')">Tolak</button>
                    <?php endif; ?>
                  </td>
                </tr>

                <!-- MODAL DETAIL PEMBAYARAN -->
                <div class="modal fade" id="detailModal<?= $row['id_sewa']; ?>" tabindex="-1" aria-labelledby="detailModalLabel<?= $row['id_sewa']; ?>" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg">
                      <div class="modal-header bg-info text-white">
                        <h5 class="modal-title"><i class="bi bi-receipt me-2"></i> Detail Pembayaran #<?= $row['id_sewa']; ?></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <div class="row g-3">
                          <div class="col-md-6">
                            <p><strong>Nama Penyewa:</strong><br><?= htmlspecialchars($row['nama_lengkap']); ?></p>
                            <p><strong>Email:</strong><br><?= htmlspecialchars($row['email']); ?></p>
                            <p><strong>No. HP:</strong><br><?= htmlspecialchars($row['no_hp']); ?></p>
                          </div>
                          <div class="col-md-6">
                            <p><strong>Mobil:</strong><br><?= $row['nama_mobil']; ?> (<?= $row['jenis']; ?>)</p>
                            <p><strong>Tanggal Sewa:</strong><br><?= date("d-m-Y", strtotime($row['tanggal_mulai'])); ?> s/d <?= date("d-m-Y", strtotime($row['tanggal_selesai'])); ?></p>
                            <p><strong>Metode Bayar:</strong><br><?= ucfirst($row['metode_bayar']); ?></p>
                          </div>
                        </div>
                        <hr>
                        <p><strong>Total Harga:</strong> Rp <?= number_format($row['total_harga'],0,',','.'); ?></p>
                        <p><strong>Status Pembayaran:</strong>
                          <?php if ($row['status_pembayaran'] == 'Sudah Dibayar'): ?>
                            <span class="badge bg-success">Sudah Dibayar</span>
                          <?php elseif ($row['status_pembayaran'] == 'Pending'): ?>
                            <span class="badge bg-warning text-dark">Pending</span>
                          <?php elseif ($row['status_pembayaran'] == 'Dibatalkan'): ?>
                            <span class="badge bg-danger">Dibatalkan</span>
                          <?php endif; ?>
                        </p>
                        <?php if (!empty($row['bukti_transfer'])): ?>
                          <div class="text-center mt-3">
                            <p><strong>Bukti Transfer:</strong></p>
                            <img src="../uploads/bukti/<?= $row['bukti_transfer']; ?>" class="img-fluid rounded shadow-sm" style="max-height: 300px; object-fit: contain;">
                          </div>
                        <?php endif; ?>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Tutup</button>
                      </div>
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

<!-- ====== SWEETALERT KONFIRMASI ====== -->
<script src="../css/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script>
function konfirmasiPembayaran(aksi, id) {
  let pesan = aksi === 'terima' ? 'Apakah Anda yakin ingin MENERIMA pembayaran ini?' : 'Apakah Anda yakin ingin MENOLAK pembayaran ini?';
  let ikon = aksi === 'terima' ? 'success' : 'warning';
  
  Swal.fire({
    title: 'Konfirmasi Aksi',
    text: pesan,
    icon: ikon,
    showCancelButton: true,
    confirmButtonText: 'Ya, lanjutkan',
    cancelButtonText: 'Batal',
    confirmButtonColor: aksi === 'terima' ? '#198754' : '#d33'
  }).then((result) => {
    if (result.isConfirmed) {
      // Kirim form via POST agar aman
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = 'konfirmasi_bayar.php';
      form.innerHTML = `
        <input type="hidden" name="aksi" value="${aksi}">
        <input type="hidden" name="id" value="${id}">
      `;
      document.body.appendChild(form);
      form.submit();
    }
  });
}
</script>

<?php include "includes/footer.php"; ?>
