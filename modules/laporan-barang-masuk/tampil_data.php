<?php
// Cegah akses langsung ke file ini
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
  exit;
}

// Fungsi untuk mengubah hari Inggris ke hari Indonesia
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
?>

<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-4">
    <div class="page-header text-white">
      <h4 class="page-title text-white"><i class="fas fa-file-import mr-2"></i> Laporan Barang Masuk</h4>
      <ul class="breadcrumbs">
        <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a>Laporan</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a>Barang Masuk</a></li>
      </ul>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Filter Data Barang Masuk</div>
    </div>
    <div class="card-body">
      <form action="?module=laporan_barang_masuk" method="post" class="needs-validation" novalidate>
        <div class="row">
          <div class="col-lg-3">
            <div class="form-group">
              <label>Tanggal Awal <span class="text-danger">*</span></label>
              <input type="text" name="tanggal_awal" class="form-control date-picker" autocomplete="off" required value="<?php echo isset($_POST['tanggal_awal']) ? htmlspecialchars($_POST['tanggal_awal']) : ''; ?>">
              <div class="invalid-feedback">Tanggal awal tidak boleh kosong.</div>
            </div>
          </div>

          <div class="col-lg-3">
            <div class="form-group">
              <label>Tanggal Akhir <span class="text-danger">*</span></label>
              <input type="text" name="tanggal_akhir" class="form-control date-picker" autocomplete="off" required value="<?php echo isset($_POST['tanggal_akhir']) ? htmlspecialchars($_POST['tanggal_akhir']) : ''; ?>">
              <div class="invalid-feedback">Tanggal akhir tidak boleh kosong.</div>
            </div>
          </div>

          <div class="col-lg-2 pr-0">
            <div class="form-group pt-4">
              <input type="submit" name="tampil" value="Tampilkan" class="btn btn-secondary btn-round btn-block">
            </div>
          </div>

          <?php if (isset($_POST['tampil'])): ?>
            <?php
              $tgl_awal = date('Y-m-d', strtotime($_POST['tanggal_awal']));
              $tgl_akhir = date('Y-m-d', strtotime($_POST['tanggal_akhir']));
            ?>
            <div class="col-lg-2 pr-0">
              <div class="form-group pt-4">
                <a href="modules/laporan-barang-masuk/cetak.php?tanggal_awal=<?php echo urlencode($tgl_awal); ?>&tanggal_akhir=<?php echo urlencode($tgl_akhir); ?>" target="_blank" class="btn btn-warning btn-round btn-block">
                  <span class="btn-label"><i class="fa fa-print mr-2"></i></span>Cetak
                </a>
              </div>
            </div>
            <div class="col-lg-2 pl-0">
              <div class="form-group pt-4">
                <a href="modules/laporan-barang-masuk/export.php?tanggal_awal=<?php echo urlencode($tgl_awal); ?>&tanggal_akhir=<?php echo urlencode($tgl_akhir); ?>" target="_blank" class="btn btn-success btn-round btn-block">
                  <span class="btn-label"><i class="fa fa-file-excel mr-2"></i></span>Export
                </a>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>

  <?php if (isset($_POST['tampil'])): ?>
    <div class="card mt-4">
      <div class="card-header">
        <div class="card-title">
          <i class="fas fa-file-alt mr-2"></i>
          Laporan Barang Masuk: <strong><?php echo htmlspecialchars($_POST['tanggal_awal']); ?></strong> s.d. <strong><?php echo htmlspecialchars($_POST['tanggal_akhir']); ?></strong>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th class="text-center">No.</th>
                <th class="text-center">ID Transaksi</th>
                <th class="text-center">Tanggal</th> <!-- Hari + Tanggal digabung di sini -->
                <th class="text-center">Barang</th>
                <th class="text-center">Foto</th>
                <th class="text-center">Jumlah Masuk</th>
                <th class="text-center">Satuan</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $no = 1;
                $query = mysqli_query($mysqli, "
                  SELECT a.id_transaksi, a.tanggal, b.nama_barang, b.foto, a.jumlah, c.nama_satuan
                  FROM tbl_barang_masuk AS a
                  INNER JOIN tbl_barang AS b ON a.barang = b.id_barang
                  INNER JOIN tbl_satuan AS c ON b.satuan = c.id_satuan
                  WHERE a.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'
                  ORDER BY a.id_transaksi ASC
                ") or die('Error query: ' . mysqli_error($mysqli));

                while ($data = mysqli_fetch_assoc($query)): ?>
                  <tr>
                    <td class="text-center"><?php echo $no++; ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($data['id_transaksi']); ?></td>
                    <td class="text-center">
                      <?php echo getHariIndonesia($data['tanggal']) . ', ' . date('d-m-Y', strtotime($data['tanggal'])); ?>
                    </td>
                    <td><?php echo htmlspecialchars($data['nama_barang']); ?></td>
                    <td class="text-center">
                      <?php if (!empty($data['foto']) && file_exists("images/{$data['foto']}")): ?>
                        <img src="images/<?php echo rawurlencode($data['foto']); ?>" alt="Foto Barang" width="60" height="60">
                      <?php else: ?>
                        <span class="text-muted">Tidak ada</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-right"><?php echo number_format($data['jumlah'], 0, '', '.'); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($data['nama_satuan']); ?></td>
                  </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>
