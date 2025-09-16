<?php
// Mencegah direct access file PHP
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
} else {
  // Tampilkan notifikasi jika ada pesan
  // Prioritas: tampilkan pesan dari session jika ada (di-set setelah aksi), lalu fallback ke GET
  if (isset($_SESSION['pesan'])) {
    echo $_SESSION['pesan'];
    unset($_SESSION['pesan']);
  }
  if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] == 1) {
      echo '<div class="alert alert-notify alert-success alert-dismissible fade show" role="alert">
              <span data-notify="icon" class="fas fa-check"></span> 
              <span data-notify="title" class="text-success">Sukses!</span> 
              <span data-notify="message">Data barang keluar berhasil disimpan.</span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';
    } elseif ($_GET['pesan'] == 2) {
      echo '<div class="alert alert-notify alert-success alert-dismissible fade show" role="alert">
              <span data-notify="icon" class="fas fa-check"></span> 
              <span data-notify="title" class="text-success">Sukses!</span> 
              <span data-notify="message">Data barang keluar berhasil dihapus.</span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';
    } elseif ($_GET['pesan'] == 4) {
      echo '<div class="alert alert-notify alert-success alert-dismissible fade show" role="alert">
              <span data-notify="icon" class="fas fa-check"></span> 
              <span data-notify="title" class="text-success">Sukses!</span> 
              <span data-notify="message">Status barang keluar berhasil diupdate.</span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';
    } elseif ($_GET['pesan'] == 'notadmin') {
      echo '<div class="alert alert-notify alert-warning alert-dismissible fade show" role="alert">
              <span data-notify="icon" class="fas fa-exclamation-triangle"></span> 
              <span data-notify="title" class="text-warning">Peringatan!</span> 
              <span data-notify="message">Anda tidak memiliki hak akses untuk melakukan aksi ini.</span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';
    }
  }

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

