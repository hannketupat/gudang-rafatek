<?php
// mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser dan hanya dapat dijalankan ketika di include oleh file lain
// jika file diakses secara langsung
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  // alihkan ke halaman error 404
  header('location: 404.html');
}
// jika file di include oleh file lain, tampilkan isi file
else { ?>
  <div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-4">
      <div class="page-header text-white">
        <!-- judul halaman -->
        <h4 class="page-title text-white"><i class="fas fa-shopping-basket mr-2"></i> Data Keranjang</h4>
        <!-- breadcrumbs -->
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="?module=keranjang" class="text-white">Data Keranjang</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Entri</a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="page-inner mt--5">
    <div class="card">
      <div class="card-header">
        <!-- judul form -->
        <div class="card-title">Entri Data Keranjang</div>
      </div>
      <!-- form entri data -->
      <form action="modules/keranjang/proses_entri.php" method="post" class="needs-validation" novalidate>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Kode Keranjang <span class="text-danger">*</span></label>
                <input type="text" name="kode_keranjang" class="form-control" autocomplete="off" placeholder="Contoh: K001" required>
                <div class="invalid-feedback">Kode keranjang tidak boleh kosong.</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Nama Keranjang <span class="text-danger">*</span></label>
                <input type="text" name="nama_keranjang" class="form-control" autocomplete="off" placeholder="Contoh: Keranjang A1" required>
                <div class="invalid-feedback">Nama keranjang tidak boleh kosong.</div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label>Rak</label>
                <select name="id_rak" class="form-control">
                  <option value="">-- Pilih Rak --</option>
                  <?php
                  // sql statement untuk menampilkan data rak
                  $query_rak = mysqli_query($mysqli, "SELECT * FROM tbl_rak ORDER BY nama_rak ASC")
                                                      or die('Ada kesalahan pada query tampil data rak : ' . mysqli_error($mysqli));
                  // ambil data hasil query
                  while ($data_rak = mysqli_fetch_assoc($query_rak)) {
                    echo '<option value="' . $data_rak['id_rak'] . '">' . $data_rak['kode_rak'] . ' - ' . $data_rak['nama_rak'] . '</option>';
                  }
                  ?>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="3" placeholder="Keterangan tambahan..."></textarea>
          </div>
        </div>
        <div class="card-action">
          <!-- tombol simpan data -->
          <input type="submit" name="simpan" value="Simpan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
          <!-- tombol kembali ke halaman data keranjang -->
          <a href="?module=keranjang" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
        </div>
      </form>
    </div>
  </div>
<?php } ?>