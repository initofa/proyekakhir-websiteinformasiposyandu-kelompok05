<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_bidan.php';

$id_pendaftaran = $_GET['id'];
$hasil = mysqli_fetch_assoc(mysqli_query($conn, "SELECT hi.*, a.nama_anak, v.nama_vaksin, j.tanggal 
    FROM hasil_imunisasi hi 
    JOIN pendaftaran_imunisasi pi ON hi.id_pendaftaran = pi.id_pendaftaran
    JOIN anak a ON pi.id_anak = a.id_anak
    JOIN jadwal_imunisasi j ON pi.id_jadwal = j.id_jadwal
    JOIN vaksin v ON j.id_vaksin = v.id_vaksin
    WHERE pi.id_pendaftaran = $id_pendaftaran"));

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $berat = $_POST['berat_badan'];
    $tinggi = $_POST['tinggi_badan'];
    $lingkar = $_POST['lingkar_kepala'];
    $status_gizi = $_POST['status_gizi'];
    $nafsu_makan = $_POST['nafsu_makan'];
    $catatan = $_POST['catatan_kesehatan'];
    $updated_by = $_SESSION['nik'];
    
    mysqli_query($conn, "UPDATE hasil_imunisasi SET 
        berat_badan='$berat', tinggi_badan='$tinggi', lingkar_kepala='$lingkar', 
        status_gizi='$status_gizi', nafsu_makan='$nafsu_makan', catatan_kesehatan='$catatan', updated_by='$updated_by' 
        WHERE id_pendaftaran IN (SELECT id_pendaftaran FROM hasil_imunisasi WHERE id_pendaftaran='$id_pendaftaran')");
    
    $_SESSION['success'] = "Hasil imunisasi berhasil diupdate!";
    header("Location: detail_hasil.php?id=$id_pendaftaran");
    exit();
}
$title = 'Edit Hasil Imunisasi';
include __DIR__ . '/../../templates/sidebar.php';
?>

<div class="max-w-2xl mx-auto fade-in">
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h1 class="text-2xl font-bold text-green-800 mb-6">Edit Hasil Imunisasi</h1>
        <div class="bg-green-50 p-4 rounded-lg mb-6 grid grid-cols-2 gap-4">
            <div><p class="text-gray-500 text-sm">Nama Anak</p><p class="font-semibold text-gray-800"><?php echo $hasil['nama_anak']; ?></p></div>
            <div><p class="text-gray-500 text-sm">Vaksin</p><p class="font-semibold text-gray-800"><?php echo $hasil['nama_vaksin']; ?></p></div>
            <div><p class="text-gray-500 text-sm">Tanggal Imunisasi</p><p class="font-semibold text-gray-800"><?php echo date('d/m/Y', strtotime($hasil['tanggal'])); ?></p></div>
        </div>
        <form method="POST">
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block font-semibold text-gray-700 mb-2">Berat Badan (kg)</label><input type="number" step="0.01" name="berat_badan" value="<?php echo $hasil['berat_badan']; ?>" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400"></div>
                <div><label class="block font-semibold text-gray-700 mb-2">Tinggi Badan (cm)</label><input type="number" step="0.01" name="tinggi_badan" value="<?php echo $hasil['tinggi_badan']; ?>" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400"></div>
                <div><label class="block font-semibold text-gray-700 mb-2">Lingkar Kepala (cm)</label><input type="number" step="0.01" name="lingkar_kepala" value="<?php echo $hasil['lingkar_kepala']; ?>" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400"></div>
                <div><label class="block font-semibold text-gray-700 mb-2">Status Gizi</label><select name="status_gizi" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400"><option value="Normal" <?php echo $hasil['status_gizi']=='Normal'?'selected':''; ?>>Normal</option><option value="Kurang Gizi" <?php echo $hasil['status_gizi']=='Kurang Gizi'?'selected':''; ?>>Kurang Gizi</option><option value="Gizi Buruk" <?php echo $hasil['status_gizi']=='Gizi Buruk'?'selected':''; ?>>Gizi Buruk</option><option value="Berisiko Gemuk" <?php echo $hasil['status_gizi']=='Berisiko Gemuk'?'selected':''; ?>>Berisiko Gemuk</option><option value="Gemuk" <?php echo $hasil['status_gizi']=='Gemuk'?'selected':''; ?>>Gemuk</option></select></div>
                <div><label class="block font-semibold text-gray-700 mb-2">Nafsu Makan</label><select name="nafsu_makan" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400"><option value="baik" <?php echo $hasil['nafsu_makan']=='baik'?'selected':''; ?>>Baik</option><option value="kurang" <?php echo $hasil['nafsu_makan']=='kurang'?'selected':''; ?>>Kurang</option><option value="buruk" <?php echo $hasil['nafsu_makan']=='buruk'?'selected':''; ?>>Buruk</option></select></div>
                <div class="col-span-2"><label class="block font-semibold text-gray-700 mb-2">Catatan Kesehatan</label><textarea name="catatan_kesehatan" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400"><?php echo $hasil['catatan_kesehatan']; ?></textarea></div>
            </div>
            <div class="flex gap-3 mt-6"><button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-500 text-white py-2 rounded-xl font-semibold hover:shadow-lg transition">Update</button><a href="detail_hasil.php?id=<?php echo $id_pendaftaran; ?>" class="flex-1 bg-gray-200 text-gray-700 text-center py-2 rounded-xl font-semibold hover:bg-gray-300 transition">Batal</a></div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>