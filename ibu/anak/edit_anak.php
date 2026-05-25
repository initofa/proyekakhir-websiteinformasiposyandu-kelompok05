<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_ibu.php';

$nik = $_SESSION['nik'];

// ID ditangkap melalui metode POST saat halaman dialihkan dari list_anak.php
$id = isset($_POST['id_anak']) ? (int)$_POST['id_anak'] : 0;

if($id === 0) { 
    header("Location: list_anak.php"); 
    exit(); 
}

$anak = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM anak WHERE id_anak=$id AND nik_ibu='$nik'"));
if(!$anak){ header("Location: list_anak.php"); exit(); }

// Membedakan tombol proses submit simpan form atau pemuatan awal halaman
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proses_update'])){
    $nama_anak = mysqli_real_escape_string($conn, $_POST['nama_anak']);
    $tempat_lahir = mysqli_real_escape_string($conn, $_POST['tempat_lahir']);
    $tanggal_lahir = mysqli_real_escape_string($conn, $_POST['tanggal_lahir']);
    $jenis_kelamin = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
    $berat_lahir = (float)$_POST['berat_lahir'];
    $panjang_lahir = (float)$_POST['panjang_lahir'];
    
    // PERUBAHAN UTAMA: Variabel updated_by dihapus agar sinkron dengan struktur tabel asli milikmu
    mysqli_query($conn, "UPDATE anak SET nama_anak='$nama_anak', tempat_lahir='$tempat_lahir', tanggal_lahir='$tanggal_lahir', jenis_kelamin='$jenis_kelamin', berat_lahir='$berat_lahir', panjang_lahir='$panjang_lahir' WHERE id_anak=$id");
    
    $_SESSION['success'] = "Data anak berhasil diupdate!";
    header("Location: list_anak.php");
    exit();
}
$title = 'Edit Anak';
include __DIR__ . '/../../templates/sidebar.php';
?>

<div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg p-6 fade-in">
    <h1 class="text-2xl font-bold text-green-800 mb-6">Edit Data Anak</h1>
    <form method="POST">
        <input type="hidden" name="id_anak" value="<?php echo $id; ?>">
        
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block font-semibold text-gray-700 mb-2">Nama Anak</label>
                <input type="text" name="nama_anak" value="<?php echo htmlspecialchars($anak['nama_anak']); ?>" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400">
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" value="<?php echo htmlspecialchars($anak['tempat_lahir']); ?>" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400">
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" value="<?php echo $anak['tanggal_lahir']; ?>" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400">
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400">
                    <option value="L" <?php echo $anak['jenis_kelamin']=='L'?'selected':''; ?>>Laki-laki</option>
                    <option value="P" <?php echo $anak['jenis_kelamin']=='P'?'selected':''; ?>>Perempuan</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Berat Lahir (kg)</label>
                <input type="number" step="0.01" name="berat_lahir" value="<?php echo $anak['berat_lahir']; ?>" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400">
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Panjang Lahir (cm)</label>
                <input type="number" step="0.01" name="panjang_lahir" value="<?php echo $anak['panjang_lahir']; ?>" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400">
            </div>
        </div>
        <div class="flex gap-3 mt-6">
            <button type="submit" name="proses_update" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-500 text-white py-2 rounded-xl font-semibold hover:shadow-lg transition">Update</button>
            <a href="list_anak.php" class="flex-1 bg-gray-200 text-gray-700 text-center py-2 rounded-xl font-semibold hover:bg-gray-300 transition">Batal</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>