<?php
// mencegah direct access file PHP
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header('location: 404.html');
    exit;
} else {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
        header('location: ../../login.php?pesan=2');
        exit;
    } else {
        require_once "config/database.php";

        function getHariIndonesia($tanggal) {
            $hariInggris = date('l', strtotime($tanggal));
            $hariIndonesia = [
                'Sunday'    => 'Minggu',
                'Monday'    => 'Senin', 
                'Tuesday'   => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday'  => 'Kamis',
                'Friday'    => 'Jumat',
                'Saturday'  => 'Sabtu',
            ];
            return $hariIndonesia[$hariInggris] ?? $hariInggris;
        }

        // cek apakah id ada di URL
        if (isset($_GET['id'])) {
            $id_transaksi = mysqli_real_escape_string($mysqli, $_GET['id']);

            // ambil data transaksi dari database
            $query = "SELECT bk.id_transaksi, bk.tanggal, bk.barang, bk.jumlah, bk.jenis, bk.status, 
                             bk.tanggal_pengembalian, bk.kondisi, bk.catatan, bk.foto_pengembalian,
                             bk.serial_number as transaksi_serial_number, bk.id_rak, bk.id_keranjang,
                             b.nama_barang, b.foto, b.id_barang, b.serial_number as barang_serial_number,
                             s.nama_satuan, r.nama_rak, r.lokasi as lokasi_rak, 
                             k.nama_keranjang, k.kondisi as kondisi_keranjang
                      FROM tbl_barang_keluar AS bk
                      LEFT JOIN tbl_barang AS b ON bk.barang = b.id_barang
                      LEFT JOIN tbl_satuan AS s ON b.satuan = s.id_satuan
                      LEFT JOIN tbl_rak AS r ON bk.id_rak = r.id_rak
                      LEFT JOIN tbl_keranjang AS k ON bk.id_keranjang = k.id_keranjang
                      WHERE bk.id_transaksi='$id_transaksi'";

            $result = mysqli_query($mysqli, $query);

            if (!$result) {
                die('Query Error: ' . mysqli_error($mysqli));
            }

            $data = mysqli_fetch_assoc($result);

            if (!$data) {
                echo '<div class="alert alert-danger">Data transaksi tidak ditemukan untuk ID: ' . htmlspecialchars($id_transaksi) . '</div>';
                exit;
            }
        } else {
            header("Location: ../../main.php?module=barang_keluar");
            exit;
        }
    }
?>

<!-- Header Panel dengan Gradient Background -->
<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-45">
    <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
      <div class="page-header text-white">
        <!-- judul halaman -->
        <h4 class="page-title text-white"><i class="fas fa-file-alt mr-2"></i> Detail Transaksi</h4>
        <!-- breadcrumbs -->
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="?module=barang_keluar" class="text-white">Barang Keluar</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Detail</a></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- Content Area -->
<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="d-flex align-items-center">
        <div class="card-title">Detail Transaksi #<?php echo htmlspecialchars($data['id_transaksi']); ?></div>
        <div class="ml-auto">
          <!-- Tombol Cetak -->
          <a href="modules/barang-keluar/cetak.php?id=<?php echo $data['id_transaksi']; ?>" 
   target="_blank" 
   class="btn btn-success btn-sm mr-2">
   <i class="fas fa-print mr-1"></i> Cetak
