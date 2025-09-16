<?php
// Cegah akses langsung ke file ini
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
  exit;
}

// ====== Fungsi hari Indonesia ======
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
        <h4 class="page-title text-white"><i class="fas fa-file-signature mr-2"></i> Laporan Stok</h4>
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Laporan</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Stok</a></li>
        </ul>
      </div>
    </div>
  </div>

  <?php if (!isset($_POST['tampil'])) { ?>
    <!-- Form Filter -->
    <div class="page-inner mt--5">
      <div class="card">
        <div class="card-header">
          <div class="card-title">Filter Data Stok</div>
        </div>
        <div class="card-body">
          <form action="?module=laporan_stok" method="post" class="needs-validation" novalidate>
            <div class="row">
              <div class="col-lg-5">
                <div class="form-group">
                  <label>Stok <span class="text-danger">*</span></label>
                  <select name="stok" class="form-control chosen-select" required>
                    <option selected disabled value="">-- Pilih --</option>
                    <option value="Seluruh">Seluruh</option>
                    <option value="Minimum">Minimum</option>
                  </select>
                  <div class="invalid-feedback">Stok tidak boleh kosong.</div>
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group pt-3">
                  <input type="submit" name="tampil" value="Tampilkan" class="btn btn-secondary btn-round btn-block mt-4">
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

  <?php } else {
    $stok = $_POST['stok'];
  ?>
    <!-- Form + Tombol Cetak/Export -->
    <div class="page-inner mt--5">
      <div class="card">
        <div class="card-header">
          <div class="card-title">Filter Data Stok</div>
        </div>
        <div class="card-body">
          <form action="?module=laporan_stok" method="post" class="needs-validation" novalidate>
            <div class="row">
              <div class="col-lg-5">
                <div class="form-group">
                  <label>Stok <span class="text-danger">*</span></label>
                  <select name="stok" class="form-control chosen-select" required>
                    <option value="<?php echo htmlspecialchars($stok); ?>"><?php echo htmlspecialchars($stok); ?></option>
                    <option disabled>-- Pilih --</option>
                    <option value="Seluruh">Seluruh</option>
                    <option value="Minimum">Minimum</option>
                  </select>
                  <div class="invalid-feedback">Stok tidak boleh kosong.</div>
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group pt-3">
                  <input type="submit" name="tampil" value="Tampilkan" class="btn btn-secondary btn-round btn-block mt-4">
                </div>
              </div>
              <div class="col-lg-2 pr-0">
                <div class="form-group pt-3">
                  <a href="modules/laporan-stok/cetak.php?stok=<?php echo urlencode($stok); ?>" target="_blank" class="btn btn-warning btn-round btn-block mt-4">
                    <span class="btn-label"><i class="fa fa-print mr-2"></i></span> Cetak
                  </a>
                </div>
              </div>
              <div class="col-lg-2 pl-0">
                <div class="form-group pt-3">
                  <a href="modules/laporan-stok/export.php?stok=<?php echo urlencode($stok); ?>" target="_blank" class="btn btn-success btn-round btn-block mt-4">
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
            <i class="fas fa-file-alt mr-2"></i>
            <?php echo ($stok == 'Seluruh') ? 'Laporan Stok Seluruh Barang' : 'Laporan Stok Barang yang Mencapai Batas Minimum'; ?>
            <br>
            <small class="text-muted">
              <!-- Tanggal cetak laporan -->
              <?php echo getHariIndonesia(date('Y-m-d')) . ', ' . date('d-m-Y'); ?>
            </small>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
              <thead>
                <tr>
                  <th class="text-center">No.</th>
                  <th class="text-center">ID Barang</th>
                  <th class="text-center">Tanggal</th> <!-- tanggal cetak laporan -->
                  <th class="text-center">Nama Barang</th>
                  <th class="text-center">Jenis Barang</th>
                  <th class="text-center">Foto</th>
                  <th class="text-center">Stok</th>
                  <th class="text-center">Satuan</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $no = 1;
                $whereClause = ($stok == 'Minimum') ? "WHERE a.stok <= a.stok_minimum" : "";
                $query = mysqli_query($mysqli, "
                  SELECT a.id_barang, a.nama_barang, a.jenis, a.foto, a.stok_minimum, a.stok, a.satuan,
                         b.nama_jenis, c.nama_satuan
                  FROM tbl_barang AS a
                  INNER JOIN tbl_jenis  AS b ON a.jenis  = b.id_jenis
                  INNER JOIN tbl_satuan AS c ON a.satuan = c.id_satuan
                  $whereClause
                  ORDER BY a.id_barang ASC
                ") or die('Ada kesalahan pada query: ' . mysqli_error($mysqli));

                // tanggal cetak (sama untuk semua baris)
                $tanggalCetak = getHariIndonesia(date('Y-m-d')) . ', ' . date('d-m-Y');

                while ($data = mysqli_fetch_assoc($query)) {
                  $isMinimum = $data['stok'] <= $data['stok_minimum'];
                ?>
                  <tr>
                    <td class="text-center"><?php echo $no++; ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($data['id_barang']); ?></td>
                    <td class="text-center"><?php echo $tanggalCetak; ?></td>
                    <td><?php echo htmlspecialchars($data['nama_barang']); ?></td>
                    <td><?php echo htmlspecialchars($data['nama_jenis']); ?></td>
                    <td class="text-center">
                      <?php if (!empty($data['foto'])) { ?>
                        <img src="images/<?php echo htmlspecialchars($data['foto']); ?>" alt="Foto Barang" width="60" height="60" style="object-fit:contain;">
                      <?php } else { ?>
                        <span class="text-muted">Tidak ada</span>
                      <?php } ?>
                    </td>
                    <td class="text-right">
                      <?php if ($isMinimum) { ?>
                        <span class="badge badge-warning"><?php echo (int)$data['stok']; ?></span>
                      <?php } else {
                        echo (int)$data['stok'];
                      } ?>
                    </td>
                    <td class="text-center"><?php echo htmlspecialchars($data['nama_satuan']); ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>
