<?php
ob_start();
require_once __DIR__ . "/../../assets/js/plugin/dompdf/autoload.inc.php";
use Dompdf\Dompdf;

// koneksi
$mysqli = mysqli_connect("localhost","root","","gudang");

// fungsi hari indonesia
function getHariIndonesia($tanggal) {
    $hariInggris = date('l', strtotime($tanggal));
    $hariIndonesia = [
        'Sunday'    => 'Minggu',
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu',
    ];
    return $hariIndonesia[$hariInggris] ?? $hariInggris;
}

// fungsi tanggal indo
function tanggal_indo($tanggal) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $split = explode('-', $tanggal);
    return intval($split[2]) . ' ' . $bulan[intval($split[1])] . ' ' . $split[0];
}

// ambil id transaksi (sanitasi)
$id = isset($_GET['id']) ? mysqli_real_escape_string($mysqli, $_GET['id']) : '';

// query dengan id_barang untuk serial number

$query = mysqli_query($mysqli, "
    SELECT k.id_transaksi, k.tanggal, k.jumlah, k.pemohon, k.jenis, k.status,
           k.serial_number as transaksi_serial_number,
           b.nama_barang, b.foto, b.id_barang, b.serial_number as barang_serial_number
    FROM tbl_barang_keluar AS k
    JOIN tbl_barang AS b ON k.barang = b.id_barang
    WHERE k.id_transaksi = '$id'
") or die('Query error: '.mysqli_error($mysqli));

$data = mysqli_fetch_assoc($query);

// ambil foto base64
$fotoHtml = "<span style='color:#888'>Tidak ada</span>";
if (!empty($data['foto'])) {
    $path = __DIR__ . "/../../images/" . $data['foto'];
    if (file_exists($path)) {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = ($ext === 'jpg') ? 'jpeg' : $ext;
        $dataImg = @file_get_contents($path);
        if ($dataImg !== false) {
            $base64 = 'data:image/' . $mime . ';base64,' . base64_encode($dataImg);
            $fotoHtml = "<img src='{$base64}' width='100' height='100' style='object-fit:contain;'>";
        } else {
            $fotoHtml = "<span style='color:#888'>Gagal membaca file gambar</span>";
        }
    }
}

// ambil barcode base64
// Tentukan serial number yang akan dipakai
$barcodeHtml = "<span style='color:#888'>Barcode tidak tersedia</span>";
$sn = !empty($data['transaksi_serial_number']) ? $data['transaksi_serial_number'] : (!empty($data['barang_serial_number']) ? $data['barang_serial_number'] : $data['id_barang']);

// Generate barcode by including local libs/barcode.php and capturing its output (binary PNG)
$barcodeHtml = "<span style='color:#888'>Barcode tidak tersedia</span>";
$barcode_png = null;
$barcode_file = __DIR__ . '/../../libs/barcode.php';
if (file_exists($barcode_file)) {
    // prepare GET params for barcode script
    $_GET_BACKUP = $_GET;
    $_GET['text'] = $sn;
    $_GET['codetype'] = 'code128';
    $_GET['size'] = '30';
    $_GET['print'] = 'true';

    ob_start();
    include $barcode_file; // barcode.php will output PNG binary
    $barcode_png = ob_get_clean();

    // restore original GET
    $_GET = $_GET_BACKUP;

    if ($barcode_png !== false && strlen($barcode_png) > 0) {
        $barcode_base64 = 'data:image/png;base64,' . base64_encode($barcode_png);
        $barcodeHtml = "<img src='{$barcode_base64}' width='120' height='30'>";
    }
}
if ($barcodeHtml === "<span style='color:#888'>Barcode tidak tersedia</span>") {
    $barcodeHtml = "<div style='border: 1px solid #ccc; padding: 5px; text-align: center; font-family: monospace; background: #f9f9f9;'>{$sn}</div>";
}

// format hari & tanggal
// pastikan data transaksi berhasil diambil
if (!$data) {
    die('Data transaksi tidak ditemukan');
}

$hari = getHariIndonesia($data['tanggal']);
$tanggal_formatted = tanggal_indo($data['tanggal']);
$tanggal_cetak = tanggal_indo(date('Y-m-d'));

// isi PDF
$html = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td, th { padding: 8px; border: 1px solid #333; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .center { text-align: center; }
        .header { text-align: center; margin-bottom: 20px; }
        .serial-row td { vertical-align: middle; }
    </style>
</head>
<body>
    <div class='header'>
        <h2>Laporan Transaksi Barang Keluar</h2>
        <hr style='border: 1px solid #333;'>
    </div>

    <table>
        <tr><th width='25%'>ID Transaksi</th><td>{$data['id_transaksi']}</td></tr>
        <tr><th>Tanggal</th><td>{$hari}, {$tanggal_formatted}</td></tr>
        <tr><th>Nama Barang</th><td>{$data['nama_barang']}</td></tr>
        <tr class='serial-row'>
            <th>Serial Number</th>
            <td>
                <strong>{$sn}</strong><br>
                {$barcodeHtml}
            </td>
        </tr>
        <tr><th>Foto Barang</th><td>{$fotoHtml}</td></tr>
        <tr><th>Jumlah</th><td>{$data['jumlah']}</td></tr>
        <tr><th>Jenis</th><td>{$data['jenis']}</td></tr>
        <tr><th>Status</th><td>{$data['status']}</td></tr>
    </table>
    
     <div style='margin-top: 30px; text-align: right;'>
         <p>Dicetak pada: {$tanggal_cetak}</p>
     </div>
</body>
</html>
";

// panggil dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

ob_end_clean();
$dompdf->stream("transaksi_{$id}.pdf", ["Attachment" => 0]);
exit;
?>