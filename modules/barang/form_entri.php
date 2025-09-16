<?php
// mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser dan hanya dapat dijalankan ketika di include oleh file lain
// jika file diakses secara langsung
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  // alihkan ke halaman error 404
  header('location: 404.html');
}
// jika file di include oleh file lain, tampilkan isi file
else { ?>
  <!-- menampilkan pesan kesalahan unggah file -->
  <div id="pesan"></div>

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
          <li class="nav-item"><a>Entri</a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="page-inner mt--5">
    <div class="card">
      <div class="card-header">
        <!-- judul form -->
        <div class="card-title">Entri Data Barang</div>
      </div>
      <!-- form entri data -->
      <form action="modules/barang/proses_entri.php" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
        <div class="card-body">
          <div class="row">
            <div class="col-md-7">
              <div class="form-group">
                <?php
                // membuat "id_barang"
                // sql statement untuk menampilkan 4 digit terakhir dari "id_barang" pada tabel "tbl_barang"
                $query = mysqli_query($mysqli, "SELECT RIGHT(id_barang,4) as nomor FROM tbl_barang ORDER BY id_barang DESC LIMIT 1")
                                                or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                // ambil jumlah baris data hasil query
                $rows = mysqli_num_rows($query);

                // cek hasil query
                // jika "id_barang" sudah ada
                if ($rows <> 0) {
                  // ambil data hasil query
                  $data = mysqli_fetch_assoc($query);
                  // nomor urut "id_barang" yang terakhir + 1 (contoh nomor urut yang terakhir adalah 2, maka 2 + 1 = 3, dst..)
                  $nomor_urut = $data['nomor'] + 1;
                }
                // jika "id_barang" belum ada
                else {
                  // nomor urut "id_barang" = 1
                  $nomor_urut = 1;
                }
                
                // menambahkan karakter "B" diawal dan karakter "0" disebelah kiri nomor urut
                $id_barang = "B" . str_pad($nomor_urut, 4, "0", STR_PAD_LEFT);
                ?>
                <label>ID Barang <span class="text-danger">*</span></label>
                <!-- tampilkan "id_barang" -->
                <input type="text" name="id_barang" class="form-control" value="<?php echo $id_barang; ?>" readonly>
              </div>

              <div class="form-group">
                <label>Nama Barang <span class="text-danger">*</span></label>
                <input type="text" name="nama_barang" class="form-control" autocomplete="off" required>
                <div class="invalid-feedback">Nama barang tidak boleh kosong.</div>
              </div>

              <div class="form-group">
                <label>Jenis Barang <span class="text-danger">*</span></label>
                <select name="jenis" id="jenis_barang" class="form-control chosen-select" autocomplete="off" required>
                  <option selected disabled value="">-- Pilih --</option>
                  <?php
                  // sql statement untuk menampilkan data dari tabel "tbl_jenis"
                  $query_jenis = mysqli_query($mysqli, "SELECT * FROM tbl_jenis ORDER BY nama_jenis ASC")
                                                        or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                  // ambil data hasil query
                  while ($data_jenis = mysqli_fetch_assoc($query_jenis)) {
                    // tampilkan data
                    echo "<option value='$data_jenis[id_jenis]' data-nama='$data_jenis[nama_jenis]'>$data_jenis[nama_jenis]</option>";
                  }
                  ?>
                </select>
                <div class="invalid-feedback">Jenis Barang tidak boleh kosong.</div>
              </div>

              <div class="form-group">
                <label>Stok Minimum <span class="text-danger">*</span></label>
                <input type="text" name="stok_minimum" class="form-control" autocomplete="off" onKeyPress="return goodchars(event,'0123456789',this)" required>
                <div class="invalid-feedback">Stok minimum tidak boleh kosong.</div>
              </div>

              <!-- Serial Number Section - Hidden by default -->
              <div class="row" id="serial_number_section" style="display: none;">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Serial Number <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <input type="text" id="serial_number" name="serial_number" class="form-control" 
                             placeholder="Masukkan atau scan serial number barang" autocomplete="off">
                      <div class="input-group-append">
                        <div class="dropdown">
                          <button type="button" id="btn_scan" class="btn btn-info dropdown-toggle" data-toggle="dropdown" title="Scan Barcode">
                            <i class="fas fa-barcode"></i> Scan
                          </button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" onclick="startCameraScan()">
                              <i class="fas fa-camera mr-2"></i> Scan dengan Kamera
                            </a>
                            <a class="dropdown-item" href="#" onclick="scanFromFile()">
                              <i class="fas fa-image mr-2"></i> Scan dari Galeri
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" onclick="showCameraGuide()">
                              <i class="fas fa-question-circle mr-2"></i> Panduan Scanner
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                    <small class="text-muted">Masukkan serial number barang atau gunakan tombol scan untuk barcode</small>
                    <div class="invalid-feedback">Serial number tidak boleh kosong.</div>
                  </div>
                </div>
              </div>

              <!-- Info Serial Number Status -->
              <div class="row" id="serial_status" style="display: none;">
                <div class="col-md-12">
                  <div id="serial_alert" class="alert alert-info">
                    <div class="row">
                      <div class="col-md-12">
                        <span id="serial_message"><i class="fas fa-info-circle mr-2"></i>Serial number telah diisi dari hasil scan barcode.</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label>Satuan <span class="text-danger">*</span></label>
                <select name="satuan" class="form-control chosen-select" autocomplete="off" required>
                  <option selected disabled value="">-- Pilih --</option>
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
                  <option selected disabled value="">-- Pilih Rak --</option>
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
                <small class="form-text text-muted">Pilih rak untuk penempatan barang</small>
              </div>

              <div class="form-group">
                <label>Keranjang</label>
                <select name="id_keranjang" id="id_keranjang" class="form-control chosen-select" autocomplete="off">
                  <option selected disabled value="">-- Pilih Rak Terlebih Dahulu --</option>
                </select>
                <small class="form-text text-muted">Pilih keranjang dalam rak yang dipilih</small>
              </div>

              <div class="form-group">
                <label>Foto Barang</label>
                <input type="file" id="foto" name="foto" class="form-control" autocomplete="off">
                <div class="card mt-3 mb-3">
                  <div class="card-body text-center">
                    <img style="max-height:200px" src="images/no_image.png" class="img-fluid foto-preview" alt="Foto Barang">
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

  <!-- Modal Scanner -->
  <div class="modal fade" id="scannerModal" tabindex="-1" role="dialog" aria-labelledby="scannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="scannerModalLabel">
            <i class="fas fa-camera mr-2"></i>Scan Barcode
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div id="qr-reader" style="width: 100%; height: 400px; border: 2px dashed #dee2e6; border-radius: 8px;"></div>
          <div id="scan-status" class="mt-3 text-center">
            <small class="text-muted">
              <i class="fas fa-info-circle mr-1"></i>
              Arahkan kamera ke barcode untuk memindai
            </small>
          </div>
          <!-- Panduan Scan -->
          <div class="mt-3">
            <div class="alert alert-info mb-0">
              <small>
                <strong><i class="fas fa-lightbulb mr-1"></i>Tips Scanner:</strong><br>
                • Pastikan barcode berada dalam kotak scan<br>
                • Jaga jarak kamera sekitar 10-30 cm dari barcode<br>
                • Pastikan pencahayaan cukup untuk hasil terbaik<br>
                • Hindari refleksi cahaya pada barcode
              </small>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times mr-1"></i>Tutup
          </button>
          <button type="button" class="btn btn-info" onclick="switchCamera()" id="switch-camera-btn" style="display: none;">
            <i class="fas fa-sync-alt mr-1"></i>Ganti Kamera
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Temporary element for file scanning -->
  <div id="temp-scan" style="display:none;"></div>

  <!-- Script Html5-QrCode Library -->
  <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

  <script type="text/javascript">
    let html5QrcodeScanner = null;
    let currentCameraId = null;
    let availableCameras = [];
    
    $(document).ready(function() {
      // Initialize camera list
      initializeCameras();
      
      // Function to toggle serial number field based on jenis barang
      function toggleSerialNumberField() {
        var selectedOption = $('#jenis_barang option:selected');
        var jenisNama = selectedOption.data('nama');
        
        if (jenisNama && jenisNama.toUpperCase() === 'MODEM') {
          $('#serial_number_section').show();
          $('#serial_number').attr('required', true);
        } else {
          $('#serial_number_section').hide();
          $('#serial_status').hide();
          $('#serial_number').attr('required', false);
          $('#serial_number').val(''); // Clear the value when hidden
        }
      }

      // Event handler untuk perubahan jenis barang
      $('#jenis_barang').change(function() {
        toggleSerialNumberField();
      });

      // Initialize on page load
      toggleSerialNumberField();

      // Event handler untuk tombol clear
      $('#btn_clear').click(function() {
        $('#serial_number').val('');
        $('#serial_status').hide();
        showAlert('info', 'Serial number telah dikosongkan.');
      });

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
          showAlert('danger', 'Tipe file tidak sesuai. Harap unggah file yang memiliki tipe *.jpg atau *.png.');
          // reset input file
          $('#foto').val('');
          // tampilkan file default
          $('.foto-preview').attr('src', 'images/no_image.png');
          return false;
        }
        // jika ukuran file yang diunggah lebih dari 3 Mb
        else if (fileSize > 3000000) {
          // tampilkan pesan peringatan ukuran file tidak sesuai
          showAlert('danger', 'Ukuran file lebih dari 3 Mb. Harap unggah file yang memiliki ukuran maksimal 3 Mb.');
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
            reader.readAsDataURL(fileInput.files[0]);
          }
        }
      });

      // Stop scanner saat modal ditutup
      $('#scannerModal').on('hidden.bs.modal', function () {
        stopScanner();
      });
    });

    // Function to show alerts with better styling
    function showAlert(type, message, title) {
      const icons = {
        success: 'fas fa-check',
        danger: 'fas fa-times', 
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
      };
      
      const titles = {
        success: title || 'Berhasil!',
        danger: title || 'Gagal!',
        warning: title || 'Peringatan!',
        info: title || 'Informasi!'
      };
      
      const alertHtml = `
        <div class="alert alert-notify alert-${type} alert-dismissible fade show" role="alert">
          <span data-notify="icon" class="${icons[type]}"></span>
          <span data-notify="title" class="text-${type}">${titles[type]}</span>
          <span data-notify="message">${message}</span>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      `;
      
      $('#pesan').html(alertHtml);
      
      // Auto hide after 5 seconds for success messages
      if (type === 'success') {
        setTimeout(() => {
          $('.alert').fadeOut();
        }, 5000);
      }
    }

    // Initialize available cameras
    function initializeCameras() {
      if (navigator.mediaDevices && navigator.mediaDevices.enumerateDevices) {
        navigator.mediaDevices.enumerateDevices()
          .then(devices => {
            availableCameras = devices.filter(device => device.kind === 'videoinput');
            console.log('Available cameras:', availableCameras.length);
          })
          .catch(error => {
            console.error('Error enumerating devices:', error);
          });
      }
    }

    // Fungsi untuk mulai scan dengan kamera - VERSI PERBAIKAN
    function startCameraScan() {
      // Cek apakah sedang menggunakan HTTPS atau localhost
      const isSecureContext = (location.protocol === 'https:' || 
                              location.hostname === 'localhost' || 
                              location.hostname === '127.0.0.1');
      
      // Jika tidak dalam konteks yang aman
      if (!isSecureContext) {
        showAlert('warning', 
          'Akses kamera memerlukan HTTPS atau localhost. Silakan gunakan fitur "Scan dari Galeri" sebagai alternatif, ' +
          'atau akses aplikasi melalui HTTPS/localhost untuk menggunakan kamera.'
        );
        return;
      }
      
      // Cek apakah browser mendukung getUserMedia
      if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        showAlert('warning', 
          'Browser tidak mendukung akses kamera. Silakan gunakan browser terbaru (Chrome, Firefox, Safari) ' +
          'atau gunakan fitur "Scan dari Galeri" sebagai alternatif.'
        );
        return;
      }

      // Show loading message
      showAlert('info', 'Meminta izin akses kamera...', 'Loading');

      // Cek izin kamera terlebih dahulu
      navigator.mediaDevices.getUserMedia({ 
        video: { 
          facingMode: 'environment' // Gunakan kamera belakang jika tersedia
        } 
      })
        .then(function(stream) {
          // Hentikan stream sementara
          stream.getTracks().forEach(track => track.stop());
          
          // Clear previous alert
          $('#pesan').empty();
          
          // Jika izin kamera berhasil, buka modal dan mulai scanner
          $('#scannerModal').modal('show');
          
          // Show switch camera button if multiple cameras available
          if (availableCameras.length > 1) {
            $('#switch-camera-btn').show();
          }
          
          // Delay sebentar untuk memastikan modal terbuka
          setTimeout(() => {
            startScanner();
          }, 500);
        })
        .catch(function(error) {
          console.error('Camera access error:', error);
          
          let errorMessage = 'Tidak dapat mengakses kamera. ';
          
          switch(error.name) {
            case 'NotAllowedError':
            case 'PermissionDeniedError':
              errorMessage += 'Izin kamera ditolak. Silakan:<br>' +
                           '• Klik ikon kamera/gembok di address bar browser<br>' +
                           '• Pilih "Always allow" atau "Allow" untuk kamera<br>' +
                           '• Refresh halaman dan coba lagi';
              break;
            case 'NotFoundError':
            case 'DevicesNotFoundError':
              errorMessage += 'Kamera tidak ditemukan. Pastikan perangkat kamera terhubung dan tidak digunakan aplikasi lain.';
              break;
            case 'NotReadableError':
            case 'TrackStartError':
              errorMessage += 'Kamera sedang digunakan oleh aplikasi lain. Tutup aplikasi lain yang menggunakan kamera.';
              break;
            case 'OverconstrainedError':
            case 'ConstraintNotSatisfiedError':
              errorMessage += 'Kamera tidak memenuhi persyaratan. Coba gunakan kamera yang berbeda.';
              break;
            case 'NotSupportedError':
              errorMessage += 'Browser tidak mendukung akses kamera. Gunakan browser terbaru.';
              break;
            case 'AbortError':
              errorMessage += 'Akses kamera dibatalkan. Silakan coba lagi.';
              break;
            default:
              errorMessage += `Error: ${error.message}. Silakan coba lagi atau gunakan fitur "Scan dari Galeri".`;
          }
          
          showAlert('danger', errorMessage);
        });
    }

    // Fungsi untuk mulai scanner - VERSI PERBAIKAN
    function startScanner() {
      const config = { 
        fps: 10, 
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0,
        supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA],
        // Tambahan konfigurasi untuk kompatibilitas yang lebih baik
        experimentalFeatures: {
          useBarCodeDetectorIfSupported: true
        },
        rememberLastUsedCamera: true,
        showTorchButtonIfSupported: true,
        // Preferensi kamera belakang
        cameraIdOrConfig: currentCameraId || { facingMode: "environment" }
      };

      html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", config);
      
      // Update status saat scanner dimulai
      $('#scan-status').html(
        '<small class="text-info"><i class="fas fa-spinner fa-spin mr-1"></i>Memulai kamera...</small>'
      );
      
      html5QrcodeScanner.render(onScanSuccess, onScanFailure);
      
      // Update status setelah scanner dimulai
      setTimeout(() => {
        $('#scan-status').html(
          '<small class="text-muted"><i class="fas fa-camera mr-1"></i>Arahkan kamera ke barcode untuk memindai</small>'
        );
      }, 2000);
    }

    // Callback ketika scan berhasil
    function onScanSuccess(decodedText, decodedResult) {
      // Scan berhasil, masukkan hasil ke input serial number
      $('#serial_number').val(decodedText);
      
      // Tutup modal
      $('#scannerModal').modal('hide');
      
      // Tampilkan info bahwa serial number telah diisi
      $('#serial_status').show();
      $('#serial_message').html(
        '<i class="fas fa-check-circle mr-2"></i>Serial number berhasil discan: <strong>' + decodedText + '</strong>'
      );
      $('#serial_alert').removeClass('alert-info').addClass('alert-success');
      
      // Tampilkan pesan sukses
      showAlert('success', 'Barcode berhasil discan: <strong>' + decodedText + '</strong>');
    }

    // Callback ketika scan gagal (tidak perlu ditampilkan)
    function onScanFailure(error) {
      // console.warn(Code scan error = ${error});
    }

    // Fungsi untuk stop scanner
    function stopScanner() {
      if (html5QrcodeScanner) {
        html5QrcodeScanner.clear().catch(error => {
          console.error("Failed to clear html5QrcodeScanner. ", error);
        });
        html5QrcodeScanner = null;
      }
      // Hide switch camera button
      $('#switch-camera-btn').hide();
    }

    // Fungsi untuk switch camera
    function switchCamera() {
      if (availableCameras.length > 1) {
        const currentIndex = availableCameras.findIndex(camera => camera.deviceId === currentCameraId);
        const nextIndex = (currentIndex + 1) % availableCameras.length;
        currentCameraId = availableCameras[nextIndex].deviceId;
        
        // Restart scanner with new camera
        stopScanner();
        setTimeout(() => {
          startScanner();
        }, 500);
      }
    }

    // Fungsi untuk scan dari file gambar - VERSI PERBAIKAN
    function scanFromFile() {
      const input = document.createElement('input');
      input.type = 'file';
      input.accept = 'image/*';
      input.multiple = false;
      
      input.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
          // Show loading message
          showAlert('info', 'Memindai barcode dari gambar...', 'Processing');
          
          const html5QrCode = new Html5Qrcode("temp-scan");
          html5QrCode.scanFile(file, true)
            .then(decodedText => {
              $('#serial_number').val(decodedText);
              $('#serial_status').show();
              $('#serial_message').html(
                '<i class="fas fa-check-circle mr-2"></i>Serial number berhasil discan dari gambar: <strong>' + decodedText + '</strong>'
              );
              $('#serial_alert').removeClass('alert-info').addClass('alert-success');
              
              showAlert('success', 'Barcode dari gambar berhasil discan: <strong>' + decodedText + '</strong>');
            })
            .catch(err => {
              console.error('Scan from file error:', err);
              showAlert('warning', 
                'Tidak dapat membaca barcode dari gambar yang dipilih. Pastikan:<br>' +
                '• Gambar berisi barcode yang jelas dan tidak buram<br>' +
                '• Barcode dalam gambar cukup besar dan kontras<br>' +
                '• Format gambar didukung (JPG, PNG, etc.)'
              );
            });
        }
      };
      
      input.click();
    }

    // Fungsi untuk menampilkan panduan penggunaan
    function showCameraGuide() {
      const guideMessage = `
        <div class="row">
          <div class="col-md-6">
            <h6><i class="fas fa-camera mr-2"></i>Scanner Kamera:</h6>
            <ul class="mb-2">
              <li>Pastikan menggunakan HTTPS atau localhost</li>
              <li>Berikan izin akses kamera ke browser</li>
              <li>Gunakan browser terbaru (Chrome, Firefox, Safari)</li>
              <li>Pastikan kamera tidak digunakan aplikasi lain</li>
            </ul>
          </div>
          <div class="col-md-6">
            <h6><i class="fas fa-image mr-2"></i>Scanner Galeri:</h6>
            <ul class="mb-2">
              <li>Pilih gambar yang berisi barcode</li>
              <li>Pastikan gambar jelas dan tidak buram</li>
              <li>Barcode dalam gambar cukup kontras</li>
              <li>Mendukung format JPG, PNG, dll</li>
            </ul>
          </div>
        </div>
        <div class="alert alert-secondary mt-2 mb-0">
          <small><strong>Troubleshooting:</strong><br>
          Jika scanner kamera tidak berfungsi, gunakan scanner galeri sebagai alternatif yang selalu tersedia.
          </small>
        </div>
      `;
      
      showAlert('info', guideMessage, 'Panduan Scanner Barcode');
    }

    // Fungsi untuk detect browser capabilities
    function detectBrowserCapabilities() {
      const capabilities = {
        getUserMedia: !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia),
        https: location.protocol === 'https:',
        localhost: location.hostname === 'localhost' || location.hostname === '127.0.0.1'
      };
      
      console.log('Browser capabilities:', capabilities);
      return capabilities;
    }

    // Initialize capabilities check on page load
    $(document).ready(function() {
      detectBrowserCapabilities();
    });

    // Function to load keranjang based on selected rak
    function loadKeranjang() {
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
            showAlert('danger', 'Error loading keranjang data');
          }
        });
      } else {
        // Destroy existing chosen instance if it exists
        if ($('#id_keranjang').hasClass('chosen-select')) {
          $('#id_keranjang').chosen('destroy');
        }
        
        $('#id_keranjang').html('<option selected disabled value="">-- Pilih Rak Terlebih Dahulu --</option>');
        
        // Reinitialize chosen
        $('#id_keranjang').addClass('chosen-select').chosen({
          width: '100%',
          placeholder_text_single: '-- Pilih Rak Terlebih Dahulu --'
        });
      }
    }
  </script>
<?php } ?>