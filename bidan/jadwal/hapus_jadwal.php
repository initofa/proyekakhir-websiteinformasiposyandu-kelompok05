<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_bidan.php';

$id = $_GET['id'];
$nik = $_SESSION['nik'];

// Cek apakah jadwal milik bidan ini
$check = mysqli_query($conn, "SELECT id_jadwal FROM jadwal_imunisasi WHERE id_jadwal=$id AND created_by='$nik'");
if(mysqli_num_rows($check) > 0){
    mysqli_query($conn, "DELETE FROM jadwal_imunisasi WHERE id_jadwal=$id");
    $_SESSION['success'] = "Jadwal berhasil dihapus!";
} else {
    $_SESSION['error'] = "Jadwal tidak ditemukan!";
}

header("Location: list_jadwal.php");
exit();
?>