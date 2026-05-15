<?php
include "includes/header.php";
include "includes/sidebar.php";

// fungsi generate id
function generateId($koneksi, $prefix, $table, $column) {
    $query = mysqli_query($koneksi, "SELECT MAX($column) as max_id FROM $table");
    $data = mysqli_fetch_assoc($query);
    $maxId = $data['max_id'];

    if ($maxId) {
        $num = (int) substr($maxId, strlen($prefix));
        $num++;
        $newId = $prefix . str_pad($num, 5, "0", STR_PAD_LEFT);
    } else {
        $newId = $prefix . "00001";
    }

    return $newId;
}

// buat id baru untuk mobil
$idMobil = generateId($koneksi, "MBL", "tbl_mobil", "id_mobil");

if (isset($_POST['simpan'])) {
    $id_mobil = $_POST['id_mobil'];
    $nama_mobil = mysqli_real_escape_string($koneksi, $_POST['nama_mobil']);
    $jenis = mysqli_real_escape_string($koneksi, $_POST['jenis']);
    $kursi = (int) $_POST['kursi'];
    $harga_per_hari = (int) $_POST['harga_per_hari'];
    $status = $_POST['status'];

    // Upload foto
    $foto = "";
    if (!empty($_FILES['foto']['name'])) {
        $foto = time() . "_" . basename($_FILES['foto']['name']);
        $target = "../uploads/" . $foto;
        move_uploaded_file($_FILES['foto']['tmp_name'], $target);
    }

    $sql = "INSERT INTO tbl_mobil (id_mobil, nama_mobil, jenis, kursi, harga_per_hari, status, foto) 
            VALUES ('$id_mobil','$nama_mobil','$jenis','$kursi','$harga_per_hari','$status','$foto')";
    mysqli_query($koneksi, $sql);

    header("Location: daftar_mobil.php");
    exit;
}
?>

  <div class="content">
  <div class="container-fluid">
  <h2>Tambah Mobil</h2>
  <form method="post" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">ID Mobil</label>
      <input type="text" name="id_mobil" class="form-control" value="<?= $idMobil; ?>" readonly>
    </div>
    <div class="mb-3">
      <label class="form-label">Nama Mobil</label>
      <input type="text" name="nama_mobil" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Jenis</label>
      <input type="text" name="jenis" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Jumlah Kursi</label>
      <input type="number" name="kursi" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Harga per Hari</label>
      <input type="number" name="harga_per_hari" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Status</label>
      <select name="status" class="form-select" required>
        <option value="Tersedia">Tersedia</option>
        <option value="Disewa">Disewa</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Foto</label>
      <input type="file" name="foto" class="form-control">
    </div>
    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
    <a href="daftar_mobil.php" class="btn btn-secondary">Kembali</a>
  </form>
<?php include "includes/footer.php"; ?>
