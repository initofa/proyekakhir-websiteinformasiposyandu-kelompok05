<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_ibu.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;

// Query dengan urutan: hari ini paling atas
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM jadwal_imunisasi WHERE tanggal >= CURDATE()"))['total'];
$total_pages = ceil($total / $limit);
$result = mysqli_query($conn, "SELECT j.*, v.nama_vaksin, v.deskripsi 
    FROM jadwal_imunisasi j 
    JOIN vaksin v ON j.id_vaksin=v.id_vaksin 
    WHERE j.tanggal >= CURDATE() 
    ORDER BY 
        CASE WHEN j.tanggal = CURDATE() THEN 0 ELSE 1 END,
        j.tanggal ASC 
    LIMIT $offset, $limit");

$title = 'Jadwal Imunisasi';
include __DIR__ . '/../../templates/sidebar.php';
?>

<div class="fade-in">
    <h1 class="text-2xl font-bold text-green-800 mb-4">Jadwal Imunisasi</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php while($row = mysqli_fetch_assoc($result)): 
            $is_today = $row['tanggal'] == date('Y-m-d');
        ?>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition relative">
            <!-- Label Hari Ini -->
            <?php if($is_today): ?>
            <div class="absolute top-0 right-0 z-10">
                <div class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg shadow-md">
                    <i class="fas fa-calendar-day mr-1"></i> HARI INI
                </div>
            </div>
            <?php endif; ?>
            
            <div class="p-6">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-syringe text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800"><?php echo $row['nama_vaksin']; ?></h3>
                    </div>
                </div>
                
                <div class="flex items-center gap-2 text-gray-600 mb-3">
                    <i class="fas fa-calendar-alt text-green-500"></i>
                    <span><?php echo date('d F Y', strtotime($row['tanggal'])); ?></span>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-3 mb-4 max-h-32 overflow-y-auto">
                    <p class="text-xs text-gray-600 leading-relaxed"><?php echo nl2br(htmlspecialchars($row['deskripsi'])); ?></p>
                </div>
                
                <a href="daftar_imunisasi.php?jadwal_id=<?php echo $row['id_jadwal']; ?>" 
                   class="block w-full text-center bg-gradient-to-r from-green-600 to-emerald-500 text-white py-2 rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-calendar-plus mr-2"></i> Daftar Sekarang
                </a>
            </div>
        </div>
        <?php endwhile; ?>
        
        <?php if(mysqli_num_rows($result) == 0): ?>
        <div class="col-span-full">
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak Ada Jadwal</h3>
                <p class="text-gray-500">Saat ini belum ada jadwal imunisasi yang tersedia</p>
                <p class="text-sm text-gray-400 mt-2">Silakan cek kembali nanti</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if($total_pages > 1): ?>
    <div class="mt-6">
        <?php echo paginate($page, $total_pages, "jadwal_imunisasi.php"); ?>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>