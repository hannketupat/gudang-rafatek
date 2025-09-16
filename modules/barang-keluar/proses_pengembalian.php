    <?php
require_once "../../config/database.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ✅ SECURITY: Validasi dan sanitasi input
    $id_transaksi = mysqli_real_escape_string($mysqli, $_POST['id_transaksi']);
    $tanggal_pengembalian = mysqli_real_escape_string($mysqli, $_POST['tanggal_pengembalian']);
    $kondisi = mysqli_real_escape_string($mysqli, $_POST['kondisi']);
    $catatan = mysqli_real_escape_string($mysqli, $_POST['catatan']);

    // ✅ IMPROVEMENT: Validasi input
    if (empty($id_transaksi) || empty($tanggal_pengembalian) || empty($kondisi)) {
        die('Error: Data tidak lengkap!');
    }

    // ✅ IMPROVEMENT: Validasi tanggal
    $today = date('Y-m-d');
    if ($tanggal_pengembalian > $today) {
        die('Error: Tanggal pengembalian tidak boleh lebih dari hari ini!');
    }

    // Upload foto dengan error handling yang lebih baik
    $nama_foto = "";
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $foto = $_FILES['foto']['name'];
        $tmp = $_FILES['foto']['tmp_name'];
        $folder = "../../images/pengembalian/";

        // ✅ IMPROVEMENT: Validasi file
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
        $file_type = $_FILES['foto']['type'];
        $file_size = $_FILES['foto']['size'];

        if (!in_array($file_type, $allowed_types)) {
            die('Error: Format file tidak didukung! Gunakan JPG, JPEG, atau PNG.');
        }

        if ($file_size > 3 * 1024 * 1024) { // 3MB
            die('Error: Ukuran file terlalu besar! Maximum 3MB.');
        }

        if (!file_exists($folder)   ) {
            mkdir($folder, 0777, true);
        }

        // ✅ IMPROVEMENT: Nama file yang lebih aman
        $file_extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nama_foto = $id_transaksi . "_" . date('YmdHis') . "." . $file_extension;
        
        if (!move_uploaded_file($tmp, $folder . $nama_foto)) {
            die('Error: Gagal upload foto!');
        }
    } else {
        die('Error: Foto wajib diupload!');
    }


    $query = "UPDATE tbl_barang_keluar 
              SET tanggal_pengembalian = '$tanggal_pengembalian', 
                  kondisi = '$kondisi',
                  catatan = '$catatan',
                  foto_pengembalian = '$nama_foto'
              WHERE id_transaksi = '$id_transaksi' 
              AND jenis = 'Pinjam' 
              AND status = 'Disetujui' 
              AND tanggal_pengembalian IS NULL";

    $result = mysqli_query($mysqli, $query);
    
    if (!$result) {
        // ✅ IMPROVEMENT: Error handling yang lebih baik
        die('Error Database: ' . mysqli_error($mysqli));
    }

    // ✅ IMPROVEMENT: Cek apakah ada row yang ter-update
    if (mysqli_affected_rows($mysqli) > 0) {
        
        // ✅ NEW FEATURE: Kembalikan stok barang setelah pengembalian
        // Ambil data barang dan jumlah yang dipinjam
        $get_data = mysqli_query($mysqli, "SELECT barang, jumlah FROM tbl_barang_keluar WHERE id_transaksi = '$id_transaksi'");
        
        if ($get_data && mysqli_num_rows($get_data) > 0) {
            $data_transaksi = mysqli_fetch_assoc($get_data);
            $id_barang = $data_transaksi['barang'];
            $jumlah_kembali = $data_transaksi['jumlah'];
            
            // Tentukan jumlah yang akan dikembalikan ke stok berdasarkan kondisi
            $jumlah_restore = $jumlah_kembali;
            if ($kondisi == 'Hilang') {
                $jumlah_restore = 0; // Jika hilang, stok tidak dikembalikan
            } elseif ($kondisi == 'Rusak') {
                // Opsional: Bisa dikembalikan ke stok tapi dengan catatan rusak
                // Atau tidak dikembalikan sama sekali, tergantung kebijakan
                $jumlah_restore = 0; // Sesuaikan dengan kebijakan Anda
            }
            
            if ($jumlah_restore > 0) {
                // Update stok barang (tambahkan kembali)
                $update_stok = mysqli_query($mysqli, "UPDATE tbl_barang 
                                                    SET stok = stok + $jumlah_restore 
                                                    WHERE id_barang = '$id_barang'");
                
                if (!$update_stok) {
                    die('Error: Gagal mengembalikan stok barang - ' . mysqli_error($mysqli));
                }
            }
            
            // ✅ OPTIONAL: Log aktivitas pengembalian dengan detail stok
            $keterangan = "Barang dikembalikan dengan kondisi: $kondisi";
            if ($jumlah_restore > 0) {
                $keterangan .= ". Stok dikembalikan: $jumlah_restore unit";
            } else {
                $keterangan .= ". Stok tidak dikembalikan karena kondisi $kondisi";
            }
            
            $log_query = "INSERT INTO tbl_log_aktivitas (id_transaksi, aktivitas, tanggal, keterangan) 
                         VALUES ('$id_transaksi', 'Pengembalian', '$tanggal_pengembalian', '$keterangan')";
            // mysqli_query($mysqli, $log_query); // Uncomment jika ada tabel log
            
        } else {
            die('Error: Data transaksi tidak ditemukan!');
        }
        
        header("Location: ../../main.php?module=barang_keluar&pesan=berhasil_kembali");
        exit;
    } else {
        die('Error: Transaksi tidak ditemukan atau sudah dikembalikan!');
    }
} else {
    die('Error: Method tidak diizinkan!');
}
?>