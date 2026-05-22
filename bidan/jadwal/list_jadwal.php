<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_bidan.php';

// ============================================
// PROSES REDIRECT - HARUS SEBELUM SIDEBAR
// ============================================

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;

$title = 'Jadwal Imunisasi';
include __DIR__ . '/../../templates/sidebar.php';

$nik = $_SESSION['nik'];
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM jadwal_imunisasi WHERE created_by='$nik'"))['total'];
$total_pages = ceil($total / $limit);

// Query dengan urutan: hari ini paling atas, lalu tanggal terbaru
$result = mysqli_query($conn, "SELECT j.*, v.nama_vaksin 
    FROM jadwal_imunisasi j 
    JOIN vaksin v ON j.id_vaksin=v.id_vaksin 
    WHERE j.created_by='$nik' 
    ORDER BY 
        CASE WHEN j.tanggal = CURDATE() THEN 0 ELSE 1 END,
        j.tanggal DESC 
    LIMIT $offset, $limit");
?>

<div class="fade-in">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-green-800">Jadwal Imunisasi</h1>
        <a href="tambah_jadwal.php" class="bg-gradient-to-r from-green-600 to-emerald-500 text-white px-4 py-2 rounded-xl hover:shadow-lg transition">
            <i class="fas fa-plus mr-2"></i>Jadwal
        </a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php while($row = mysqli_fetch_assoc($result)): 
            $is_today = $row['tanggal'] == date('Y-m-d');
        ?>
        <div class="bg-white rounded-2xl shadow-lg p-4 hover:shadow-xl transition relative">
            <!-- Label Hari Ini -->
            <?php if($is_today): ?>
            <div class="absolute top-0 right-0">
                <div class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg rounded-tr-lg shadow-md">
                    <i class="fas fa-calendar-day mr-1"></i> HARI INI
                </div>
            </div>
            <?php endif; ?>
            
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-green-600"></i>
                </div>
                <h3 class="font-bold text-gray-800"><?php echo $row['nama_vaksin']; ?></h3>
            </div>
            <p><i class="fas fa-calendar-day w-4 text-green-500"></i> <?php echo date('d F Y', strtotime($row['tanggal'])); ?></p>
            <div class="flex gap-2 mt-3">
                <a href="edit_jadwal.php?id=<?php echo $row['id_jadwal']; ?>" class="flex-1 text-center bg-blue-500 text-white py-1 rounded-lg text-sm hover:bg-blue-600 transition">Edit</a>
                <a href="hapus_jadwal.php?id=<?php echo $row['id_jadwal']; ?>" class="flex-1 text-center bg-red-500 text-white py-1 rounded-lg text-sm hover:bg-red-600 transition" onclick="confirmDelete(event, this.href)">Hapus</a>
            </div>
        </div>
        <?php endwhile; ?>
        
        <?php if(mysqli_num_rows($result) == 0): ?>
        <div class="col-span-full">
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Jadwal</h3>
                <p class="text-gray-500">Silakan buat jadwal imunisasi terlebih dahulu</p>
                <a href="tambah_jadwal.php" class="inline-block mt-4 bg-green-600 text-white px-6 py-2 rounded-xl hover:bg-green-700 transition">
                    <i class="fas fa-plus mr-2"></i> Buat Jadwal
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php if($total_pages > 1) echo paginate($page, $total_pages, "list_jadwal.php"); ?>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>