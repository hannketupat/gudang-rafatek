<?php
// mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser dan hanya dapat dijalankan ketika di include oleh file lain
// jika file diakses secara langsung
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  // alihkan ke halaman error 404
  header('location: 404.html');
}
// jika file di include oleh file lain, tampilkan isi file
else {
  // menampilkan pesan sesuai dengan proses yang dijalankan
  // jika pesan tersedia
  if (isset($_GET['pesan'])) {
    // jika pesan = 1
    if ($_GET['pesan'] == 1) {
      // tampilkan pesan sukses simpan data
      echo '<div class="alert alert-notify alert-success alert-dismissible fade show" role="alert">
              <span data-notify="icon" class="fas fa-check"></span> 
              <span data-notify="title" class="text-success">Sukses!</span> 
              <span data-notify="message">Data keranjang berhasil disimpan.</span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';
    }
    // jika pesan = 2
    elseif ($_GET['pesan'] == 2) {
      // tampilkan pesan sukses ubah data
      echo '<div class="alert alert-notify alert-success alert-dismissible fade show" role="alert">
              <span data-notify="icon" class="fas fa-check"></span> 
              <span data-notify="title" class="text-success">Sukses!</span> 
              <span data-notify="message">Data keranjang berhasil diubah.</span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';
    }
    // jika pesan = 3
    elseif ($_GET['pesan'] == 3) {
      // tampilkan pesan sukses hapus data
      echo '<div class="alert alert-notify alert-success alert-dismissible fade show" role="alert">
              <span data-notify="icon" class="fas fa-check"></span> 
              <span data-notify="title" class="text-success">Sukses!</span> 
              <span data-notify="message">Data keranjang berhasil dihapus.</span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';
    }
    // jika pesan = 4
    elseif ($_GET['pesan'] == 4) {
      // ambil data GET dari proses simpan/ubah
      $kode_keranjang = $_GET['kode_keranjang'];
      // tampilkan pesan gagal simpan data
      echo '<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
              <span data-notify="icon" class="fas fa-times"></span> 
              <span data-notify="title" class="text-danger">Gagal!</span> 
              <span data-notify="message">Kode keranjang <strong>' . $kode_keranjang . '</strong> sudah ada. Silahkan ganti kode keranjang yang Anda masukan.</span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';
    }
  }
?>
  <div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-45">
      <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
        <div class="page-header text-white">
          <!-- judul halaman -->
          <h4 class="page-title text-white"><i class="fas fa-shopping-basket mr-2"></i> Data Keranjang</h4>
          <!-- breadcrumbs -->
          <ul class="breadcrumbs">
            <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item"><a href="?module=keranjang" class="text-white">Data Keranjang</a></li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item"><a>Data</a></li>
          </ul>
        </div>
        <div class="ml-md-auto py-2 py-md-0">
          <!-- tombol entri data -->
          <a href="?module=form_entri_keranjang" class="btn btn-secondary btn-round mr-2">
            <span class="btn-label"><i class="fa fa-plus mr-2"></i></span> Entri Data
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="page-inner mt--5">
    <div class="card">
      <div class="card-header">
        <!-- judul tabel -->
        <div class="card-title">Data Keranjang</div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <!-- tabel untuk menampilkan data dari database -->
          <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th class="text-center">No.</th>
                <th class="text-center">Kode Keranjang</th>
                <th class="text-center">Serial Number</th>
                <th class="text-center">Nama Keranjang</th>
                <th class="text-center">Rak</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // variabel untuk nomor urut tabel
              $no = 1;
              // sql statement untuk menampilkan data dari tabel "tbl_keranjang" dengan join ke "tbl_rak"
              $query = mysqli_query($mysqli, "SELECT k.*, r.nama_rak 
                                             FROM tbl_keranjang k 
                                             LEFT JOIN tbl_rak r ON k.id_rak = r.id_rak 
                                             ORDER BY k.id_keranjang DESC")
                                              or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
              // ambil data hasil query
              while ($data = mysqli_fetch_assoc($query)) { 
                $idKeranjang = htmlspecialchars($data['id_keranjang']);
                ?>
                <!-- tampilkan data dengan accordion toggle -->
                <tr class="accordion-toggle collapsed" data-toggle="collapse" data-target="#aksi-<?php echo $idKeranjang; ?>" aria-expanded="false" style="cursor:pointer;">
                  <td width="30" class="text-center"><?php echo $no++; ?></td>
                  <td width="100" class="text-center"><?php echo $data['kode_keranjang']; ?></td>
                  <td class="text-center">
                    <div class="d-flex justify-content-center">
                      <img src="libs/barcode.php?text=<?php echo urlencode($data['kode_keranjang']); ?>&codetype=code128&size=30&print=true" 
                           alt="Barcode <?php echo $data['kode_keranjang']; ?>"
                           class="img-fluid" 
                           style="max-height: 60px;"
                           onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                      <small class="text-muted" style="display: none; font-size: 10px;">Barcode tidak tersedia</small>
                    </div>
                  </td>
                  <td width="200"><?php echo $data['nama_keranjang']; ?></td>
                  <td width="150"><?php echo $data['nama_rak'] ? $data['nama_rak'] : '-'; ?></td>
                </tr>
                <!-- Row untuk aksi yang akan muncul/tersembunyi -->
                <tr class="hide-table-padding aksi-row" style="display:none;">
                  <td></td>
                  <td colspan="4" style="border-top:1px solid #dee2e6; background:#f8f9fa; padding:16px 24px;">
                    <div id="aksi-<?php echo $idKeranjang; ?>">
                      <div class="row">
                        <div class="col-12">
                          <div class="d-flex flex-wrap">
                            <!-- tombol ubah data -->
                            <a href="?module=form_ubah_keranjang&id=<?php echo $data['id_keranjang']; ?>" 
                               class="btn btn-secondary btn-sm mr-2 mb-2">
                              <i class="fas fa-pencil-alt fa-sm mr-1"></i> Ubah
                            </a>
                            <!-- tombol hapus data -->
                            <a href="modules/keranjang/proses_hapus.php?id=<?php echo $data['id_keranjang']; ?>" 
                               onclick="return confirm('Anda yakin ingin menghapus data keranjang <?php echo addslashes($data['nama_keranjang']); ?>?')" 
                               class="btn btn-danger btn-sm mb-2">
                              <i class="fas fa-trash fa-sm mr-1"></i> Hapus
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- CSS untuk styling accordion -->
  <style>
    .hide-table-padding td { 
      padding: 0 !important; 
      background: #fff; 
    }
    .aksi-row { 
      background: #f8f9fa; 
    }
    .accordion-toggle.table-active, 
    .accordion-toggle:active, 
    .accordion-toggle:focus { 
      background: #e9ecef !important; 
    }
    .accordion-toggle:hover {
      background: #f8f9fa !important;
    }
    .accordion-toggle.collapsed {
      cursor: pointer;
    }
  </style>

  <!-- JavaScript untuk accordion functionality -->
  <script>
  // Initialize tooltips
  $(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
  });

  // Accordion functionality
  document.addEventListener('DOMContentLoaded', function() {
    var toggles = document.querySelectorAll('.accordion-toggle');
    var aksiRows = document.querySelectorAll('.aksi-row');
    var lastOpen = null;
    
    toggles.forEach(function(toggle) {
      toggle.addEventListener('click', function(e) {
        var next = toggle.nextElementSibling;
        
        // Tutup semua dulu
        aksiRows.forEach(function(row) { 
          row.style.display = 'none'; 
        });
        toggles.forEach(function(t) { 
          t.classList.remove('table-active'); 
          t.classList.add('collapsed'); 
        });
        
        // Toggle baris ini
        if (lastOpen === next) {
          lastOpen = null;
        } else {
          next.style.display = 'table-row';
          toggle.classList.add('table-active');
          toggle.classList.remove('collapsed');
          lastOpen = next;
        }
        
        e.stopPropagation();
      });
    });
    
    // Tutup semua jika klik di luar
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.accordion-toggle') && !e.target.closest('.aksi-row')) {
        aksiRows.forEach(function(row) { 
          row.style.display = 'none'; 
        });
        toggles.forEach(function(t) { 
          t.classList.remove('table-active'); 
          t.classList.add('collapsed'); 
        });
        lastOpen = null;
      }
    });
  });
  </script>

<?php } ?>