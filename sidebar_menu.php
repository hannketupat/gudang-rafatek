<?php
// Cegah akses langsung file PHP
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header('location: 404.html');
    exit;
}

if (!isset($_SESSION)) session_start();

// Ambil hak akses
$akses  = $_SESSION['hak_akses'] ?? '';
$module = $_GET['module'] ?? '';

/* =======================
   HAK AKSES : ADMINISTRATOR
   ======================= */
if ($akses == 'Administrator') { ?>

    <!-- Dashboard -->
    <li class="nav-item <?= ($module == 'dashboard') ? 'active' : '' ?>">
        <a href="?module=dashboard">
            <i class="fas fa-home"></i>
            <p>Dashboard</p>
        </a>
    </li>

    <!-- MASTER -->
    <li class="nav-section">
        <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
        <h4 class="text-section">Master</h4>
    </li>

    <li class="nav-item submenu <?= in_array($module, ['barang','tampil_detail_barang','form_entri_barang','form_ubah_barang','jenis','form_entri_jenis','form_ubah_jenis','satuan','form_entri_satuan','form_ubah_satuan']) ? 'active' : '' ?>">
        <a data-toggle="collapse" href="#barang" <?= in_array($module, ['barang','jenis','satuan']) ? '' : 'class="collapsed"' ?>>
            <i class="fas fa-clone"></i>
            <p>Barang</p>
            <span class="caret"></span>
        </a>
        <div class="collapse <?= in_array($module, ['barang','jenis','satuan']) ? 'show' : '' ?>" id="barang">
            <ul class="nav nav-collapse">
                <li class="<?= in_array($module, ['barang','tampil_detail_barang','form_entri_barang','form_ubah_barang']) ? 'active' : '' ?>">
                    <a href="?module=barang"><span class="sub-item">Data Barang</span></a>
                </li>
                <li class="<?= in_array($module, ['jenis','form_entri_jenis','form_ubah_jenis']) ? 'active' : '' ?>">
                    <a href="?module=jenis"><span class="sub-item">Jenis Barang</span></a>
                </li>
                <li class="<?= in_array($module, ['satuan','form_entri_satuan','form_ubah_satuan']) ? 'active' : '' ?>">
                    <a href="?module=satuan"><span class="sub-item">Satuan</span></a>
                </li>
                <!-- Lokasi Rak moved out of this submenu -->
            </ul>
        </div>
    </li>

    <!-- Lokasi Rak (submenu with rack and basket management) -->
    <li class="nav-item submenu <?= in_array($module, ['lokasi_rak','rak','keranjang','form_entri_lokasi_rak','form_ubah_lokasi_rak','form_entri_rak','form_ubah_rak','form_entri_keranjang','form_ubah_keranjang']) ? 'active' : '' ?>">
        <a data-toggle="collapse" href="#lokasi" <?= in_array($module, ['lokasi_rak','rak','keranjang']) ? '' : 'class="collapsed"' ?>>
            <i class="fas fa-server"></i>
            <p>Lokasi Rak</p>
            <span class="caret"></span>
        </a>
        <div class="collapse <?= in_array($module, ['lokasi_rak','rak','keranjang']) ? 'show' : '' ?>" id="lokasi">
            <ul class="nav nav-collapse">
                <li class="<?= in_array($module, ['lokasi_rak','form_entri_lokasi_rak','form_ubah_lokasi_rak']) ? 'active' : '' ?>">
                    <a href="?module=lokasi_rak"><span class="sub-item">Lokasi Rak</span></a>
                </li>
                <li class="<?= in_array($module, ['rak','form_entri_rak','form_ubah_rak']) ? 'active' : '' ?>">
                    <a href="?module=rak"><span class="sub-item">Data Rak</span></a>
                </li>
                <li class="<?= in_array($module, ['keranjang','form_entri_keranjang','form_ubah_keranjang']) ? 'active' : '' ?>">
                    <a href="?module=keranjang"><span class="sub-item">Data Keranjang</span></a>
                </li>
            </ul>
        </div>
    </li>

    <!-- TRANSAKSI -->
    <li class="nav-section">
        <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
        <h4 class="text-section">Transaksi</h4>
    </li>

    <li class="nav-item <?= in_array($module, ['barang_masuk','form_entri_barang_masuk']) ? 'active' : '' ?>">
        <a href="?module=barang_masuk">
            <i class="fas fa-sign-in-alt"></i>
            <p>Barang Masuk</p>
        </a>
    </li>

    <li class="nav-item <?= in_array($module, ['barang_keluar','form_entri_barang_keluar']) ? 'active' : '' ?>">
        <a href="?module=barang_keluar">
            <i class="fas fa-sign-out-alt"></i>
            <p>Barang Keluar</p>
        </a>
    </li>

    <!-- LAPORAN -->
    <li class="nav-section">
        <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
        <h4 class="text-section">Laporan</h4>
    </li>

    <li class="nav-item <?= ($module == 'laporan_stok') ? 'active' : '' ?>">
        <a href="?module=laporan_stok">
            <i class="fas fa-file-signature"></i>
            <p>Laporan Stok</p>
        </a>
    </li>
    <li class="nav-item <?= ($module == 'laporan_barang_masuk') ? 'active' : '' ?>">
        <a href="?module=laporan_barang_masuk">
            <i class="fas fa-file-import"></i>
            <p>Laporan Barang Masuk</p>
        </a>
    </li>
    <li class="nav-item <?= ($module == 'laporan_barang_keluar') ? 'active' : '' ?>">
        <a href="?module=laporan_barang_keluar">
            <i class="fas fa-file-export"></i>
            <p>Laporan Barang Keluar</p>
        </a>
    </li>
    

    <!-- PENGATURAN -->
    <li class="nav-section">
        <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
        <h4 class="text-section">Pengaturan</h4>
    </li>

    <li class="nav-item <?= in_array($module, ['user','form_entri_user','form_ubah_user']) ? 'active' : '' ?>">
        <a href="?module=user">
            <i class="fas fa-user"></i>
            <p>Manajemen User</p>
        </a>
    </li>

<?php
}

