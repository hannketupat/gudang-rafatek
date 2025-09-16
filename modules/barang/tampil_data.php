<?php
// mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser 
// dan hanya dapat dijalankan ketika di include oleh file lain
// jika file diakses secara langsung
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  // alihkan ke halaman error 404
  header('location: 404.html');
  exit;
} else {
  // panggil file database.php untuk koneksi ke database
  require_once "config/database.php";

  // menampilkan pesan sesuai dengan proses yang dijalankan
  // jika pesan tersedia
  if (isset($_GET['pesan'])) {
    // jika pesan = 1
    if ($_GET['pesan'] == 1) {
      // tampilkan pesan sukses simpan data
      echo '<div class="alert alert-notify alert-success alert-dismissible fade show" role="alert">
              <span data-notify="icon" class="fas fa-check"></span> 
              <span data-notify="title" class="text-success">Sukses!</span> 
              <span data-notify="message">Data barang berhasil disimpan.</span>
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
              <span data-notify="message">Data barang berhasil diubah.</span>
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
              <span data-notify="message">Data barang berhasil dihapus.</span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';
    }
    // jika pesan = 4
    elseif ($_GET['pesan'] == 4) {
      // tampilkan pesan gagal hapus data
      echo '<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
              <span data-notify="icon" class="fas fa-times"></span> 
              <span data-notify="title" class="text-danger">Gagal!</span> 
              <span data-notify="message">Data barang tidak bisa dihapus karena sudah tercatat pada Data Transaksi.</span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';
    }
  }
?>
  <!-- Header Panel dengan Gradient Background -->
  <div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-45">
      <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
        <div class="page-header text-white">
          <!-- judul halaman -->
          <h4 class="page-title text-white"><i class="fas fa-clone mr-2"></i> Barang</h4>
          <!-- breadcrumbs -->
          <ul class="breadcrumbs">
            <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item"><a href="?module=barang" class="text-white">Barang</a></li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item"><a>Data</a></li>
          </ul>
        </div>
        <div class="ml-md-auto py-2 py-md-0">
          <!-- tombol entri data -->
          <a href="?module=form_entri_barang" class="btn btn-secondary btn-round">
            <span class="btn-label"><i class="fa fa-plus mr-2"></i></span> Entri Data
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Content Area -->
  <div class="page-inner mt--5">
    <div class="card">
      <div class="card-header">
        <!-- judul tabel -->
        <div class="card-title">Data Barang</div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <!-- Search box will be automatically added by DataTables -->
          <div class="mb-3">
            <label for="basic-datatables_filter" class="form-label">Cari Data Barang:</label>
          </div>
          <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th class="text-center" width="30">No</th>
                <th class="text-center" width="150">Serial Number</th>
                <th class="text-center" width="200">Nama Barang</th>
                <th class="text-center" width="80">Stok</th>
                <th class="text-center" width="100">Satuan</th>
                <th class="text-center" width="150">Lokasi Rak</th>
                <th class="text-center" width="120">Keranjang</th>
                <th class="text-center" width="120">Foto</th>
              </tr>
            </thead>
            <tbody>
              <?php
              
              $no = 1;
              
              $query = mysqli_query($mysqli, "SELECT a.id_barang, a.serial_number, a.nama_barang, a.stok, a.satuan, a.foto, 
                                                     b.nama_satuan, 
                                                     r.nama_rak, r.lokasi as lokasi_rak,
                                                     k.nama_keranjang, k.kondisi as kondisi_keranjang
                                              FROM tbl_barang as a 
                                              INNER JOIN tbl_satuan as b ON a.satuan = b.id_satuan 
                                              LEFT JOIN tbl_rak as r ON a.id_rak = r.id_rak
                                              LEFT JOIN tbl_keranjang as k ON a.id_keranjang = k.id_keranjang
                                              ORDER BY a.id_barang DESC")
                                              or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
              // ambil data hasil query
              while ($data = mysqli_fetch_assoc($query)) { 
                $idBarang = htmlspecialchars($data['id_barang']);
                ?>
                <!-- tampilkan data dengan accordion toggle -->
                <tr class="accordion-toggle collapsed" data-toggle="collapse" data-target="#aksi-<?php echo $idBarang; ?>" aria-expanded="false" style="cursor:pointer;">
                  <td class="text-center"><?php echo $no++; ?></td>
                  <td class="text-center">
                    <div style="padding: 5px;">
                      <?php 
                        $sn = !empty($data['serial_number']) ? $data['serial_number'] : $data['id_barang']; 
                      ?>
                      <div style="font-weight: 600; margin-bottom: 5px;"><?php echo htmlspecialchars($sn); ?></div>
                      <div>
                        <?php
                        $barcode_url = "libs/barcode.php?text=" . urlencode($sn) . "&codetype=code128&size=30&print=true";
                        ?>
                        <img src="<?php echo $barcode_url; ?>" 
                             alt="Barcode <?php echo htmlspecialchars($sn); ?>"
                             style="max-width: 120px; height: auto;"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <div style="display: none; color: #666; font-size: 12px;">Barcode tidak tersedia</div>
                      </div>
                    </div>
                  </td>
                  <td><?php echo htmlspecialchars($data['nama_barang']); ?></td>
                  <td class="text-right"><?php echo number_format($data['stok']); ?></td>
                  <td class="text-center"><?php echo htmlspecialchars($data['nama_satuan']); ?></td>
                  <td class="text-center">
                    <?php if (!empty($data['nama_rak'])) { ?>
                      <div style="font-weight: 600; color: #495057;"><?php echo htmlspecialchars($data['nama_rak']); ?></div>
                      <small class="text-muted"><?php echo htmlspecialchars($data['lokasi_rak']); ?></small>
                    <?php } else { ?>
                      <span class="text-muted">-</span>
                    <?php } ?>
                  </td>
                  <td class="text-center">
                    <?php if (!empty($data['nama_keranjang'])) { ?>
                      <div style="font-weight: 600; color: #495057;"><?php echo htmlspecialchars($data['nama_keranjang']); ?></div>
                      <small class="text-muted">Kondisi: <?php echo htmlspecialchars($data['kondisi_keranjang']); ?></small>
                      <span class="text-muted">-</span>
                    <?php } ?>
                  </td>
                  <td class="text-center">
                    <?php if (!empty($data['foto']) && file_exists("images/" . $data['foto'])) { ?>
                      <img src="images/<?php echo $data['foto']; ?>" 
                           alt="Foto Barang" 
                           style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px; cursor: pointer; border: 2px solid #dee2e6;"
                           onclick="event.stopPropagation(); tampilGambar('images/<?php echo $data['foto']; ?>')">
                    <?php } else { ?>
                      <div style="width: 60px; height: 60px; background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 5px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                        <i class="fas fa-image text-muted"></i>
                      </div>
                    <?php } ?>
                  </td>
                </tr>
                <!-- Row untuk aksi yang akan muncul/tersembunyi -->
                <tr class="hide-table-padding aksi-row" style="display:none;">
                  <td></td>
                  <td colspan="7" style="border-top:1px solid #dee2e6; background:#f8f9fa; padding:16px 24px;">
                    <div id="aksi-<?php echo $idBarang; ?>">
                      <div class="row">
                        <div class="col-12">
                          <div class="d-flex flex-wrap">
                            <!-- tombol detail data -->
                            <a href="?module=tampil_detail_barang&id=<?php echo $data['id_barang']; ?>" 
                               class="btn btn-primary btn-sm mr-2 mb-2">
                              <i class="fas fa-clone fa-sm mr-1"></i> Detail
                            </a>
                            <!-- tombol ubah data -->
                            <a href="?module=form_ubah_barang&id=<?php echo $data['id_barang']; ?>" 
                               class="btn btn-secondary btn-sm mr-2 mb-2">
                              <i class="fas fa-pencil-alt fa-sm mr-1"></i> Ubah
                            </a>
                            <!-- tombol hapus data -->
                            <a href="modules/barang/proses_hapus.php?id=<?php echo $data['id_barang']; ?>" 
                               onclick="return confirm('Anda yakin ingin menghapus data barang <?php echo addslashes($data['nama_barang']); ?>?')" 
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

  <!-- Modal Preview Gambar -->
  <div class="modal fade" id="modalGambar" tabindex="-1" role="dialog" aria-labelledby="modalGambarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalGambarLabel">Preview Gambar</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body text-center">
          <img id="previewImg" src="" alt="Preview" class="img-fluid rounded shadow">
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
  function tampilGambar(src) {
    document.getElementById("previewImg").src = src;
    $('#modalGambar').modal('show');
  }

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