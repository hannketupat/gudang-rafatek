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
        <h4 class="page-title text-white"><i class="fas fa-warehouse mr-2"></i> Data Rak</h4>
        <!-- breadcrumbs -->
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="?module=rak" class="text-white">Data Rak</a></li>
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
        <div class="card-title">Entri Data Rak</div>
      </div>
      <!-- form entri data -->
      <form action="modules/rak/proses_entri.php" method="post" class="needs-validation" novalidate>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Kode Rak <span class="text-danger">*</span></label>
                <input type="text" name="kode_rak" class="form-control" autocomplete="off" placeholder="Contoh: R001" required>
                <div class="invalid-feedback">Kode rak tidak boleh kosong.</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Nama Rak <span class="text-danger">*</span></label>
                <input type="text" name="nama_rak" class="form-control" autocomplete="off" placeholder="Contoh: Rak Utama 1" required>
                <div class="invalid-feedback">Nama rak tidak boleh kosong.</div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Lokasi</label>
                <input type="text" name="lokasi" class="form-control" autocomplete="off" placeholder="Contoh: Lantai 1 - Sektor A">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Kapasitas</label>
                <input type="number" name="kapasitas" class="form-control" autocomplete="off" placeholder="0" min="0">
              </div>
            </div>
          </div>
        </div>
        <div class="card-action">
          <!-- tombol simpan data -->
          <input type="submit" name="simpan" value="Simpan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
          <!-- tombol kembali ke halaman data rak -->
          <a href="?module=rak" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
        </div>
      </form>
    </div>
  </div>
<?php } ?>