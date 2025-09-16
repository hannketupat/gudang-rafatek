<?php
// pengecekan ajax request untuk mencegah direct access file, agar file tidak bisa diakses secara langsung dari browser
// jika ada ajax request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
  // panggil file "database.php" untuk koneksi ke database
  require_once "../../config/database.php";

  // mengecek data GET dari ajax
  if (isset($_GET['id_barang'])) {
    // ambil data GET dari ajax
    $id_barang = $_GET['id_barang'];

    // sql statement untuk menampilkan data dari tabel "tbl_barang" dan tabel "tbl_satuan" berdasarkan "id_barang"
    // Also get rack and keranjang information for auto-filling
    $query = mysqli_query($mysqli, "SELECT a.stok, a.nama_barang, a.id_rak, a.id_keranjang, b.nama_satuan, j.nama_jenis 
                                    FROM tbl_barang as a 
                                    INNER JOIN tbl_satuan as b ON a.satuan=b.id_satuan 
                                    INNER JOIN tbl_jenis as j ON a.jenis=j.id_jenis
                                    WHERE id_barang='$id_barang'")
                                    or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
    // ambil data hasil query
    $data  = mysqli_fetch_assoc($query);
    
    // Check if item is a modem
    $data['is_modem'] = (isset($data['nama_jenis']) && strtoupper($data['nama_jenis']) === 'MODEM');
    
    // Include rack and keranjang information
    $data['id_rak'] = $data['id_rak'] ?? null;
    $data['id_keranjang'] = $data['id_keranjang'] ?? null;

    // kirimkan data
    echo json_encode($data);
  }
}
// jika tidak ada ajax request
else {
  // alihkan ke halaman error 404
  header('location: ../../404.html');
}