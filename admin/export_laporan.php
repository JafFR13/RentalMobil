<?php
require '../vendor/autoload.php'; // pastikan path ini sesuai dengan lokasi composer autoload
include "../config/koneksi.php";

use Dompdf\Dompdf;
use Dompdf\Options;

// Ambil parameter tanggal dari GET
$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-t');

// Query laporan
$where = "";
if (!empty($tgl_awal) && !empty($tgl_akhir)) {
    $where = "WHERE l.tanggal_dibuat BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}

$qLaporan = mysqli_query($koneksi, "
    SELECT 
        l.*, 
        u.nama_lengkap AS nama_penyewa,
        m.nama_mobil
    FROM tbl_laporan l
    LEFT JOIN users u ON l.id_user = u.id
    LEFT JOIN tbl_mobil m ON l.id_mobil = m.id_mobil
    $where
    ORDER BY l.tanggal_dibuat DESC
");

// Generate HTML untuk PDF
$html = '
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
h2 { text-align: center; margin-bottom: 10px; }
table { border-collapse: collapse; width: 100%; margin-top: 10px; }
th, td { border: 1px solid #000; padding: 6px; text-align: center; }
th { background-color: #eee; }
.total { text-align: right; font-weight: bold; margin-top: 10px; }
</style>

<h2>Laporan Transaksi Rental Mobil</h2>
<p style="text-align:center;">Periode: '.date('d/m/Y', strtotime($tgl_awal)).' - '.date('d/m/Y', strtotime($tgl_akhir)).'</p>

<table>
<thead>
<tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>Penyewa</th>
    <th>Mobil</th>
    <th>Tgl Sewa</th>
    <th>Tgl Kembali</th>
    <th>Total Sewa</th>
    <th>Denda</th>
    <th>Total Pendapatan</th>
    <th>Keterangan</th>
</tr>
</thead>
<tbody>
';

$totalPendapatan = 0;
$no = 1;
while ($row = mysqli_fetch_assoc($qLaporan)) {
    $totalPendapatan += $row['total_pendapatan'];
    $html .= '
    <tr>
        <td>'.$no++.'</td>
        <td>'.date('d/m/Y', strtotime($row['tanggal_dibuat'])).'</td>
        <td>'.$row['nama_penyewa'].'</td>
        <td>'.$row['nama_mobil'].'</td>
        <td>'.$row['tanggal_sewa'].'</td>
        <td>'.$row['tanggal_kembali'].'</td>
        <td>Rp '.number_format($row['total_bayar'], 0, ',', '.').'</td>
        <td>Rp '.number_format($row['denda'], 0, ',', '.').'</td>
        <td><strong>Rp '.number_format($row['total_pendapatan'], 0, ',', '.').'</strong></td>
        <td>'.$row['keterangan'].'</td>
    </tr>';
}

if ($no == 1) {
    $html .= '<tr><td colspan="10">Tidak ada data laporan untuk periode ini</td></tr>';
}

$html .= '
</tbody>
</table>

<p class="total">Total Pendapatan: Rp '.number_format($totalPendapatan, 0, ',', '.').'</p>
';

// Buat instance Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Load HTML ke Dompdf
$dompdf->loadHtml($html);

// Ukuran dan orientasi kertas
$dompdf->setPaper('A4', 'landscape');

// Render dan tampilkan PDF
$dompdf->render();
$dompdf->stream("laporan_transaksi_".date('Ym').".pdf", array("Attachment" => false));
exit;
?>
