<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';

// ============================================
// PROSES FORM - HARUS SEBELUM SIDEBAR
// ============================================

// Menangkap id_jadwal dari POST (dari list_jadwal.php) atau GET (fallback manual)
$id = isset($_POST['id_jadwal']) ? (int)$_POST['id_jadwal'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);

if ($id === 0) {
    $_SESSION['error'] = "Akses tidak sah!";
    header("Location: list_jadwal.php");
    exit();
}

// Ambil data jadwal lama
$jadwal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM jadwal_imunisasi WHERE id_jadwal=$id"));

if(!$jadwal){
    $_SESSION['error'] = "Jadwal tidak ditemukan!";
    header("Location: list_jadwal.php");
    exit();
}

// Ambil master data vaksin untuk pilihan dropdown
$vaksin = mysqli_query($conn, "SELECT * FROM vaksin ORDER BY usia_rekomendasi ASC");

// PERUBAHAN: Ambil data master user dengan ROLE 'bidan' dan STATUS 'active' untuk pilihan dropdown petugas
$petugas_res = mysqli_query($conn, "SELECT nik, nama_lengkap FROM users WHERE ROLE = 'bidan' AND STATUS = 'active' ORDER BY nama_lengkap ASC");

// Proses pembaruan data ketika form dikirim
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update'){
    $id_vaksin = (int)$_POST['id_vaksin'];
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $lokasi = mysqli_real_escape_string($conn, trim($_POST['lokasi']));
    $petugas_nik = mysqli_real_escape_string($conn, $_POST['petugas_nik']); // Menangkap input petugas_nik baru
    
    // PERUBAHAN: Menambahkan kolom 'petugas_nik' ke dalam query UPDATE
    $query = "UPDATE jadwal_imunisasi 
              SET id_vaksin='$id_vaksin', tanggal='$tanggal', lokasi='$lokasi', petugas_nik='$petugas_nik' 
              WHERE id_jadwal=$id";
    
    if(mysqli_query($conn, $query)){
        $_SESSION['success'] = "Jadwal berhasil diupdate!";
        header("Location: list_jadwal.php");
        exit();
    } else {
        $_SESSION['error'] = "Gagal mengupdate jadwal: " . mysqli_error($conn);
    }
}

$title = 'Edit Jadwal';
include __DIR__ . '/../../templates/sidebar.php';
?>

<div class="max-w-md mx-auto bg-white rounded-2xl shadow-lg p-6 fade-in">
    <h1 class="text-2xl font-bold text-green-800 mb-6">Edit Jadwal Imunisasi</h1>
    
    <!-- Pop-up Pesan Error Server-side via SweetAlert -->
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
        <!-- Mengunci ID Jadwal dan Penanda Aksi via Input Tersembunyi -->
        <input type="hidden" name="id_jadwal" value="<?php echo $id; ?>">
        <input type="hidden" name="action" value="update">

        <div class="space-y-4">
            <!-- Pilihan Vaksin -->
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Vaksin</label>
                <select name="id_vaksin" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 bg-white">
                    <?php while($v = mysqli_fetch_assoc($vaksin)): ?>
                        <option value="<?php echo $v['id_vaksin']; ?>" <?php echo $v['id_vaksin'] == $jadwal['id_vaksin'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($v['nama_vaksin']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <!-- Input Tanggal -->
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Tanggal Imunisasi</label>
                <input type="date" name="tanggal" value="<?php echo $jadwal['tanggal']; ?>" required 
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200">
            </div>

            <!-- Input Lokasi/Tempat -->
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Lokasi / Tempat Posyandu</label>
                <input type="text" name="lokasi" value="<?php echo htmlspecialchars($jadwal['lokasi']); ?>" required placeholder="Contoh: Posyandu Mawar - RT 01"
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200">
            </div>

            <!-- PERUBAHAN: Dropdown Pilihan Petugas Medis (Bidan) dengan deteksi Selected otomatis -->
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Petugas Pelaksana (Bidan)</label>
                <select name="petugas_nik" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 bg-white">
                    <option value="" disabled>-- Pilih Bidan Pelaksana --</option>
                    <?php if(mysqli_num_rows($petugas_res) > 0): ?>
                        <?php while($p = mysqli_fetch_assoc($petugas_res)): ?>
                            <option value="<?php echo $p['nik']; ?>" <?php echo $p['nik'] == $jadwal['petugas_nik'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($p['nama_lengkap']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <!-- Fallback aman jika bidan penanggung jawab sebelumnya dinonaktifkan statusnya -->
                        <option value="" disabled selected>Belum ada data bidan aktif yang tersedia</option>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-500 text-white py-2 rounded-xl font-semibold hover:shadow-lg transition">
                <i class="fas fa-save mr-1"></i> Update
            </button>
            <a href="list_jadwal.php" class="flex-1 bg-gray-200 text-gray-700 text-center py-2 rounded-xl font-semibold hover:bg-gray-300 transition flex items-center justify-center">
                Batal
            </a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>