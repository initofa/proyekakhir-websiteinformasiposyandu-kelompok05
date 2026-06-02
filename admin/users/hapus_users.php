<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';

$nik = $_GET['nik'];
$query = "DELETE FROM users WHERE nik='$nik'";

if(mysqli_query($conn, $query)){
    $_SESSION['success'] = "User berhasil dihapus!";
} else {
    $_SESSION['error'] = "Gagal menghapus user!";
}

header("Location: index.php");
exit();
?>