<?php
// mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser dan hanya dapat dijalankan ketika di include oleh file lain
// jika file diakses secara langsung
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  // alihkan ke halaman error 404
  header('location: 404.html');
}
// jika file di include oleh file lain, tampilkan isi file
else { ?>
  <!-- menampilkan pesan kesalahan -->
  <div id="pesan"></div>

  <div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-4">
      <div class="page-header text-white">
        <!-- judul halaman -->
        <h4 class="page-title text-white"><i class="fas fa-sign-out-alt mr-2"></i> Barang Keluar</h4>
        <!-- breadcrumbs -->
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="?module=barang_keluar" class="text-white">Barang Keluar</a></li>
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
        <div class="card-title">Entri Data Barang Keluar</div>
      </div>
      <!-- form entri data -->
      <form action="modules/barang-keluar/proses_entri.php" method="post" class="needs-validation" novalidate>
  <?php $type = isset($_GET['type']) ? $_GET['type'] : 'keluar'; ?>
        <div class="card-body">
          <input type="hidden" name="jenis" value="<?php echo ($type == 'pinjam') ? 'Pinjam' : 'Keluar'; ?>">
          <div class="row">
            <div class="col-md-7">
              <div class="form-group">
                <?php
                // membuat "id_transaksi"
                // sql statement untuk menampilkan 7 digit terakhir dari "id_transaksi" pada tabel "tbl_barang_keluar"
                $query = mysqli_query($mysqli, "SELECT RIGHT(id_transaksi,7) as nomor FROM tbl_barang_keluar ORDER BY id_transaksi DESC LIMIT 1")
                                                or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                // ambil jumlah baris data hasil query
                $rows = mysqli_num_rows($query);

                // cek hasil query
                // jika "id_transaksi" sudah ada
                if ($rows <> 0) {
                  // ambil data hasil query
                  $data = mysqli_fetch_assoc($query);
                  // nomor urut "id_transaksi" yang terakhir + 1 (contoh nomor urut yang terakhir adalah 2, maka 2 + 1 = 3, dst..)
                  $nomor_urut = $data['nomor'] + 1;
                }
                // jika "id_transaksi" belum ada
                else {
                  // nomor urut "id_transaksi" = 1
                  $nomor_urut = 1;
                }

                // menambahkan karakter "TK-" diawal dan karakter "0" disebelah kiri nomor urut
                $id_transaksi = "TK-" . str_pad($nomor_urut, 7, "0", STR_PAD_LEFT);
                ?>
                <label>ID Transaksi <span class="text-danger">*</span></label>
                <!-- tampilkan "id_transaksi" -->
                <input type="text" name="id_transaksi" class="form-control" value="<?php echo $id_transaksi; ?>" readonly>
              </div>
            </div>

            <div class="col-md-5 ml-auto">
              <div class="form-group">
                <label>Tanggal <span class="text-danger">*</span></label>
                <input type="text" name="tanggal" class="form-control date-picker" autocomplete="off" value="<?php echo date("d-m-Y"); ?>" required>
                <div class="invalid-feedback">Tanggal tidak boleh kosong.</div>
              </div>
            </div>
          </div>

          <hr class="mt-3 mb-4">

          <div class="row">
            <div class="col-md-7">
              <div class="form-group">
                <label>Barang <span class="text-danger">*</span></label>
                <select id="data_barang" name="barang" class="form-control chosen-select" autocomplete="off" required>
                  <option selected disabled value="">-- Pilih --</option>
                  <?php
                  // sql statement untuk menampilkan data dari tabel "tbl_barang"
                  $query_barang = mysqli_query($mysqli, "SELECT id_barang, nama_barang FROM tbl_barang ORDER BY id_barang ASC")
                                                         or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                  // ambil data hasil query
                  while ($data_barang = mysqli_fetch_assoc($query_barang)) {
                    // tampilkan data
                    echo "<option value='$data_barang[id_barang]'>$data_barang[nama_barang]</option>";
                  }
                  ?>
                </select>
                <div class="invalid-feedback">Barang tidak boleh kosong.</div>
              </div>

              <!-- Nested dropdown untuk Serial Number Modem -->
              <div class="form-group" id="serial_number_group" style="display: none;">
                <label>Serial Number <span class="text-danger">*</span></label>
                <select id="serial_number" name="serial_number" class="form-control chosen-select" autocomplete="off">
                  <option selected disabled value="">-- Pilih Serial Number --</option>
                </select>
                <div class="invalid-feedback">Serial Number tidak boleh kosong.</div>
              </div>

              <div class="form-group">
                <label>Stok <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input type="text" id="data_stok" name="stok" class="form-control" readonly>
                  <div id="data_satuan" class="input-group-append"></div>
                </div>
              </div>
            </div>

            <div class="col-md-5 ml-auto">
              <div class="form-group">
                <label>Jumlah Keluar <span class="text-danger">*</span></label>
                <input type="text" id="jumlah" name="jumlah" class="form-control" autocomplete="off" onKeyPress="return goodchars(event,'0123456789',this)" required>
                <div class="invalid-feedback">Jumlah keluar tidak boleh kosong.</div>
              </div>

              <div class="form-group">
                <label>Sisa Stok <span class="text-danger">*</span></label>
                <input type="text" id="sisa" name="sisa" class="form-control" readonly>
              </div>

              <div class="form-group">
                <label>Lokasi Rak <small class="text-muted">(Otomatis dari Barang Masuk)</small></label>
                <div class="d-flex align-items-center">
                  <select name="id_rak" id="id_rak" class="form-control chosen-select" autocomplete="off" onchange="loadKeranjangKeluar()" disabled>
                    <option selected disabled value="">-- Akan diisi otomatis --</option>
                    <?php
                    // sql statement untuk menampilkan data dari tabel "tbl_rak"
                    $query_rak = mysqli_query($mysqli, "SELECT * FROM tbl_rak ORDER BY nama_rak ASC")
                                              or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                    // ambil data hasil query
                    while ($data_rak = mysqli_fetch_assoc($query_rak)) {
                      // tampilkan data
                      echo "<option value='$data_rak[id_rak]' data-lokasi='$data_rak[lokasi]'>$data_rak[nama_rak] - $data_rak[lokasi]</option>";
                    }
                    ?>
                  </select>
                  <button type="button" class="btn btn-sm btn-outline-secondary ml-2" id="btn_manual_rak" onclick="enableManualRak()" title="Ubah Manual">
                    <i class="fas fa-edit"></i>
                  </button>
                </div>
                <small class="form-text text-muted">Lokasi diambil dari transaksi barang masuk terakhir</small>
              </div>

              <div class="form-group">
                <label>Keranjang <small class="text-muted">(Otomatis dari Barang Masuk)</small></label>
                <div class="d-flex align-items-center">
                  <select name="id_keranjang" id="id_keranjang" class="form-control chosen-select" autocomplete="off" disabled>
                    <option selected disabled value="">-- Akan diisi otomatis --</option>
                  </select>
                  <button type="button" class="btn btn-sm btn-outline-secondary ml-2" id="btn_manual_keranjang" onclick="enableManualKeranjang()" title="Ubah Manual">
                    <i class="fas fa-edit"></i>
                  </button>
                </div>
                <small class="form-text text-muted">Keranjang diambil dari transaksi barang masuk terakhir</small>
              </div>
            </div>
          </div>
        </div>
        <div class="card-action">
          <!-- tombol simpan data -->
          <input type="submit" name="simpan" value="Simpan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
          <!-- tombol kembali ke halaman data barang keluar -->
          <a href="?module=barang_keluar" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
        </div>
      </form>
    </div>
  </div>

  <script type="text/javascript">
    $(document).ready(function() {
      // Menampilkan data barang dari select box ke textfield
      $('#data_barang').change(function() {
        // mengambil value dari "id_barang"
        var id_barang = $('#data_barang').val();
        var nama_barang = $('#data_barang option:selected').text().toLowerCase();

        // Reset serial number dropdown dan hide
        $('#serial_number').html('<option selected disabled value="">-- Pilih Serial Number --</option>');
        $('#serial_number_group').hide();

        // Check if selected item is modem
        if (nama_barang.includes('modem')) {
          // Show serial number dropdown
          $('#serial_number_group').show();
          
          console.log('Loading serial numbers for id_barang:', id_barang);
          
          // Load serial numbers for modem
          $.ajax({
            type: "GET",
            url: "modules/barang-keluar/get_serial_numbers.php",
            data: {id_barang: id_barang},
            dataType: "JSON",
            success: function(result) {
              console.log('AJAX response:', result);
              
              if (result && result.error) {
                // Show error message
                $('#pesan').html('<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-times"></span><span data-notify="title" class="text-danger">Error!</span> <span data-notify="message">' + result.error + '</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return;
              }
              
              if (result && result.length > 0) {
                var options = '<option selected disabled value="">-- Pilih Serial Number --</option>';
                $.each(result, function(index, item) {
                  options += '<option value="' + item.serial_number + '">' + item.serial_number + '</option>';
                });
                $('#serial_number').html(options);
                
                // Reinitialize chosen if using chosen plugin
                if ($('#serial_number').hasClass('chosen-select')) {
                  $('#serial_number').trigger('chosen:updated');
                }
              } else {
                $('#serial_number').html('<option selected disabled value="">-- Tidak ada Serial Number tersedia --</option>');
                $('#pesan').html('<div class="alert alert-notify alert-info alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-info"></span><span data-notify="title" class="text-info">Info!</span> <span data-notify="message">Tidak ada serial number tersedia untuk barang ini.</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
              }
            },
            error: function(xhr, status, error) {
              console.error('AJAX Error:', {xhr: xhr, status: status, error: error});
              $('#pesan').html('<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-times"></span><span data-notify="title" class="text-danger">Error!</span> <span data-notify="message">Gagal memuat data serial number: ' + error + '</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            }
          });
        }

        $.ajax({
          type: "GET",                                  // mengirim data dengan method GET 
          url: "modules/barang-keluar/get_barang.php",  // proses get data berdasarkan "id_barang"
          data: {id_barang: id_barang},                 // data yang dikirim
          dataType: "JSON",                             // tipe data JSON
          success: function(result) {                   // ketika proses get data selesai
            // tampilkan data
            $('#data_stok').val(result.stok);
            $('#data_satuan').html('<span class="input-group-text">' + result.nama_satuan + '</span>');
            // set focus
            if (!nama_barang.includes('modem')) {
              $('#jumlah').focus();
            }
          }
        });
        
        // Auto-populate location from latest barang masuk
        $.ajax({
          type: "GET",
          url: "modules/barang-keluar/get_location_from_masuk.php",
          data: {id_barang: id_barang},
          dataType: "JSON",
          success: function(result) {
            if (result.success && result.data) {
              // Set rak
              $('#id_rak').val(result.data.id_rak);
              $('#id_rak').prop('disabled', false);
              $('#id_rak').trigger('chosen:updated');
              
              // Set keranjang
              var keranjangOption = '<option value="' + result.data.id_keranjang + '" selected>' + 
                                   result.data.nama_keranjang + ' (' + result.data.kondisi_keranjang + ')' + '</option>';
              $('#id_keranjang').html(keranjangOption);
              $('#id_keranjang').prop('disabled', false);
              $('#id_keranjang').trigger('chosen:updated');
              
              // Show success message
              $('#pesan').html('<div class="alert alert-notify alert-info alert-dismissible fade show" role="alert">' +
                              '<span data-notify="icon" class="fas fa-info"></span>' +
                              '<span data-notify="title" class="text-info">Info!</span> ' +
                              '<span data-notify="message">Lokasi otomatis diambil dari barang masuk: ' + 
                              result.data.nama_rak + ' → ' + result.data.nama_keranjang + '</span>' +
                              '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                              '<span aria-hidden="true">&times;</span></button></div>');
            } else {
              // Reset location fields
              $('#id_rak').val('');
              $('#id_rak').prop('disabled', true);
              $('#id_rak').trigger('chosen:updated');
              
              $('#id_keranjang').html('<option selected disabled value="">-- Tidak ada data lokasi --</option>');
              $('#id_keranjang').prop('disabled', true);
              $('#id_keranjang').trigger('chosen:updated');
              
              // Show warning message
              $('#pesan').html('<div class="alert alert-notify alert-warning alert-dismissible fade show" role="alert">' +
                              '<span data-notify="icon" class="fas fa-exclamation-triangle"></span>' +
                              '<span data-notify="title" class="text-warning">Peringatan!</span> ' +
                              '<span data-notify="message">Tidak ditemukan data lokasi dari barang masuk. Silakan pilih lokasi secara manual.</span>' +
                              '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                              '<span aria-hidden="true">&times;</span></button></div>');
              
              // Enable manual selection
              $('#id_rak').prop('disabled', false);
              $('#id_rak').trigger('chosen:updated');
            }
          },
          error: function() {
            // Enable manual selection on error
            $('#id_rak').prop('disabled', false);
            $('#id_rak').trigger('chosen:updated');
            
            $('#pesan').html('<div class="alert alert-notify alert-warning alert-dismissible fade show" role="alert">' +
                            '<span data-notify="icon" class="fas fa-exclamation-triangle"></span>' +
                            '<span data-notify="title" class="text-warning">Peringatan!</span> ' +
                            '<span data-notify="message">Gagal memuat data lokasi. Silakan pilih lokasi secara manual.</span>' +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span></button></div>');
          }
        });
      });

      // Handle serial number selection
      $('#serial_number').change(function() {
        $('#jumlah').focus();
      });

      // menghitung sisa stok
      $('#jumlah').keyup(function() {
        // mengambil data dari form entri
        var stok = $('#data_stok').val();
        var jumlah = $('#jumlah').val();
        var nama_barang = $('#data_barang option:selected').text().toLowerCase();
        var serial_number = $('#serial_number').val();

        // mengecek input data
        // jika data barang belum diisi
        if (stok == "") {
          // tampilkan pesan info
          $('#pesan').html('<div class="alert alert-notify alert-info alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-info"></span><span data-notify="title" class="text-info">Info!</span> <span data-notify="message">Silahkan isi data barang terlebih dahulu.</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
          // reset input "jumlah"
          $('#jumlah').val('');
          // sisa stok kosong
          var sisa_stok = "";
        }
        // jika barang modem dan serial number belum dipilih
        else if (nama_barang.includes('modem') && serial_number == null) {
          // tampilkan pesan info
          $('#pesan').html('<div class="alert alert-notify alert-info alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-info"></span><span data-notify="title" class="text-info">Info!</span> <span data-notify="message">Silahkan pilih serial number modem terlebih dahulu.</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
          // reset input "jumlah"
          $('#jumlah').val('');
          // sisa stok kosong
          var sisa_stok = "";
        }
        // jika "jumlah" belum diisi
        else if (jumlah == "") {
          // sisa stok kosong
          var sisa_stok = "";
        }
        // jika "jumlah" diisi 0
        else if (jumlah == 0) {
          // tampilkan pesan peringatan
          $('#pesan').html('<div class="alert alert-notify alert-warning alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-exclamation"></span><span data-notify="title" class="text-warning">Peringatan!</span> <span data-notify="message">Jumlah keluar tidak boleh 0 (nol).</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
          // reset input "jumlah"
          $('#jumlah').val('');
          // sisa stok kosong
          var sisa_stok = "";
        }
        // jika "jumlah" lebih dari "stok"
        else if (eval(jumlah) > eval(stok)) {
          // tampilkan pesan peringatan
          $('#pesan').html('<div class="alert alert-notify alert-warning alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-exclamation"></span><span data-notify="title" class="text-warning">Peringatan!</span> <span data-notify="message">Stok tidak memenuhi, kurangi jumlah keluar.</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
          // reset input "jumlah"
          $('#jumlah').val('');
          // sisa stok kosong
          var sisa_stok = "";
        }
        // jika "jumlah" sudah diisi
        else {
          // hitung sisa stok
          var sisa_stok = eval(stok) - eval(jumlah);
        }

        // tampilkan sisa stok
        $('#sisa').val(sisa_stok);
      });

      // Form validation before submit
      $('form').on('submit', function(e) {
        var nama_barang = $('#data_barang option:selected').text().toLowerCase();
        var serial_number = $('#serial_number').val();
        
        if (nama_barang.includes('modem') && (serial_number == null || serial_number == "")) {
          e.preventDefault();
          $('#pesan').html('<div class="alert alert-notify alert-warning alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-exclamation"></span><span data-notify="title" class="text-warning">Peringatan!</span> <span data-notify="message">Serial number modem harus dipilih.</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
          return false;
        }
      });
    });

    // Function to load keranjang based on selected rak for barang keluar
    function loadKeranjangKeluar() {
      var idRak = $('#id_rak').val();
      
      if (idRak) {
        $.ajax({
          url: 'modules/barang/get_keranjang.php',
          type: 'POST',
          data: { id_rak: idRak },
          dataType: 'json',
          success: function(response) {
            var options = '<option selected disabled value="">-- Pilih Keranjang --</option>';
            
            if (response.success && response.data.length > 0) {
              $.each(response.data, function(index, keranjang) {
                options += '<option value="' + keranjang.id_keranjang + '">' + 
                          keranjang.nama_keranjang + ' (' + keranjang.kondisi + ')' + '</option>';
              });
            } else {
              options += '<option disabled value="">-- Tidak ada keranjang tersedia --</option>';
            }
            
            $('#id_keranjang').html(options);
            $('#id_keranjang').trigger('chosen:updated'); // Update chosen dropdown
          },
          error: function() {
            $('#id_keranjang').html('<option selected disabled value="">-- Error loading data --</option>');
            $('#id_keranjang').trigger('chosen:updated');
          }
        });
      } else {
        $('#id_keranjang').html('<option selected disabled value="">-- Pilih Rak Terlebih Dahulu --</option>');
        $('#id_keranjang').trigger('chosen:updated');
      }
    }

    // Function to enable manual rack selection
    function enableManualRak() {
      $('#id_rak').prop('disabled', false);
      $('#id_rak').trigger('chosen:updated');
      $('#btn_manual_rak').hide();
      $('#pesan').html('<div class="alert alert-notify alert-info alert-dismissible fade show" role="alert">' +
                      '<span data-notify="icon" class="fas fa-info"></span>' +
                      '<span data-notify="title" class="text-info">Info!</span> ' +
                      '<span data-notify="message">Mode manual diaktifkan. Anda dapat memilih rak secara manual.</span>' +
                      '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                      '<span aria-hidden="true">&times;</span></button></div>');
    }

    // Function to enable manual basket selection
    function enableManualKeranjang() {
      $('#id_keranjang').prop('disabled', false);
      $('#id_keranjang').trigger('chosen:updated');
      $('#btn_manual_keranjang').hide();
    }
  </script>
<?php } ?>