<?php
// panggil file database.php untuk koneksi ke database
require_once "../../config/database.php";

// set header untuk response JSON
header('Content-Type: application/json');

// inisialisasi response
$response = array(
    'success' => false,
    'data' => array(),
    'message' => ''
);

// cek apakah ada parameter id_rak yang dikirim via POST
if (isset($_POST['id_rak']) && !empty($_POST['id_rak'])) {
    $id_rak = mysqli_real_escape_string($mysqli, $_POST['id_rak']);
    
    // sql statement untuk menampilkan data keranjang berdasarkan id_rak
    $query = mysqli_query($mysqli, "SELECT id_keranjang, kode_keranjang, nama_keranjang, kapasitas, kondisi
                                   FROM tbl_keranjang 
                                   WHERE id_rak = '$id_rak' 
                                   ORDER BY nama_keranjang ASC")
                                   or die('Ada kesalahan pada query : ' . mysqli_error($mysqli));
    
    if (mysqli_num_rows($query) > 0) {
        $keranjang_list = array();
        
        // ambil data hasil query
        while ($data = mysqli_fetch_assoc($query)) {
            $keranjang_list[] = array(
                'id_keranjang' => $data['id_keranjang'],
                'kode_keranjang' => $data['kode_keranjang'],
                'nama_keranjang' => $data['nama_keranjang'],
                'kapasitas' => $data['kapasitas'],
                'kondisi' => $data['kondisi']
            );
        }
        
        $response['success'] = true;
        $response['data'] = $keranjang_list;
        $response['message'] = 'Data keranjang berhasil dimuat';
    } else {
        $response['success'] = false;
        $response['message'] = 'Tidak ada keranjang tersedia untuk rak yang dipilih';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Parameter id_rak tidak ditemukan';
}

// output response dalam format JSON
echo json_encode($response);
?>