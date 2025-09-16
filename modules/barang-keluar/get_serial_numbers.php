<?php
// Disable any output before JSON
ob_start();

try {
    // Panggil koneksi database
    require_once "../../config/database.php";
    
    // Clear any previous output
    ob_clean();
    
    // Set content type to JSON
    header('Content-Type: application/json; charset=utf-8');
    
    if (!isset($_GET['id_barang']) || empty($_GET['id_barang'])) {
        echo json_encode(array('error' => 'Parameter id_barang tidak ditemukan'));
        exit;
    }
    
    $id_barang = mysqli_real_escape_string($mysqli, $_GET['id_barang']);
    
    // Array untuk menyimpan hasil
    $data = array();
    
    // Ambil serial number dari tbl_serial_inventory berdasarkan id_barang
    // yang statusnya 'Available' (belum direservasi atau digunakan)
    $query = "SELECT serial_number 
              FROM tbl_serial_inventory 
              WHERE id_barang = '$id_barang' 
              AND status = 'Available'
              ORDER BY serial_number ASC";
    
    $result = mysqli_query($mysqli, $query);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = array('serial_number' => $row['serial_number']);
        }
    } else {
        echo json_encode(array('error' => 'Query error: ' . mysqli_error($mysqli)));
        exit;
    }
    
    // Return hasil
    echo json_encode($data);
    
} catch (Exception $e) {
    // Clear any output buffer
    ob_clean();
    
    // Set JSON header
    header('Content-Type: application/json; charset=utf-8');
    
    // Return error as JSON
    echo json_encode(array('error' => 'Server error: ' . $e->getMessage()));
}

// End and clean output buffer
ob_end_flush();
?>