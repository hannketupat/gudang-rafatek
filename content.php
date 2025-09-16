<?php
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
}
else {
  require_once "config/database.php";

  if ($_GET['module'] == 'dashboard') {
    include "modules/dashboard/tampil_data.php";
  }
  elseif ($_GET['module'] == 'test_email') {
    include "test_email_config.php";
  }
  elseif ($_GET['module'] == 'barang' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
    include "modules/barang/tampil_data.php";
  }
  elseif ($_GET['module'] == 'form_entri_barang' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
    include "modules/barang/form_entri.php";
  }
  elseif ($_GET['module'] == 'form_ubah_barang' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
    include "modules/barang/form_ubah.php";
  }
  elseif ($_GET['module'] == 'tampil_detail_barang' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
    include "modules/barang/tampil_detail.php";
  }
  elseif ($_GET['module'] == 'jenis' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
    include "modules/jenis/tampil_data.php";
  }
  elseif ($_GET['module'] == 'form_entri_jenis' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
    include "modules/jenis/form_entri.php";
  }
  elseif ($_GET['module'] == 'form_ubah_jenis' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
    include "modules/jenis/form_ubah.php";
  }
  elseif ($_GET['module'] == 'satuan' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
    include "modules/satuan/tampil_data.php";
  }
  elseif ($_GET['module'] == 'form_entri_satuan' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
    include "modules/satuan/form_entri.php";
  }
  elseif ($_GET['module'] == 'form_ubah_satuan' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
    include "modules/satuan/form_ubah.php";
  }
  // ================== RAK MODULE ==================
  elseif ($_GET['module'] == 'lokasi_rak' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
    include "modules/lokasi_rak/tampil_data.php";
  }
  elseif ($_GET['module'] == 'form_entri_lokasi_rak' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
    include "modules/lokasi_rak/form_entri.php";
  }
  elseif ($_GET['module'] == 'form_ubah_lokasi_rak' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
    include "modules/lokasi_rak/form_ubah.php";
  }
  elseif ($_GET['module'] == 'rak' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
    include "modules/rak/tampil_data.php";
  }
  elseif ($_GET['module'] == 'form_entri_rak' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
    include "modules/rak/form_entri.php";
  }
  elseif ($_GET['module'] == 'form_ubah_rak' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
    include "modules/rak/form_ubah.php";
  }
  // ================== KERANJANG MODULE ==================
  elseif ($_GET['module'] == 'keranjang' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
    include "modules/keranjang/tampil_data.php";
  }
  elseif ($_GET['module'] == 'form_entri_keranjang' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
    include "modules/keranjang/form_entri.php";
  }
  elseif ($_GET['module'] == 'form_ubah_keranjang' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
    include "modules/keranjang/form_ubah.php";
  }
  // ================== EXISTING MODULES ==================
  elseif ($_GET['module'] == 'barang_masuk' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
    include "modules/barang-masuk/tampil_data.php";
  }
  elseif ($_GET['module'] == 'form_entri_barang_masuk' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
    include "modules/barang-masuk/form_entri.php";
  }
  elseif ($_GET['module'] == 'barang_keluar' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
    include "modules/barang-keluar/tampil_data.php";
  }
  elseif ($_GET['module'] == 'form_entri_barang_keluar' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
    include "modules/barang-keluar/form_entri.php";
  }
  // handle special actions for barang_keluar (like printing)
  elseif ($_GET['module'] == 'barang_keluar' && isset($_GET['act']) && $_GET['act'] == 'cetak') {
    include "modules/barang-keluar/cetak.php";
  }
  elseif ($_GET['module'] == 'form_selesai' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
    include "modules/barang-keluar/form_selesai.php";
  }
  elseif ($_GET['module'] == 'laporan_stok') {
    include "modules/laporan-stok/tampil_data.php";
  }
  elseif ($_GET['module'] == 'laporan_barang_masuk') {
    include "modules/laporan-barang-masuk/tampil_data.php";
  }
  elseif ($_GET['module'] == 'laporan_barang_keluar') {
    include "modules/laporan-barang-keluar/tampil_data.php";
  }
  elseif ($_GET['module'] == 'user' && $_SESSION['hak_akses'] == 'Administrator') {
    include "modules/user/tampil_data.php";
  }
  elseif ($_GET['module'] == 'form_entri_user' && $_SESSION['hak_akses'] == 'Administrator') {
    include "modules/user/form_entri.php";
  }
  elseif ($_GET['module'] == 'form_ubah_password') {
    include "modules/password/form_ubah.php";
  }
  elseif ($_GET['module'] == 'form_edit_profil') {
    include "modules/profil/form_edit_profil.php";
  }
  elseif ($_GET['module'] == 'pengembalian_form') {
    include "modules/barang-keluar/pengembalian_form.php";
  }
  // ================== PEMINJAMAN ==================
  elseif ($_GET['module'] == 'peminjaman') {
    include "modules/peminjaman/form_peminjaman.php";
  }
  elseif ($_GET['module'] == 'persetujuan') {
    include "modules/peminjaman/persetujuan.php";
  }
  elseif ($_GET['module'] == 'proses_peminjaman') {
    include "modules/peminjaman/proses_peminjaman.php";
  }
  elseif ($_GET['module'] == 'simpan_peminjaman') {
    include "modules/peminjaman/simpan_peminjaman.php";
  }
  elseif ($_GET['module'] == 'update_status') {
    include "modules/peminjaman/update_status.php";
  }
  // ================== DEFAULT ==================
  else {
    echo "<h3>Halaman tidak ditemukan!</h3>";
  }
}
?>