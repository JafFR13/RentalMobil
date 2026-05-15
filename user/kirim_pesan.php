<?php
include "../config/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email  = mysqli_real_escape_string($koneksi, $_POST['email']);
    $subjek = mysqli_real_escape_string($koneksi, $_POST['subjek']);
    $pesan  = mysqli_real_escape_string($koneksi, $_POST['pesan']);
    
    // Simpan ke database
    $query = mysqli_query($koneksi, "
        INSERT INTO tbl_pesan (nama, email, subjek, pesan, tanggal) 
        VALUES ('$nama', '$email', '$subjek', '$pesan', NOW())
    ");

    echo "<html><head>
        <script src='../css/sweetalert2/dist/sweetalert2.all.min.js'></script>
    </head><body>";

    if ($query) {
        echo "
        <script>
        Swal.fire({
            icon: 'success',
            title: 'Pesan Terkirim!',
            text: 'Terima kasih, pesan Anda telah berhasil dikirim.',
            confirmButtonColor: '#3085d6'
        }).then(() => {
            window.location = 'kontak.php';
        });
        </script>";
    } else {
        echo "
        <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Terjadi kesalahan saat mengirim pesan. Coba lagi.',
            confirmButtonColor: '#d33'
        }).then(() => {
            window.location = 'kontak.php';
        });
        </script>";
    }

    echo "</body></html>";
}
?>
