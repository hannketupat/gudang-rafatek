<?php
session_start();
require_once "../../config/database.php";
require_once "../../libs/EmailNotificationService.php";

// ===== HANDLER UNTUK GET REQUEST (dari link Setujui/Tolak) =====
if (isset($_GET['id']) && isset($_GET['status'])) {
    $id_transaksi = mysqli_real_escape_string($mysqli, $_GET['id']);
    $status_baru = mysqli_real_escape_string($mysqli, $_GET['status']);
    
    // Validasi status yang diizinkan
    $allowed_status = ['Disetujui', 'Ditolak'];
    if (!in_array($status_baru, $allowed_status)) {
        $_SESSION['pesan'] = '<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
                                <span data-notify="icon" class="fas fa-times"></span>
                                <span data-notify="title" class="text-danger">Error!</span> 
                                <span data-notify="message">Status tidak valid!</span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>';
        header('Location: ../../main.php?module=barang_keluar');
        exit;
    }
    
    // Mulai transaksi database
    mysqli_autocommit($mysqli, FALSE);
    
    try {
        // Ambil data transaksi saat ini
        $query_current = mysqli_query($mysqli, 
            "SELECT bk.*, b.stok, b.nama_barang 
             FROM tbl_barang_keluar bk 
             JOIN tbl_barang b ON bk.barang = b.id_barang 
             WHERE bk.id_transaksi = '$id_transaksi'"
        );
        
        if (!$query_current) {
            throw new Exception('Gagal mengambil data transaksi: ' . mysqli_error($mysqli));
        }
        
        $data_current = mysqli_fetch_assoc($query_current);
        
        if (!$data_current) {
            throw new Exception('Data transaksi tidak ditemukan');
        }
        
        $status_lama = $data_current['status'];
        $id_barang = $data_current['barang'];
        $jumlah = $data_current['jumlah'];
        $stok_sekarang = $data_current['stok'];
        $jenis = $data_current['jenis'];
        
        // Cek apakah status sudah sama
        if ($status_lama == $status_baru) {
            throw new Exception('Status sudah ' . $status_baru);
        }
        
        // Update status transaksi
        $query_update_status = "UPDATE tbl_barang_keluar SET status = '$status_baru'";
        
        if ($status_baru == 'Disetujui') {
            $query_update_status .= ", tanggal_persetujuan = NOW()";
            
            // Kurangi stok hanya jika belum pernah disetujui sebelumnya
            if ($status_lama != 'Disetujui') {
                // Validasi stok mencukupi
                if ($stok_sekarang < $jumlah) {
                    throw new Exception('Stok tidak mencukupi. Stok tersedia: ' . $stok_sekarang);
                }
                
                $stok_baru = $stok_sekarang - $jumlah;
                
                // Update stok barang
                $update_stok = mysqli_query($mysqli, 
                    "UPDATE tbl_barang SET stok = '$stok_baru' WHERE id_barang = '$id_barang'"
                );
                
                if (!$update_stok) {
                    throw new Exception('Gagal mengupdate stok: ' . mysqli_error($mysqli));
                }
            }
            
        } elseif ($status_baru == 'Ditolak') {
            $query_update_status .= ", tanggal_penolakan = NOW()";
            
            // Kembalikan stok jika sebelumnya sudah disetujui
            if ($status_lama == 'Disetujui') {
                $stok_baru = $stok_sekarang + $jumlah;
                
                $update_stok = mysqli_query($mysqli, 
                    "UPDATE tbl_barang SET stok = '$stok_baru' WHERE id_barang = '$id_barang'"
                );
                
                if (!$update_stok) {
                    throw new Exception('Gagal mengembalikan stok: ' . mysqli_error($mysqli));
                }
            }
        }
        
        $query_update_status .= " WHERE id_transaksi = '$id_transaksi'";
        
        $update_status = mysqli_query($mysqli, $query_update_status);
        
        if (!$update_status) {
            throw new Exception('Gagal mengupdate status transaksi: ' . mysqli_error($mysqli));
        }
        
        // Update serial number status untuk modem (jika ada)
        if (stripos($data_current['nama_barang'], 'modem') !== false && !empty($data_current['serial_number'])) {
            if ($status_baru == 'Disetujui') {
                $status_serial = 'Used';
            } else {
                $status_serial = 'Available';
            }
            
            // Update serial number status (jika tabel tbl_serial_inventory ada)
            if ($status_baru == 'Disetujui') {
                // Set status=Used, set used_in_transaction and clear reserved_for_transaction
                $update_serial = mysqli_query($mysqli,
                    "UPDATE tbl_serial_inventory 
                     SET status = 'Used', used_in_transaction = '$id_transaksi', reserved_for_transaction = NULL 
                     WHERE serial_number = '{$data_current['serial_number']}' 
                     AND id_barang = '$id_barang'"
                );
            } else {
                // Ditolak -> make Available and clear reservation/usage
                $update_serial = mysqli_query($mysqli,
                    "UPDATE tbl_serial_inventory 
                     SET status = 'Available', reserved_for_transaction = NULL, used_in_transaction = NULL 
                     WHERE serial_number = '{$data_current['serial_number']}' 
                     AND id_barang = '$id_barang'"
                );
            }
            // Ignore errors if table missing
        }
        
    // Commit transaksi
    mysqli_commit($mysqli);
    // Pastikan autocommit dikembalikan sebelum redirect
    mysqli_autocommit($mysqli, TRUE);
    
    // Send status update email notification
    try {
        // Get user and item details for email
        $user_query = mysqli_query($mysqli, "SELECT u.nama_user, u.email 
                                            FROM tbl_user u 
                                            INNER JOIN tbl_barang_keluar bk ON u.id_user = bk.created_by 
                                            WHERE bk.id_transaksi = '$id_transaksi'");
        $user_data = mysqli_fetch_assoc($user_query);
        
        $item_query = mysqli_query($mysqli, "SELECT b.nama_barang, s.nama_satuan 
                                           FROM tbl_barang b 
                                           INNER JOIN tbl_satuan s ON b.satuan = s.id_satuan 
                                           WHERE b.id_barang = '$id_barang'");
        $item_data = mysqli_fetch_assoc($item_query);
        
        if ($user_data && $item_data && !empty($user_data['email'])) {
            $emailService = new EmailNotificationService();
            
            $emailData = [
                'id_transaksi' => $id_transaksi,
                'tanggal' => $data_current['tanggal'],
                'nama_barang' => $item_data['nama_barang'],
                'jumlah' => $jumlah,
                'satuan' => $item_data['nama_satuan'],
                'jenis' => $jenis,
                'serial_number' => $data_current['serial_number'],
                'user_name' => $user_data['nama_user'],
                'user_email' => $user_data['email'],
                'status' => $status_baru,
                'admin_note' => ''
            ];
            
            $emailSent = $emailService->sendStatusUpdateNotification($emailData);
            
            if ($emailSent) {
                error_log("Status update email sent for transaction: $id_transaksi");
            } else {
                error_log("Failed to send status update email for transaction: $id_transaksi");
            }
        }
    } catch (Exception $emailException) {
        // Don't fail the transaction if email fails
        error_log("Status update email notification error: " . $emailException->getMessage());
    }
        
    // Set pesan sukses
        $pesan_stok = '';
        if ($status_baru == 'Disetujui' && $status_lama != 'Disetujui') {
            $pesan_stok = ' Stok barang telah dikurangi sebanyak ' . $jumlah . ' unit.';
        } elseif ($status_baru == 'Ditolak' && $status_lama == 'Disetujui') {
            $pesan_stok = ' Stok barang telah dikembalikan sebanyak ' . $jumlah . ' unit.';
        }
        
        $_SESSION['pesan'] = '<div class="alert alert-notify alert-success alert-dismissible fade show" role="alert">
                                <span data-notify="icon" class="fas fa-check"></span>
                                <span data-notify="title" class="text-success">Berhasil!</span> 
                                <span data-notify="message">Status transaksi ' . $id_transaksi . ' berhasil diubah ke "' . $status_baru . '".' . $pesan_stok . '</span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>';
        
        // Redirect ke halaman barang keluar
        header('Location: ../../main.php?module=barang_keluar');
        exit;
        
    } catch (Exception $e) {
        // Rollback jika ada error
        mysqli_rollback($mysqli);
        // Pastikan autocommit dikembalikan sebelum redirect
        mysqli_autocommit($mysqli, TRUE);
        
        $_SESSION['pesan'] = '<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
                                <span data-notify="icon" class="fas fa-times"></span>
                                <span data-notify="title" class="text-danger">Gagal!</span> 
                                <span data-notify="message">' . $e->getMessage() . '</span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>';
        
        header('Location: ../../main.php?module=barang_keluar');
        exit;
    }
    
    // // Kembalikan autocommit (jika belum dikembalikan sebelumnya)
    // if (mysqli_autocommit($mysqli) === FALSE) {
    //     mysqli_autocommit($mysqli, TRUE);
    // }
}

// ===== HANDLER UNTUK POST REQUEST (form update status) =====
if (isset($_POST['update_status'])) {
    $id_transaksi = mysqli_real_escape_string($mysqli, $_POST['id_transaksi']);
    $status_baru = mysqli_real_escape_string($mysqli, $_POST['status']);
    $catatan = isset($_POST['catatan']) ? mysqli_real_escape_string($mysqli, $_POST['catatan']) : '';
    
    // Mulai transaksi database
    mysqli_autocommit($mysqli, FALSE);
    
    try {
        // Ambil data transaksi saat ini
        $query_current = mysqli_query($mysqli, 
            "SELECT bk.*, b.stok, b.nama_barang 
             FROM tbl_barang_keluar bk 
             JOIN tbl_barang b ON bk.barang = b.id_barang 
             WHERE bk.id_transaksi = '$id_transaksi'"
        );
        
        if (!$query_current) {
            throw new Exception('Gagal mengambil data transaksi: ' . mysqli_error($mysqli));
        }
        
        $data_current = mysqli_fetch_assoc($query_current);
        
        if (!$data_current) {
            throw new Exception('Data transaksi tidak ditemukan');
        }
        
        $status_lama = $data_current['status'];
        $id_barang = $data_current['barang'];
        $jumlah = $data_current['jumlah'];
        $stok_sekarang = $data_current['stok'];
        
        // Logika perubahan stok berdasarkan perubahan status
        $perlu_update_stok = false;
        $operasi_stok = '';
        $stok_baru = $stok_sekarang;
        
        // Status yang mempengaruhi stok
        $status_approved = ['Disetujui', 'Selesai', 'approved'];
        $status_rejected = ['Ditolak', 'cancelled', 'ditolak'];
        $status_pending = ['Menunggu Persetujuan', 'pending'];
        
        // Cek apakah status lama adalah approved
        $was_approved = in_array($status_lama, $status_approved);
        // Cek apakah status baru adalah approved  
        $is_approved = in_array($status_baru, $status_approved);
        // Cek apakah status baru adalah rejected
        $is_rejected = in_array($status_baru, $status_rejected);
        
        // Logika perubahan stok:
        if ($was_approved && $is_rejected) {
            // Dari Approved ke Rejected: Kembalikan stok
            $stok_baru = $stok_sekarang + $jumlah;
            $operasi_stok = 'dikembalikan';
            $perlu_update_stok = true;
            
        } elseif ($was_approved && in_array($status_baru, $status_pending)) {
            // Dari Approved ke Pending: Kembalikan stok
            $stok_baru = $stok_sekarang + $jumlah;
            $operasi_stok = 'dikembalikan';
            $perlu_update_stok = true;
            
        } elseif (!$was_approved && $is_approved) {
            // Dari Non-Approved ke Approved: Kurangi stok
            if ($stok_sekarang < $jumlah) {
                throw new Exception('Stok tidak mencukupi. Stok tersedia: ' . $stok_sekarang);
            }
            $stok_baru = $stok_sekarang - $jumlah;
            $operasi_stok = 'dikurangi';
            $perlu_update_stok = true;
        }
        
        // Update status transaksi
        $query_update_status = "UPDATE tbl_barang_keluar 
                               SET status = '$status_baru'";
        
        if (!empty($catatan)) {
            $query_update_status .= ", catatan = '$catatan'";
        }
        
        if ($is_rejected) {
            $query_update_status .= ", tanggal_penolakan = NOW()";
        } elseif ($is_approved) {
            $query_update_status .= ", tanggal_persetujuan = NOW()";
        }
        
        $query_update_status .= " WHERE id_transaksi = '$id_transaksi'";
        
        $update_status = mysqli_query($mysqli, $query_update_status);
        
        if (!$update_status) {
            throw new Exception('Gagal mengupdate status: ' . mysqli_error($mysqli));
        }
        
        // Update stok jika diperlukan
        if ($perlu_update_stok) {
            $query_update_stok = "UPDATE tbl_barang 
                                 SET stok = '$stok_baru' 
                                 WHERE id_barang = '$id_barang'";
            
            $update_stok = mysqli_query($mysqli, $query_update_stok);
            
            if (!$update_stok) {
                throw new Exception('Gagal mengupdate stok: ' . mysqli_error($mysqli));
            }
        }
        
    // Commit transaksi
    mysqli_commit($mysqli);
    // Pastikan autocommit dikembalikan sebelum redirect
    mysqli_autocommit($mysqli, TRUE);
        
    $pesan_stok = $perlu_update_stok ? " Stok barang telah $operasi_stok sebanyak $jumlah unit." : '';
        
    $_SESSION['pesan'] = '<div class="alert alert-notify alert-success alert-dismissible fade show" role="alert">
                                <span data-notify="icon" class="fas fa-check"></span>
                                <span data-notify="title" class="text-success">Berhasil!</span> 
                                <span data-notify="message">Status transaksi berhasil diubah ke "' . $status_baru . '".' . $pesan_stok . '</span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>';
        
        header('Location: ../../main.php?module=barang_keluar');
        exit;
        
    } catch (Exception $e) {
        // Rollback jika ada error
        mysqli_rollback($mysqli);
        // Pastikan autocommit dikembalikan sebelum redirect
        mysqli_autocommit($mysqli, TRUE);
        
        $_SESSION['pesan'] = '<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
                                <span data-notify="icon" class="fas fa-times"></span>
                                <span data-notify="title" class="text-danger">Gagal!</span> 
                                <span data-notify="message">' . $e->getMessage() . '</span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>';
        
        header('Location: ../../main.php?module=barang_keluar');
        exit;
    }
    
    // Kembalikan autocommit
    // mysqli_autocommit($mysqli, TRUE);
}

// ===== HANDLER UNTUK POST REQUEST (existing code) =====
if (isset($_POST['update_status'])) {
    $id_transaksi = mysqli_real_escape_string($mysqli, $_POST['id_transaksi']);
    $status_baru = mysqli_real_escape_string($mysqli, $_POST['status']);
    $catatan = isset($_POST['catatan']) ? mysqli_real_escape_string($mysqli, $_POST['catatan']) : '';
    
    // Mulai transaksi database
    mysqli_autocommit($mysqli, FALSE);
    
    try {
        // Ambil data transaksi saat ini
        $query_current = mysqli_query($mysqli, 
            "SELECT bk.*, b.stok, b.nama_barang 
             FROM tbl_barang_keluar bk 
             JOIN tbl_barang b ON bk.barang = b.id_barang 
             WHERE bk.id_transaksi = '$id_transaksi'"
        );
        
        if (!$query_current) {
            throw new Exception('Gagal mengambil data transaksi: ' . mysqli_error($mysqli));
        }
        
        $data_current = mysqli_fetch_assoc($query_current);
        
        if (!$data_current) {
            throw new Exception('Data transaksi tidak ditemukan');
        }
        
        $status_lama = $data_current['status'];
        $id_barang = $data_current['barang'];
        $jumlah = $data_current['jumlah'];
        $stok_sekarang = $data_current['stok'];
        
        // Logika perubahan stok berdasarkan perubahan status
        $perlu_update_stok = false;
        $operasi_stok = '';
        $stok_baru = $stok_sekarang;
        
        // Status yang mempengaruhi stok
        $status_approved = ['Disetujui', 'Selesai', 'approved'];
        $status_rejected = ['Ditolak', 'cancelled', 'ditolak'];
        $status_pending = ['Menunggu Persetujuan', 'pending'];
        
        // Cek apakah status lama adalah approved
        $was_approved = in_array($status_lama, $status_approved);
        // Cek apakah status baru adalah approved  
        $is_approved = in_array($status_baru, $status_approved);
        // Cek apakah status baru adalah rejected
        $is_rejected = in_array($status_baru, $status_rejected);
        
        // Logika perubahan stok:
        if ($was_approved && $is_rejected) {
            // Dari Approved ke Rejected: Kembalikan stok
            $stok_baru = $stok_sekarang + $jumlah;
            $operasi_stok = 'dikembalikan';
            $perlu_update_stok = true;
            
        } elseif ($was_approved && in_array($status_baru, $status_pending)) {
            // Dari Approved ke Pending: Kembalikan stok
            $stok_baru = $stok_sekarang + $jumlah;
            $operasi_stok = 'dikembalikan';
            $perlu_update_stok = true;
            
        } elseif (!$was_approved && $is_approved) {
            // Dari Non-Approved ke Approved: Kurangi stok
            if ($stok_sekarang < $jumlah) {
                throw new Exception('Stok tidak mencukupi. Stok tersedia: ' . $stok_sekarang);
            }
            $stok_baru = $stok_sekarang - $jumlah;
            $operasi_stok = 'dikurangi';
            $perlu_update_stok = true;
        }
        
        // Update status transaksi
        $query_update_status = "UPDATE tbl_barang_keluar 
                               SET status = '$status_baru'";
        
        if (!empty($catatan)) {
            $query_update_status .= ", catatan = '$catatan'";
        }
        
        if ($is_rejected) {
            $query_update_status .= ", tanggal_penolakan = NOW()";
        } elseif ($is_approved) {
            $query_update_status .= ", tanggal_persetujuan = NOW()";
        }
        
        $query_update_status .= " WHERE id_transaksi = '$id_transaksi'";
        
        $update_status = mysqli_query($mysqli, $query_update_status);
        
        if (!$update_status) {
            throw new Exception('Gagal mengupdate status: ' . mysqli_error($mysqli));
        }
        
        // Update stok jika diperlukan
        if ($perlu_update_stok) {
            $query_update_stok = "UPDATE tbl_barang 
                                 SET stok = '$stok_baru' 
                                 WHERE id_barang = '$id_barang'";
            
            $update_stok = mysqli_query($mysqli, $query_update_stok);
            
            if (!$update_stok) {
                throw new Exception('Gagal mengupdate stok: ' . mysqli_error($mysqli));
            }
        }
        
        // Commit transaksi
        mysqli_commit($mysqli);
        
        $pesan_stok = $perlu_update_stok ? " Stok barang telah $operasi_stok sebanyak $jumlah unit." : '';
        
        $_SESSION['pesan'] = '<div class="alert alert-notify alert-success alert-dismissible fade show" role="alert">
                                <span data-notify="icon" class="fas fa-check"></span>
                                <span data-notify="title" class="text-success">Berhasil!</span> 
                                <span data-notify="message">Status transaksi berhasil diubah ke "' . $status_baru . '".' . $pesan_stok . '</span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>';
        
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
    }
    
    // Kembalikan autocommit
    mysqli_autocommit($mysqli, TRUE);
    
    header('Location: ../../main.php?module=barang_keluar');
    exit;
}

// Jika tidak ada POST atau GET yang valid
if (!isset($_GET['id']) && !isset($_POST['update_status'])) {
    header('Location: ../../main.php?module=barang_keluar');
    exit;
}
?>