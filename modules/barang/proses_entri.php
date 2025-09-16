<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start output buffering to prevent header issues
ob_start();

session_start();      // mengaktifkan session

// Debug log function
function debug_log($message) {
    // Try specific path first, then document root, then relative
    $log_paths = [
        '/var/www/gudang/debug_entri.log',
        $_SERVER['DOCUMENT_ROOT'] . '/debug_entri.log',
        dirname(__DIR__, 2) . '/debug_entri.log'
    ];
    
    foreach ($log_paths as $log_file) {
        $log_dir = dirname($log_file);
        if (is_writable($log_dir)) {
            @file_put_contents($log_file, date('[Y-m-d H:i:s] ') . $message . "\n", FILE_APPEND);
            break;
        }
    }
}

debug_log('Script started');

// pengecekan session login user 
// jika user belum login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
  debug_log('User not logged in, redirecting to login');
  // alihkan ke halaman login dan tampilkan pesan peringatan login
  header('location: ../../login.php?pesan=2');
  exit;
}
// jika user sudah login, maka jalankan perintah untuk insert
else {
  debug_log('User logged in: ' . $_SESSION['username']);
  // panggil file "database.php" untuk koneksi ke database
  require_once "../../config/database.php";
  debug_log('Database connection included');

  // Test database connection
  if (!$mysqli) {
    debug_log('Database connection failed: ' . mysqli_connect_error());
    die('Database connection failed: ' . mysqli_connect_error());
  }
  debug_log('Database connection successful');

  // mengecek data hasil submit dari form
  if (isset($_POST['simpan'])) {
    debug_log('Form submitted with simpan button');
    debug_log('POST data: ' . json_encode($_POST));
    // ambil data hasil submit dari form
    $id_barang          = mysqli_real_escape_string($mysqli, $_POST['id_barang']);
    $nama_barang        = mysqli_real_escape_string($mysqli, trim($_POST['nama_barang']));
    $jenis              = mysqli_real_escape_string($mysqli, $_POST['jenis']);
    $stok_minimum       = mysqli_real_escape_string($mysqli, $_POST['stok_minimum']);
    $satuan             = mysqli_real_escape_string($mysqli, $_POST['satuan']);
    $serial_number      = isset($_POST['serial_number']) ? mysqli_real_escape_string($mysqli, trim($_POST['serial_number'])) : '';
    $id_rak             = isset($_POST['id_rak']) && !empty($_POST['id_rak']) ? mysqli_real_escape_string($mysqli, $_POST['id_rak']) : null;
    $id_keranjang       = isset($_POST['id_keranjang']) && !empty($_POST['id_keranjang']) ? mysqli_real_escape_string($mysqli, $_POST['id_keranjang']) : null;

    debug_log('Form data processed: id_barang=' . $id_barang . ', nama_barang=' . $nama_barang);

    // ambil data file hasil submit dari form
    $nama_file          = isset($_FILES['foto']['name']) ? $_FILES['foto']['name'] : '';
    $tmp_file           = isset($_FILES['foto']['tmp_name']) ? $_FILES['foto']['tmp_name'] : '';
    $file_error         = isset($_FILES['foto']['error']) ? $_FILES['foto']['error'] : UPLOAD_ERR_NO_FILE;

    // direktori penyimpanan file foto - using absolute path with better fallback
    $upload_dir = '/var/www/gudang/images/';
    
    // If the hardcoded path doesn't exist, try document root
    if (!is_dir($upload_dir)) {
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/images/';
    }
    
    // Final fallback to relative path
    if (!is_dir($upload_dir)) {
        $upload_dir = dirname(__DIR__, 2) . '/images/';
    }

    // pastikan direktori ada dan writable
    if (!is_dir($upload_dir)) {
      if (!@mkdir($upload_dir, 0775, true)) {
        debug_log('Failed to create upload directory: ' . $upload_dir);
        die('Upload directory cannot be created. Please check permissions.');
      }
    }
    
    // Check if directory is writable
    if (!is_writable($upload_dir)) {
        debug_log('Upload directory not writable: ' . $upload_dir);
        // Try to fix permissions
        @chmod($upload_dir, 0775);
        if (!is_writable($upload_dir)) {
            die('Upload directory is not writable. Please check permissions for: ' . $upload_dir);
        }
    }

    // siapkan nama file terenkripsi bila ada file
    $nama_file_enkripsi = '';
    $upload_file_path = ''; // Full path for the uploaded file
    if (!empty($nama_file)) {
      $ext = pathinfo($nama_file, PATHINFO_EXTENSION);
      $ext = strtolower($ext);
      $nama_file_enkripsi = sha1(md5(time() . $nama_file)) . '.' . $ext;
      $upload_file_path = $upload_dir . $nama_file_enkripsi;
    }

    // file debug log untuk membantu troubleshooting upload - using multiple fallback paths
    $debug_log_paths = [
        '/var/www/gudang/upload_debug.log',
        $_SERVER['DOCUMENT_ROOT'] . '/upload_debug.log',
        dirname(__DIR__, 2) . '/upload_debug.log'
    ];
    
    $debug_log = null;
    foreach ($debug_log_paths as $debug_path) {
        if (is_writable(dirname($debug_path))) {
            $debug_log = $debug_path;
            break;
        }
    }

    // mengecek data foto dari form entri data
  // jika data foto tidak ada
  if (empty($nama_file) || $file_error === UPLOAD_ERR_NO_FILE) {
      debug_log('No file uploaded, proceeding with database insert');
      // sql statement untuk insert data ke tabel "tbl_barang"
      if ($id_rak && $id_keranjang) {
        debug_log('Inserting with rak and keranjang');
        $insert = mysqli_query($mysqli, "INSERT INTO tbl_barang(id_barang, nama_barang, serial_number, jenis, stok_minimum, satuan, id_rak, id_keranjang) 
                                       VALUES('$id_barang', '$nama_barang', '$serial_number', '$jenis', '$stok_minimum', '$satuan', '$id_rak', '$id_keranjang')")
                                       or die('Ada kesalahan pada query insert : ' . mysqli_error($mysqli));
      } elseif ($id_rak) {
        debug_log('Inserting with rak only');
        $insert = mysqli_query($mysqli, "INSERT INTO tbl_barang(id_barang, nama_barang, serial_number, jenis, stok_minimum, satuan, id_rak) 
                                       VALUES('$id_barang', '$nama_barang', '$serial_number', '$jenis', '$stok_minimum', '$satuan', '$id_rak')")
                                       or die('Ada kesalahan pada query insert : ' . mysqli_error($mysqli));
      } else {
        debug_log('Inserting without rak and keranjang');
        $insert = mysqli_query($mysqli, "INSERT INTO tbl_barang(id_barang, nama_barang, serial_number, jenis, stok_minimum, satuan) 
                                       VALUES('$id_barang', '$nama_barang', '$serial_number', '$jenis', '$stok_minimum', '$satuan')")
                                       or die('Ada kesalahan pada query insert : ' . mysqli_error($mysqli));
      }
      // cek query
      // jika proses insert berhasil
      if ($insert) {
        debug_log('Insert successful, redirecting to main.php');
        // alihkan ke halaman barang dan tampilkan pesan berhasil simpan data
        header('location: ../../main.php?module=barang&pesan=1');
        exit;
      } else {
        debug_log('Insert failed: ' . mysqli_error($mysqli));
        die('Insert failed: ' . mysqli_error($mysqli));
      }
    }
    // jika data foto ada
    else {
      // log awal untuk debugging
      @file_put_contents($debug_log, date('[Y-m-d H:i:s] ') . "Upload attempt: name={$nama_file} tmp={$tmp_file} error={$file_error} size=" . (isset($_FILES['foto']['size']) ? $_FILES['foto']['size'] : 'N/A') . "\n", FILE_APPEND);

      // cek error upload PHP
      if ($file_error !== UPLOAD_ERR_OK) {
        @file_put_contents($debug_log, date('[Y-m-d H:i:s] ') . "Upload failed: PHP error code {$file_error}\n", FILE_APPEND);
        // arahkan kembali dengan pesan error (UI dapat ditambahkan untuk menampilkan pesan ini)
        header('location: ../../main.php?module=barang&pesan=7');
        exit;
      }

      // cek ekstensi file di server-side
      if (!in_array($ext, ['jpg','jpeg','png'])) {
        @file_put_contents($debug_log, date('[Y-m-d H:i:s] ') . "Upload failed: invalid extension {$ext}\n", FILE_APPEND);
        header('location: ../../main.php?module=barang&pesan=6');
        exit;
      }

      // cek ukuran file
      if (isset($_FILES['foto']['size']) && $_FILES['foto']['size'] > 3000000) {
        @file_put_contents($debug_log, date('[Y-m-d H:i:s] ') . "Upload failed: size too large " . $_FILES['foto']['size'] . "\n", FILE_APPEND);
        header('location: ../../main.php?module=barang&pesan=8');
        exit;
      }

      // lakukan proses unggah file
      // jika file berhasil diunggah
      if (is_uploaded_file($tmp_file)) {
        debug_log("Attempting to move file from {$tmp_file} to {$upload_file_path}");
        
        if (move_uploaded_file($tmp_file, $upload_file_path)) {
          debug_log("Upload success: file saved to {$upload_file_path}");
          if ($debug_log) {
            @file_put_contents($debug_log, date('[Y-m-d H:i:s] ') . "Upload success: file saved to {$upload_file_path}\n", FILE_APPEND);
          }
          @chmod($upload_file_path, 0644);
        // sql statement untuk insert data ke tabel "tbl_barang"
        if ($id_rak && $id_keranjang) {
          $insert = mysqli_query($mysqli, "INSERT INTO tbl_barang(id_barang, nama_barang, serial_number, jenis, stok_minimum, satuan, foto, id_rak, id_keranjang) 
                                         VALUES('$id_barang', '$nama_barang', '$serial_number', '$jenis', '$stok_minimum', '$satuan', '$nama_file_enkripsi', '$id_rak', '$id_keranjang')")
                                         or die('Ada kesalahan pada query insert : ' . mysqli_error($mysqli));
        } elseif ($id_rak) {
          $insert = mysqli_query($mysqli, "INSERT INTO tbl_barang(id_barang, nama_barang, serial_number, jenis, stok_minimum, satuan, foto, id_rak) 
                                         VALUES('$id_barang', '$nama_barang', '$serial_number', '$jenis', '$stok_minimum', '$satuan', '$nama_file_enkripsi', '$id_rak')")
                                         or die('Ada kesalahan pada query insert : ' . mysqli_error($mysqli));
        } else {
          $insert = mysqli_query($mysqli, "INSERT INTO tbl_barang(id_barang, nama_barang, serial_number, jenis, stok_minimum, satuan, foto) 
                                         VALUES('$id_barang', '$nama_barang', '$serial_number', '$jenis', '$stok_minimum', '$satuan', '$nama_file_enkripsi')")
                                         or die('Ada kesalahan pada query insert : ' . mysqli_error($mysqli));
        }
        // cek query
        // jika proses insert berhasil
        if ($insert) {
          debug_log('Insert with file successful, redirecting to main.php');
          // alihkan ke halaman barang dan tampilkan pesan berhasil simpan data
          header('location: ../../main.php?module=barang&pesan=1');
          exit;
        } else {
          debug_log('Insert with file failed: ' . mysqli_error($mysqli));
          die('Insert with file failed: ' . mysqli_error($mysqli));
        }
        } else {
          debug_log('File upload failed: move_uploaded_file returned false');
          debug_log('Source: ' . $tmp_file . ', Destination: ' . $upload_file_path);
          debug_log('Upload dir writable: ' . (is_writable($upload_dir) ? 'yes' : 'no'));
          debug_log('Upload dir permissions: ' . substr(sprintf('%o', fileperms($upload_dir)), -4));
          if ($debug_log) {
            @file_put_contents($debug_log, date('[Y-m-d H:i:s] ') . "File upload failed: move_uploaded_file error\n", FILE_APPEND);
          }
          die('File upload failed. Upload directory: ' . $upload_dir . ' - Check permissions and disk space.');
        }
      } else {
        debug_log('File upload failed: is_uploaded_file returned false');
        debug_log('Temp file: ' . $tmp_file . ', exists: ' . (file_exists($tmp_file) ? 'yes' : 'no'));
        if ($debug_log) {
          @file_put_contents($debug_log, date('[Y-m-d H:i:s] ') . "File upload failed: invalid uploaded file\n", FILE_APPEND);
        }
        die('File upload failed: Invalid uploaded file.');
      }
    }
  } else {
    debug_log('Form not submitted with simpan button');
  }
}

debug_log('Script ended');
ob_end_flush();
?>
