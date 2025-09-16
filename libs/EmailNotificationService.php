<?php
/**
 * Email Notification Service for Gudang System
 * Handles sending email notifications for outgoing goods transactions
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/email.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailNotificationService {
    
    private $mailer;
    
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->configureSMTP();
    }
    
    private function configureSMTP() {
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host       = SMTP_HOST;
            $this->mailer->SMTPAuth   = true;
            $this->mailer->Username   = SMTP_USERNAME;
            $this->mailer->Password   = SMTP_PASSWORD;
            $this->mailer->SMTPSecure = SMTP_ENCRYPTION;
            $this->mailer->Port       = SMTP_PORT;
            
            // Sender info
            $this->mailer->setFrom(FROM_EMAIL, FROM_NAME);
            $this->mailer->addReplyTo(REPLY_TO_EMAIL, FROM_NAME);
            
            // Content settings
            $this->mailer->isHTML(true);
            $this->mailer->CharSet = 'UTF-8';
            
        } catch (Exception $e) {
            error_log("Email configuration error: " . $e->getMessage());
        }
    }
    
    public function sendOutgoingGoodsNotification($transactionData) {
        try {
            // Clear any previous recipients
            $this->mailer->clearAddresses();
            
            // Add recipients
            if (!empty($transactionData['user_email'])) {
                $this->mailer->addAddress($transactionData['user_email'], $transactionData['user_name']);
            }
            
            // Add admin as CC
            $this->mailer->addCC(ADMIN_EMAIL, 'Admin Gudang');
            
            // Email subject
            $jenis = $transactionData['jenis'];
            $subject = "Notifikasi Barang {$jenis} - {$transactionData['id_transaksi']}";
            $this->mailer->Subject = $subject;
            
            // Email body
            $this->mailer->Body = $this->generateEmailBody($transactionData);
            $this->mailer->AltBody = $this->generatePlainTextBody($transactionData);
            
            // Send email
            $result = $this->mailer->send();
            
            if ($result) {
                error_log("Email sent successfully for transaction: " . $transactionData['id_transaksi']);
                return true;
            } else {
                error_log("Email sending failed for transaction: " . $transactionData['id_transaksi']);
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Email error: " . $e->getMessage());
            return false;
        }
    }
    
    public function sendStatusUpdateNotification($transactionData) {
        try {
            // Clear any previous recipients
            $this->mailer->clearAddresses();
            
            // Add recipients
            if (!empty($transactionData['user_email'])) {
                $this->mailer->addAddress($transactionData['user_email'], $transactionData['user_name']);
            }
            
            // Add admin as CC
            $this->mailer->addCC(ADMIN_EMAIL, 'Admin Gudang');
            
            // Email subject
            $status = $transactionData['status'];
            $subject = "Update Status Transaksi {$status} - {$transactionData['id_transaksi']}";
            $this->mailer->Subject = $subject;
            
            // Email body
            $this->mailer->Body = $this->generateStatusUpdateEmailBody($transactionData);
            $this->mailer->AltBody = $this->generateStatusUpdatePlainTextBody($transactionData);
            
            // Send email
            $result = $this->mailer->send();
            
            if ($result) {
                error_log("Status update email sent successfully for transaction: " . $transactionData['id_transaksi']);
                return true;
            } else {
                error_log("Status update email sending failed for transaction: " . $transactionData['id_transaksi']);
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Status update email error: " . $e->getMessage());
            return false;
        }
    }
    
    private function generateEmailBody($data) {
        $jenis = $data['jenis'];
        $actionText = ($jenis == 'Pinjam') ? 'Peminjaman' : 'Pengeluaran';
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Notifikasi Barang ' . $jenis . '</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f5f5f5; }
                .container { max-width: 600px; margin: 0 auto; background-color: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { padding: 30px; }
                .transaction-info { background-color: #f8f9fa; padding: 20px; border-radius: 6px; margin: 20px 0; }
                .info-row { display: flex; justify-content: space-between; margin: 10px 0; padding: 8px 0; border-bottom: 1px solid #eee; }
                .info-label { font-weight: bold; color: #333; }
                .info-value { color: #666; }
                .status-badge { display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
                .status-pending { background-color: #fff3cd; color: #856404; }
                .button-container { text-align: center; margin: 30px 0; }
                .cta-button { 
                    display: inline-block; 
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                    color: white; 
                    text-decoration: none; 
                    padding: 15px 30px; 
                    border-radius: 25px; 
                    font-weight: bold; 
                    font-size: 16px;
                    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
                    transition: all 0.3s ease;
                }
                .cta-button:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4); }
                .footer { background-color: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 12px; border-radius: 0 0 8px 8px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🏪 Sistem Gudang Material</h1>
                    <p>Notifikasi ' . $actionText . ' Barang</p>
                </div>
                
                <div class="content">
                    <h2>Halo, ' . htmlspecialchars($data['user_name']) . '!</h2>
                    <p>Transaksi barang ' . strtolower($jenis) . ' Anda telah berhasil dibuat dan sedang menunggu persetujuan.</p>
                    
                    <div class="transaction-info">
                        <h3>Detail Transaksi</h3>
                        <div class="info-row">
                            <span class="info-label">ID Transaksi:</span>
                            <span class="info-value"><strong>' . htmlspecialchars($data['id_transaksi']) . '</strong></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tanggal:</span>
                            <span class="info-value">' . date('d F Y', strtotime($data['tanggal'])) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Barang:</span>
                            <span class="info-value">' . htmlspecialchars($data['nama_barang']) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Jumlah:</span>
                            <span class="info-value">' . number_format($data['jumlah']) . ' ' . htmlspecialchars($data['satuan']) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Jenis:</span>
                            <span class="info-value">' . htmlspecialchars($jenis) . '</span>
                        </div>
                        ' . (!empty($data['serial_number']) ? '
                        <div class="info-row">
                            <span class="info-label">Serial Number:</span>
                            <span class="info-value">' . htmlspecialchars($data['serial_number']) . '</span>
                        </div>' : '') . '
                        <div class="info-row">
                            <span class="info-label">Status:</span>
                            <span class="info-value"><span class="status-badge status-pending">Menunggu Persetujuan</span></span>
                        </div>
                    </div>
                    
                    <div class="button-container">
                        <a href="' . BASE_URL . '/main.php?module=barang_keluar" class="cta-button">
                            📋 Lihat Detail Transaksi
                        </a>
                    </div>
                    
                    <p><strong>Catatan:</strong> Transaksi Anda akan diproses oleh admin gudang. Anda akan menerima notifikasi lanjutan setelah transaksi disetujui atau ditolak.</p>
                </div>
                
                <div class="footer">
                    <p>Email ini dikirim secara otomatis dari Sistem Gudang Material.</p>
                    <p>Jika Anda memiliki pertanyaan, silakan hubungi admin gudang.</p>
                    <p>&copy; ' . date('Y') . ' Sistem Gudang Material. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    private function generatePlainTextBody($data) {
        $jenis = $data['jenis'];
        $actionText = ($jenis == 'Pinjam') ? 'Peminjaman' : 'Pengeluaran';
        
        $text = "SISTEM GUDANG MATERIAL\n";
        $text .= "Notifikasi {$actionText} Barang\n\n";
        $text .= "Halo, {$data['user_name']}!\n\n";
        $text .= "Transaksi barang " . strtolower($jenis) . " Anda telah berhasil dibuat dan sedang menunggu persetujuan.\n\n";
        $text .= "DETAIL TRANSAKSI:\n";
        $text .= "ID Transaksi: {$data['id_transaksi']}\n";
        $text .= "Tanggal: " . date('d F Y', strtotime($data['tanggal'])) . "\n";
        $text .= "Barang: {$data['nama_barang']}\n";
        $text .= "Jumlah: " . number_format($data['jumlah']) . " {$data['satuan']}\n";
        $text .= "Jenis: {$jenis}\n";
        if (!empty($data['serial_number'])) {
            $text .= "Serial Number: {$data['serial_number']}\n";
        }
        $text .= "Status: Menunggu Persetujuan\n\n";
        $text .= "Untuk melihat detail transaksi, kunjungi: " . BASE_URL . "/main.php?module=barang_keluar\n\n";
        $text .= "Catatan: Transaksi Anda akan diproses oleh admin gudang. Anda akan menerima notifikasi lanjutan setelah transaksi disetujui atau ditolak.\n\n";
        $text .= "Email ini dikirim secara otomatis dari Sistem Gudang Material.";
        
        return $text;
    }
    
    private function generateStatusUpdateEmailBody($data) {
        $status = $data['status'];
        $statusColor = ($status == 'Disetujui') ? '#4caf50' : (($status == 'Ditolak') ? '#f44336' : '#ff9800');
        $statusIcon = ($status == 'Disetujui') ? '✅' : (($status == 'Ditolak') ? '❌' : '⏳');
        $statusMessage = ($status == 'Disetujui') ? 'Transaksi Anda telah disetujui!' : (($status == 'Ditolak') ? 'Transaksi Anda ditolak.' : 'Status transaksi Anda telah diperbarui.');
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Update Status Transaksi - ' . $status . '</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f5f5f5; }
                .container { max-width: 600px; margin: 0 auto; background-color: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { padding: 30px; }
                .status-banner { text-align: center; padding: 20px; margin: 20px 0; border-radius: 8px; }
                .status-approved { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
                .status-rejected { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
                .status-other { background-color: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
                .transaction-info { background-color: #f8f9fa; padding: 20px; border-radius: 6px; margin: 20px 0; }
                .info-row { display: flex; justify-content: space-between; margin: 10px 0; padding: 8px 0; border-bottom: 1px solid #eee; }
                .info-label { font-weight: bold; color: #333; }
                .info-value { color: #666; }
                .status-badge { display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; color: white; background-color: ' . $statusColor . '; }
                .button-container { text-align: center; margin: 30px 0; }
                .cta-button { 
                    display: inline-block; 
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                    color: white; 
                    text-decoration: none; 
                    padding: 15px 30px; 
                    border-radius: 25px; 
                    font-weight: bold; 
                    font-size: 16px;
                    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
                    transition: all 0.3s ease;
                }
                .cta-button:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4); }
                .footer { background-color: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 12px; border-radius: 0 0 8px 8px; }
                .admin-note { background-color: #e9ecef; padding: 15px; border-radius: 6px; margin: 15px 0; border-left: 4px solid #6c757d; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🏪 Sistem Gudang Material</h1>
                    <p>Update Status Transaksi</p>
                </div>
                
                <div class="content">
                    <h2>Halo, ' . htmlspecialchars($data['user_name']) . '!</h2>
                    
                    <div class="status-banner ' . (($status == 'Disetujui') ? 'status-approved' : (($status == 'Ditolak') ? 'status-rejected' : 'status-other')) . '">
                        <h3>' . $statusIcon . ' ' . $statusMessage . '</h3>
                    </div>
                    
                    <div class="transaction-info">
                        <h3>Detail Transaksi</h3>
                        <div class="info-row">
                            <span class="info-label">ID Transaksi:</span>
                            <span class="info-value"><strong>' . htmlspecialchars($data['id_transaksi']) . '</strong></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tanggal:</span>
                            <span class="info-value">' . date('d F Y', strtotime($data['tanggal'])) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Barang:</span>
                            <span class="info-value">' . htmlspecialchars($data['nama_barang']) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Jumlah:</span>
                            <span class="info-value">' . number_format($data['jumlah']) . ' ' . htmlspecialchars($data['satuan']) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Jenis:</span>
                            <span class="info-value">' . htmlspecialchars($data['jenis']) . '</span>
                        </div>
                        ' . (!empty($data['serial_number']) ? '
                        <div class="info-row">
                            <span class="info-label">Serial Number:</span>
                            <span class="info-value">' . htmlspecialchars($data['serial_number']) . '</span>
                        </div>' : '') . '
                        <div class="info-row">
                            <span class="info-label">Status:</span>
                            <span class="info-value"><span class="status-badge">' . htmlspecialchars($status) . '</span></span>
                        </div>
                    </div>
                    
                    ' . (!empty($data['admin_note']) ? '
                    <div class="admin-note">
                        <strong>Catatan Admin:</strong><br>
                        ' . nl2br(htmlspecialchars($data['admin_note'])) . '
                    </div>' : '') . '
                    
                    <div class="button-container">
                        <a href="' . BASE_URL . '/main.php?module=barang_keluar" class="cta-button">
                            📋 Lihat Detail Transaksi
                        </a>
                    </div>
                    
                    ' . (($status == 'Disetujui') ? '<p><strong>Selamat!</strong> Transaksi Anda telah disetujui dan barang dapat diambil sesuai prosedur yang berlaku.</p>' : 
                        (($status == 'Ditolak') ? '<p>Mohon maaf, transaksi Anda tidak dapat disetujui. Silakan hubungi admin gudang untuk informasi lebih lanjut.</p>' : 
                        '<p>Status transaksi Anda telah diperbarui. Silakan cek sistem untuk informasi lebih lanjut.</p>')) . '
                </div>
                
                <div class="footer">
                    <p>Email ini dikirim secara otomatis dari Sistem Gudang Material.</p>
                    <p>Jika Anda memiliki pertanyaan, silakan hubungi admin gudang.</p>
                    <p>&copy; ' . date('Y') . ' Sistem Gudang Material. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    private function generateStatusUpdatePlainTextBody($data) {
        $status = $data['status'];
        $statusIcon = ($status == 'Disetujui') ? '[DISETUJUI]' : (($status == 'Ditolak') ? '[DITOLAK]' : '[UPDATE]');
        
        $text = "SISTEM GUDANG MATERIAL\n";
        $text .= "Update Status Transaksi\n\n";
        $text .= "Halo, {$data['user_name']}!\n\n";
        $text .= "{$statusIcon} Status transaksi Anda telah diperbarui ke: {$status}\n\n";
        $text .= "DETAIL TRANSAKSI:\n";
        $text .= "ID Transaksi: {$data['id_transaksi']}\n";
        $text .= "Tanggal: " . date('d F Y', strtotime($data['tanggal'])) . "\n";
        $text .= "Barang: {$data['nama_barang']}\n";
        $text .= "Jumlah: " . number_format($data['jumlah']) . " {$data['satuan']}\n";
        $text .= "Jenis: {$data['jenis']}\n";
        if (!empty($data['serial_number'])) {
            $text .= "Serial Number: {$data['serial_number']}\n";
        }
        $text .= "Status: {$status}\n\n";
        
        if (!empty($data['admin_note'])) {
            $text .= "Catatan Admin: {$data['admin_note']}\n\n";
        }
        
        $text .= "Untuk melihat detail transaksi, kunjungi: " . BASE_URL . "/main.php?module=barang_keluar\n\n";
        
        if ($status == 'Disetujui') {
            $text .= "Selamat! Transaksi Anda telah disetujui dan barang dapat diambil sesuai prosedur yang berlaku.\n\n";
        } elseif ($status == 'Ditolak') {
            $text .= "Mohon maaf, transaksi Anda tidak dapat disetujui. Silakan hubungi admin gudang untuk informasi lebih lanjut.\n\n";
        }
        
        $text .= "Email ini dikirim secara otomatis dari Sistem Gudang Material.";
        
        return $text;
    }
}
?>