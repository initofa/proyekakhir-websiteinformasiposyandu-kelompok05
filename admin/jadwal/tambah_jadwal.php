<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';

$vaksin = mysqli_query($conn, "SELECT * FROM vaksin");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $id_vaksin = $_POST['id_vaksin'];
    $tanggal = $_POST['tanggal'];
    $created_by = $_SESSION['nik'];
    
    $query = "INSERT INTO jadwal_imunisasi (id_vaksin, tanggal, created_by) VALUES ('$id_vaksin', '$tanggal', '$created_by')";
    if(mysqli_query($conn, $query)){
        $_SESSION['success'] = "Jadwal berhasil ditambahkan!";
        header("Location: list_jadwal.php");
        exit();
        }
        }

    $title = 'Tambah Jadwal';
    include __DIR__ . '/../../templates/sidebar.php';
?>

<div class="max-w-md mx-auto bg-white rounded-2xl shadow-lg p-6 fade-in">
    <h1 class="text-2xl font-bold text-green-800 mb-6">Tambah Jadwal Imunisasi</h1>
    <form method="POST">
        <div class="space-y-4">
            <div><label class="block font-semibold text-gray-700 mb-2">Vaksin</label><select name="id_vaksin" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400"><?php while($v = mysqli_fetch_assoc($vaksin)): ?><option value="<?php echo $v['id_vaksin']; ?>"><?php echo $v['nama_vaksin']; ?></option><?php endwhile; ?></select></div>
            <div><label class="block font-semibold text-gray-700 mb-2">Tanggal Imunisasi</label><input type="date" name="tanggal" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400"></div>
        </div>
        <div class="flex gap-3 mt-6">
            <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-500 text-white py-2 rounded-xl font-semibold hover:shadow-lg transition">Simpan</button>
            <a href="list_jadwal.php" class="flex-1 bg-gray-200 text-gray-700 text-center py-2 rounded-xl font-semibold hover:bg-gray-300 transition">Batal</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>