<?php
session_start();

// include autoloader dompdf
require_once("../../assets/js/plugin/dompdf/autoload.inc.php");
use Dompdf\Dompdf;

// cek session login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    header('location: ../../login.php?pesan=2');
    exit;
} else {
    require_once "../../config/database.php";

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

    // fungsi tanggal indo (format: 18 Agustus 2025)
    function tanggal_indo($tanggal) {
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $split = explode('-', $tanggal);
        return intval($split[2]) . ' ' . $bulan[intval($split[1])] . ' ' . $split[0];
    }

    // ambil parameter tanggal dari URL
    $tanggal_awal  = $_GET['tanggal_awal'];
    $tanggal_akhir = $_GET['tanggal_akhir'];

    // format tanggal untuk query
    $tanggal_awal_db  = date('Y-m-d', strtotime($tanggal_awal));
    $tanggal_akhir_db = date('Y-m-d', strtotime($tanggal_akhir));

    $dompdf = new Dompdf();
    $options = $dompdf->getOptions();
    $options->setIsRemoteEnabled(true);
    // sesuaikan dengan folder root project kamu
    $options->setChroot('C:/xampp/htdocs/gudang');
    $dompdf->setOptions($options);

    $html = '<!DOCTYPE html>
    <html>
    <head>
        <title>Laporan Data Barang Masuk</title>
        <style>
            body { font-family: Arial, sans-serif; font-size: 12px; color: #000; }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .table { border-collapse: collapse; width: 100%; }
            .table th, .table td { border: 1px solid #000; padding: 5px; }
            .bg-secondary { background-color: #6c757d; color: #fff; }
            .mt-4 { margin-top: 1rem; }
            .mt-5 { margin-top: 1.5rem; }
            img { object-fit: contain; }
        </style>
    </head>
    <body>
        <div class="text-center">
            <h2>LAPORAN DATA BARANG MASUK</h2>
            <span>Tanggal ' . htmlspecialchars($tanggal_awal) . ' s.d. ' . htmlspecialchars($tanggal_akhir) . '</span>
        </div>
        <hr>
        <div class="mt-4">
            <table class="table">
                <thead class="bg-secondary text-center">
                    <tr>
                        <th>No.</th>
                        <th>ID Transaksi</th>
                        <th>Tanggal (Hari)</th>
                        <th>Barang</th>
                        <th>Foto</th>
                        <th>Jumlah Barang Masuk</th>
                        <th>Satuan</th>
                    </tr>
                </thead>
                <tbody>';

    $no = 1;
    $query = mysqli_query($mysqli, "SELECT a.id_transaksi, a.tanggal, a.barang, a.jumlah, 
                                            b.nama_barang, b.foto, c.nama_satuan
                                     FROM tbl_barang_masuk as a 
                                     INNER JOIN tbl_barang as b ON a.barang=b.id_barang
                                     INNER JOIN tbl_satuan as c ON b.satuan=c.id_satuan
                                     WHERE a.tanggal BETWEEN '$tanggal_awal_db' AND '$tanggal_akhir_db' 
                                     ORDER BY a.id_transaksi ASC")
        or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));

    while ($data = mysqli_fetch_assoc($query)) {
        $hari = getHariIndonesia($data['tanggal']);
        $tanggal_formatted = date('d-m-Y', strtotime($data['tanggal']));

        // ambil foto
        $path = __DIR__ . "/../../images/" . $data['foto'];
        if (!empty($data['foto']) && file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $dataImg = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImg);
            $foto = "<img src='{$base64}' width='50' height='50'>";
        } else {
            $foto = "<span style=\"color:#888;\">-</span>";
        }

        $html .= '<tr>
            <td class="text-center">' . $no++ . '</td>
            <td class="text-center">' . htmlspecialchars($data['id_transaksi']) . '</td>
            <td class="text-center">' . $hari . ', ' . $tanggal_formatted . '</td>
            <td>' . htmlspecialchars($data['barang']) . ' - ' . htmlspecialchars($data['nama_barang']) . '</td>
            <td class="text-center">' . $foto . '</td>
            <td class="text-right">' . number_format($data['jumlah'], 0, '', '.') . '</td>
            <td class="text-center">' . htmlspecialchars($data['nama_satuan']) . '</td>
        </tr>';
    }

    $html .= '</tbody>
            </table>
        </div>
        <div class="text-right mt-5">' . getHariIndonesia(date('Y-m-d')) . ', ' . tanggal_indo(date('Y-m-d')) . '</div>
    </body>
    </html>';

    // load HTML ke Dompdf
    ob_end_clean();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream('Laporan_Data_Barang_Masuk.pdf', ['Attachment' => 0]);
}
?>