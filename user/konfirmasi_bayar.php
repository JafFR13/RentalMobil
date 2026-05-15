<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "../config/koneksi.php";

if (isset($_POST['konfirmasi'])) {
    $id_sewa = mysqli_real_escape_string($koneksi, $_POST['id_sewa']);
    $metode = mysqli_real_escape_string($koneksi, $_POST['metode']);
    $jumlah_bayar = mysqli_real_escape_string($koneksi, $_POST['jumlah_bayar']);

    // Ambil ID mobil
    $qMobil = mysqli_query($koneksi, "SELECT id_mobil FROM tbl_sewa WHERE id_sewa='$id_sewa' LIMIT 1");
    $dataMobil = mysqli_fetch_assoc($qMobil);
    $id_mobil = $dataMobil['id_mobil'];

    // Upload bukti
    $file = $_FILES['bukti_transfer']['name'];
    $tmp = $_FILES['bukti_transfer']['tmp_name'];
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $namaFileBaru = "bukti_" . $id_sewa . "." . $ext;
    $folderTujuan = "../uploads/bukti/";

    if (!file_exists($folderTujuan)) {
        mkdir($folderTujuan, 0777, true);
    }

    move_uploaded_file($tmp, $folderTujuan . $namaFileBaru);

    // Insert ke tbl_pembayaran
    $insertPembayaran = mysqli_query($koneksi, "
        INSERT INTO tbl_pembayaran (id_sewa, metode, jumlah_bayar, bukti_bayar, status_pembayaran)
        VALUES ('$id_sewa', '$metode', '$jumlah_bayar', '$namaFileBaru', 'Pending')
    ");

    // Update status sewa + simpan bukti ke tbl_sewa
    $updateSewa = mysqli_query($koneksi, "
        UPDATE tbl_sewa 
        SET status_pembayaran = 'Pending',
            bukti_transfer = '$namaFileBaru'
        WHERE id_sewa = '$id_sewa'
    ");

    echo "<html><head>
        <script src='../css/sweetalert2/dist/sweetalert2.all.min.js'></script>
    </head><body>";

    if ($insertPembayaran && $updateSewa) {
        echo "
        <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Bukti pembayaran berhasil dikirim. Menunggu konfirmasi admin.',
            confirmButtonColor: '#3085d6'
        }).then(() => {
            window.location.href = 'detail_sewa.php?id=$id_sewa';
        });
        </script>";
    } else {
        echo "
        <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Terjadi kesalahan saat mengirim data pembayaran!'
        }).then(() => {
            window.history.back();
        });
        </script>";
    }

    echo "</body></html>";
} else {
    header("Location: daftar_sewa.php");
    exit;
}
?>
