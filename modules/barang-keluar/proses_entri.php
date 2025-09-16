<?php
session_start();

// Panggil koneksi database
require_once "../../config/database.php";
require_once "../../libs/EmailNotificationService.php";

if (isset($_POST['simpan'])) {
    // Ambil data dari formulir
    $id_transaksi = mysqli_real_escape_string($mysqli, $_POST['id_transaksi']);
    $tanggal = mysqli_real_escape_string($mysqli, $_POST['tanggal']);
    $barang = mysqli_real_escape_string($mysqli, $_POST['barang']);
    $jumlah = mysqli_real_escape_string($mysqli, $_POST['jumlah']);
    $jenis = mysqli_real_escape_string($mysqli, $_POST['jenis']);
    $serial_number = isset($_POST['serial_number']) ? mysqli_real_escape_string($mysqli, $_POST['serial_number']) : null;
    $id_rak = isset($_POST['id_rak']) && !empty($_POST['id_rak']) ? mysqli_real_escape_string($mysqli, $_POST['id_rak']) : null;
    $id_keranjang = isset($_POST['id_keranjang']) && !empty($_POST['id_keranjang']) ? mysqli_real_escape_string($mysqli, $_POST['id_keranjang']) : null;
    
    // Get current user ID from session
    $created_by = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : null;

    // PERBAIKAN: Konversi tanggal dari dd-mm-yyyy ke yyyy-mm-dd
    $tanggal_parts = explode('-', $tanggal);
    if (count($tanggal_parts) == 3) {
        // Format: dd-mm-yyyy -> yyyy-mm-dd
        $tanggal_formatted = $tanggal_parts[2] . '-' . $tanggal_parts[1] . '-' . $tanggal_parts[0];
    } else {
        // Fallback jika format tidak sesuai
        $tanggal_formatted = date('Y-m-d');
    }
    
    // Validasi tanggal yang sudah diformat
    $timestamp = strtotime($tanggal_formatted);
    if ($timestamp === false) {
        $_SESSION['pesan'] = '<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
                                <span data-notify="icon" class="fas fa-times"></span>
                                <span data-notify="title" class="text-danger">Gagal!</span> 
                                <span data-notify="message">Format tanggal tidak valid.</span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>';
        header('Location: ../../main.php?module=barang_keluar&action=entri');
        exit;
    }

    // Cek apakah barang adalah modem
    $query_barang = mysqli_query($mysqli, "SELECT nama_barang FROM tbl_barang WHERE id_barang = '$barang'");
    $data_barang = mysqli_fetch_assoc($query_barang);
    $is_modem = stripos($data_barang['nama_barang'], 'modem') !== false;

    // Validasi serial number untuk modem
    if ($is_modem && empty($serial_number)) {
        $_SESSION['pesan'] = '<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
                                <span data-notify="icon" class="fas fa-times"></span>
                                <span data-notify="title" class="text-danger">Gagal!</span> 
                                <span data-notify="message">Serial number wajib diisi untuk barang modem.</span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>';
        header('Location: ../../main.php?module=barang_keluar&action=entri');
        exit;
    }

    // Ambil data stok barang dari database
    $query_stok = mysqli_query($mysqli, "SELECT stok FROM tbl_barang WHERE id_barang = '$barang'");
    $data_stok = mysqli_fetch_assoc($query_stok);
    $stok_tersedia = $data_stok['stok'];

    // Validasi stok
    if ($jumlah > $stok_tersedia) {
        $_SESSION['pesan'] = '<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
                                <span data-notify="icon" class="fas fa-times"></span>
                                <span data-notify="title" class="text-danger">Gagal!</span> 
                                <span data-notify="message">Stok tidak mencukupi. Stok tersedia: ' . $stok_tersedia . '</span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>';
        header('Location: ../../main.php?module=barang_keluar&action=entri');
        exit;
    }

    // Mulai transaksi database
    mysqli_autocommit($mysqli, FALSE);

    try {
        // Query insert data ke tabel barang keluar
        if ($is_modem && !empty($serial_number)) {
            // Untuk modem dengan serial number
            if ($id_rak && $id_keranjang) {
                $query_insert = "INSERT INTO tbl_barang_keluar (id_transaksi, tanggal, barang, serial_number, jumlah, jenis, status, id_rak, id_keranjang, created_by) 
                                VALUES ('$id_transaksi', '$tanggal_formatted', '$barang', '$serial_number', '$jumlah', '$jenis', 'Menunggu Persetujuan', '$id_rak', '$id_keranjang', '$created_by')";
            } elseif ($id_rak) {
                $query_insert = "INSERT INTO tbl_barang_keluar (id_transaksi, tanggal, barang, serial_number, jumlah, jenis, status, id_rak, created_by) 
                                VALUES ('$id_transaksi', '$tanggal_formatted', '$barang', '$serial_number', '$jumlah', '$jenis', 'Menunggu Persetujuan', '$id_rak', '$created_by')";
            } else {
                $query_insert = "INSERT INTO tbl_barang_keluar (id_transaksi, tanggal, barang, serial_number, jumlah, jenis, status, created_by) 
                                VALUES ('$id_transaksi', '$tanggal_formatted', '$barang', '$serial_number', '$jumlah', '$jenis', 'Menunggu Persetujuan', '$created_by')";
            }
        } else {
            // Untuk barang non-modem
            if ($id_rak && $id_keranjang) {
                $query_insert = "INSERT INTO tbl_barang_keluar (id_transaksi, tanggal, barang, jumlah, jenis, status, id_rak, id_keranjang, created_by) 
                                VALUES ('$id_transaksi', '$tanggal_formatted', '$barang', '$jumlah', '$jenis', 'Menunggu Persetujuan', '$id_rak', '$id_keranjang', '$created_by')";
            } elseif ($id_rak) {
                $query_insert = "INSERT INTO tbl_barang_keluar (id_transaksi, tanggal, barang, jumlah, jenis, status, id_rak, created_by) 
                                VALUES ('$id_transaksi', '$tanggal_formatted', '$barang', '$jumlah', '$jenis', 'Menunggu Persetujuan', '$id_rak', '$created_by')";
            } else {
                $query_insert = "INSERT INTO tbl_barang_keluar (id_transaksi, tanggal, barang, jumlah, jenis, status, created_by) 
                                VALUES ('$id_transaksi', '$tanggal_formatted', '$barang', '$jumlah', '$jenis', 'Menunggu Persetujuan', '$created_by')";
            }
        }

    $insert = mysqli_query($mysqli, $query_insert);

    if (!$insert) {
      throw new Exception('Gagal menyimpan data transaksi: ' . mysqli_error($mysqli));
    }

    // Jika item adalah modem dan ada serial_number, reserve serial di tbl_serial_inventory
    if ($is_modem && !empty($serial_number)) {
      // Coba reservasi serial yang statusnya Available
      $reserve_sql = "UPDATE tbl_serial_inventory 
              SET status = 'Reserved', reserved_for_transaction = '$id_transaksi' 
              WHERE id_barang = '$barang' 
                AND serial_number = '$serial_number' 
                AND status = 'Available' 
              LIMIT 1";

      $reserve_res = mysqli_query($mysqli, $reserve_sql);

      if ($reserve_res === false) {
        // Jika tabel tidak ada (legacy), lanjutkan tanpa error
        if (mysqli_errno($mysqli) != 1146) { // 1146 = Table doesn't exist
          throw new Exception('Gagal mereservasi serial: ' . mysqli_error($mysqli));
        }
      } else {
        // Jika tidak ada baris yg ter-update berarti serial tidak tersedia
        if (mysqli_affected_rows($mysqli) == 0) {
          throw new Exception('Serial number yang dipilih tidak tersedia (sudah dipakai/direservasi).');
        }
      }
    }

    // Commit transaksi
    mysqli_commit($mysqli);
    
    // Send email notification
    try {
        // Get user and item details for email
        $user_query = mysqli_query($mysqli, "SELECT nama_user, email FROM tbl_user WHERE id_user = '$created_by'");
        $user_data = mysqli_fetch_assoc($user_query);
        
        $item_query = mysqli_query($mysqli, "SELECT b.nama_barang, s.nama_satuan 
                                           FROM tbl_barang b 
                                           INNER JOIN tbl_satuan s ON b.satuan = s.id_satuan 
                                           WHERE b.id_barang = '$barang'");
        $item_data = mysqli_fetch_assoc($item_query);
        
        if ($user_data && $item_data && !empty($user_data['email'])) {
            $emailService = new EmailNotificationService();
            
            $emailData = [
                'id_transaksi' => $id_transaksi,
                'tanggal' => $tanggal_formatted,
                'nama_barang' => $item_data['nama_barang'],
                'jumlah' => $jumlah,
                'satuan' => $item_data['nama_satuan'],
                'jenis' => $jenis,
                'serial_number' => $serial_number,
                'user_name' => $user_data['nama_user'],
                'user_email' => $user_data['email']
            ];
            
            $emailSent = $emailService->sendOutgoingGoodsNotification($emailData);
            
            if ($emailSent) {
                error_log("Email notification sent for transaction: $id_transaksi");
            } else {
                error_log("Failed to send email notification for transaction: $id_transaksi");
            }
        }
    } catch (Exception $emailException) {
        // Don't fail the transaction if email fails
        error_log("Email notification error: " . $emailException->getMessage());
    }

        // Set pesan sukses
        $_SESSION['pesan'] = '<div class="alert alert-notify alert-success alert-dismissible fade show" role="alert">
                                <span data-notify="icon" class="fas fa-check"></span>
                                <span data-notify="title" class="text-success">Berhasil!</span> 
                                <span data-notify="message">Data barang keluar berhasil disimpan dengan status "Menunggu Persetujuan". ID Transaksi: ' . $id_transaksi . '</span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>';

        // Redirect ke halaman barang keluar
        header('Location: ../../main.php?module=barang_keluar');

    } catch (Exception $e) {
        // Rollback jika ada error
        mysqli_rollback($mysqli);

        $_SESSION['pesan'] = '<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
                                <span data-notify="icon" class="fas fa-times"></span>
                                <span data-notify="title" class="text-danger">Gagal!</span> 
                                <span data-notify="message">' . $e->getMessage() . '</span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>';

        header('Location: ../../main.php?module=barang_keluar&action=entri');
    }

    // Kembalikan autocommit ke true
    mysqli_autocommit($mysqli, TRUE);
} else {
    // Jika tidak ada data POST
    header('Location: ../../main.php?module=barang_keluar');
}
?>