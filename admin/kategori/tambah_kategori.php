<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama_kategori']));
    $query = "INSERT INTO kategori_artikel (nama_kategori) VALUES ('$nama')";
    
    if(mysqli_query($conn, $query)){
        $_SESSION['success'] = "Kategori berhasil ditambahkan!";
        header("Location: list_kategori.php");
        exit();
    } else {
        $_SESSION['error'] = "Gagal menambahkan kategori: " . mysqli_error($conn);
    }
}

$title = 'Tambah Kategori';
include __DIR__ . '/../../templates/sidebar.php';
?>

<div class="max-w-md mx-auto bg-white rounded-2xl shadow-lg p-6 fade-in">
    <h1 class="text-2xl font-bold text-green-800 mb-6">Tambah Kategori</h1>
    
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
        <div>
            <label class="block font-semibold text-gray-700 mb-2">Nama Kategori</label>
            <input type="text" name="nama_kategori" required autocomplete="off" placeholder="Contoh: Gizi Balita"
                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200">
        </div>
        <div class="flex gap-3 mt-6">
            <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-500 text-white py-2 rounded-xl font-semibold hover:shadow-lg transition">
                <i class="fas fa-save mr-1"></i> Simpan
            </button>
            <a href="list_kategori.php" class="flex-1 bg-gray-200 text-gray-700 text-center py-2 rounded-xl font-semibold hover:bg-gray-300 transition flex items-center justify-center">
                Batal
            </a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>