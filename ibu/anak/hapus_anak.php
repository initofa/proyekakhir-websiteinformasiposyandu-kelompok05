<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_ibu.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$nik_ibu = $_SESSION['nik'];

if ($id > 0) {
    $query_cari = mysqli_query($conn, "SELECT berkas FROM anak WHERE id_anak = $id AND nik_ibu = '$nik_ibu'");
    $data_anak = mysqli_fetch_assoc($query_cari);

    if ($data_anak) {
        $nama_file = $data_anak['berkas'];
        $path_file = "../../uploads/berkas_anak/" . $nama_file;

        if (!empty($nama_file) && file_exists($path_file)) {
            unlink($path_file);
        }

        $query_delete = mysqli_query($conn, "DELETE FROM anak WHERE id_anak = $id AND nik_ibu = '$nik_ibu'");
        
        if ($query_delete) {
            $_SESSION['success'] = "Data anak beserta berkas kelengkapannya berhasil dihapus permanen!";
        } else {
            $_SESSION['error'] = "Gagal menghapus data anak dari database.";
        }
    } else {
        $_SESSION['error'] = "Akses ditolak! Anda tidak memiliki otoritas menghapus data ini.";
    }
} else {
    $_SESSION['error'] = "ID Anak tidak valid atau tidak ditemukan.";
}

header("Location: index.php");
exit();
?>