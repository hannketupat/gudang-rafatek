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
          <li class="nav-item"><a>Entri Data</a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="page-inner mt--5">
    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Tambah Rak Baru</div>
          </div>
          <div class="card-body">
            <p>Kelola data rak untuk penyimpanan barang di gudang.</p>
            <div class="text-center">
              <a href="?module=form_entri_rak" class="btn btn-secondary btn-lg">
                <i class="fas fa-warehouse mr-2"></i> Entri Data Rak
              </a>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Tambah Keranjang Baru</div>
          </div>
          <div class="card-body">
            <p>Kelola data keranjang untuk mengorganisir barang di dalam rak.</p>
            <div class="text-center">
              <a href="?module=form_entri_keranjang" class="btn btn-success btn-lg">
                <i class="fas fa-shopping-basket mr-2"></i> Entri Data Keranjang
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
            <div class="card-title">Petunjuk</div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <h5><i class="fas fa-warehouse text-secondary"></i> Rak</h5>
                <ul>
                  <li>Rak adalah unit penyimpanan utama di gudang</li>
                  <li>Setiap rak memiliki kode unik dan lokasi</li>
                  <li>Kapasitas rak menunjukkan daya tampung maksimal</li>
                  <li>Satu rak dapat memiliki beberapa keranjang</li>
                </ul>
              </div>
              <div class="col-md-6">
                <h5><i class="fas fa-shopping-basket text-success"></i> Keranjang</h5>
                <ul>
                  <li>Keranjang adalah subdivisi dari rak</li>
                  <li>Membantu mengorganisir barang dalam rak</li>
                  <li>Setiap keranjang dapat ditugaskan ke rak tertentu</li>
                  <li>Status kondisi: Baik, Rusak, atau Maintenance</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php } ?>