<?php
// mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser dan hanya dapat dijalankan ketika di include oleh file lain
// jika file diakses secara langsung
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  // alihkan ke halaman error 404
  header('location: 404.html');
}
// jika file di include oleh file lain, tampilkan isi file
else {
  // mengecek data GET "id_rak"
  if (isset($_GET['id'])) {
    // ambil data GET dari tombol ubah
    $id_rak = $_GET['id'];

    // sql statement untuk menampilkan data dari tabel "tbl_rak" berdasarkan "id_rak"
    $query = mysqli_query($mysqli, "SELECT * FROM tbl_rak WHERE id_rak='$id_rak'")
                                    or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
    // ambil data hasil query
    $data = mysqli_fetch_assoc($query);
  }
?>
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
          <li class="nav-item"><a>Ubah</a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="page-inner mt--5">
    <div class="card">
      <div class="card-header">
        <!-- judul form -->
        <div class="card-title">Ubah Data Rak</div>
      </div>
      <!-- form ubah data -->
      <form action="modules/rak/proses_ubah.php" method="post" class="needs-validation" novalidate>
        <div class="card-body">
          <input type="hidden" name="id_rak" value="<?php echo $data['id_rak']; ?>">

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Kode Rak <span class="text-danger">*</span></label>
                <input type="text" name="kode_rak" class="form-control" autocomplete="off" value="<?php echo $data['kode_rak']; ?>" required>
                <div class="invalid-feedback">Kode rak tidak boleh kosong.</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Nama Rak <span class="text-danger">*</span></label>
                <input type="text" name="nama_rak" class="form-control" autocomplete="off" value="<?php echo $data['nama_rak']; ?>" required>
                <div class="invalid-feedback">Nama rak tidak boleh kosong.</div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Lokasi</label>
                <input type="text" name="lokasi" class="form-control" autocomplete="off" value="<?php echo $data['lokasi']; ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Kapasitas</label>
                <input type="number" name="kapasitas" class="form-control" autocomplete="off" value="<?php echo $data['kapasitas']; ?>" min="0">
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