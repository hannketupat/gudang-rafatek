<?php
// Pengecekan ajax request untuk mencegah direct access file
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
  // Panggil file "database.php" untuk koneksi ke database
  require_once "../../config/database.php";

  // Mengecek data GET dari ajax
  if (isset($_GET['id_barang'])) {
    // Ambil data GET dari ajax
    $id_barang = mysqli_real_escape_string($mysqli, $_GET['id_barang']);

    // SQL statement untuk mendapatkan lokasi dari transaksi barang masuk terbaru
    $query = mysqli_query($mysqli, "
      SELECT 
        bm.id_rak,
        bm.id_keranjang,
        r.nama_rak,
        r.lokasi as lokasi_rak,
        k.nama_keranjang,
        k.kondisi as kondisi_keranjang
      FROM tbl_barang_masuk AS bm
      LEFT JOIN tbl_rak AS r ON bm.id_rak = r.id_rak
      LEFT JOIN tbl_keranjang AS k ON bm.id_keranjang = k.id_keranjang
      WHERE bm.barang = '$id_barang' 
        AND bm.id_rak IS NOT NULL 
      ORDER BY bm.tanggal DESC, bm.id_transaksi DESC
      LIMIT 1
    ") or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
    
    // Cek apakah ada data ditemukan
    if (mysqli_num_rows($query) > 0) {
      // Ambil data hasil query
      $data = mysqli_fetch_assoc($query);
      
      // Kirimkan data dalam format JSON
      echo json_encode([
        'success' => true,
        'data' => $data
      ]);
    } else {
      // Jika tidak ada data lokasi di barang_masuk, cek lokasi default dari tabel barang
      $query2 = mysqli_query($mysqli, "
        SELECT 
          b.id_rak,
          b.id_keranjang,
          r.nama_rak,
          r.lokasi as lokasi_rak,
          k.nama_keranjang,
          k.kondisi as kondisi_keranjang
        FROM tbl_barang AS b
        LEFT JOIN tbl_rak AS r ON b.id_rak = r.id_rak
        LEFT JOIN tbl_keranjang AS k ON b.id_keranjang = k.id_keranjang
        WHERE b.id_barang = '$id_barang' 
          AND b.id_rak IS NOT NULL 
        LIMIT 1
      ") or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
      
      if (mysqli_num_rows($query2) > 0) {
        // Ambil data hasil query
        $data = mysqli_fetch_assoc($query2);
        
        // Kirimkan data dalam format JSON
        echo json_encode([
          'success' => true,
          'data' => $data
        ]);
      } else {
        // Tidak ada data lokasi ditemukan
        echo json_encode([
          'success' => false,
          'message' => 'Tidak ada data lokasi ditemukan untuk barang ini'
        ]);
      }
    }
  } else {
    // Parameter tidak lengkap
    echo json_encode([
      'success' => false, 
      'message' => 'Parameter id_barang tidak ditemukan'
    ]);
  }
} else {
  // Jika tidak ada ajax request, alihkan ke halaman error 404
  header('location: ../../404.html');
}
?>