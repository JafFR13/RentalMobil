<?php
include "includes/header.php";

// Ambil data user login
$qUser = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' LIMIT 1");
$user = mysqli_fetch_assoc($qUser);
$nama_penyewa = $user['nama_lengkap'];

// ====== PROSES KONFIRMASI PEMBAYARAN ======
if (isset($_POST['konfirmasi'])) {
    $id_sewa = $_POST['id_sewa'];
    $metode = $_POST['metode_bayar'];
    
    $bukti = $_FILES['bukti_bayar']['name'];
    $tmp = $_FILES['bukti_bayar']['tmp_name'];
    $folder = "../uploads/bukti/";
    if (!is_dir($folder)) mkdir($folder, 0777, true);

    if ($bukti) {
        $nama_file = time() . "_" . $bukti;
        move_uploaded_file($tmp, $folder . $nama_file);

        mysqli_query($koneksi, "
            UPDATE tbl_sewa 
            SET bukti_bayar='$nama_file', status='Disewa' 
            WHERE id_sewa='$id_sewa'
        ");
    }
    echo "<script>alert('Konfirmasi pembayaran berhasil!'); location.href='daftar_sewa.php';</script>";
    exit;
}

// Ambil daftar sewa user
$query = mysqli_query($koneksi, "
    SELECT s.*, m.nama_mobil, m.foto 
    FROM tbl_sewa s 
    JOIN tbl_mobil m ON s.id_mobil = m.id_mobil
    WHERE s.nama_penyewa = '$nama_penyewa'
    ORDER BY s.tanggal_mulai DESC
");
?>

<main class="flex-shrink-0">
<div class="container py-5">
  <h2 class="mb-4 text-center fw-bold text-primary">Daftar Sewa Mobil Anda</h2>

  <?php if (mysqli_num_rows($query) == 0): ?>
    <div class="alert alert-info text-center shadow-sm p-3">
      Anda belum pernah melakukan penyewaan mobil.
    </div>
  <?php else: ?>
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-dark">
              <tr class="text-center">
                <th>No</th>
                <th>Mobil</th>
                <th>Tgl Sewa</th>
                <th>Tgl Kembali</th>
                <th>Total Harga</th>
                <th>Metode Bayar</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $no = 1;
              while ($row = mysqli_fetch_assoc($query)): 
              ?>
              <tr class="text-center">
                <td><?= $no++; ?></td>
                <td class="text-start">
                  <div class="d-flex align-items-center">
                    <img src="../uploads/<?= $row['foto']; ?>" 
                         alt="<?= $row['nama_mobil']; ?>" 
                         width="80" height="50" 
                         class="rounded me-2" 
                         style="object-fit: cover;">
                    <span><?= htmlspecialchars($row['nama_mobil']); ?></span>
                  </div>
                </td>
                <td><?= date("d-m-Y", strtotime($row['tanggal_mulai'])); ?></td>
                <td><?= date("d-m-Y", strtotime($row['tanggal_selesai'])); ?></td>
                <td>Rp <?= number_format($row['total_harga'],0,',','.'); ?></td>
                <td>
                  <span class="badge bg-info text-dark"><?= ucfirst($row['metode_bayar']); ?></span>
                </td>
                <td>
                  <?php if($row['status'] == "Pending"): ?>
                    <span class="badge bg-secondary">Pending</span>
                  <?php elseif($row['status'] == "Disewa"): ?>
                    <span class="badge bg-warning text-dark">Sedang Disewa</span>
                  <?php elseif($row['status'] == "Selesai"): ?>
                    <span class="badge bg-success">Selesai</span>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="detail_sewa.php?id=<?= $row['id_sewa']; ?>" class="btn btn-sm btn-outline-primary mb-1">
                    <i class="bi bi-info-circle"></i> Detail
                  </a>

                  <?php if ($row['metode_bayar'] == "Transfer" && $row['status'] == "Pending"): ?>
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#bayarModal<?= $row['id_sewa']; ?>">
                      <i class="bi bi-cash-stack"></i> Konfirmasi Bayar
                    </button>
                  <?php endif; ?>
                </td>
              </tr>

              <!-- Modal Konfirmasi Pembayaran -->
              <div class="modal fade" id="bayarModal<?= $row['id_sewa']; ?>" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <form method="post" enctype="multipart/form-data">
                      <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Konfirmasi Pembayaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <input type="hidden" name="id_sewa" value="<?= $row['id_sewa']; ?>">
                        <input type="hidden" name="metode_bayar" value="<?= $row['metode_bayar']; ?>">

                        <p class="mb-2">Silakan transfer ke rekening berikut:</p>
                        <div class="bg-light p-3 rounded mb-3">
                          <strong>BANK BCA</strong><br>
                          No. Rekening: <b>1234567890</b><br>
                          a.n <b>Rental Mobil Jaya</b><br>
                          Total Bayar: <b>Rp <?= number_format($row['total_harga'],0,',','.'); ?></b>
                        </div>

                        <div class="mb-3">
                          <label class="form-label">Upload Bukti Transfer</label>
                          <input type="file" name="bukti_bayar" class="form-control" accept="image/*" required>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="konfirmasi" class="btn btn-success">Kirim Bukti</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>
</main>

<?php include "includes/footer.php"; ?>
