<?php
$type = isset($_GET['type']) ? $_GET['type'] : 'keluar';
?>

<h4 class="page-title">
  <?php echo ($type == 'pinjam') ? 'Form Peminjaman Barang' : 'Form Barang Keluar'; ?>
</h4>

<form action="modules/barang-keluar/proses_entri.php" method="POST" enctype="multipart/form-data">
  <input type="hidden" name="jenis" value="<?php echo ($type == 'pinjam') ? 'Pinjam' : 'Keluar'; ?>">
  <!-- isi form barang sama seperti biasa -->
  <!-- ... -->
</form>