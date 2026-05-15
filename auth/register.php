<?php
include "../config/koneksi.php";

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ambil & sanitasi input
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $password = $_POST['password']; // jangan trim password (biarkan spasi jika pengguna mau)
    $nama     = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $email    = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $no_hp    = mysqli_real_escape_string($koneksi, trim($_POST['no_hp']));
    $role     = "pengguna"; // otomatis pengguna umum

    // validasi sederhana
    if ($username === "" || $password === "" || $nama === "") {
        $error = "Nama, username, dan password wajib diisi.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    } else {
        // cek apakah username sudah ada
        $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' LIMIT 1");
        if (mysqli_num_rows($cek) > 0) {
            $error = "Username sudah digunakan, silakan pilih yang lain.";
        } else {
            // hash password dengan algorithm default (bcrypt/argon2 tergantung PHP)
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);

            // gunakan kolom nama_lengkap sesuai struktur DB Anda
            $sql = "INSERT INTO users (username, password, nama_lengkap, email, no_hp, role) 
                    VALUES ('$username', '$password_hashed', '$nama', '$email', '$no_hp', '$role')";
            if (mysqli_query($koneksi, $sql)) {
                $success = "Registrasi berhasil! Silakan <a href='login.php'>login</a>.";
            } else {
                $error = "Terjadi kesalahan: " . mysqli_error($koneksi);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - Rent a Car</title>
  <link rel="stylesheet" href="../css/bootstrap.css">
  <link href="../css/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #2c5364 0%, #203a43 50%, #0f2027 100%);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card {
      border-radius: 1rem;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }
    .form-control {
      border-radius: .5rem;
    }
    .btn-custom {
      border-radius: .5rem;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card p-4">
        <div class="card-body">
          <h3 class="text-center fw-bold mb-4"><i class="bi bi-car-front-fill text-success"></i> Rent a Car</h3>
          <h5 class="text-center mb-3">Buat Akun Baru</h5>

          <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
          <?php endif; ?>
          <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="mb-3">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" name="nama" class="form-control" required value="<?= isset($_POST['nama'])?htmlspecialchars($_POST['nama']):''; ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input type="text" name="username" class="form-control" required value="<?= isset($_POST['username'])?htmlspecialchars($_POST['username']):''; ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Password <small class="text-muted">(min 6 karakter)</small></label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" value="<?= isset($_POST['email'])?htmlspecialchars($_POST['email']):''; ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">No HP</label>
              <input type="text" name="no_hp" class="form-control" value="<?= isset($_POST['no_hp'])?htmlspecialchars($_POST['no_hp']):''; ?>">
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-success btn-custom">
                <i class="bi bi-person-plus"></i> Register
              </button>
            </div>
          </form>

          <p class="text-center mt-3">
            Sudah punya akun? <a href="login.php">Login di sini</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
