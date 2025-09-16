<?php
// Mencegah direct access file PHP
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
  exit;
}

// Pastikan koneksi database sudah di-include sebelumnya dan variabel $mysqli siap digunakan
?>

<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-4">
    <div class="page-header text-white">
      <h4 class="page-title text-white"><i class="fas fa-file-export mr-2"></i> Laporan Barang Keluar</h4>
      <ul class="breadcrumbs">
        <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a>Laporan</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a>Barang Keluar</a></li>
      </ul>
    </div>
  </div>
</div>

<?php if (!isset($_POST['tampil'])): ?>
  <div class="page-inner mt--5">
    <div class="card">
      <div class="card-header">
        <div class="card-title">Filter Data Barang Keluar</div>
      </div>
      <div class="card-body">
        <form action="?module=laporan_barang_keluar" method="post" class="needs-validation" novalidate>
          <div class="row">
            <div class="col-lg-3">
              <div class="form-group">
                <label>Tanggal Awal <span class="text-danger">*</span></label>
                <input type="text" name="tanggal_awal" class="form-control date-picker" autocomplete="off" required>
                <div class="invalid-feedback">Tanggal awal tidak boleh kosong.</div>
              </div>
            </div>

            <div class="col-lg-3">
              <div class="form-group">
                <label>Tanggal Akhir <span class="text-danger">*</span></label>
                <input type="text" name="tanggal_akhir" class="form-control date-picker" autocomplete="off" required>
                <div class="invalid-feedback">Tanggal akhir tidak boleh kosong.</div>
              </div>
            </div>

            <div class="col-lg-2 pr-0">
              <div class="form-group pt-3">
                <input type="submit" name="tampil" value="Tampilkan" class="btn btn-secondary btn-round btn-block mt-4">
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

<?php else:
  // Ambil data filter
  $tanggal_awal  = $_POST['tanggal_awal'];
  $tanggal_akhir = $_POST['tanggal_akhir'];

  // Format tanggal ke Y-m-d untuk query
  $tgl_awal_db = date('Y-m-d', strtotime($tanggal_awal));
  $tgl_akhir_db = date('Y-m-d', strtotime($tanggal_akhir));

  // Query data barang keluar dengan foto
  $query = mysqli_query($mysqli, "SELECT a.id_transaksi, a.tanggal, a.barang, a.jumlah, b.nama_barang, b.foto, c.nama_satuan
                                  FROM tbl_barang_keluar AS a
                                  INNER JOIN tbl_barang AS b ON a.barang = b.id_barang
                                  INNER JOIN tbl_satuan AS c ON b.satuan = c.id_satuan
                                  WHERE a.tanggal BETWEEN '$tgl_awal_db' AND '$tgl_akhir_db'
                                  ORDER BY a.id_transaksi ASC") or die('Error: ' . mysqli_error($mysqli));
?>

  <div class="page-inner mt--5">
    <div class="card">
      <div class="card-header">
        <div class="card-title">Filter Data Barang Keluar</div>
      </div>
      <div class="card-body">
        <form action="?module=laporan_barang_keluar" method="post" class="needs-validation" novalidate>
          <div class="row">
            <div class="col-lg-3">
              <div class="form-group">
                <label>Tanggal Awal <span class="text-danger">*</span></label>
                <input type="text" name="tanggal_awal" class="form-control date-picker" autocomplete="off" value="<?php echo htmlspecialchars($tanggal_awal); ?>" required>
                <div class="invalid-feedback">Tanggal awal tidak boleh kosong.</div>
              </div>
            </div>

            <div class="col-lg-3">
              <div class="form-group">
                <label>Tanggal Akhir <span class="text-danger">*</span></label>
                <input type="text" name="tanggal_akhir" class="form-control date-picker" autocomplete="off" value="<?php echo htmlspecialchars($tanggal_akhir); ?>" required>
                <div class="invalid-feedback">Tanggal akhir tidak boleh kosong.</div>
              </div>
            </div>

            <div class="col-lg-2 pr-0">
              <div class="form-group pt-3">
                <input type="submit" name="tampil" value="Tampilkan" class="btn btn-secondary btn-round btn-block mt-4">
              </div>
            </div>

            <div class="col-lg-2 pr-0">
              <div class="form-group pt-3">
                <a href="modules/laporan-barang-keluar/cetak.php?tanggal_awal=<?php echo urlencode($tanggal_awal); ?>&tanggal_akhir=<?php echo urlencode($tanggal_akhir); ?>" target="_blank" class="btn btn-warning btn-round btn-block mt-4">
                  <span class="btn-label"><i class="fa fa-print mr-2"></i></span> Cetak
                </a>
              </div>
            </div>

            <div class="col-lg-2 pl-0">
              <div class="form-group pt-3">
                <a href="modules/laporan-barang-keluar/export.php?tanggal_awal=<?php echo urlencode($tanggal_awal); ?>&tanggal_akhir=<?php echo urlencode($tanggal_akhir); ?>" target="_blank" class="btn btn-success btn-round btn-block mt-4">
                  <span class="btn-label"><i class="fa fa-file-excel mr-2"></i></span> Export
                </a>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <div class="card-title">
          <i class="fas fa-file-alt mr-2"></i> Laporan Data Barang Keluar Tanggal <strong><?php echo htmlspecialchars($tanggal_awal); ?></strong> s.d. <strong><?php echo htmlspecialchars($tanggal_akhir); ?></strong>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
    <thead>
  <tr>
    <th class="text-center">No.</th>
    <th class="text-center">ID Transaksi</th>
    <th class="text-center">Tanggal</th>
    <th class="text-center">Barang</th>
    <th class="text-center">Foto</th>
    <th class="text-center">Jumlah Keluar</th>
    <th class="text-center">Satuan</th>
  </tr>
</thead>


<tbody>
  <?php
  // Fungsi untuk mengubah nama hari ke bahasa Indonesia
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

  $no = 1;
  while ($data = mysqli_fetch_assoc($query)) {
    $tanggal = $data['tanggal'];
    $hari = getHariIndonesia($tanggal);
  ?>
    <tr>
      <td class="text-center"><?php echo $no++; ?></td>
      <td class="text-center"><?php echo htmlspecialchars($data['id_transaksi']); ?></td>
      <td class="text-center"><?php echo $hari . ', ' . date('d-m-Y', strtotime($tanggal)); ?></td>
      <td><?php echo htmlspecialchars($data['nama_barang']); ?></td>
      <td class="text-center">
        <?php
        $foto_path = "images/" . $data['foto'];
        if (!empty($data['foto']) && file_exists($foto_path)) {
          echo '<img src="' . htmlspecialchars($foto_path) . '" width="60" height="60" style="object-fit: contain;">';
        } else {
          echo '<span class="text-muted">-</span>';
        }
        ?>
      </td>
      <td class="text-right"><?php echo number_format($data['jumlah'], 0, '', '.'); ?></td>
      <td><?php echo htmlspecialchars($data['nama_satuan']); ?></td>
    </tr>
  <?php } ?>
</tbody>



          </table>
        </div>
      </div>
    </div>
  </div>

<?php endif; ?>