<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama_vaksin']));
    $deskripsi = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
    $usia = (int)$_POST['usia_rekomendasi'];
    $satuan = $_POST['satuan_waktu'];
    
    if($satuan == 'tahun'){
        $usia_bulan = $usia * 12;
    } else {
        $usia_bulan = $usia;
    }
    
    if ($usia_bulan < 0) {
        $_SESSION['error'] = "Usia rekomendasi tidak boleh bernilai negatif!";
    } else {
        $query = "INSERT INTO vaksin (nama_vaksin, deskripsi, usia_rekomendasi) VALUES ('$nama', '$deskripsi', '$usia_bulan')";
        
        if(mysqli_query($conn, $query)){
            $_SESSION['success'] = "Vaksin berhasil ditambahkan!";
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['error'] = "Gagal menambahkan vaksin ke database: " . mysqli_error($conn);
        }
    }
}

$title = 'Tambah Vaksin';
include __DIR__ . '/../../templates/sidebar.php';
?>

<div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg p-6 fade-in">
    <h1 class="text-2xl font-bold text-green-800 mb-6">Tambah Vaksin</h1>
    
    <?php if(isset($_SESSION['error'])): ?>
    <script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '<?php echo $_SESSION['error']; unset($_SESSION['error']); ?>',
        confirmButtonColor: '#dc2626'
    });
    </script>
    <?php endif; ?>

    <form method="POST">
        <div class="space-y-4">
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Nama Vaksin</label>
                <input type="text" name="nama_vaksin" required autocomplete="off" placeholder="Contoh: BCG / Polio"
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200">
            </div>
            
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Deskripsi</label>
                <textarea name="deskripsi" rows="4" placeholder="Masukkan deskripsi kegunaan vaksin dan efek samping jika ada..."
                          class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200"></textarea>
            </div>
            
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Usia Rekomendasi</label>
                <div class="flex gap-3">
                    <input type="number" name="usia_rekomendasi" required class="w-2/3 px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200" placeholder="Jumlah" min="0">
                    <select name="satuan_waktu" class="w-1/3 px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 bg-white">
                        <option value="bulan">Bulan</option>
                        <option value="tahun">Tahun</option>
                    </select>
                </div>
                <p class="text-xs text-gray-400 mt-1">Contoh: 0 bulan (baru lahir), 6 bulan, 1 tahun, 2 tahun, dll</p>
            </div>
        </div>
        
        <div class="flex gap-3 mt-6">
            <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-500 text-white py-2 rounded-xl font-semibold hover:shadow-lg transition">
                <i class="fas fa-save mr-1"></i> Simpan
            </button>
            <a href="index.php" class="flex-1 bg-gray-200 text-gray-700 text-center py-2 rounded-xl font-semibold hover:bg-gray-300 transition flex items-center justify-center">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    let usia = parseInt(document.querySelector('input[name="usia_rekomendasi"]').value);
    if (usia < 0 || isNaN(usia)) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Usia rekomendasi tidak boleh bernilai negatif!',
            confirmButtonColor: '#dc2626'
        });
    }
});
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>