<?php
session_start();
include "../config/koneksi.php";

$error = '';

// proses login saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password']; // tidak perlu escape karena tidak digunakan di query

    // cari user berdasarkan username
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' LIMIT 1");
    $user = mysqli_fetch_assoc($query);

    if ($user) {
        // === Cek password yang di-hash ===
        if (password_verify($password, $user['password'])) {
            // set session
            $_SESSION['login'] = true;
            $_SESSION['id'] = $user['id']; // pastikan kolom ini sesuai di database
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // arahkan berdasarkan role
            if ($user['role'] === 'admin') {
                header("Location: ../admin/index.php");
            } else {
                header("Location: ../index.php");
            }
            exit;
        }
    }

    // jika username tidak ditemukan atau password salah
    $error = "Username atau password salah!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Rent a Car</title>
  <link rel="stylesheet" href="../css/bootstrap.css">
  <link href="../css/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
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
    <div class="col-md-5">
      <div class="card p-4">
        <div class="card-body">
          <h3 class="text-center fw-bold mb-4">
            <i class="bi bi-car-front-fill text-primary"></i> Rent a Car
          </h3>
          <h5 class="text-center mb-3">Silakan Login</h5>
          
          <?php if ($error): ?>
            <div class="alert alert-danger py-2"><?= $error ?></div>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" name="username" id="username" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-primary btn-custom">
                <i class="bi bi-box-arrow-in-right"></i> Login
              </button>
            </div>
          </form>

          <p class="text-center mt-3">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
          </p>

          <p class="text-center text-muted mt-4 mb-0" style="font-size: 0.9rem;">
            &copy; <?= date("Y"); ?> RentalMobil
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
