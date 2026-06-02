<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_bidan.php';

$pendaftaran_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$jadwal_id = isset($_GET['jadwal_id']) ? (int)$_GET['jadwal_id'] : 0;

$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT pi.*, a.nama_anak, a.tanggal_lahir, a.berat_lahir, a.panjang_lahir, 
    u.nama_lengkap as nama_ibu, v.nama_vaksin, j.tanggal, j.lokasi
    FROM pendaftaran_imunisasi pi 
    JOIN anak a ON pi.id_anak=a.id_anak 
    JOIN users u ON a.nik_ibu=u.nik 
    JOIN jadwal_imunisasi j ON pi.id_jadwal=j.id_jadwal 
    JOIN vaksin v ON j.id_vaksin=v.id_vaksin 
    WHERE pi.id_pendaftaran=$pendaftaran_id"));

if(!$data) {
    $_SESSION['error'] = "Data pendaftaran tidak ditemukan!";
    header("Location: index.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $berat = $_POST['berat_badan'];
    $tinggi = $_POST['tinggi_badan'];
    $lingkar = $_POST['lingkar_kepala'];
    $status_gizi = $_POST['status_gizi'];
    $nafsu_makan = $_POST['nafsu_makan'];
    $catatan = $_POST['catatan_kesehatan'];
    $petugas_nik = $_SESSION['nik'];
    
    mysqli_query($conn, "INSERT INTO hasil_imunisasi (id_pendaftaran, berat_badan, tinggi_badan, lingkar_kepala, status_gizi, nafsu_makan, catatan_kesehatan, tgl_imunisasi, petugas_nik, created_by) 
        VALUES ('$pendaftaran_id', '$berat', '$tinggi', '$lingkar', '$status_gizi', '$nafsu_makan', '$catatan', CURDATE(), '$petugas_nik', '$petugas_nik')");
    mysqli_query($conn, "UPDATE pendaftaran_imunisasi SET STATUS='selesai', updated_at=NOW() WHERE id_pendaftaran=$pendaftaran_id");
    
    $_SESSION['success'] = "Hasil imunisasi berhasil disimpan!";
    header("Location: index.php");
    exit();
}

$title = 'Input Hasil Imunisasi';
include __DIR__ . '/../../templates/sidebar.php';
?>

<div class="max-w-2xl mx-auto fade-in">
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h1 class="text-2xl font-bold text-green-800 mb-6">Input Hasil Imunisasi</h1>
        
        <div class="bg-green-50 p-4 rounded-lg mb-6 grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500">Nama Anak:</span> <strong><?php echo htmlspecialchars($data['nama_anak']); ?></strong></div>
            <div><span class="text-gray-500">Tanggal Lahir:</span> <strong><?php echo date('d/m/Y', strtotime($data['tanggal_lahir'])); ?></strong></div>
            <div><span class="text-gray-500">Nama Ibu:</span> <strong><?php echo htmlspecialchars($data['nama_ibu']); ?></strong></div>
            <div><span class="text-gray-500">Vaksin:</span> <strong><?php echo htmlspecialchars($data['nama_vaksin']); ?></strong></div>
            <div><span class="text-gray-500">Lokasi:</span> <strong><?php echo htmlspecialchars($data['lokasi']); ?></strong></div>
            <div><span class="text-gray-500">Tanggal:</span> <strong><?php echo date('d/m/Y', strtotime($data['tanggal'])); ?></strong></div>
        </div>
        
        <form method="POST">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-gray-700 mb-2">Berat Badan (kg)</label>
                    <input type="number" step="0.01" name="berat_badan" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-green-400">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-2">Tinggi Badan (cm)</label>
                    <input type="number" step="0.01" name="tinggi_badan" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-green-400">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-2">Lingkar Kepala (cm)</label>
                    <input type="number" step="0.01" name="lingkar_kepala" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-green-400">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-2">Status Gizi</label>
                    <select name="status_gizi" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-green-400">
                        <option value="Normal">Normal</option>
                        <option value="Kurang">Kurang</option>
                        <option value="Buruk">Buruk</option>
                        <option value="Lebih">Lebih</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-2">Nafsu Makan</label>
                    <select name="nafsu_makan" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-green-400">
                        <option value="baik">Baik</option>
                        <option value="kurang">Kurang</option>
                        <option value="buruk">Buruk</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block font-semibold text-gray-700 mb-2">Catatan Kesehatan</label>
                    <textarea name="catatan_kesehatan" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-green-400"></textarea>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-500 text-white py-2 rounded-xl font-semibold hover:shadow-lg transition">Simpan</button>
                <a href="index.php" class="flex-1 bg-gray-200 text-gray-700 text-center py-2 rounded-xl font-semibold hover:bg-gray-300 transition">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>