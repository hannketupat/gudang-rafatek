<?php
session_start();      // mengaktifkan session

// pengecekan session login user 
// jika user belum login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
  // alihkan ke halaman login dan tampilkan pesan peringatan login
  header('location: ../../login.php?pesan=2');
}
// jika user sudah login, maka jalankan perintah untuk insert
else {
  // panggil file "database.php" untuk koneksi ke database
  require_once "../../config/database.php";

  // mengecek data hasil submit dari form
  if (isset($_POST['simpan'])) {
    // ambil data hasil submit dari form
    $kode_keranjang = mysqli_real_escape_string($mysqli, trim($_POST['kode_keranjang']));
    $nama_keranjang = mysqli_real_escape_string($mysqli, trim($_POST['nama_keranjang']));
    $id_rak = !empty($_POST['id_rak']) ? (int)$_POST['id_rak'] : null;
    $keterangan = mysqli_real_escape_string($mysqli, trim($_POST['keterangan']));

    // mengecek "kode_keranjang" untuk mencegah data duplikat
    // sql statement untuk menampilkan data "kode_keranjang" dari tabel "tbl_keranjang" berdasarkan input "kode_keranjang"
    $query = mysqli_query($mysqli, "SELECT kode_keranjang FROM tbl_keranjang WHERE kode_keranjang='$kode_keranjang'")
                                    or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
    // ambil jumlah baris data hasil query
    $rows = mysqli_num_rows($query);

    // cek hasil query
    // jika "kode_keranjang" sudah ada di tabel "tbl_keranjang"
    if ($rows <> 0) {
      // alihkan ke halaman keranjang dan tampilkan pesan gagal simpan data
      header("location: ../../main.php?module=keranjang&pesan=4&kode_keranjang=$kode_keranjang");
    }
    // jika "kode_keranjang" belum ada di tabel "tbl_keranjang"
    else {
      // prepare SQL statement untuk insert data ke tabel "tbl_keranjang"
      if ($id_rak !== null) {
        $insert = mysqli_query($mysqli, "INSERT INTO tbl_keranjang(kode_keranjang, nama_keranjang, id_rak, keterangan) 
                                         VALUES('$kode_keranjang', '$nama_keranjang', '$id_rak', '$keterangan')")
                                         or die('Ada kesalahan pada query insert : ' . mysqli_error($mysqli));
      } else {
        $insert = mysqli_query($mysqli, "INSERT INTO tbl_keranjang(kode_keranjang, nama_keranjang, keterangan) 
                                         VALUES('$kode_keranjang', '$nama_keranjang', '$keterangan')")
                                         or die('Ada kesalahan pada query insert : ' . mysqli_error($mysqli));
      }
      
      // cek query
      // jika proses insert berhasil
      if ($insert) {
        // alihkan ke halaman keranjang dan tampilkan pesan berhasil simpan data
        header('location: ../../main.php?module=keranjang&pesan=1');
      }
    }
  }
}
?>