<?php
// mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser dan hanya dapat dijalankan ketika di include oleh file lain
// jika file diakses secara langsung
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  // alihkan ke halaman error 404
  header('location: 404.html');
}
// jika file di include oleh file lain, tampilkan isi file

else {
?>
  <div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-45">
      <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
        <div class="page-header text-white">
          <!-- judul halaman -->
          <h4 class="page-title text-white"><i class="fas fa-server"></i> Lokasi Rak</h4>
          <!-- breadcrumbs -->
          <ul class="breadcrumbs">
            <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item"><a href="?module=lokasi_rak" class="text-white">Lokasi Rak</a></li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item"><a>Overview</a></li>
          </ul>
        </div>
        <div class="ml-md-auto py-2 py-md-0">
          <!-- tombol ke modul rak -->
          <a href="?module=rak" class="btn btn-secondary btn-round mr-2">
            <span class="btn-label"><i class="fas fa-warehouse mr-2"></i></span> Kelola Rak
          </a>
          <!-- tombol ke modul keranjang -->
          <a href="?module=keranjang" class="btn btn-success btn-round">
            <span class="btn-label"><i class="fas fa-shopping-basket mr-2"></i></span> Kelola Keranjang
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="page-inner mt--5">
    <!-- Summary Cards -->
    <div class="row justify-content-center">
      <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-primary bubble-shadow-small">
                  <i class="fas fa-warehouse"></i>
                </div>
              </div>
              <div class="col col-stats ml-3 ml-sm-0">
                <div class="numbers">
                  <p class="card-category">Total Rak</p>
                  <h4 class="card-title">
                    <?php
                    $query_rak = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM tbl_rak") or die('Ada kesalahan pada query : ' . mysqli_error($mysqli));
                    $total_rak = mysqli_fetch_assoc($query_rak)['total'];
                    echo $total_rak;
                    ?>
                  </h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-success bubble-shadow-small">
                  <i class="fas fa-shopping-basket"></i>
                </div>
              </div>
              <div class="col col-stats ml-3 ml-sm-0">
                <div class="numbers">
                  <p class="card-category">Total Keranjang</p>
                  <h4 class="card-title">
                    <?php
                    $query_keranjang = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM tbl_keranjang") or die('Ada kesalahan pada query : ' . mysqli_error($mysqli));
                    $total_keranjang = mysqli_fetch_assoc($query_keranjang)['total'];
                    echo $total_keranjang;
                    ?>
                  </h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-info bubble-shadow-small">
                  <i class="fas fa-calculator"></i>
                </div>
              </div>
              <div class="col col-stats ml-3 ml-sm-0">
                <div class="numbers">
                  <p class="card-category">Total Kapasitas Rak</p>
                  <h4 class="card-title">
                    <?php
                    $query_kapasitas = mysqli_query($mysqli, "SELECT SUM(kapasitas) as total FROM tbl_rak") or die('Ada kesalahan pada query : ' . mysqli_error($mysqli));
                    $total_kapasitas = mysqli_fetch_assoc($query_kapasitas)['total'] ?? 0;
                    echo $total_kapasitas;
                    ?>
                  </h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Recent Racks -->
    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <div class="card-head-row">
              <div class="card-title">Rak Terbaru</div>
              <div class="card-tools">
                <a href="?module=rak" class="btn btn-info btn-border btn-round btn-sm">
                  <span class="btn-label"><i class="fa fa-eye"></i></span> Lihat Semua
                </a>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Kode</th>
                    <th>Serial Number</th>
                    <th>Nama Rak</th>
                    <th>Lokasi</th>
                    <th>Kapasitas</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  // sql statement untuk menampilkan data rak terbaru (5 terakhir)
                  $query_rak_recent = mysqli_query($mysqli, "SELECT * FROM tbl_rak ORDER BY id_rak DESC LIMIT 5")
                                                   or die('Ada kesalahan pada query tampil data rak : ' . mysqli_error($mysqli));
                  // ambil data hasil query
                  while ($data_rak = mysqli_fetch_assoc($query_rak_recent)) { ?>
                    <tr>
                      <td><strong><?php echo $data_rak['kode_rak']; ?></strong></td>
                      <td class="text-center">
                        <div class="d-flex justify-content-center">
                          <img src="libs/barcode.php?text=<?php echo urlencode($data_rak['kode_rak']); ?>&codetype=code128&size=25&print=true" 
                               alt="Barcode <?php echo $data_rak['kode_rak']; ?>"
                               class="img-fluid" 
                               style="max-height: 50px;"
                               onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                          <small class="text-muted" style="display: none; font-size: 10px;">Barcode tidak tersedia</small>
                        </div>
                      </td>
                      <td><?php echo $data_rak['nama_rak']; ?></td>
                      <td><?php echo $data_rak['lokasi'] ?: '-'; ?></td>
                      <td class="text-center"><?php echo $data_rak['kapasitas']; ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Baskets -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <div class="card-head-row">
              <div class="card-title">Keranjang Terbaru</div>
              <div class="card-tools">
                <a href="?module=keranjang" class="btn btn-success btn-border btn-round btn-sm">
                  <span class="btn-label"><i class="fa fa-eye"></i></span> Lihat Semua
                </a>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Kode</th>
                    <th>Serial Number</th>
                    <th>Nama Keranjang</th>
                    <th>Rak</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  // sql statement untuk menampilkan data keranjang terbaru (5 terakhir)
                  $query_keranjang_recent = mysqli_query($mysqli, "SELECT k.*, r.nama_rak 
                                                                  FROM tbl_keranjang k 
                                                                  LEFT JOIN tbl_rak r ON k.id_rak = r.id_rak 
                                                                  ORDER BY k.id_keranjang DESC LIMIT 5")
                                                         or die('Ada kesalahan pada query tampil data keranjang : ' . mysqli_error($mysqli));
                  // ambil data hasil query
                  while ($data_keranjang = mysqli_fetch_assoc($query_keranjang_recent)) { ?>
                    <tr>
                      <td><strong><?php echo $data_keranjang['kode_keranjang']; ?></strong></td>
                      <td class="text-center">
                        <div class="d-flex justify-content-center">
                          <img src="libs/barcode.php?text=<?php echo urlencode($data_keranjang['kode_keranjang']); ?>&codetype=code128&size=15&print=true" 
                               alt="Barcode <?php echo $data_keranjang['kode_keranjang']; ?>"
                               class="img-fluid" 
                               style="max-height: 40px;"
                               onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                          <small class="text-muted" style="display: none; font-size: 10px;">Barcode tidak tersedia</small>
                        </div>
                      </td>
                      <td><?php echo $data_keranjang['nama_keranjang']; ?></td>
                      <td><?php echo $data_keranjang['nama_rak'] ?: '-'; ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
<?php } ?>