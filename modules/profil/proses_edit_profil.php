<?php
// mulai session
session_start();

// panggil file "database.php" untuk koneksi ke database
require_once "../../config/database.php";

// mengecek data POST
if (isset($_POST['simpan'])) {
    // ambil data dari form
    $id_user = $_POST['id_user'];
    $nama_user = $_POST['nama_user'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    $foto_lama = $_POST['foto_lama'];
    $hak_akses = isset($_POST['hak_akses']) ? $_POST['hak_akses'] : '';
    
    // cek apakah ini admin edit user lain atau user edit sendiri
    $is_admin_edit = isset($_SESSION['hak_akses']) && $_SESSION['hak_akses'] == 'Administrator' && $id_user != $_SESSION['id_user'];
    $redirect_param = $is_admin_edit ? '&id=' . $id_user : '';

    // validasi password jika diisi
    if (!empty($password)) {
      if ($password !== $konfirmasi_password) {
        // jika konfirmasi password tidak cocok, tampilkan pesan error
        header("location: ../../main.php?module=form_edit_profil{$redirect_param}&alert=4");
        exit;
      }
    }

    // cek apakah username sudah ada (kecuali username sendiri)
    $cek_username = mysqli_query($mysqli, "SELECT username FROM tbl_user WHERE username='$username' AND id_user != '$id_user'")
                                           or die('Ada kesalahan pada query cek username: ' . mysqli_error($mysqli));
    if (mysqli_num_rows($cek_username) > 0) {
      // jika username sudah ada, tampilkan pesan error
      header("location: ../../main.php?module=form_edit_profil{$redirect_param}&alert=5");
      exit;
    }

    // cek apakah email sudah ada (kecuali email sendiri)
    $cek_email = mysqli_query($mysqli, "SELECT email FROM tbl_user WHERE email='$email' AND id_user != '$id_user'")
                                       or die('Ada kesalahan pada query cek email: ' . mysqli_error($mysqli));
    if (mysqli_num_rows($cek_email) > 0) {
      // jika email sudah ada, tampilkan pesan error
      header("location: ../../main.php?module=form_edit_profil{$redirect_param}&alert=6");
      exit;
    }

    // proses upload foto profil
    $nama_foto = $foto_lama; // default gunakan foto lama
    
    if (!empty($_FILES['foto_profil']['name'])) {
      $target_dir = "../../assets/img/profiles/";
      $foto_extension = strtolower(pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION));
      $nama_foto = 'profile_' . $id_user . '_' . time() . '.' . $foto_extension;
      $target_file = $target_dir . $nama_foto;

      // validasi upload
      $upload_ok = 1;
      $check = getimagesize($_FILES['foto_profil']['tmp_name']);
      
      if ($check === false) {
        header("location: ../../main.php?module=form_edit_profil{$redirect_param}&alert=7");
        exit;
      }

      // cek ukuran file (max 3MB)
      if ($_FILES['foto_profil']['size'] > 3 * 1024 * 1024) {
        header("location: ../../main.php?module=form_edit_profil{$redirect_param}&alert=8");
        exit;
      }

      // cek format file
      if ($foto_extension != "jpg" && $foto_extension != "png" && $foto_extension != "jpeg") {
        header("location: ../../main.php?module=form_edit_profil{$redirect_param}&alert=9");
        exit;
      }

      // buat folder jika belum ada
      if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
      }

      // upload file
      if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $target_file)) {
        // hapus foto lama jika ada dan bukan foto default
        if (!empty($foto_lama) && $foto_lama != 'avatar-1.png' && $foto_lama != 'avatar-2.png') {
          $foto_lama_path = $target_dir . $foto_lama;
          if (file_exists($foto_lama_path)) {
            unlink($foto_lama_path);
          }
        }
      } else {
        header("location: ../../main.php?module=form_edit_profil{$redirect_param}&alert=10");
        exit;
      }
    }

    // buat query update berdasarkan apakah password diubah atau tidak
    if (!empty($password)) {
      // hash password baru
      $password_hash = password_hash($password, PASSWORD_DEFAULT);
      if ($is_admin_edit && !empty($hak_akses)) {
        $query = "UPDATE tbl_user SET 
                  nama_user = '$nama_user',
                  username = '$username', 
                  email = '$email',
                  password = '$password_hash',
                  foto_profil = '$nama_foto',
                  hak_akses = '$hak_akses'
                  WHERE id_user = '$id_user'";
      } else {
        $query = "UPDATE tbl_user SET 
                  nama_user = '$nama_user',
                  username = '$username', 
                  email = '$email',
                  password = '$password_hash',
                  foto_profil = '$nama_foto'
                  WHERE id_user = '$id_user'";
      }
    } else {
      if ($is_admin_edit && !empty($hak_akses)) {
        $query = "UPDATE tbl_user SET 
                  nama_user = '$nama_user',
                  username = '$username', 
                  email = '$email',
                  foto_profil = '$nama_foto',
                  hak_akses = '$hak_akses'
                  WHERE id_user = '$id_user'";
      } else {
        $query = "UPDATE tbl_user SET 
                  nama_user = '$nama_user',
                  username = '$username', 
                  email = '$email',
                  foto_profil = '$nama_foto'
                  WHERE id_user = '$id_user'";
      }
    }

    // jalankan query update
    $update = mysqli_query($mysqli, $query) or die('Ada kesalahan pada query update: ' . mysqli_error($mysqli));

    // cek query
    if ($update) {
      // update session dengan data baru hanya jika user edit profil sendiri
      if (!$is_admin_edit) {
        $_SESSION['nama_user'] = $nama_user;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['foto_profil'] = $nama_foto;
      }

      // jika berhasil tampilkan pesan berhasil update data
      header("location: ../../main.php?module=form_edit_profil{$redirect_param}&alert=1");
    } else {
      // jika gagal tampilkan pesan kesalahan
      header("location: ../../main.php?module=form_edit_profil{$redirect_param}&alert=2");
    }
} else {
  // jika tidak ada POST data, redirect ke dashboard
  header('location: ../../main.php?module=dashboard');
}
?>