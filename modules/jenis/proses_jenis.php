<?php
session_start();
require_once "../../config/database.php";

$id = isset($_GET['id']) ? $_GET['id'] : '';
$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';

if ($id && $aksi == 'pinjam') {
  // Update status jenis menjadi 'Dipinjam'
  $update = mysqli_query($mysqli, "UPDATE tbl_jenis SET nama_jenis='Dipinjam' WHERE id_jenis='$id'")
    or die('Ada kesalahan pada query update : ' . mysqli_error($mysqli));
  if ($update) {
    header("location: ../../main.php?module=jenis&pesan=6");
    exit;
  }
}
// Proses acc oleh admin gudang
if ($id && $aksi == 'acc') {
  // Cek hak akses admin gudang
  if (isset($_SESSION['hak_akses']) && $_SESSION['hak_akses'] === 'Admin Gudang') {
    $update = mysqli_query($mysqli, "UPDATE tbl_jenis SET nama_jenis='Keluar' WHERE id_jenis='$id'")
      or die('Ada kesalahan pada query update : ' . mysqli_error($mysqli));
    if ($update) {
      header("location: ../../main.php?module=jenis&pesan=7");
      exit;
    }
  } else {
    header("location: ../../main.php?module=jenis&pesan=notadmin");
    exit;
  }
}
// Jika parameter tidak sesuai, redirect saja
header("location: ../../main.php?module=jenis");
?>
