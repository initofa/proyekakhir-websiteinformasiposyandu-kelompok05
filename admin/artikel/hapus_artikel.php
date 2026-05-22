<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';

$id = $_GET['id'];

// Ambil data artikel untuk mendapatkan nama file thumbnail
$query = "SELECT thumbnail FROM artikel WHERE id_artikel = $id";
$result = mysqli_query($conn, $query);
$artikel = mysqli_fetch_assoc($result);

if($artikel){
    // Hapus file thumbnail jika ada
    if(!empty($artikel['thumbnail'])){
        $file_path = "../../uploads/artikel/" . $artikel['thumbnail'];
        if(file_exists($file_path)){
            unlink($file_path); // Hapus file gambar
        }
    }
    
    // Hapus data artikel dari database
    $delete_query = "DELETE FROM artikel WHERE id_artikel = $id";
    if(mysqli_query($conn, $delete_query)){
        $_SESSION['success'] = "Artikel berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus artikel!";
    }
} else {
    $_SESSION['error'] = "Artikel tidak ditemukan!";
}

header("Location: list_artikel.php");
exit();
?>