<?php
include "includes/header.php";
include "includes/sidebar.php";

$id = $_GET['id'];
$data = mysqli_query($koneksi, "SELECT * FROM tbl_mobil WHERE id_mobil='$id'");
$mobil = mysqli_fetch_assoc($data);

if (isset($_POST['update'])) {
    $nama_mobil = mysqli_real_escape_string($koneksi, $_POST['nama_mobil']);
    $jenis = mysqli_real_escape_string($koneksi, $_POST['jenis']);
    $kursi = (int) $_POST['kursi'];
    $harga_per_hari = (int) $_POST['harga_per_hari'];
    $status = $_POST['status'];

    $foto = $mobil['foto'];
    if (!empty($_FILES['foto']['name'])) {
        if ($foto && file_exists("../uploads/$foto")) {
            unlink("../uploads/$foto");
        }
        $foto = time() . "_" . basename($_FILES['foto']['name']);
        $target = "../uploads/" . $foto;
        move_uploaded_file($_FILES['foto']['tmp_name'], $target);
    }

    $sql = "UPDATE tbl_mobil 
            SET nama_mobil='$nama_mobil', jenis='$jenis', kursi='$kursi', harga_per_hari='$harga_per_hari', status='$status', foto='$foto'
            WHERE id_mobil='$id'";
    mysqli_query($koneksi, $sql);

    header("Location: daftar_mobil.php");
    exit;
}
?>

<div class="content">
  <div class="container-fluid">

  <h2>Edit Mobil</h2>
  <form method="post" enctype="multipart/form-data">
  <div class="mb-3">
      <label class="form-label">ID Mobil</label>
      <input type="text" name="id_mobil" class="form-control" value="<?= $id; ?>" readonly>
    </div>
    <div class="mb-3">
      <label class="form-label">Nama Mobil</label>
      <input type="text" name="nama_mobil" value="<?= htmlspecialchars($mobil['nama_mobil']); ?>" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Jenis</label>
      <input type="text" name="jenis" value="<?= htmlspecialchars($mobil['jenis']); ?>" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Jumlah Kursi</label>
      <input type="number" name="kursi" value="<?= $mobil['kursi']; ?>" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Harga per Hari</label>
      <input type="number" name="harga_per_hari" value="<?= $mobil['harga_per_hari']; ?>" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Status</label>
      <select name="status" class="form-select" required>
        <option value="Tersedia" <?= $mobil['status']=="Tersedia"?"selected":""; ?>>Tersedia</option>
        <option value="Disewa" <?= $mobil['status']=="Disewa"?"selected":""; ?>>Disewa</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Foto</label>
      <?php if ($mobil['foto']): ?>
        <div class="mb-2">
          <img src="../uploads/<?= $mobil['foto']; ?>" alt="foto" style="width:120px; border-radius:5px;">
        </div>
      <?php endif; ?>
      <input type="file" name="foto" class="form-control">
    </div>
    <button type="submit" name="update" class="btn btn-success">Update</button>
    <a href="daftar_mobil.php" class="btn btn-secondary">Kembali</a>
  </form>

  <?php include "includes/footer.php" ?>