/* =======================
   HAK AKSES : TEKNISI
   ======================= */
elseif ($akses == 'Teknisi') { ?>
    <!-- Dashboard -->
    <li class="nav-item <?= ($module == 'dashboard') ? 'active' : '' ?>">
        <a href="?module=dashboard">
            <i class="fas fa-home"></i>
            <p>Dashboard</p>
        </a>
    </li>
    <li class="nav-item <?= in_array($module, ['barang_keluar','form_entri_barang_keluar']) ? 'active' : '' ?>">
        <a href="?module=barang_keluar">
            <i class="fas fa-sign-out-alt"></i>
            <p>Barang Keluar</p>
        </a>
    </li>
<?php
}

/* =======================
   HAK AKSES : ADMIN GUDANG
   ======================= */
elseif ($akses == 'Admin Gudang') { ?>

    <!-- Dashboard -->
    <li class="nav-item <?= ($module == 'dashboard') ? 'active' : '' ?>">
        <a href="?module=dashboard">
            <i class="fas fa-home"></i>
            <p>Dashboard</p>
        </a>
    </li>

    <!-- MASTER -->
    <li class="nav-section">
        <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
        <h4 class="text-section">Master</h4>
    </li>

    <li class="nav-item submenu <?= in_array($module, ['barang','jenis','satuan']) ? 'active' : '' ?>">
        <a data-toggle="collapse" href="#barang" <?= in_array($module, ['barang','jenis','satuan']) ? '' : 'class="collapsed"' ?>>
            <i class="fas fa-clone"></i>
            <p>Barang</p>
            <span class="caret"></span>
        </a>
        <div class="collapse <?= in_array($module, ['barang','jenis','satuan']) ? 'show' : '' ?>" id="barang">
            <ul class="nav nav-collapse">
                <li class="<?= ($module == 'barang') ? 'active' : '' ?>">
                    <a href="?module=barang"><span class="sub-item">Data Barang</span></a>
                </li>
                <li class="<?= ($module == 'jenis') ? 'active' : '' ?>">
                    <a href="?module=jenis"><span class="sub-item">Jenis Barang</span></a>
                </li>
                <li class="<?= ($module == 'satuan') ? 'active' : '' ?>">
                    <a href="?module=satuan"><span class="sub-item">Satuan</span></a>
                </li>
            </ul>
        </div>
    </li>

   
    <li class="nav-item submenu <?= in_array($module, ['lokasi_rak','rak','keranjang','form_entri_lokasi_rak','form_ubah_lokasi_rak','form_entri_rak','form_ubah_rak','form_entri_keranjang','form_ubah_keranjang']) ? 'active' : '' ?>">
        <a data-toggle="collapse" href="#lokasi_admin" <?= in_array($module, ['lokasi_rak','rak','keranjang']) ? '' : 'class="collapsed"' ?>>
            <i class="fas fa-map-marker-alt"></i>
            <p>Lokasi Rak</p>
            <span class="caret"></span>
        </a>
        <div class="collapse <?= in_array($module, ['lokasi_rak','rak','keranjang']) ? 'show' : '' ?>" id="lokasi_admin">
            <ul class="nav nav-collapse">
                <li class="<?= in_array($module, ['lokasi_rak','form_entri_lokasi_rak','form_ubah_lokasi_rak']) ? 'active' : '' ?>">
                    <a href="?module=lokasi_rak"><span class="sub-item">Lokasi Rak</span></a>
                </li>
                <li class="<?= in_array($module, ['rak','form_entri_rak','form_ubah_rak']) ? 'active' : '' ?>">
                    <a href="?module=rak"><span class="sub-item">Data Rak</span></a>
                </li>
                <li class="<?= in_array($module, ['keranjang','form_entri_keranjang','form_ubah_keranjang']) ? 'active' : '' ?>">
                    <a href="?module=keranjang"><span class="sub-item">Data Keranjang</span></a>
                </li>
            </ul>
        </div>
    </li>

    <!-- TRANSAKSI -->
    <li class="nav-section">
        <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
        <h4 class="text-section">Transaksi</h4>
    </li>

    <li class="nav-item <?= ($module == 'barang_masuk') ? 'active' : '' ?>">
        <a href="?module=barang_masuk"><i class="fas fa-sign-in-alt"></i><p>Barang Masuk</p></a>
    </li>
    <li class="nav-item <?= ($module == 'barang_keluar') ? 'active' : '' ?>">
        <a href="?module=barang_keluar"><i class="fas fa-sign-out-alt"></i><p>Barang Keluar</p></a>
    </li>

    <!-- LAPORAN -->
    <li class="nav-section">
        <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
        <h4 class="text-section">Laporan</h4>
    </li>

    <li class="nav-item <?= ($module == 'laporan_stok') ? 'active' : '' ?>">
        <a href="?module=laporan_stok"><i class="fas fa-file-signature"></i><p>Laporan Stok</p></a>
    </li>
    <li class="nav-item <?= ($module == 'laporan_barang_masuk') ? 'active' : '' ?>">
        <a href="?module=laporan_barang_masuk"><i class="fas fa-file-import"></i><p>Laporan Barang Masuk</p></a>
    </li>
    <li class="nav-item <?= ($module == 'laporan_barang_keluar') ? 'active' : '' ?>">
        <a href="?module=laporan_barang_keluar"><i class="fas fa-file-export"></i><p>Laporan Barang Keluar</p></a>
    </li>

<?php
}

/* =======================
   HAK AKSES : KEPALA GUDANG
   ======================= */
elseif ($akses == 'Kepala Gudang') { ?>

    <!-- Dashboard -->
    <li class="nav-item <?= ($module == 'dashboard') ? 'active' : '' ?>">
        <a href="?module=dashboard"><i class="fas fa-home"></i><p>Dashboard</p></a>
    </li>


    <!-- LAPORAN -->
    <li class="nav-section">
        <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
        <h4 class="text-section">Laporan</h4>
    </li>

    <li class="nav-item <?= ($module == 'laporan_stok') ? 'active' : '' ?>">
        <a href="?module=laporan_stok"><i class="fas fa-file-signature"></i><p>Laporan Stok</p></a>
    </li>
    <li class="nav-item <?= ($module == 'laporan_barang_masuk') ? 'active' : '' ?>">
        <a href="?module=laporan_barang_masuk"><i class="fas fa-file-import"></i><p>Laporan Barang Masuk</p></a>
    </li>
    <li class="nav-item <?= ($module == 'laporan_barang_keluar') ? 'active' : '' ?>">
        <a href="?module=laporan_barang_keluar"><i class="fas fa-file-export"></i><p>Laporan Barang Keluar</p></a>
    </li>

<?php } ?>