<?php
include "includes/header.php";
include "includes/sidebar.php";

// Ambil data mobil
$sql = "SELECT * FROM tbl_mobil ORDER BY id_mobil ASC";
$result = mysqli_query($koneksi, $sql);
?>

<div class="content">
  <div class="container-fluid">
    <h1 class="mb-4">Kelola Mobil</h1>

    <div class="mb-3">
      <a href="tambah_mobil.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah Mobil</a>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Nama Mobil</th>
                <th>Jenis</th>
                <th>Kursi</th>
                <th>Harga/Hari</th>
                <th>Status</th>
                <th>Foto</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                  <tr>
                    <td><?= $row['id_mobil']; ?></td>
                    <td><?= htmlspecialchars($row['nama_mobil']); ?></td>
                    <td><?= htmlspecialchars($row['jenis']); ?></td>
                    <td><?= $row['kursi']; ?> Orang</td>
                    <td>Rp <?= number_format($row['harga_per_hari'],0,',','.'); ?></td>
                    <td>
                      <?php if($row['status']=="Tersedia"): ?>
                        <span class="badge bg-success">Tersedia</span>
                      <?php else: ?>
                        <span class="badge bg-danger">Disewa</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if(!empty($row['foto'])): ?>
                        <img src="../uploads/<?= $row['foto']; ?>" alt="foto" style="width:80px; height:50px; object-fit:cover; border-radius:5px;">
                      <?php else: ?>
                        <span class="text-muted">-</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <a href="edit_mobil.php?id=<?= $row['id_mobil']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                      <a href="hapus_mobil.php?id=<?= $row['id_mobil']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus mobil ini?');"><i class="bi bi-trash"></i></a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" class="text-center text-muted">Belum ada data mobil.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include "includes/footer.php"; ?>
