<?php
session_start();      // mengaktifkan session

// pengecekan session login user 
// jika user belum login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
  // alihkan ke halaman login dan tampilkan pesan peringatan login
  header('location: ../../login.php?pesan=2');
}
// jika user sudah login, maka jalankan perintah untuk update
else {
  // panggil file "database.php" untuk koneksi ke database
  require_once "../../config/database.php";

  // mengecek data hasil submit dari form
  if (isset($_POST['simpan'])) {
    // ambil data hasil submit dari form
    $id_rak = $_POST['id_rak'];
    $kode_rak = mysqli_real_escape_string($mysqli, trim($_POST['kode_rak']));
    $nama_rak = mysqli_real_escape_string($mysqli, trim($_POST['nama_rak']));
    $lokasi = mysqli_real_escape_string($mysqli, trim($_POST['lokasi']));
    $kapasitas = !empty($_POST['kapasitas']) ? (int)$_POST['kapasitas'] : 0;
    $keterangan = mysqli_real_escape_string($mysqli, trim($_POST['keterangan']));

    // mengecek "kode_rak" untuk mencegah data duplikat
    // sql statement untuk menampilkan data "kode_rak" dari tabel "tbl_rak" berdasarkan input "kode_rak" dan "id_rak"
    $query = mysqli_query($mysqli, "SELECT kode_rak FROM tbl_rak WHERE kode_rak='$kode_rak' AND id_rak<>'$id_rak'")
                                    or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
    // ambil jumlah baris data hasil query
    $rows = mysqli_num_rows($query);

    // cek hasil query
    // jika "kode_rak" sudah ada di tabel "tbl_rak"
    if ($rows <> 0) {
      // alihkan ke halaman rak dan tampilkan pesan gagal ubah data
      header("location: ../../main.php?module=rak&pesan=4&kode_rak=$kode_rak");
    }
    // jika "kode_rak" belum ada di tabel "tbl_rak"
    else {
      // sql statement untuk update data ke tabel "tbl_rak"
      $update = mysqli_query($mysqli, "UPDATE tbl_rak SET kode_rak='$kode_rak', nama_rak='$nama_rak', lokasi='$lokasi', kapasitas='$kapasitas', keterangan='$keterangan'
                                       WHERE id_rak='$id_rak'")
                                       or die('Ada kesalahan pada query update : ' . mysqli_error($mysqli));
      // cek query
      // jika proses update berhasil
      if ($update) {
        // alihkan ke halaman rak dan tampilkan pesan berhasil ubah data
        header('location: ../../main.php?module=rak&pesan=2');
      }
    }
  }
}
?>