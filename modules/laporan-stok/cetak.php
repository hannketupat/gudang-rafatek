<?php
session_start();      // mengaktifkan session

require_once("../../assets/js/plugin/dompdf/autoload.inc.php");


use Dompdf\Dompdf;


if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
  header('location: ../../login.php?pesan=2');
} else {
  require_once "../../config/database.php";
  require_once "../../helper/fungsi_tanggal_indo.php";

  $stok = $_GET['stok'];
  $no = 1;

  $dompdf = new Dompdf();
  $options = $dompdf->getOptions();
  $options->setIsRemoteEnabled(true);
  $options->setChroot('C:\xampp\htdocs\gudang');
  $dompdf->setOptions($options);

  if ($stok == 'Seluruh') {
    $html = '
<!DOCTYPE html>
<html>
<head>
  <title>Laporan Stok Barang Seluruh</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 12px; }
    h1 { font-size: 18px; margin-bottom: 10px; }
    table { border-collapse: collapse; width: 100%; }
    table, th, td { border: 1px solid #000; }
    th, td { padding: 6px; }
    th { background: #f2f2f2; text-align: center; }
    td.text-center { text-align: center; }
    td.text-right { text-align: right; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .mt-5 { margin-top: 40px; }
  </style>
</head>
<body class="text-dark">
  <div class="text-center mb-4">
    <h1>LAPORAN STOK BARANG SELURUH</h1>
  </div>
  <hr>
  <div class="mt-4">
    <table>
      <thead>
        <tr>
          <th>No.</th>
          <th>ID Barang</th>
          <th>Nama Barang</th>
          <th>Foto Barang</th>
          <th>Jenis Barang</th>
          <th>Stok</th>
          <th>Satuan</th>
        </tr>
      </thead>
      <tbody>';


      $query = mysqli_query($mysqli, "SELECT a.id_barang, a.nama_barang, a.jenis, a.stok, a.stok, a.satuan, a.foto, b.nama_jenis, c.nama_satuan
                                FROM tbl_barang as a 
                                INNER JOIN tbl_jenis as b ON a.jenis=b.id_jenis 
                                INNER JOIN tbl_satuan as c ON a.satuan=c.id_satuan 
                                WHERE a.stok<=a.stok
                                ORDER BY a.id_barang ASC")
                                or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));

      while ($data = mysqli_fetch_assoc($query)) {
        // Path asli gambar
        $path = __DIR__ . "/../../images/" . $data['foto'];

        // cek file ada & tidak kosong
        if (!empty($data['foto']) && file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $dataImg = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImg);
            $foto = '<img src="' . $base64 . '" width="60" height="60">';
        } else {
            $foto = '<span class="text-muted">Tidak ada</span>';
        }

        $html .= '
        <tr>
          <td class="text-center">' . $no++ . '</td>
          <td class="text-center">' . $data['id_barang'] . '</td>
          <td>' . $data['nama_barang'] . '</td>
          <td class="text-center">' . $foto . '</td>
          <td>' . $data['nama_jenis'] . '</td>
          <td class="text-right">' . $data['stok'] . '</td>
          <td>' . $data['nama_satuan'] . '</td>
        </tr>';
      }

      $html .= '
          </tbody>
        </table>
      </div>
      <div class="text-right mt-5">' . hari_indo(date('Y-m-d')) . ', ' . tanggal_indo(date('Y-m-d')) . '</div>
    </body>
    </html>';

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream('Laporan Stok Barang Seluruh.pdf', array('Attachment' => 0));
  
  } else {
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
      <title>Laporan Stok Barang Minimum</title>
      <link rel="stylesheet" href="../../assets/css/laporan.css">
    </head>
    <body class="text-dark">
      <div class="text-center mb-4">
        <h1>LAPORAN STOK BARANG YANG MENCAPAI BATAS MINIMUM</h1>
      </div>
      <hr>
      <div class="mt-4">
        <table class="table table-bordered" width="100%" cellspacing="0">
          <thead class="bg-secondary text-white text-center">
            <tr>
              <th>No.</th>
              <th>ID Barang</th>
              <th>Nama Barang</th>
              <th>Foto Barang</th>
              <th>Jenis Barang</th>
              <th>Stok</th>
              <th>Satuan</th>
            </tr>
          </thead>
          <tbody class="text-dark">';

    $query = mysqli_query($mysqli, "SELECT a.id_barang, a.nama_barang, a.jenis, a.stok_minimum, a.stok, a.satuan, a.foto, b.nama_jenis, c.nama_satuan
                                FROM tbl_barang as a 
                                INNER JOIN tbl_jenis as b ON a.jenis=b.id_jenis 
                                INNER JOIN tbl_satuan as c ON a.satuan=c.id_satuan 
                                WHERE a.stok<=a.stok_minimum 
                                ORDER BY a.id_barang ASC")
                                or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));

    while ($data = mysqli_fetch_assoc($query)) {
      // Path asli gambar
      $path = __DIR__ . "/../../images/" . $data['foto'];

      // cek file ada & tidak kosong
      if (!empty($data['foto']) && file_exists($path)) {
          $type = pathinfo($path, PATHINFO_EXTENSION);
          $dataImg = file_get_contents($path);
          $base64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImg);
          $foto = '<img src="' . $base64 . '" width="60" height="60">';
      } else {
          $foto = '<span class="text-muted">Tidak ada</span>';
      }

      $html .= '
        <tr>
          <td class="text-center">' . $no++ . '</td>
          <td class="text-center">' . $data['id_barang'] . '</td>
          <td>' . $data['nama_barang'] . '</td>
          <td class="text-center">' . $foto . '</td>
          <td>' . $data['nama_jenis'] . '</td>
          <td class="text-right">' . $data['stok'] . '</td>
          <td>' . $data['nama_satuan'] . '</td>
        </tr>';
    }

    $html .= '
          </tbody>
        </table>
      </div>
      <div class="text-right mt-5">' . hari_indo(date('Y-m-d')) . ', ' . tanggal_indo(date('Y-m-d')) . '</div>
    </body>
    </html>';

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream('Laporan Stok Barang Minimum.pdf', array('Attachment' => 0));
  }
}
?>
