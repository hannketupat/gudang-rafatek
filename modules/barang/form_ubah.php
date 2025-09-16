<?php
// mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser dan hanya dapat dijalankan ketika di include oleh file lain
// jika file diakses secara langsung
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  // alihkan ke halaman error 404
  header('location: 404.html');
}
// jika file di include oleh file lain, tampilkan isi file
else {
  // mengecek data GET "id_barang"
  if (isset($_GET['id'])) {
    // ambil data GET dari tombol ubah
    $id_barang = $_GET['id'];

    // sql statement untuk menampilkan data dari tabel "tbl_barang" dengan join ke tabel "tbl_jenis", "tbl_satuan", "tbl_rak", dan "tbl_keranjang" berdasarkan "id_barang"
    $query = mysqli_query($mysqli, "SELECT a.id_barang, a.nama_barang, a.jenis, a.stok_minimum, a.stok, a.satuan, a.foto, a.id_rak, a.id_keranjang,
                                           b.nama_jenis, c.nama_satuan, 
                                           r.nama_rak, r.lokasi as lokasi_rak,
                                           k.nama_keranjang, k.kondisi as kondisi_keranjang
                                    FROM tbl_barang as a 
                                    INNER JOIN tbl_jenis as b ON a.jenis=b.id_jenis 
                                    INNER JOIN tbl_satuan as c ON a.satuan=c.id_satuan 
                                    LEFT JOIN tbl_rak as r ON a.id_rak=r.id_rak
                                    LEFT JOIN tbl_keranjang as k ON a.id_keranjang=k.id_keranjang
                                    WHERE a.id_barang='$id_barang'")
                                    or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
    // ambil data hasil query
    $data = mysqli_fetch_assoc($query);
  }
?>
  <!-- menampilkan pesan kesalahan unggah file -->
  <div id="pesan"></div>
  
  <?php
  // Menampilkan pesan error jika ada
  if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] == 6) {
      echo '<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
              <span data-notify="icon" class="fas fa-times"></span>
              <span data-notify="title" class="text-danger">Gagal!</span>
              <span data-notify="message">Tipe file tidak sesuai. Harap unggah file yang memiliki tipe *.jpg atau *.png.</span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';
    } elseif ($_GET['pesan'] == 8) {
      echo '<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
              <span data-notify="icon" class="fas fa-times"></span>
              <span data-notify="title" class="text-danger">Gagal!</span>
              <span data-notify="message">Ukuran file lebih dari 3 Mb. Harap unggah file yang memiliki ukuran maksimal 3 Mb.</span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';
    }
  }
  ?>

  <div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-4">
      <div class="page-header text-white">
        <!-- judul halaman -->
        <h4 class="page-title text-white"><i class="fas fa-clone mr-2"></i> Barang</h4>
        <!-- breadcrumbs -->
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="?module=barang" class="text-white">Barang</a></li>
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
        <div class="card-title">Ubah Data Barang</div>
      </div>
      <!-- form ubah data -->
      <form action="modules/barang/proses_ubah.php" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
        <div class="card-body">
          <div class="row">
            <div class="col-md-7">
              <div class="form-group">
                <label>ID Barang <span class="text-danger">*</span></label>
                <input type="text" name="id_barang" class="form-control" value="<?php echo $data['id_barang']; ?>" readonly>
              </div>

              <div class="form-group">
                <label>Nama Barang <span class="text-danger">*</span></label>
                <input type="text" name="nama_barang" class="form-control" autocomplete="off" value="<?php echo $data['nama_barang']; ?>" required>
                <div class="invalid-feedback">Nama barang tidak boleh kosong.</div>
              </div>

              <div class="form-group">
                <label>Jenis Barang <span class="text-danger">*</span></label>
                <select name="jenis" class="form-control chosen-select" autocomplete="off" required>
                  <option value="<?php echo $data['jenis']; ?>"><?php echo $data['nama_jenis']; ?></option>
                  <option disabled value="">-- Pilih --</option>
                  <?php
                  // sql statement untuk menampilkan data dari tabel "tbl_jenis"
                  $query_jenis = mysqli_query($mysqli, "SELECT * FROM tbl_jenis ORDER BY nama_jenis ASC")
                                                        or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                  // ambil data hasil query
                  while ($data_jenis = mysqli_fetch_assoc($query_jenis)) {
                    // tampilkan data
                    echo "<option value='$data_jenis[id_jenis]'>$data_jenis[nama_jenis]</option>";
                  }
                  ?>
                </select>
                <div class="invalid-feedback">Jenis Barang tidak boleh kosong.</div>
              </div>

              <div class="form-group">
                <label>Stok Minimum <span class="text-danger">*</span></label>
                <input type="text" name="stok_minimum" class="form-control" autocomplete="off" onKeyPress="return goodchars(event,'0123456789',this)" value="<?php echo $data['stok_minimum']; ?>" required>
                <div class="invalid-feedback">Stok minimum tidak boleh kosong.</div>
              </div>

              <div class="form-group">
                <label>Satuan <span class="text-danger">*</span></label>
                <select name="satuan" class="form-control chosen-select" autocomplete="off" required>
                  <option value="<?php echo $data['satuan']; ?>"><?php echo $data['nama_satuan']; ?></option>
                  <option disabled value="">-- Pilih --</option>
                  <?php
                  // sql statement untuk menampilkan data dari tabel "tbl_satuan"
                  $query_satuan = mysqli_query($mysqli, "SELECT * FROM tbl_satuan ORDER BY nama_satuan ASC")
                                                         or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                  // ambil data hasil query
                  while ($data_satuan = mysqli_fetch_assoc($query_satuan)) {
                    // tampilkan data
                    echo "<option value='$data_satuan[id_satuan]'>$data_satuan[nama_satuan]</option>";
                  }
                  ?>
                </select>
                <div class="invalid-feedback">Satuan tidak boleh kosong.</div>
              </div>
            </div>
            <div class="col-md-5 ml-auto">
              <div class="form-group">
                <label>Lokasi Rak</label>
                <select name="id_rak" id="id_rak" class="form-control chosen-select" autocomplete="off" onchange="loadKeranjang()">
                  <?php if (!empty($data['id_rak'])) { ?>
                    <option value="<?php echo $data['id_rak']; ?>"><?php echo htmlspecialchars($data['nama_rak'] . ' - ' . $data['lokasi_rak']); ?></option>
                  <?php } ?>
                  <option selected disabled value="">-- Pilih Rak --</option>
                  <?php
                  // sql statement untuk menampilkan data dari tabel "tbl_rak"
                  $query_rak = mysqli_query($mysqli, "SELECT * FROM tbl_rak ORDER BY nama_rak ASC")
                                            or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                  // ambil data hasil query
                  while ($data_rak = mysqli_fetch_assoc($query_rak)) {
                    // tampilkan data jika tidak sama dengan data saat ini
                    if ($data_rak['id_rak'] != $data['id_rak']) {
                      echo "<option value='$data_rak[id_rak]' data-lokasi='$data_rak[lokasi]'>$data_rak[nama_rak] - $data_rak[lokasi]</option>";
                    }
                  }
                  ?>
                </select>
                <small class="form-text text-muted">Pilih rak untuk penempatan barang</small>
              </div>

              <div class="form-group">
                <label>Keranjang</label>
                <select name="id_keranjang" id="id_keranjang" class="form-control chosen-select" autocomplete="off">
                  <?php if (!empty($data['id_keranjang'])) { ?>
                    <option value="<?php echo $data['id_keranjang']; ?>"><?php echo htmlspecialchars($data['nama_keranjang'] . ' (' . $data['kondisi_keranjang'] . ')'); ?></option>
                  <?php } ?>
                  <option selected disabled value="">-- Pilih Rak Terlebih Dahulu --</option>
                </select>
                <small class="form-text text-muted">Pilih keranjang dalam rak yang dipilih</small>
              </div>

              <div class="form-group">
                <label>Foto Barang</label>
                <input type="file" id="foto" name="foto" class="form-control" autocomplete="off">
                <div class="card mt-3 mb-3">
                  <div class="card-body text-center">
                    <?php
                    // mengecek data foto barang
                    // jika data "foto" tidak ada di database
                    if (is_null($data['foto'])) { ?>
                      <!-- tampilkan foto default -->
                      <img style="max-height:200px" src="images/no_image.png" class="img-fluid foto-preview" alt="Foto Barang">
                    <?php
                    }
                    // jika data "foto" ada di database
                    else { ?>
                      <!-- tampilkan foto barang dari database -->
                      <img style="max-height:200px" src="images/<?php echo $data['foto']; ?>" class="img-fluid foto-preview" alt="Foto Barang">
                    <?php } ?>
                  </div>
                </div>
                <small class="form-text text-secondary">
                  Keterangan : <br>
                  - Tipe file yang bisa diunggah adalah *.jpg atau *.png. <br>
                  - Ukuran file yang bisa diunggah maksimal 3 Mb.
                </small>
              </div>
            </div>
          </div>
        </div>
        <div class="card-action">
          <!-- tombol simpan data -->
          <input type="submit" name="simpan" value="Simpan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
          <!-- tombol kembali ke halaman data barang -->
          <a href="?module=barang" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
        </div>
      </form>
    </div>
  </div>

  <script type="text/javascript">
    $(document).ready(function() {
      // Load keranjang based on current rak selection
      <?php if (!empty($data['id_rak'])) { ?>
        loadKeranjang();
      <?php } ?>
      
      // validasi file dan preview file sebelum diunggah
      $('#foto').change(function() {
        // mengambil value dari file
        var filePath = $('#foto').val();
        var fileSize = $('#foto')[0].files[0].size;
        // tentukan extension file yang diperbolehkan
        var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;

        // Jika tipe file yang diunggah tidak sesuai dengan "allowedExtensions"
        if (!allowedExtensions.exec(filePath)) {
          // tampilkan pesan peringatan tipe file tidak sesuai
          $('#pesan').html('<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-times"></span><span data-notify="title" class="text-danger">Gagal!</span> <span data-notify="message">Tipe file tidak sesuai. Harap unggah file yang memiliki tipe *.jpg atau *.png.</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
          // reset input file
          $('#foto').val('');
          // tampilkan file default
          $('.foto-preview').attr('src', 'images/no_image.png');

          return false;
        }
        // jika ukuran file yang diunggah lebih dari 3 Mb
        else if (fileSize > 3000000) {
          // tampilkan pesan peringatan ukuran file tidak sesuai
          $('#pesan').html('<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-times"></span><span data-notify="title" class="text-danger">Gagal!</span> <span data-notify="message">Ukuran file lebih dari 3 Mb. Harap unggah file yang memiliki ukuran maksimal 3 Mb.</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
          // reset input file
          $('#foto').val('');
          // tampilkan file default
          $('.foto-preview').attr('src', 'images/no_image.png');

          return false;
        }
        // jika file yang diunggah sudah sesuai, tampilkan preview file
        else {
          var fileInput = document.getElementById('foto');

          if (fileInput.files && fileInput.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
              // preview file
              $('.foto-preview').attr('src', e.target.result);
            };
          };
          reader.readAsDataURL(fileInput.files[0]);
        }
      });
    });
    
    // Function to load keranjang based on selected rak
    function loadKeranjang() {
      var idRak = $('#id_rak').val();
      var currentKeranjang = <?php echo json_encode($data['id_keranjang']); ?>;
      
      if (idRak) {
        $.ajax({
          url: 'modules/barang/get_keranjang.php',
          type: 'POST',
          data: { id_rak: idRak },
          dataType: 'json',
          success: function(response) {
            var options = '';
            
            // Add current selection first if it exists
            if (currentKeranjang && response.success) {
              var found = false;
              $.each(response.data, function(index, keranjang) {
                if (keranjang.id_keranjang == currentKeranjang) {
                  options += '<option value="' + keranjang.id_keranjang + '" selected>' + 
                            keranjang.nama_keranjang + ' (' + keranjang.kondisi + ')' + '</option>';
                  found = true;
                }
              });
              
              if (!found) {
                options += '<option value="' + currentKeranjang + '" selected><?php echo htmlspecialchars($data['nama_keranjang']); ?> (Current)</option>';
              }
            }
            
            options += '<option disabled value="">-- Pilih Keranjang --</option>';
            
            if (response.success && response.data.length > 0) {
              $.each(response.data, function(index, keranjang) {
                if (keranjang.id_keranjang != currentKeranjang) {
                  options += '<option value="' + keranjang.id_keranjang + '">' + 
                            keranjang.nama_keranjang + ' (' + keranjang.kondisi + ')' + '</option>';
                }
              });
            } else {
              options += '<option disabled value="">-- Tidak ada keranjang tersedia --</option>';
            }
            
            // Destroy existing chosen instance if it exists
            if ($('#id_keranjang').hasClass('chosen-select')) {
              $('#id_keranjang').chosen('destroy');
            }
            
            // Update options
            $('#id_keranjang').html(options);
            
            // Reinitialize chosen
            $('#id_keranjang').addClass('chosen-select').chosen({
              width: '100%',
              placeholder_text_single: '-- Pilih Keranjang --'
            });
          },
          error: function() {
            $('#pesan').html('<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-times"></span><span data-notify="title" class="text-danger">Error!</span> <span data-notify="message">Error loading keranjang data</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
          }
        });
      } else {
        var options = '<option selected disabled value="">-- Pilih Rak Terlebih Dahulu --</option>';
        if (currentKeranjang) {
          options = '<option value="' + currentKeranjang + '" selected><?php echo htmlspecialchars($data['nama_keranjang']); ?> (Current)</option>' + options;
        }
        
        // Destroy existing chosen instance if it exists
        if ($('#id_keranjang').hasClass('chosen-select')) {
          $('#id_keranjang').chosen('destroy');
        }
        
        $('#id_keranjang').html(options);
        
        // Reinitialize chosen
        $('#id_keranjang').addClass('chosen-select').chosen({
          width: '100%',
          placeholder_text_single: '-- Pilih Rak Terlebih Dahulu --'
        });
      }
    }
  </script>
<?php } ?>