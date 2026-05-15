<?php
include "includes/header.php";
include "includes/sidebar.php";

// Hapus data jika ada parameter delete
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM tbl_pesan WHERE id_pesan = $id");
    echo "<script>window.location='daftar_pesan.php';</script>";
}

// Ambil data pesan
$pesan = mysqli_query($koneksi, "SELECT * FROM tbl_pesan ORDER BY id_pesan DESC");
?>

<div class="content-wrapper p-4">
    <h2 class="mb-4">Daftar Pesan Pelanggan</h2>

    <div class="row g-3">

        <?php while($row = mysqli_fetch_assoc($pesan)): ?>
        <div class="col-md-4 col-sm-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['nama']); ?></h5>
                    <p class="text-muted small mb-1"><?= htmlspecialchars($row['email']); ?></p>
                    <p class="fw-bold small">Subjek: <?= htmlspecialchars($row['subjek']); ?></p>
                    <p class="text-truncate"><?= htmlspecialchars($row['pesan']); ?></p>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center small">
                    <span>📅 <?= date("d M Y H:i", strtotime($row['tanggal'])); ?></span>

                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#detailModal<?= $row['id_pesan']; ?>">
                            Detail
                        </button>

                        <button class="btn btn-sm btn-danger" onclick="hapusPesan(<?= $row['id_pesan']; ?>)">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL DETAIL -->
        <div class="modal fade" id="detailModal<?= $row['id_pesan']; ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Pesan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Nama:</strong> <?= htmlspecialchars($row['nama']); ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($row['email']); ?></p>
                        <p><strong>Subjek:</strong> <?= htmlspecialchars($row['subjek']); ?></p>
                        <p><strong>Pesan:</strong><br><?= nl2br(htmlspecialchars($row['pesan'])); ?></p>
                        <p class="text-muted small">Waktu: <?= date("d M Y H:i", strtotime($row['tanggal'])); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <?php endwhile; ?>

    </div>
</div>

<?php include "includes/footer.php"; ?>

<!-- SweetAlert & Bootstrap JS -->
<script src="../css/sweetalert2/dist/sweetalert2.all.min.js"></script>

<script>
function hapusPesan(id) {
    Swal.fire({
        title: "Yakin ingin menghapus?",
        text: "Pesan yang dihapus tidak bisa dikembalikan.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Hapus",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = "daftar_pesan.php?hapus=" + id;
        }
    });
}
</script>
