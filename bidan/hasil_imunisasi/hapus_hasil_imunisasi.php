<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_bidan.php';
$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM hasil_imunisasi WHERE id_hasil=$id");
$_SESSION['success'] = "Hasil imunisasi berhasil dihapus!";
header("Location: list_hasil_imunisasi.php");
exit();
?>