</a>

          <!-- Tombol Kembali -->
          <a href="main.php?module=dashboard" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
          </a>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="row">
        <!-- Informasi Transaksi -->
        <div class="col-md-8">
          <div class="card shadow-sm">
            <div class="card-header bg-light">
              <h5 class="card-title mb-0"><i class="fas fa-info-circle mr-2"></i>Informasi Transaksi</h5>
            </div>
            <div class="card-body">
              <div class="row mb-3">
                <div class="col-sm-4"><strong>ID Transaksi:</strong></div>
                <div class="col-sm-8"><?php echo htmlspecialchars($data['id_transaksi']); ?></div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-4"><strong>Tanggal:</strong></div>
                <div class="col-sm-8">
                  <?php 
                    $tanggal = date('d-m-Y', strtotime($data['tanggal']));
                    $hari = getHariIndonesia($data['tanggal']);
                    echo "$hari, $tanggal";
                  ?>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-4"><strong>Nama Barang:</strong></div>
                <div class="col-sm-8"><?php echo htmlspecialchars($data['nama_barang']); ?></div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-4"><strong>Serial Number:</strong></div>
                <div class="col-sm-8">
                  <?php 
                    // gunakan serial transaksi jika ada, else barang serial atau id
                    $sn = !empty($data['transaksi_serial_number']) ? $data['transaksi_serial_number'] : (!empty($data['barang_serial_number']) ? $data['barang_serial_number'] : $data['id_barang']);
                    echo htmlspecialchars($sn);
                  ?>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-4"><strong>Jumlah:</strong></div>
                <div class="col-sm-8"><?php echo number_format($data['jumlah']) . ' ' . htmlspecialchars($data['nama_satuan']); ?></div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-4"><strong>Jenis Transaksi:</strong></div>
                <div class="col-sm-8">
                  <span class="badge badge-<?php echo ($data['jenis'] == 'Masuk') ? 'success' : 'primary'; ?>">
                    <?php echo htmlspecialchars($data['jenis']); ?>
                  </span>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-4"><strong>Status:</strong></div>
                <div class="col-sm-8">
                  <?php
                    $status_class = '';
                    switch($data['status']) {
                      case 'Disetujui':
                        $status_class = 'success'; // hijau
                        break;
                      case 'Ditolak':
                        $status_class = 'danger'; // merah
                        break;
                      case 'Dipinjam':
                        $status_class = 'warning';
                        break;
                      case 'Dikembalikan':
                        $status_class = 'success';
                        break;
                      default:
                        $status_class = 'secondary';
                    }
                  ?>
                  <span class="badge badge-<?php echo $status_class; ?>">
                    <?php echo htmlspecialchars($data['status']); ?>
                  </span>
                </div>
              </div>
              
              <!-- Informasi Lokasi -->
              <div class="row mb-3">
                <div class="col-sm-4"><strong>Lokasi Rak:</strong></div>
                <div class="col-sm-8">
                  <?php 
                  if (!empty($data['nama_rak'])) {
                    echo htmlspecialchars($data['nama_rak']);
                    if (!empty($data['lokasi_rak'])) {
                      echo '<br><small class="text-muted">' . htmlspecialchars($data['lokasi_rak']) . '</small>';
                    }
                  } else {
                    echo '<span class="text-muted">Tidak ditentukan</span>';
                  }
                  ?>
                </div>
              </div>
              
              <div class="row mb-3">
                <div class="col-sm-4"><strong>Keranjang:</strong></div>
                <div class="col-sm-8">
                  <?php 
                  if (!empty($data['nama_keranjang'])) {
                    echo htmlspecialchars($data['nama_keranjang']);
                  } else {
                    echo '<span class="text-muted">Tidak ditentukan</span>';
                  }
                  ?>
                </div>
              </div>
              <?php if (!empty($data['tanggal_pengembalian']) || !empty($data['kondisi']) || !empty($data['catatan'])) { ?>
              <!-- Informasi Pengembalian -->
              <hr>
              <h6><i class="fas fa-undo mr-2"></i>Informasi Pengembalian</h6>
              
              <?php if (!empty($data['tanggal_pengembalian'])) { ?>
              <div class="row mb-3">
                <div class="col-sm-4"><strong>Tanggal Pengembalian:</strong></div>
                <div class="col-sm-8">
                  <?php 
                    $tgl_kembali = date('d-m-Y', strtotime($data['tanggal_pengembalian']));
                    $hari_kembali = getHariIndonesia($data['tanggal_pengembalian']);
                    echo "$hari_kembali, $tgl_kembali";
                  ?>
                </div>
              </div>
              <?php } ?>
              
              <?php if (!empty($data['kondisi'])) { ?>
              <div class="row mb-3">
                <div class="col-sm-4"><strong>Kondisi:</strong></div>
                <div class="col-sm-8">
                  <?php
                    $kondisi_class = '';
                    switch($data['kondisi']) {
                      case 'Baik':
                        $kondisi_class = 'success';
                        break;
                      case 'Rusak':
                        $kondisi_class = 'warning';
                        break;
                      case 'Hilang':
                        $kondisi_class = 'danger';
                        break;
                      case 'Rusak Ringan':
                        $kondisi_class = 'warning';
                        break;
                      case 'Rusak Berat':
                        $kondisi_class = 'danger';
                        break;
                      default:
                        $kondisi_class = 'secondary';
                    }
                  ?>
                  <span class="badge badge-<?php echo $kondisi_class; ?>">
                    <?php echo htmlspecialchars($data['kondisi']); ?>
                  </span>
                </div>
              </div>
              <?php } ?>
              
              <?php if (!empty($data['catatan'])) { ?>
              <div class="row mb-3">
                <div class="col-sm-4"><strong>Catatan:</strong></div>
                <div class="col-sm-8"><?php echo nl2br(htmlspecialchars($data['catatan'])); ?></div>
              </div>
              <?php } ?>
              <?php } ?>
            </div>
          </div>
        </div>
        
        <!-- Foto dan Barcode -->
        <div class="col-md-4">
          <!-- Foto Barang -->
          <div class="card shadow-sm mb-3">
            <div class="card-header bg-light">
              <h6 class="card-title mb-0"><i class="fas fa-image mr-2"></i>Foto Barang</h6>
            </div>
            <div class="card-body text-center">
              <?php 
                $img_file = $data['foto'] ?? '';
                $img_abs_path = __DIR__ . '/../../images/' . $img_file;
                $img_url = 'images/' . rawurlencode($img_file);
                if (!empty($img_file) && file_exists($img_abs_path)) { ?>
                    <img src="<?php echo $img_url; ?>" 
                         alt="Foto Barang" 
                         class="img-fluid rounded shadow"
                         style="max-height: 200px; cursor: pointer;"
                         onclick="tampilGambar('<?php echo $img_url; ?>')">
              <?php } else { ?>
                <div class="d-flex align-items-center justify-content-center" style="height: 150px; background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 5px;">
                  <div class="text-center">
                    <i class="fas fa-image fa-3x text-muted mb-2"></i>
                    <p class="text-muted">Tidak ada foto</p>
                  </div>
                </div>
                  <?php } ?>
                <?php if (!empty($img_file)): ?>
                <?php endif; ?>
            </div>
          </div>
          
          <!-- Barcode -->
          <div class="card shadow-sm mb-3">
            <div class="card-header bg-light">
              <h6 class="card-title mb-0"><i class="fas fa-barcode mr-2"></i>Barcode</h6>
            </div>
            <div class="card-body text-center">
              <?php
              // gunakan serial transaksi jika tersedia, fallback ke serial barang atau id
              $sn_barcode = !empty($data['transaksi_serial_number']) ? $data['transaksi_serial_number'] : (!empty($data['barang_serial_number']) ? $data['barang_serial_number'] : $data['id_barang']);
              $barcode_url = 'libs/barcode.php?text=' . urlencode($sn_barcode) . '&codetype=code128&size=40&print=true';
              ?>
              <div class="mb-2">
                <strong><?php echo htmlspecialchars($sn_barcode); ?></strong>
              </div>
              <img src="<?php echo $barcode_url; ?>" 
                   alt="Barcode <?php echo htmlspecialchars($sn_barcode); ?>"
                   class="img-fluid"
                   onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
              <div style="display: none; color: #666; font-size: 12px;">Barcode tidak tersedia</div>
            </div>
          </div>
          
          <!-- Foto Pengembalian jika ada -->
          <?php 
      $foto_peng = $data['foto_pengembalian'] ?? '';
      // cek kemungkinan lokasi: images/ atau images/pengembalian/
      $foto_peng_abs_root = __DIR__ . '/../../images/' . $foto_peng;
      $foto_peng_abs_sub  = __DIR__ . '/../../images/pengembalian/' . $foto_peng;
      $foto_peng_url_root  = 'images/' . rawurlencode($foto_peng);
      $foto_peng_url_sub   = 'images/pengembalian/' . rawurlencode($foto_peng);
      $foto_peng_found = false;
      $foto_peng_url = '';
      if (!empty($foto_peng) && file_exists($foto_peng_abs_root)) {
        $foto_peng_found = true;
        $foto_peng_url = $foto_peng_url_root;
      } elseif (!empty($foto_peng) && file_exists($foto_peng_abs_sub)) {
        $foto_peng_found = true;
        $foto_peng_url = $foto_peng_url_sub;
      }
      // Tampilkan foto pengembalian jika ada, tidak peduli status
      if (!empty($foto_peng) && $foto_peng_found) { ?>
          <div class="card shadow-sm">
            <div class="card-header bg-light">
              <h6 class="card-title mb-0"><i class="fas fa-camera mr-2"></i>Foto Pengembalian</h6>
            </div>
            <div class="card-body text-center">
              <img src="<?php echo $foto_peng_url; ?>" 
                   alt="Foto Pengembalian" 
                   class="img-fluid rounded shadow"
                   style="max-height: 200px; cursor: pointer; object-fit: cover;"
                   onclick="tampilGambar('<?php echo $foto_peng_url; ?>')">
            </div>
          </div>
          <?php } ?>
        </div>
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

<script>
function tampilGambar(src) {
  document.getElementById("previewImg").src = src;
  $('#modalGambar').modal('show');
}
</script>

<?php } ?>