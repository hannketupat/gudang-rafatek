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
        <h4 class="page-title text-white"><i class="fas fa-map-marker-alt mr-2"></i> Lokasi Rak</h4>
        <!-- breadcrumbs -->
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="?module=lokasi_rak" class="text-white">Lokasi Rak</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Ubah Data</a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="page-inner mt--5">
    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Kelola Data Rak</div>
          </div>
          <div class="card-body">
            <p>Ubah atau kelola data rak yang sudah ada.</p>
            <div class="text-center">
              <a href="?module=rak" class="btn btn-secondary btn-lg">
                <i class="fas fa-warehouse mr-2"></i> Kelola Data Rak
              </a>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Kelola Data Keranjang</div>
          </div>
          <div class="card-body">
            <p>Ubah atau kelola data keranjang yang sudah ada.</p>
            <div class="text-center">
              <a href="?module=keranjang" class="btn btn-success btn-lg">
                <i class="fas fa-shopping-basket mr-2"></i> Kelola Data Keranjang
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Quick Actions</div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-3">
                <a href="?module=form_entri_rak" class="btn btn-outline-secondary btn-block">
                  <i class="fas fa-plus"></i> Tambah Rak
                </a>
              </div>
              <div class="col-md-3">
                <a href="?module=form_entri_keranjang" class="btn btn-outline-success btn-block">
                  <i class="fas fa-plus"></i> Tambah Keranjang
                </a>
              </div>
              <div class="col-md-3">
                <a href="?module=rak" class="btn btn-outline-info btn-block">
                  <i class="fas fa-list"></i> Lihat Semua Rak
                </a>
              </div>
              <div class="col-md-3">
                <a href="?module=keranjang" class="btn btn-outline-warning btn-block">
                  <i class="fas fa-list"></i> Lihat Semua Keranjang
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php } ?>