<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';

$id = isset($_POST['id_kategori']) ? (int)$_POST['id_kategori'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);

if ($id === 0) {
    $_SESSION['error'] = "Akses tidak sah!";
    header("Location: list_kategori.php");
    exit();
}

// Ambil data kategori lama
$kategori = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM kategori_artikel WHERE id_kategori=$id"));

if (!$kategori) {
    $_SESSION['error'] = "Kategori tidak ditemukan!";
    header("Location: list_kategori.php");
    exit();
}

// Proses pembaruan data ketika form dikirim
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update'){
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama_kategori']));
    
    $query = "UPDATE kategori_artikel SET nama_kategori='$nama' WHERE id_kategori=$id";
    
    if(mysqli_query($conn, $query)){
        $_SESSION['success'] = "Kategori berhasil diupdate!";
        header("Location: list_kategori.php");
        exit();
    } else {
        $_SESSION['error'] = "Gagal mengupdate kategori: " . mysqli_error($conn);
    }
}

$title = 'Edit Kategori';
include __DIR__ . '/../../templates/sidebar.php';
?>

<div class="max-w-md mx-auto bg-white rounded-2xl shadow-lg p-6 fade-in">
    <h1 class="text-2xl font-bold text-green-800 mb-6">Edit Kategori</h1>
    
    <!-- Integrasi Pop-up Pesan Error via SweetAlert -->
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
        <!-- Mengunci ID Kategori dan Penanda Aksi via Input Tersembunyi -->
        <input type="hidden" name="id_kategori" value="<?php echo $id; ?>">
        <input type="hidden" name="action" value="update">

        <div>
            <label class="block font-semibold text-gray-700 mb-2">Nama Kategori</label>
            <input type="text" name="nama_kategori" value="<?php echo htmlspecialchars($kategori['nama_kategori']); ?>" required 
                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200">
        </div>
        
        <div class="flex gap-3 mt-6">
            <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-500 text-white py-2 rounded-xl font-semibold hover:shadow-lg transition">
                <i class="fas fa-save mr-1"></i> Update
            </button>
            <a href="list_kategori.php" class="flex-1 bg-gray-200 text-gray-700 text-center py-2 rounded-xl font-semibold hover:bg-gray-300 transition flex items-center justify-center">
                Batal
            </a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>