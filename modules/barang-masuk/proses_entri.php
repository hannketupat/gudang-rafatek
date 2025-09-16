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
    $id_transaksi  = mysqli_real_escape_string($mysqli, $_POST['id_transaksi']);
    $tanggal       = mysqli_real_escape_string($mysqli, trim($_POST['tanggal']));
    $barang        = mysqli_real_escape_string($mysqli, $_POST['barang']);
    $jumlah        = mysqli_real_escape_string($mysqli, $_POST['jumlah']);
    $serial_number = isset($_POST['serial_number']) ? mysqli_real_escape_string($mysqli, trim($_POST['serial_number'])) : '';
    $id_rak        = isset($_POST['id_rak']) && !empty($_POST['id_rak']) ? mysqli_real_escape_string($mysqli, $_POST['id_rak']) : null;
    $id_keranjang  = isset($_POST['id_keranjang']) && !empty($_POST['id_keranjang']) ? mysqli_real_escape_string($mysqli, $_POST['id_keranjang']) : null;

    // ubah format tanggal menjadi Tahun-Bulan-Hari (Y-m-d) sebelum disimpan ke database
    $tanggal_masuk = date('Y-m-d', strtotime($tanggal));
    
    // Validasi tanggal - pastikan tanggal valid dan tidak berada di masa lalu yang tidak masuk akal
    $timestamp = strtotime($tanggal_masuk);
    if ($timestamp === false || $timestamp < strtotime('2020-01-01') || $timestamp > strtotime('+1 year')) {
        // alihkan ke halaman barang masuk dan tampilkan pesan error
        header('location: ../../main.php?module=barang_masuk&pesan=error_tanggal');
        exit;
    }

    // sql statement untuk insert data ke tabel "tbl_barang_masuk"
    if ($id_rak && $id_keranjang) {
      $insert = mysqli_query($mysqli, "INSERT INTO tbl_barang_masuk(id_transaksi, tanggal, barang, serial_number, jumlah, id_rak, id_keranjang) 
                                       VALUES('$id_transaksi', '$tanggal_masuk', '$barang', '$serial_number', '$jumlah', '$id_rak', '$id_keranjang')")
                                       or die('Ada kesalahan pada query insert : ' . mysqli_error($mysqli));
    } elseif ($id_rak) {
      $insert = mysqli_query($mysqli, "INSERT INTO tbl_barang_masuk(id_transaksi, tanggal, barang, serial_number, jumlah, id_rak) 
                                       VALUES('$id_transaksi', '$tanggal_masuk', '$barang', '$serial_number', '$jumlah', '$id_rak')")
                                       or die('Ada kesalahan pada query insert : ' . mysqli_error($mysqli));
    } else {
      $insert = mysqli_query($mysqli, "INSERT INTO tbl_barang_masuk(id_transaksi, tanggal, barang, serial_number, jumlah) 
                                       VALUES('$id_transaksi', '$tanggal_masuk', '$barang', '$serial_number', '$jumlah')")
                                       or die('Ada kesalahan pada query insert : ' . mysqli_error($mysqli));
    }
    
    // jika proses insert berhasil
    if ($insert) {
      // Jika ada serial number untuk modem, tambahkan ke inventory
      if (!empty($serial_number)) {
        // Check if this is a modem item
        $check_modem = mysqli_query($mysqli, "SELECT nama_barang FROM tbl_barang WHERE id_barang='$barang'");
        $barang_data = mysqli_fetch_assoc($check_modem);
        
        if (stripos($barang_data['nama_barang'], 'modem') !== false) {
          // Add serial number to inventory for each unit received
          for ($i = 0; $i < $jumlah; $i++) {
            // If multiple units, append index to serial number
            $current_serial = ($jumlah > 1) ? $serial_number . '-' . str_pad(($i + 1), 2, '0', STR_PAD_LEFT) : $serial_number;
            
            // Insert into serial inventory
            $insert_serial = mysqli_query($mysqli, "INSERT INTO tbl_serial_inventory (id_barang, serial_number, status) 
                                                  VALUES ('$barang', '$current_serial', 'Available')");
            
            if (!$insert_serial) {
              // If serial number already exists, skip it
              if (mysqli_errno($mysqli) == 1062) { // Duplicate entry error
                continue;
              } else {
                die('Ada kesalahan pada query insert serial inventory : ' . mysqli_error($mysqli));
              }
            }
          }
        }
      }
      
      // Note: Stock is automatically updated by database trigger 'stok_masuk'
      // No manual stock update needed here
      
      // alihkan ke halaman barang masuk dan tampilkan pesan berhasil simpan data
      header('location: ../../main.php?module=barang_masuk&pesan=1');
    }
  }
}
