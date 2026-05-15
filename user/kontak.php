<?php include "includes/header.php"; ?>
<main class="flex-shrink-0">
  <div class="container py-5">

    <!-- Header -->
    <div class="text-center mb-5">
      <h2 class="fw-bold text-primary">Hubungi Kami</h2>
      <p class="text-muted">Kami siap membantu Anda kapan saja. Silakan hubungi kami melalui informasi di bawah ini.</p>
      <hr class="w-25 mx-auto">
    </div>

    <div class="row g-4">
      <!-- Informasi Kontak -->
      <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-body">
            <h4 class="fw-bold mb-3 text-primary"><i class="bi bi-telephone-forward me-2"></i>Informasi Kontak</h4>
            <p><i class="bi bi-geo-alt-fill text-primary me-2"></i><strong>Alamat:</strong> Jl. Merdeka No. 45, Jakarta, Indonesia</p>
            <p><i class="bi bi-envelope-fill text-primary me-2"></i><strong>Email:</strong> support@rentalmobilpro.com</p>
            <p><i class="bi bi-telephone-fill text-primary me-2"></i><strong>Telepon:</strong> +62 812-3456-7890</p>
            <p><i class="bi bi-clock-fill text-primary me-2"></i><strong>Jam Operasional:</strong> Senin - Minggu, 08.00 - 20.00 WIB</p>

            <hr>
            <h5 class="fw-bold text-primary mt-4"><i class="bi bi-share-fill me-2"></i>Media Sosial</h5>
            <div class="d-flex gap-3 mt-2">
              <a href="#" class="text-primary fs-4"><i class="bi bi-facebook"></i></a>
              <a href="#" class="text-info fs-4"><i class="bi bi-twitter-x"></i></a>
              <a href="#" class="text-danger fs-4"><i class="bi bi-instagram"></i></a>
              <a href="#" class="text-success fs-4"><i class="bi bi-whatsapp"></i></a>
            </div>
          </div>
        </div>
      </div>

      <!-- Formulir Kontak -->
      <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-body">
            <h4 class="fw-bold mb-3 text-primary"><i class="bi bi-envelope-paper me-2"></i>Kirim Pesan</h4>
            <form action="kirim_pesan.php" method="POST">
              <div class="mb-3">
                <label for="nama" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama Anda" required>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Alamat Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email aktif" required>
              </div>
              <div class="mb-3">
                <label for="subjek" class="form-label">Subjek</label>
                <input type="text" class="form-control" id="subjek" name="subjek" placeholder="Judul pesan" required>
              </div>
              <div class="mb-3">
                <label for="pesan" class="form-label">Pesan</label>
                <textarea class="form-control" id="pesan" name="pesan" rows="5" placeholder="Tulis pesan Anda..." required></textarea>
              </div>
              <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-send-fill me-2"></i>Kirim Pesan
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Peta Lokasi -->
    <div class="mt-5">
      <h4 class="fw-bold text-center text-primary mb-3"><i class="bi bi-geo-alt-fill me-2"></i>Lokasi Kami</h4>
      <div class="ratio ratio-16x9 shadow-sm">
        <iframe 
          src="https://www.google.com/maps?q=Jakarta&output=embed" 
          style="border:0;" 
          allowfullscreen 
          loading="lazy">
        </iframe>
      </div>
    </div>

  </div>
</main>
<?php include "includes/footer.php"; ?>
