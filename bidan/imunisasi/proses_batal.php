<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_bidan.php';

$id_pendaftaran = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$jadwal_id = isset($_GET['jadwal_id']) ? (int)$_GET['jadwal_id'] : 0;

if($id_pendaftaran > 0) {
    $check = mysqli_query($conn, "SELECT STATUS FROM pendaftaran_imunisasi WHERE id_pendaftaran = '$id_pendaftaran'");
    $data = mysqli_fetch_assoc($check);
    
    if($data && $data['STATUS'] == 'pending') {
        $update = mysqli_query($conn, "UPDATE pendaftaran_imunisasi SET STATUS='batal', updated_at=NOW() WHERE id_pendaftaran='$id_pendaftaran'");
        
        if($update) {
            $_SESSION['success'] = "Pendaftaran berhasil dibatalkan.";
        } else {
            $_SESSION['error'] = "Gagal membatalkan pendaftaran.";
        }
    } else {
        $_SESSION['error'] = "Pendaftaran tidak dapat dibatalkan karena sudah diproses.";
    }
} else {
    $_SESSION['error'] = "ID pendaftaran tidak valid.";
}

header("Location: index.php?buka_jadwal=" . $jadwal_id);
exit();
?>