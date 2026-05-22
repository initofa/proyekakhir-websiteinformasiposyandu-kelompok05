<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';
$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM kategori_artikel WHERE id_kategori=$id");
$_SESSION['success'] = "Kategori berhasil dihapus!";
header("Location: list_kategori.php");
exit();
?>