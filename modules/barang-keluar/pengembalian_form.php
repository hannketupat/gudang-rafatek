<?php
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
} else {
  require_once __DIR__ . '/../../config/database.php';
  $id_transaksi = isset($_GET['id']) ? $_GET['id'] : '';
  
  // ✅ FIXED: Query semua transaksi pinjam yang sudah disetujui dan belum dikembalikan
  // Hanya cek NULL saja, tidak perlu '0000-00-00' karena field sudah nullable
  $query_transaksi = mysqli_query($mysqli, "SELECT a.id_transaksi, b.nama_barang 
    FROM tbl_barang_keluar a 
    INNER JOIN tbl_barang b ON a.barang = b.id_barang 
    WHERE a.jenis='Pinjam' 
    AND a.status='Disetujui' 
    AND a.tanggal_pengembalian IS NULL 
    ORDER BY a.id_transaksi DESC");
  
  // ✅ IMPROVEMENT: Error handling untuk query
  if (!$query_transaksi) {
    die('Error: ' . mysqli_error($mysqli));
  }
?>
<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-4">
    <div class="page-header text-white">
      <h4 class="page-title text-white"><i class="fas fa-undo mr-2"></i> Pengembalian Barang</h4>
      <ul class="breadcrumbs">
        <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="?module=barang_keluar" class="text-white">Barang Keluar</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a>Pengembalian</a></li>
      </ul>
    </div>
  </div>
</div>
<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Entri Data Pengembalian Barang</div>
    </div>
    <form action="modules/barang-keluar/proses_pengembalian.php" method="POST" enctype="multipart/form-data">
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>ID Transaksi <span class="text-danger">*</span></label>
              <select class="form-control" name="id_transaksi" required>
                <option value="">-- Pilih ID Transaksi --</option>
                <?php 
                // ✅ IMPROVEMENT: Cek jika ada data
                if (mysqli_num_rows($query_transaksi) > 0) {
                  while($row = mysqli_fetch_assoc($query_transaksi)) { ?>
                    <option value="<?php echo htmlspecialchars($row['id_transaksi']); ?>" <?php if($id_transaksi == $row['id_transaksi']) echo 'selected'; ?>>
                      <?php echo htmlspecialchars($row['id_transaksi']) . ' - ' . htmlspecialchars($row['nama_barang']); ?>
                    </option>
                  <?php }
                } else { ?>
                  <option value="" disabled>Tidak ada transaksi yang perlu dikembalikan</option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Tanggal Pengembalian <span class="text-danger">*</span></label>
              <input type="date" class="form-control" name="tanggal_pengembalian" 
                     value="<?php echo date('Y-m-d'); ?>" 
                     max="<?php echo date('Y-m-d'); ?>" required>
              <small class="text-muted">Tanggal tidak boleh lebih dari hari ini</small>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Kondisi Barang <span class="text-danger">*</span></label>
              <select class="form-control" name="kondisi" required>
                <option value="">-- Pilih Kondisi --</option>
                <option value="Baik">Baik</option>
                <option value="Rusak">Rusak</option>
                <option value="Hilang">Hilang</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Upload Foto Barang <span class="text-danger">*</span></label>
              <input type="file" class="form-control" name="foto" 
                     accept="image/jpeg,image/jpg,image/png" 
                     capture="environment" required>
              <small class="text-muted">Format: JPG, JPEG, PNG</small>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label>Catatan</label>
          <textarea class="form-control" name="catatan" rows="3" 
                    placeholder="Tambahkan catatan jika ada kerusakan atau hal lain yang perlu dicatat..."></textarea>
        </div>
      </div>
      <div class="card-action">
        <button type="submit" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
          <i></i> simpan
        </button>
        <a href="?module=barang_keluar" class="btn btn-default btn-round pl-4 pr-4">
          <i></i> Batal
        </a>
      </div>
    </form>
  </div>
</div>

<!-- ✅ IMPROVEMENT: JavaScript untuk validasi tambahan -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const kondisiSelect = document.querySelector('select[name="kondisi"]');
  const catatanTextarea = document.querySelector('textarea[name="catatan"]');
  
  kondisiSelect.addEventListener('change', function() {
    if (this.value === 'Rusak' || this.value === 'Hilang') {
      catatanTextarea.setAttribute('required', 'required');
      catatanTextarea.placeholder = 'Wajib isi catatan untuk kondisi ' + this.value.toLowerCase();
    } else {
      catatanTextarea.removeAttribute('required');
      catatanTextarea.placeholder = 'Tambahkan catatan jika ada kerusakan atau hal lain yang perlu dicatat...';
    }
  });
  
  // Validasi file format saja
  const fileInput = document.querySelector('input[name="foto"]');
  fileInput.addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
      const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
      if (!allowedTypes.includes(file.type)) {
        alert('Format file tidak didukung! Gunakan JPG, JPEG, atau PNG');
        this.value = '';
      }
    }
  });
});
</script>

<?php } ?>