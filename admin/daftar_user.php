<?php
include "includes/header.php";
include "includes/sidebar.php";

$id_user = "";
$username = "";
$email = "";
$role = "pengguna";
$mode = "tambah";

// jika tombol simpan ditekan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_user = isset($_POST['id']) ? (int) $_POST['id_user'] : 0;
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);

    if ($_POST['mode'] === "tambah") {
        mysqli_query($koneksi, "INSERT INTO users (username, email, password, role) 
            VALUES ('$username', '$email', '$password', '$role')");
    } else {
        if (!empty($password)) {
            mysqli_query($koneksi, "UPDATE users SET username='$username', email='$email', password='$password', role='$role' WHERE id_user='$id_user'");
        } else {
            mysqli_query($koneksi, "UPDATE users SET username='$username', email='$email', role='$role' WHERE id_user='$id_user'");
        }
    }

    header("Location: kelola_user.php");
    exit;
}

// jika klik edit
if (isset($_GET['edit'])) {
    $mode = "edit";
    $id_user = (int) $_GET['edit'];
    $res = mysqli_query($koneksi, "SELECT * FROM users WHERE idr='$id_user'");
    $row = mysqli_fetch_assoc($res);

    if ($row) {
        $username = $row['username'];
        $email = $row['email'];
        $role = $row['role'];
    }
}

// jika hapus
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM users WHERE id_user='$id'");
    header("Location: kelola_user.php");
    exit;
}

// ambil semua user
$result = mysqli_query($koneksi, "SELECT * FROM users ORDER BY id ASC");
?>

  <!-- Konten -->
  <div class="content">
    <h2 class="mb-4">Kelola User</h2>

    <!-- Form tambah/edit -->
    <div class="card card-hover mb-4">
      <div class="card-body">
        <h5 class="card-title"><?= $mode === "tambah" ? "Tambah User Baru" : "Edit User" ?></h5>
        <form method="POST">
          <input type="hidden" name="mode" value="<?= $mode; ?>">
          <?php if ($mode === "edit"): ?>
            <input type="hidden" name="id_user" value="<?= $id_user; ?>">
          <?php endif; ?>
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($username); ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email); ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password <?= $mode === "edit" ? "(kosongkan jika tidak ingin ubah)" : "" ?></label>
            <input type="password" name="password" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select">
              <option value="pengguna" <?= $role === "pengguna" ? "selected" : "" ?>>Pengguna</option>
              <option value="admin" <?= $role === "admin" ? "selected" : "" ?>>Admin</option>
            </select>
          </div>
          <button type="submit" class="btn btn-success"><?= $mode === "tambah" ? "Tambah" : "Update" ?></button>
          <?php if($mode === "edit"): ?>
            <a href="kelola_user.php" class="btn btn-secondary">Batal</a>
          <?php endif; ?>
        </form>
      </div>
    </div>

    <!-- Tabel User -->
    <div class="card card-hover">
      <div class="card-body">
        <h5 class="card-title">Daftar User</h5>
        <div class="table-responsive">
          <table class="table table-bordered table-striped align-middle">
            <thead>
              <tr>
                <th>ID User</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php while($user = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td><?= $user['id']; ?></td>
                <td><?= htmlspecialchars($user['username']); ?></td>
                <td><?= htmlspecialchars($user['email']); ?></td>
                <td>
                  <?php if($user['role'] == 'admin'): ?>
                    <span class="badge bg-danger">Admin</span>
                  <?php else: ?>
                    <span class="badge bg-primary">Pengguna</span>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="kelola_user.php?edit=<?= $user['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                  <a href="kelola_user.php?hapus=<?= $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus user ini?')">Hapus</a>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
        <?php include "includes/footer.php" ?>
