<?php
// mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser dan hanya dapat dijalankan ketika di include oleh file lain
// jika file diakses secara langsung
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  // alihkan ke halaman error 404
  header('location: 404.html');
}
// jika file di include oleh file lain, tampilkan isi file
else {
  // mengecek apakah ada session user
  if (isset($_SESSION['id_user'])) {
    // cek apakah ada parameter ID (untuk admin mengedit user lain)
    if (isset($_GET['id']) && $_SESSION['hak_akses'] == 'Administrator') {
      $id_user = $_GET['id'];
      $is_admin_edit = true;
    } else {
      // ambil data user dari session (edit profil sendiri)
      $id_user = $_SESSION['id_user'];
      $is_admin_edit = false;
    }

    // sql statement untuk menampilkan data dari tabel "tbl_user" berdasarkan "id_user"
    $query = mysqli_query($mysqli, "SELECT id_user, nama_user, username, email, foto_profil, hak_akses FROM tbl_user WHERE id_user='$id_user'")
                                    or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
    // ambil data hasil query
    $data = mysqli_fetch_assoc($query);
  }

  // menampilkan pesan sesuai dengan proses yang dijalankan
  // jika alert tersedia
  if (isset($_GET['alert'])) {
    // jika alert = 1
    if ($_GET['alert'] == 1) {
      // tampilkan pesan sukses update profil
      echo '<div class="alert alert-notify alert-success alert-dismissible fade show" role="alert">
              <span data-notify="icon" class="fas fa-check"></span> 
              <span data-notify="title" class="text-success">Sukses!</span> 
              <span data-notify="message">Profil berhasil diperbarui.</span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';
    }
    // jika alert = 2
    elseif ($_GET['alert'] == 2) {
      // tampilkan pesan gagal update profil
      echo '<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
              <span data-notify="icon" class="fas fa-times"></span> 
              <span data-notify="title" class="text-danger">Gagal!</span> 
              <span data-notify="message">Terjadi kesalahan saat memperbarui profil.</span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';
    }
    // jika alert = 4
    elseif ($_GET['alert'] == 4) {
      // tampilkan pesan password tidak cocok
      echo '<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
              <span data-notify="icon" class="fas fa-times"></span> 
              <span data-notify="title" class="text-danger">Gagal!</span> 
              <span data-notify="message">Password baru dan konfirmasi password tidak cocok.</span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';
    }
    // jika alert = 5
    elseif ($_GET['alert'] == 5) {
      // tampilkan pesan username sudah ada
      echo '<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
              <span data-notify="icon" class="fas fa-times"></span> 
              <span data-notify="title" class="text-danger">Gagal!</span> 
              <span data-notify="message">Username sudah digunakan. Silakan gunakan username lain.</span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';
    }
    // jika alert = 6
    elseif ($_GET['alert'] == 6) {
      // tampilkan pesan email sudah ada
      echo '<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
              <span data-notify="icon" class="fas fa-times"></span> 
              <span data-notify="title" class="text-danger">Gagal!</span> 
              <span data-notify="message">Email sudah digunakan. Silakan gunakan email lain.</span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';
    }
    // jika alert = 7
    elseif ($_GET['alert'] == 7) {
      // tampilkan pesan file bukan gambar
      echo '<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
              <span data-notify="icon" class="fas fa-times"></span> 
              <span data-notify="title" class="text-danger">Gagal!</span> 
              <span data-notify="message">File yang dipilih bukan gambar.</span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';
    }
    // jika alert = 8
    elseif ($_GET['alert'] == 8) {
      // tampilkan pesan ukuran file terlalu besar
      echo '<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
              <span data-notify="icon" class="fas fa-times"></span> 
              <span data-notify="title" class="text-danger">Gagal!</span> 
              <span data-notify="message">Ukuran file foto terlalu besar. Maksimal 3MB.</span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';
    }
    // jika alert = 9
    elseif ($_GET['alert'] == 9) {
      // tampilkan pesan format file tidak valid
      echo '<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
              <span data-notify="icon" class="fas fa-times"></span> 
              <span data-notify="title" class="text-danger">Gagal!</span> 
              <span data-notify="message">Format file tidak valid. Gunakan JPG, JPEG, atau PNG.</span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';
    }
    // jika alert = 10
    elseif ($_GET['alert'] == 10) {
      // tampilkan pesan gagal upload file
      echo '<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
              <span data-notify="icon" class="fas fa-times"></span> 
              <span data-notify="title" class="text-danger">Gagal!</span> 
              <span data-notify="message">Gagal mengupload foto. Silakan coba lagi.</span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';
    }
  }
?>
  <div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-4">
      <div class="page-header text-white">
        <!-- judul halaman -->
        <h4 class="page-title text-white">
          <i class="fas fa-user-edit mr-2"></i> 
          <?php echo $is_admin_edit ? 'Edit Profil User' : 'Edit Profil'; ?>
        </h4>
        <!-- breadcrumbs -->
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <?php if ($is_admin_edit) { ?>
          <li class="nav-item"><a href="?module=user" class="text-white">User</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <?php } ?>
          <li class="nav-item"><a><?php echo $is_admin_edit ? 'Edit Profil User' : 'Edit Profil'; ?></a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="page-inner mt--5">
    <div class="card">
      <div class="card-header">
        <!-- judul form -->
        <div class="card-title"><?php echo $is_admin_edit ? 'Edit Profil User: ' . $data['nama_user'] : 'Edit Profil Saya'; ?></div>
      </div>
      <!-- form edit profil -->
      <form action="modules/profil/proses_edit_profil.php" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
        <div class="card-body">
          <div class="row">
            <!-- Foto Profil -->
            <div class="col-md-4">
              <div class="form-group">
                <label>Foto Profil</label>
                <div class="text-center">
                  <?php
                  // tentukan foto yang akan ditampilkan
                  if (!empty($data['foto_profil']) && file_exists('assets/img/profiles/' . $data['foto_profil'])) {
                    $foto_src = 'assets/img/profiles/' . $data['foto_profil'];
                  } else {
                    $foto_src = 'assets/img/avatar-2.png';
                  }
                  ?>
                  <img id="preview-foto" src="<?php echo $foto_src; ?>" alt="Foto Profil" class="img-thumbnail mb-3" style="width: 200px; height: 200px; object-fit: cover;">
                  
                  <div class="input-file-image">
                    <input type="file" name="foto_profil" id="foto_profil" class="form-control-file" accept="image/*">
                    <small class="form-text text-muted">Format: JPG, JPEG, PNG. Maksimal 3MB.</small>
                  </div>
                </div>
              </div>
            </div>

            <!-- Data Profil -->
            <div class="col-md-8">
              <input type="hidden" name="id_user" value="<?php echo $data['id_user']; ?>">
              <input type="hidden" name="foto_lama" value="<?php echo $data['foto_profil']; ?>">

              <div class="form-group">
                <label>Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama_user" class="form-control" autocomplete="off" value="<?php echo $data['nama_user']; ?>" required>
                <div class="invalid-feedback">Nama lengkap tidak boleh kosong.</div>
              </div>

              <div class="form-group">
                <label>Username <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control" autocomplete="off" value="<?php echo $data['username']; ?>" required>
                <div class="invalid-feedback">Username tidak boleh kosong.</div>
              </div>

              <div class="form-group">
                <label>Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" autocomplete="off" value="<?php echo $data['email']; ?>" required>
                <div class="invalid-feedback">Email tidak boleh kosong atau tidak valid.</div>
              </div>

              <div class="form-group">
                <label>Password Baru</label>
                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password" autocomplete="off">
                <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
              </div>

              <div class="form-group">
                <label>Konfirmasi Password Baru</label>
                <input type="password" name="konfirmasi_password" class="form-control" placeholder="Konfirmasi password baru" autocomplete="off">
                <small class="form-text text-muted">Masukkan kembali password baru untuk konfirmasi.</small>
              </div>

              <div class="form-group">
                <label>Hak Akses <?php echo $is_admin_edit ? '<span class="text-danger">*</span>' : ''; ?></label>
                <?php if ($is_admin_edit) { ?>
                <select name="hak_akses" class="form-control chosen-select" autocomplete="off" required>
                  <option value="<?php echo $data['hak_akses']; ?>"><?php echo $data['hak_akses']; ?></option>
                  <option disabled value="">-- Pilih --</option>
                  <option value="Administrator">Administrator</option>
                  <option value="Admin Gudang">Admin Gudang</option>
                  <option value="Kepala Gudang">Kepala Gudang</option>
                  <option value="Teknisi">Teknisi</option>
                </select>
                <div class="invalid-feedback">Hak akses tidak boleh kosong.</div>
                <?php } else { ?>
                <input type="text" class="form-control" value="<?php echo $data['hak_akses']; ?>" readonly>
                <small class="form-text text-muted">Hak akses tidak dapat diubah.</small>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
        
        <div class="card-action">
          <!-- tombol simpan data -->
          <input type="submit" name="simpan" value="Simpan Perubahan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
          <!-- tombol kembali -->
          <a href="<?php echo $is_admin_edit ? '?module=user' : '?module=dashboard'; ?>" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
        </div>
      </form>
    </div>
  </div>

  <!-- Custom Script untuk Preview Foto -->
  <script>
  document.getElementById('foto_profil').addEventListener('change', function(e) {
    var file = e.target.files[0];
    if (file) {
      // Validasi ukuran file (max 3MB)
      if (file.size > 3 * 1024 * 1024) {
        alert('Ukuran file terlalu besar. Maksimal 3MB.');
        this.value = '';
        return;
      }
      
      // Validasi format file
      var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
      if (!allowedTypes.includes(file.type)) {
        alert('Format file tidak valid. Gunakan JPG, JPEG, atau PNG.');
        this.value = '';
        return;
      }
      
      // Preview foto
      var reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('preview-foto').src = e.target.result;
      }
      reader.readAsDataURL(file);
    }
  });
  
  // Initialize Chosen plugin untuk dropdown
  $(document).ready(function() {
    $('.chosen-select').chosen({
      width: '100%'
    });
  });
  </script>

<?php } ?>