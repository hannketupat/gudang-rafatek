<?php
session_start();      // mengaktifkan session

// pengecekan session login user 
// jika user belum login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
  // alihkan ke halaman login dan tampilkan pesan peringatan login
  header('location: ../../login.php?pesan=2');
}
// jika user sudah login, maka jalankan perintah untuk delete
else {
  // panggil file "database.php" untuk koneksi ke database
  require_once "../../config/database.php";

  // mengecek data GET "id_rak"
  if (isset($_GET['id'])) {
    // ambil data GET dari tombol hapus
    $id_rak = $_GET['id'];

    // mengecek apakah rak masih memiliki keranjang yang terkait
    // sql statement untuk menampilkan data "keranjang" dari tabel "tbl_keranjang" berdasarkan "id_rak"
    $query_cek = mysqli_query($mysqli, "SELECT id_keranjang FROM tbl_keranjang WHERE id_rak='$id_rak'")
                                        or die('Ada kesalahan pada query cek data : ' . mysqli_error($mysqli));
    // ambil jumlah baris data hasil query
    $rows_cek = mysqli_num_rows($query_cek);

    // cek hasil query
    // jika masih ada keranjang yang terkait dengan rak ini
    if ($rows_cek <> 0) {
      // alihkan ke halaman rak dan tampilkan pesan gagal hapus data
      header('location: ../../main.php?module=rak&pesan=5');
    }
    // jika tidak ada keranjang yang terkait dengan rak ini
    else {
      // sql statement untuk delete data dari tabel "tbl_rak" berdasarkan "id_rak"
      $delete = mysqli_query($mysqli, "DELETE FROM tbl_rak WHERE id_rak='$id_rak'")
                                       or die('Ada kesalahan pada query delete : ' . mysqli_error($mysqli));
      // cek query
      // jika proses delete berhasil
      if ($delete) {
        // alihkan ke halaman rak dan tampilkan pesan berhasil hapus data
        header('location: ../../main.php?module=rak&pesan=3');
      }
    }
  }
}
?>