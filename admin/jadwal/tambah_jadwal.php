<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';


if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $id_vaksin = (int)$_POST['id_vaksin'];
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $lokasi = mysqli_real_escape_string($conn, trim($_POST['lokasi']));
    $petugas_nik = mysqli_real_escape_string($conn, $_POST['petugas_nik']);
    $created_by = $_SESSION['nik'];
    
    $query = "INSERT INTO jadwal_imunisasi (id_vaksin, tanggal, lokasi, petugas_nik, created_by) 
              VALUES ('$id_vaksin', '$tanggal', '$lokasi', '$petugas_nik', '$created_by')";
              
    if(mysqli_query($conn, $query)){
        $_SESSION['success'] = "Jadwal berhasil ditambahkan!";
        header("Location: list_jadwal.php");
        exit();
    } else {
        $_SESSION['error'] = "Gagal menambahkan jadwal: " . mysqli_error($conn);
    }
}

$vaksin = mysqli_query($conn, "SELECT * FROM vaksin ORDER BY usia_rekomendasi ASC");

$petugas = mysqli_query($conn, "SELECT nik, nama_lengkap FROM users WHERE ROLE = 'bidan' AND STATUS = 'active' ORDER BY nama_lengkap ASC");

$title = 'Tambah Jadwal';
include __DIR__ . '/../../templates/sidebar.php';
?>

<div class="max-w-md mx-auto bg-white rounded-2xl shadow-lg p-6 fade-in">
    <h1 class="text-2xl font-bold text-green-800 mb-6">Tambah Jadwal Imunisasi</h1>
    
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
                <label class="block font-semibold text-gray-700 mb-2">Vaksin</label>
                <select name="id_vaksin" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 bg-white">
                    <option value="" disabled selected>Pilih Jenis Vaksin</option>
                    <?php while($v = mysqli_fetch_assoc($vaksin)): ?>
                        <option value="<?php echo $v['id_vaksin']; ?>"><?php echo htmlspecialchars($v['nama_vaksin']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Tanggal Imunisasi</label>
                <input type="date" name="tanggal" required 
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200">
            </div>

            <div>
                <label class="block font-semibold text-gray-700 mb-2">Lokasi</label>
                <input type="text" name="lokasi" required placeholder="Contoh: Sipanda - RW 01" autocomplete="off"
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200">
            </div>

            <div>
                <label class="block font-semibold text-gray-700 mb-2">Petugas(Bidan)</label>
                <select name="petugas_nik" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 bg-white">
                    <option value="" disabled selected>Pilih Bidan </option>
                    <?php if(mysqli_num_rows($petugas) > 0): ?>
                        <?php while($p = mysqli_fetch_assoc($petugas)): ?>
                            <option value="<?php echo $p['nik']; ?>"><?php echo htmlspecialchars($p['nama_lengkap']); ?></option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option value="" disabled>Belum ada data bidan yang aktif</option>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-500 text-white py-2 rounded-xl font-semibold hover:shadow-lg transition">
                <i class="fas fa-save mr-1"></i> Simpan
            </button>
            <a href="list_jadwal.php" class="flex-1 bg-gray-200 text-gray-700 text-center py-2 rounded-xl font-semibold hover:bg-gray-300 transition flex items-center justify-center">
                Batal
            </a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>