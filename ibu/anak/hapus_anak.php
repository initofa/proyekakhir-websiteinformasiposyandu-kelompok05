<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_ibu.php';
$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM anak WHERE id_anak=$id");
$_SESSION['success'] = "Data anak berhasil dihapus!";
header("Location: list_anak.php");
exit();
?>