<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';
$title = 'Edit Kategori';
include __DIR__ . '/../../templates/sidebar.php';

$id = $_GET['id'];
$kategori = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM kategori_artikel WHERE id_kategori=$id"));

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nama = $_POST['nama_kategori'];
    $updated_by = $_SESSION['nik'];
    
    $query = "UPDATE kategori_artikel SET nama_kategori='$nama', updated_by='$updated_by' WHERE id_kategori=$id";
    if(mysqli_query($conn, $query)){
        $_SESSION['success'] = "Kategori berhasil diupdate!";
        header("Location: list_kategori.php");
        exit();
    }
}
?>

<div class="max-w-md mx-auto bg-white rounded-2xl shadow-lg p-6 fade-in">
    <h1 class="text-2xl font-bold text-green-800 mb-6">Edit Kategori</h1>
    <form method="POST">
        <div><label class="block font-semibold text-gray-700 mb-2">Nama Kategori</label><input type="text" name="nama_kategori" value="<?php echo $kategori['nama_kategori']; ?>" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400"></div>
        <div class="flex gap-3 mt-6">
            <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-500 text-white py-2 rounded-xl font-semibold hover:shadow-lg transition">Update</button>
            <a href="list_kategori.php" class="flex-1 bg-gray-200 text-gray-700 text-center py-2 rounded-xl font-semibold hover:bg-gray-300 transition">Batal</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>