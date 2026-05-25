<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_ibu.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nik_ibu = $_SESSION['nik'];
    $nama_anak = mysqli_real_escape_string($conn, $_POST['nama_anak']);
    $tempat_lahir = mysqli_real_escape_string($conn, $_POST['tempat_lahir']);
    $tanggal_lahir = mysqli_real_escape_string($conn, $_POST['tanggal_lahir']);
    $jenis_kelamin = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
    $berat_lahir = (float)$_POST['berat_lahir'];
    $panjang_lahir = (float)$_POST['panjang_lahir'];
    
    // PERUBAHAN UTAMA: Kolom created_by dan variabelnya dihapus agar sesuai dengan struktur tabel asli milikmu
    $query = "INSERT INTO anak (nik_ibu, nama_anak, tempat_lahir, tanggal_lahir, jenis_kelamin, berat_lahir, panjang_lahir) 
              VALUES ('$nik_ibu', '$nama_anak', '$tempat_lahir', '$tanggal_lahir', '$jenis_kelamin', '$berat_lahir', '$panjang_lahir')";
    
    if(mysqli_query($conn, $query)){
        $_SESSION['success'] = "Data anak berhasil ditambahkan!";
        header("Location: list_anak.php");
        exit();
    } else {
        echo "Gagal menambahkan data: " . mysqli_error($conn);
    }
}
$title = 'Tambah Anak';
include __DIR__ . '/../../templates/sidebar.php';
?>

<div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg p-6 fade-in">
    <h1 class="text-2xl font-bold text-green-800 mb-6">Tambah Data Anak</h1>
    <form method="POST">
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block font-semibold text-gray-700 mb-2">Nama Anak</label>
                <input type="text" name="nama_anak" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400">
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400">
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400">
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400">
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Berat Lahir (kg)</label>
                <input type="number" step="0.01" name="berat_lahir" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400" placeholder="Contoh: 3.15">
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Panjang Lahir (cm)</label>
                <input type="number" step="0.01" name="panjang_lahir" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400" placeholder="Contoh: 49.5">
            </div>
        </div>
        <div class="flex gap-3 mt-6">
            <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-500 text-white py-2 rounded-xl font-semibold hover:shadow-lg transition">Simpan</button>
            <a href="list_anak.php" class="flex-1 bg-gray-200 text-gray-700 text-center py-2 rounded-xl font-semibold hover:bg-gray-300 transition">Batal</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>