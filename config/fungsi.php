<?php
function generateId($koneksi, $prefix, $table, $column) {
    $query = mysqli_query($koneksi, "SELECT MAX($column) as max_id FROM $table");
    $data = mysqli_fetch_assoc($query);
    $maxId = $data['max_id'];

    if ($maxId) {
        $num = (int) substr($maxId, strlen($prefix));
        $num++;
        $newId = $prefix . str_pad($num, 5, "0", STR_PAD_LEFT);
    } else {
        $newId = $prefix . "00001";
    }

    return $newId;
}

// contoh penggunaan:
$idMobil = generateId($koneksi, "MBL", "tbl_mobil", "id_mobil");
$idSewa  = generateId($koneksi, "SW", "tbl_sewa", "id_sewa");



?>