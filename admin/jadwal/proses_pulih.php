<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';

$id_pendaftaran = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$jadwal_id = isset($_GET['jadwal_id']) ? (int)$_GET['jadwal_id'] : 0;

if ($id_pendaftaran > 0) {
    $update = mysqli_query($conn, "UPDATE pendaftaran_imunisasi SET STATUS='pending', updated_at=NOW() WHERE id_pendaftaran='$id_pendaftaran' AND STATUS='batal'");
    
    if ($update) {
        $_SESSION['success'] = "Peserta berhasil didaftarkan kembali ke antrean.";
    } else {
        $_SESSION['error'] = "Gagal memproses pemulihan data pendaftaran.";
    }
} else {
    $_SESSION['error'] = "Parameter data tidak valid.";
}

header("Location: index.php?buka_jadwal=" . $jadwal_id);
exit();
?>