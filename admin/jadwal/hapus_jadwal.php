<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';
$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM jadwal_imunisasi WHERE id_jadwal=$id");
$_SESSION['success'] = "Jadwal berhasil dihapus!";
header("Location: list_jadwal.php");
exit();
?>