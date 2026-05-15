<?php
include "includes/header.php";
include "includes/sidebar.php";

// ====== INISIALISASI FILTER TANGGAL ======
$tgl_awal = isset($_GET['tgl_awal']) && $_GET['tgl_awal'] != '' 
    ? $_GET['tgl_awal'] 
    : date('Y-m-01'); // otomatis tanggal 1 bulan ini
$tgl_akhir = isset($_GET['tgl_akhir']) && $_GET['tgl_akhir'] != '' 
    ? $_GET['tgl_akhir'] 
    : date('Y-m-t'); // otomatis tanggal terakhir bulan ini

// ====== QUERY LAPORAN ======
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

$totalPendapatan = 0;
?>
<div class="content">
<h2 class="mb-4">Laporan Transaksi</h2>
    <div class="card shadow border-0">
        

        <div class="card-body">
            <!-- FILTER -->
            <form class="row g-3 mb-4" method="get">
                <div class="col-md-4">
                    <label class="form-label">Tanggal Awal</label>
                    <input type="date" name="tgl_awal" value="<?= $tgl_awal ?>" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="tgl_akhir" value="<?= $tgl_akhir ?>" class="form-control">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-funnel-fill"></i> Tampilkan
                    </button>
                </div>
            </form>

            <!-- TABEL -->
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Penyewa</th>
                            <th>Mobil</th>
                            <th>Tanggal Sewa</th>
                            <th>Tanggal Kembali</th>
                            <th>Total Sewa</th>
                            <th>Denda</th>
                            <th>Total Pendapatan</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($qLaporan) > 0): ?>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($qLaporan)): 
                                $totalPendapatan += $row['total_pendapatan'];
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d/m/Y', strtotime($row['tanggal_dibuat'])) ?></td>
                                <td><?= htmlspecialchars($row['nama_penyewa']) ?></td>
                                <td><?= htmlspecialchars($row['nama_mobil']) ?></td>
                                <td><?= $row['tanggal_sewa'] ?></td>
                                <td><?= $row['tanggal_kembali'] ?></td>
                                <td>Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($row['denda'], 0, ',', '.') ?></td>
                                <td><strong>Rp <?= number_format($row['total_pendapatan'], 0, ',', '.') ?></strong></td>
                                <td><?= htmlspecialchars($row['keterangan']) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted py-3">
                                    <i class="bi bi-info-circle me-1"></i> Tidak ada data laporan untuk periode ini
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- TOTAL -->
            <div class="mt-4 p-3 bg-light border rounded text-end">
                <h5>Total Pendapatan: 
                    <span class="text-success fw-bold">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></span>
                </h5>
            </div>

            <a href="export_laporan.php?tgl_awal=<?= $tgl_awal ?>&tgl_akhir=<?= $tgl_akhir ?>" 
                target="_blank" 
                class="btn btn-danger mt-4">
                <i class="bi bi-file-earmark-pdf-fill"></i> Cetak PDF
            </a>
        </div>
    </div>
</div>

<style>
@media print {
    .card-header, form, button, .sidebar, .navbar { display: none !important; }
    .table { font-size: 12px; }
}
</style>

<?php include "includes/footer.php"; ?>
