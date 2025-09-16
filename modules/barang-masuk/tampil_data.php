<?php
// Mencegah direct access file PHP
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
} else {
  // Menampilkan pesan notifikasi jika ada
  if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] == 1) {
      echo '<div class="alert alert-notify alert-success alert-dismissible fade show" role="alert">
              <span data-notify="icon" class="fas fa-check"></span> 
              <span data-notify="title" class="text-success">Sukses!</span> 
              <span data-notify="message">Data barang masuk berhasil disimpan.</span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';
    } elseif ($_GET['pesan'] == 2) {
      echo '<div class="alert alert-notify alert-success alert-dismissible fade show" role="alert">
              <span data-notify="icon" class="fas fa-check"></span> 
              <span data-notify="title" class="text-success">Sukses!</span> 
              <span data-notify="message">Data barang masuk berhasil dihapus.</span>
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
          <h4 class="page-title text-white"><i class="fas fa-sign-in-alt mr-2"></i> Barang Masuk</h4>
          <ul class="breadcrumbs">
            <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item"><a href="?module=barang_masuk" class="text-white">Barang Masuk</a></li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item"><a>Data</a></li>
          </ul>
        </div>
        <div class="ml-md-auto py-2 py-md-0">
          <a href="?module=form_entri_barang_masuk" class="btn btn-secondary btn-round">
            <span class="btn-label"><i class="fa fa-plus mr-2"></i></span> Entri Data
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="page-inner mt--5">
    <div class="card">
      <div class="card-header">
        <div class="card-title">Data Barang Masuk</div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <!-- Remove search form completely -->
          <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
            <thead>
  <tr>
    <th class="text-center">No.</th>
    <th class="text-center">ID Transaksi</th>
    <th class="text-center">Tanggal</th>
    <th class="text-center">Serial Number</th> <!-- Tambahan -->
    <th class="text-center">Barang</th>
    <th class="text-center">Foto</th>
    <th class="text-center">Jumlah Masuk</th>
    <th class="text-center">Satuan</th>
    <th class="text-center">Lokasi Rak</th>
    <th class="text-center">Keranjang</th>
    <th class="text-center">Aksi</th>
  </tr>
</thead>
<tbody>
              <?php
              $no = 1;
              
              // Simplified query without search functionality
              $query_str = "
                SELECT a.id_transaksi, a.tanggal, a.barang AS id_barang, a.jumlah, a.serial_number as transaction_serial_number,
                  b.nama_barang, b.foto, b.serial_number as barang_serial_number, c.nama_satuan,
                  r.nama_rak, r.lokasi as lokasi_rak, k.nama_keranjang, k.kondisi as kondisi_keranjang
                FROM tbl_barang_masuk AS a
                INNER JOIN tbl_barang AS b ON a.barang = b.id_barang
                INNER JOIN tbl_satuan AS c ON b.satuan = c.id_satuan
                LEFT JOIN tbl_rak AS r ON a.id_rak = r.id_rak
                LEFT JOIN tbl_keranjang AS k ON a.id_keranjang = k.id_keranjang
                ORDER BY a.id_transaksi DESC";
              
              $query = mysqli_query($mysqli, $query_str) or die('Ada kesalahan pada query tampil data: ' . mysqli_error($mysqli));

              // Check if any data found
              if (mysqli_num_rows($query) == 0) {
                echo '<tr><td colspan="11" class="text-center">Tidak ada data yang ditemukan.</td></tr>';
              }

              while ($data = mysqli_fetch_assoc($query)) { ?>
                <tr>
      <td class="text-center"><?php echo $no++; ?></td>
      <td class="text-center"><?php echo $data['id_transaksi']; ?></td>
      <td class="text-center">
            <?php echo getHariIndonesia($data['tanggal']) . ', ' . date('d-m-Y', strtotime($data['tanggal'])); ?>
      </td>
      <td width="150" class="text-center">
  <?php 
    // Prioritize transaction serial number over item serial number
    if (!empty($data['transaction_serial_number'])) {
        $sn = $data['transaction_serial_number'];
        $sn_source = 'transaction';
    } elseif (!empty($data['barang_serial_number'])) {
        $sn = $data['barang_serial_number'];
        $sn_source = 'item';
    } else {
        $sn = $data['id_barang'];
        $sn_source = 'item_id';
    }

    echo htmlspecialchars($sn); 
  ?><br>
  <img src="libs/barcode.php?text=<?php echo urlencode($sn); ?>&codetype=code128&size=30&print=true" alt="Barcode <?php echo htmlspecialchars($sn); ?>" style="margin: 5px 0;"><br>
  <!-- Jika ada foto khusus serial number, tampilkan di sini -->
  <?php if (!empty($data['foto_serial'])) { ?>
    <img src="images/<?php echo $data['foto_serial']; ?>" alt="Foto Serial Number" width="60" height="60" style="margin-top:5px;object-fit:cover;border-radius:8px;">
  <?php } ?>
</td>
      <td> <?php echo $data['nama_barang']; ?></td>

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
      <td class="text-center"><?php echo $data['nama_satuan']; ?></td>
      <td class="text-center">
        <?php 
        if (!empty($data['nama_rak'])) {
          echo htmlspecialchars($data['nama_rak']);
          if (!empty($data['lokasi_rak'])) {
            echo '<br><small class="text-muted">' . htmlspecialchars($data['lokasi_rak']) . '</small>';
          }
        } else {
          echo '<span class="text-muted">-</span>';
        }
        ?>
      </td>
      <td class="text-center">
        <?php 
        if (!empty($data['nama_keranjang'])) {
          echo htmlspecialchars($data['nama_keranjang']);
        } else {
          echo '<span class="text-muted">-</span>';
        }
        ?>
      </td>
      <td class="text-center">
        <a href="modules/barang-masuk/proses_hapus.php?id=<?php echo $data['id_transaksi']; ?>" onclick="return confirm('Anda yakin ingin menghapus data barang masuk <?php echo $data['id_transaksi']; ?>?')" class="btn btn-icon btn-round btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Hapus">
          <i class="fas fa-trash fa-sm"></i>
        </a>
      </td>
    </tr>
              <?php } ?>
            </tbody>

                      

          </table>
        </div>
      </div>
    </div>
  </div>
  
  <script>
  // Remove auto-search JavaScript functionality
  $(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
  });
  </script>
  
<?php } ?>