?>
  <div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-45">
      <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
        <div class="page-header text-white">
          <h4 class="page-title text-white"><i class="fas fa-sign-out-alt mr-2"></i> Barang Keluar</h4>
          <ul class="breadcrumbs">
            <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item"><a href="?module=barang_keluar" class="text-white">Barang Keluar</a></li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item"><a>Data</a></li>
          </ul>
        </div>
        <div class="ml-md-auto py-2 py-md-0">
          <?php 
            if (isset($_SESSION['hak_akses'])) { 
            ?>
                <a href="?module=form_entri_barang_keluar&type=keluar" class="btn btn-candy-green btn-round mr-2">
                  <span class="btn-label"><i class="fa fa-sign-out-alt mr-2"></i></span> Barang Keluar
                </a>
                <a href="?module=form_entri_barang_keluar&type=pinjam" class="btn btn-warning btn-round">
                  <span class="btn-label"><i class="fa fa-handshake mr-2"></i></span> Peminjaman
                </a>
                <a href="?module=pengembalian_form&type=pinjam" class="btn btn-info btn-round">
                  <span class="btn-label"><i class="fa fa-undo mr-2"></i></span> Pengembalian
                </a>
          <?php } ?>

        </div>
      </div>
    </div>
  </div>

  <div class="page-inner mt--5">
    <div class="card">
      <div class="card-header">
        <div class="card-title">Data Barang Keluar</div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th class="text-center">No.</th>
                <th class="text-center">ID Transaksi</th>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Serial Number</th> 
                <th class="text-center">Barang</th>
                <th class="text-center">Foto</th>
                <th class="text-center">Jumlah Keluar</th>
                <th class="text-center">Satuan</th>
                <th class="text-center">Jenis</th>
                <th class="text-center">Dibuat Oleh</th>
                <th class="text-center">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $no = 1;
              $query = mysqli_query($mysqli, "
  SELECT a.id_transaksi, a.tanggal, a.barang AS id_barang, a.jumlah, a.jenis, a.status, a.serial_number, a.created_by,
         b.nama_barang, b.foto, b.serial_number as barang_serial_number, c.nama_satuan,
         r.nama_rak, r.lokasi as lokasi_rak, k.nama_keranjang, k.kondisi as kondisi_keranjang,
         u.nama_user, u.email as user_email
  FROM tbl_barang_keluar AS a
  INNER JOIN tbl_barang AS b ON a.barang = b.id_barang
  INNER JOIN tbl_satuan AS c ON b.satuan = c.id_satuan
  LEFT JOIN tbl_rak AS r ON a.id_rak = r.id_rak
  LEFT JOIN tbl_keranjang AS k ON a.id_keranjang = k.id_keranjang
  LEFT JOIN tbl_user AS u ON a.created_by = u.id_user
  ORDER BY a.id_transaksi DESC
") or die('Ada kesalahan pada query tampil data: ' . mysqli_error($mysqli));

              while ($data = mysqli_fetch_assoc($query)) { 
                $idTransaksi = htmlspecialchars($data['id_transaksi']);
                $jenis = $data['jenis'];
                $status = $data['status'];
                ?>
                <tr class="accordion-toggle collapsed" data-toggle="collapse" data-target="#aksi-<?php echo $idTransaksi; ?>" aria-expanded="false" style="cursor:pointer;">
                  <td class="text-center"><?php echo $no++; ?></td>
                  <td class="text-center"><?php echo $idTransaksi; ?></td>
                  <td class="text-center"><?php echo getHariIndonesia($data['tanggal']) . ', ' . date('d-m-Y', strtotime($data['tanggal'])); ?></td>
                  <td width="150" class="text-center">
                    <?php 
                      // Determine which serial number to use (transaction serial number or item serial number)
                      if (!empty($data['serial_number'])) {
                        // Use transaction serial number
                        $sn = $data['serial_number'];
                        $sn_source = 'transaction';
                      } elseif (!empty($data['barang_serial_number'])) {
                        // Use item serial number
                        $sn = $data['barang_serial_number'];
                        $sn_source = 'item';
                      } else {
                        // Use item ID as fallback for barcode generation
                        $sn = $data['id_barang'];
                        $sn_source = 'item_id';
                      }
                      
                      // Display the serial number or item ID
                      if ($sn_source === 'item_id') {
                        echo '<span class="text-muted">' . htmlspecialchars($sn) . '</span><br>';
                        echo '<small class="text-muted">ID Barang</small>';
                      } else {
                        echo htmlspecialchars($sn);
                      }
                    ?><br>
                    <img src="libs/barcode.php?text=<?php echo urlencode($sn); ?>&codetype=code128&size=30&print=true" alt="Barcode <?php echo htmlspecialchars($sn); ?>" style="margin: 5px 0;">
                  </td>
                  <td>
                    <?php echo htmlspecialchars($data['nama_barang']); ?>
                  </td>
                  <td class="text-center">
                    <?php 
                    if (!empty($data['foto'])) {
                      // Use absolute path for file_exists check
                      $absolute_path = __DIR__ . "/../../images/" . $data['foto'];
                      // Use relative path for src attribute
                      $relative_path = "images/" . $data['foto'];
                      
                      if (file_exists($absolute_path)) { ?>
                        <img src="<?php echo $relative_path; ?>" alt="Foto Barang" width="60" height="60" style="object-fit: cover; border-radius: 8px;">
                      <?php } else { ?>
                        <img src="images/no_image.png" alt="Foto tidak ditemukan" width="60" height="60" style="object-fit: cover; border-radius: 8px; opacity: 0.5;">
                      <?php }
                    } else { ?>
                      <img src="images/no_image.png" alt="Tidak ada foto" width="60" height="60" style="object-fit: cover; border-radius: 8px; opacity: 0.5;">
                    <?php } ?>
                  </td>
                  <td class="text-right"><?php echo number_format($data['jumlah'], 0, '', '.'); ?></td>
                  <td class="text-center"><?php echo htmlspecialchars($data['nama_satuan']); ?></td>
                  <td class="text-center"><?php echo htmlspecialchars($jenis); ?></td>
                  <td class="text-center">
                    <?php 
                      if (!empty($data['nama_user'])) {
                        echo '<span class="badge badge-info">' . htmlspecialchars($data['nama_user']) . '</span>';
                      } else {
                        echo '<span class="badge badge-secondary">Tidak diketahui</span>';
                      }
                    ?>
                  </td>
                  <td class="text-center">
                    <?php
                      if ($status == 'Menunggu Persetujuan') {
                        echo '<span class="badge badge-warning">Menunggu Persetujuan</span>';
                      } elseif ($status == 'Barang Keluar') {
                        echo '<span class="badge badge-success">Barang Keluar</span>';
                      } elseif ($status == 'Disetujui') {
                        echo '<span class="badge badge-success">Disetujui</span>';
                      } else {
                        echo '<span class="badge badge-danger">Ditolak</span>';
                      }
                    ?>
                  </td>
                </tr>
                <tr class="hide-table-padding aksi-row" style="display:none;">
                  <td></td>
                  <td colspan="13" style="border-top:1px solid #dee2e6; background:#f8f9fa; padding:16px 24px;"> <!-- Update colspan menjadi 13 -->
                    <div id="aksi-<?php echo $idTransaksi; ?>">
                      <div class="row">
                        <div class="col-12">
                          <?php
                          // Cek hak akses user
                          $userRole = isset($_SESSION['hak_akses']) ? $_SESSION['hak_akses'] : '';
                          
                          if ($userRole === 'Teknisi') {
                            // TEKNISI: Bisa lihat detail transaksi untuk semua jenis
                            echo '<a class="btn btn-secondary btn-sm" href="main.php?module=form_selesai&id='.$idTransaksi.'"><i class="fas fa-info-circle mr-1"></i> Detail Transaksi</a>';
                          } else {
                            // ROLE LAIN (Admin Gudang, Administrator, Kepala Gudang): Akses penuh
                            if ($status === 'Menunggu Persetujuan') {
                              echo '<a class="btn btn-success btn-sm mr-2" href="modules/barang-keluar/proses_status.php?id='.$idTransaksi.'&status=Disetujui" onclick="return confirm(\'Setujui barang keluar '.$idTransaksi.'?\')"><i class="fas fa-check fa-sm mr-1"></i> Setujui</a>';
                              echo '<a class="btn btn-warning btn-sm mr-2" href="modules/barang-keluar/proses_status.php?id='.$idTransaksi.'&status=Ditolak" onclick="return confirm(\'Tolak barang keluar '.$idTransaksi.'?\')"><i class="fas fa-times fa-sm mr-1"></i> Tolak</a>';
                            }
                            // Detail Transaksi untuk semua jenis transaksi
                            echo '<a class="btn btn-secondary btn-sm mr-2" href="main.php?module=form_selesai&id='.$idTransaksi.'"><i class="fas fa-info-circle mr-1"></i> Detail Transaksi</a>';
                            echo '<a class="btn btn-danger btn-sm" href="modules/barang-keluar/proses_hapus.php?id='.$idTransaksi.'" onclick="return confirm(\'Anda yakin ingin menghapus data barang keluar '.$idTransaksi.'?\')"><i class="fas fa-trash fa-sm mr-1"></i> Hapus</a>';
                          }
                          ?>
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
  <style>
    .hide-table-padding td { padding: 0 !important; background: #fff; }
    .aksi-row { background: #f8f9fa; }
    .accordion-toggle.table-active, .accordion-toggle:active, .accordion-toggle:focus { background: #e9ecef !important; }
    /* Candy green button style (applied to .btn-candy-green) */
    .btn-candy-green {
      background: linear-gradient(180deg, #9be7b6 0%, #6fdc92 100%);
      border-color: #5dd07d;
      color: #fff;
      box-shadow: 0 2px 6px rgba(111,220,146,0.25);
    }
    .btn-candy-green:hover, .btn-candy-green:focus {
      background: linear-gradient(180deg, #86e3a7 0%, #54c86f 100%);
      border-color: #44bd5f;
      color: #fff;
    }
  </style>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    var toggles = document.querySelectorAll('.accordion-toggle');
    var aksiRows = document.querySelectorAll('.aksi-row');
    var lastOpen = null;
    toggles.forEach(function(toggle) {
      toggle.addEventListener('click', function(e) {
        var next = toggle.nextElementSibling;
        // Tutup semua dulu
        aksiRows.forEach(function(row) { row.style.display = 'none'; });
        toggles.forEach(function(t) { t.classList.remove('table-active'); t.classList.add('collapsed'); });
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
        aksiRows.forEach(function(row) { row.style.display = 'none'; });
        toggles.forEach(function(t) { t.classList.remove('table-active'); t.classList.add('collapsed'); });
        lastOpen = null;
      }
    });
  });
</script>

<?php } ?>