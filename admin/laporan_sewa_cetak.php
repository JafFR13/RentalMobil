<?php
require '../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;
include "../config/koneksi.php";

// Ambil filter dari GET
$where = [];
if (!empty($_GET['status'])) $where[] = "s.status = '".mysqli_real_escape_string($koneksi, $_GET['status'])."'";
if (!empty($_GET['status_pembayaran'])) $where[] = "s.status_pembayaran = '".mysqli_real_escape_string($koneksi, $_GET['status_pembayaran'])."'";
if (!empty($_GET['metode_bayar'])) $where[] = "s.metode_bayar = '".mysqli_real_escape_string($koneksi, $_GET['metode_bayar'])."'";
if (!empty($_GET['tanggal_mulai']) && !empty($_GET['tanggal_selesai'])) {
  $tgl1 = $_GET['tanggal_mulai'];
  $tgl2 = $_GET['tanggal_selesai'];
  $where[] = "(s.tanggal_mulai BETWEEN '$tgl1' AND '$tgl2')";
}
$whereSQL = count($where) ? "WHERE " . implode(" AND ", $where) : "";

// Ambil data sewa
$q = mysqli_query($koneksi, "
  SELECT s.*, m.nama_mobil, m.jenis, u.nama_lengkap as nama_user
  FROM tbl_sewa s
  JOIN tbl_mobil m ON s.id_mobil = m.id_mobil
  JOIN users u ON s.nama_penyewa = u.nama_lengkap
  $whereSQL
  ORDER BY s.tanggal_mulai DESC
");

// Siapkan HTML
$html = '
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
h2 { text-align: center; margin-bottom: 20px; }
table { width: 100%; border-collapse: collapse; }
th, td { border: 1px solid #555; padding: 6px; text-align: center; }
th { background: #0d6efd; color: white; }
</style>
<h2>Laporan Penyewaan Mobil</h2>
<table>
<tr>
<th>ID</th><th>Nama Penyewa</th><th>Mobil</th><th>Tgl Sewa</th>
<th>Tgl Kembali</th><th>Total</th><th>Metode</th><th>Status</th>
</tr>';

while ($r = mysqli_fetch_assoc($q)) {
  $html .= "
  <tr>
    <td>{$r['id_sewa']}</td>
    <td>{$r['nama_user']}</td>
    <td>{$r['nama_mobil']} ({$r['jenis']})</td>
    <td>".date("d-m-Y", strtotime($r['tanggal_mulai']))."</td>
    <td>".date("d-m-Y", strtotime($r['tanggal_selesai']))."</td>
    <td>Rp ".number_format($r['total_harga'],0,',','.')."</td>
    <td>{$r['metode_bayar']}</td>
    <td>{$r['status']}</td>
  </tr>";
}
$html .= "</table>";

// Buat PDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("Laporan_Sewa.pdf", ["Attachment" => false]);
exit;
?>
