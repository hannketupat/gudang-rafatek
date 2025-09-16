<?php
require_once "../../config/database.php";

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = intval($_GET['id']);
    $status = $_GET['status']; // Disetujui / Ditolak

    mysqli_query($mysqli, "UPDATE tbl_barang_keluar SET status='$status' WHERE id_transaksi='$id'")
    or die('Error: ' . mysqli_error($mysqli));

    header("location: ../../main.php?module=laporan_barang_keluar&pesan=1");
}
?>