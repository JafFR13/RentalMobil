<?php
include "includes/header.php";

// pastikan user sudah login
if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['id'];

// ambil data user
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$id_user'");
$user = mysqli_fetch_assoc($query);

if (!$user) {
    die("Data pengguna tidak ditemukan!");
}

// proses update data
$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username  = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email     = mysqli_real_escape_string($koneksi, $_POST['email']);
    $no_telp   = mysqli_real_escape_string($koneksi, $_POST['no_telp']);
    $password  = $_POST['password'];

    // jika password diisi, update dengan password baru (hash)
    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $update = mysqli_query($koneksi, "
            UPDATE tbl_user 
            SET nama='$nama', username='$username', email='$email', no_telp='$no_telp', password='$hashed' 
            WHERE id_user='$id_user'
        ");
    } else {
        // password tidak diubah
        $update = mysqli_query($koneksi, "
            UPDATE tbl_user 
            SET nama='$nama', username='$username', email='$email', no_telp='$no_telp' 
            WHERE id_user='$id_user'
        ");
    }

    if ($update) {
        $success = "Profil berhasil diperbarui!";
        // refresh data dari database
        $query = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE id_user='$id_user'");
        $user = mysqli_fetch_assoc($query);
        $_SESSION['username'] = $user['username']; // update session
    } else {
        $error = "Gagal memperbarui profil: " . mysqli_error($koneksi);
    }
}
?>



<br><br><br><br>
<!-- main -->
<main class="flex-shrink-0">
<div class="profile-card">
  <h3 class="text-center mb-4"><i class="bi bi-person-circle"></i> Profil Saya</h3>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <div class="mb-3">
      <label class="form-label">Nama Lengkap</label>
      <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama_lengkap']); ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']); ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">No. Telepon</label>
      <input type="text" name="no_telp" class="form-control" value="<?= htmlspecialchars($user['no_hp']); ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Password Baru (kosongkan jika tidak diubah)</label>
      <input type="password" name="password" class="form-control" placeholder="********">
    </div>

    <div class="text-start">
      <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Perubahan</button>
      <a href="../index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
  </form>
</div>
</main>
<?php include "includes/footer.php" ?>
