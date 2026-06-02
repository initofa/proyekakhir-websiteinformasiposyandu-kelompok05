<?php
require_once __DIR__ . '/../../config/database.php';
$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM vaksin WHERE id_vaksin=$id");
$_SESSION['success'] = "Vaksin berhasil dihapus!";
header("Location: index.php");
exit();
?>