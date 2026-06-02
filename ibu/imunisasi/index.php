<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_ibu.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

$where_base = "WHERE j.tanggal >= CURDATE()";
if ($search !== '') {
    $where_base .= " AND (v.nama_vaksin LIKE '%$search%' OR j.lokasi LIKE '%$search%' OR u.nama_lengkap LIKE '%$search%')";
}

$total_query = "SELECT COUNT(*) as total FROM jadwal_imunisasi j JOIN vaksin v ON j.id_vaksin=v.id_vaksin LEFT JOIN users u ON j.petugas_nik = u.nik $where_base";
$total = mysqli_fetch_assoc(mysqli_query($conn, $total_query))['total'];
$total_pages = ceil($total / $limit);

$query_jadwal = "SELECT j.*, v.nama_vaksin, v.deskripsi, u.nama_lengkap as nama_bidan 
    FROM jadwal_imunisasi j 
    JOIN vaksin v ON j.id_vaksin=v.id_vaksin 
    LEFT JOIN users u ON j.petugas_nik = u.nik 
    $where_base
    ORDER BY 
        CASE WHEN j.tanggal = CURDATE() THEN 0 ELSE 1 END,
        j.tanggal ASC 
    LIMIT $offset, $limit";

$result = mysqli_query($conn, $query_jadwal);

$title = 'Jadwal Imunisasi';
include __DIR__ . '/../../templates/sidebar.php';
?>

<div class="fade-in">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-green-800">Jadwal Imunisasi</h1>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-4 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Cari jadwal (Vaksin, Lokasi, atau Bidan Pelaksana)..." 
                       class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 text-sm">
            </div>
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-xl hover:bg-green-700 transition flex items-center justify-center gap-2 text-sm font-semibold shadow-sm">
                <i class="fas fa-search"></i> Cari
            </button>
            <?php if($search): ?>
            <a href="index.php" class="bg-gray-500 text-white px-6 py-2 rounded-xl hover:bg-gray-600 transition flex items-center justify-center gap-2 text-sm font-semibold shadow-sm">
                <i class="fas fa-times"></i> Reset
            </a>
            <?php endif; ?>
        </form>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if(mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)): 
            $tanggal_eval = new DateTime($row['tanggal']);
            $is_today = $row['tanggal'] == date('Y-m-d');
            
            $status_color = 'bg-green-50/60 border-green-100';
        ?>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition flex flex-col justify-between border border-gray-100 relative">
            <?php if($is_today): ?>
            <div class="absolute top-0 right-0 z-10">
                <span class="bg-red-500 text-white text-[10px] tracking-wider font-extrabold px-3 py-1 rounded-bl-xl shadow-sm block animate-pulse">
                    <i class="fas fa-calendar-day mr-1"></i> HARI INI
                </span>
            </div>
            <?php endif; ?>
            
            <div>
                <div class="<?php echo $status_color; ?> p-4 border-b">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center text-white shadow-sm flex-shrink-0">
                                <i class="fas fa-syringe"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 leading-tight"><?php echo htmlspecialchars($row['nama_vaksin']); ?></h3>
                                <span class="text-[11px] font-medium text-green-600">
                                    <i class="fas fa-dot-circle mr-1 text-[9px]"></i>Tersedia
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 space-y-3">
                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 text-xs text-gray-600 space-y-2.5">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-map-marker-alt text-red-500 mt-0.5 flex-shrink-0 w-3 text-center"></i>
                            <div>
                                <span class="text-gray-400 block font-medium">Tempat Pelaksanaan:</span>
                                <strong class="text-gray-700 font-semibold break-words"><?php echo htmlspecialchars($row['lokasi']); ?></strong>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-2 pt-2 border-t border-gray-200/50">
                            <i class="fas fa-user-md text-blue-500 mt-0.5 flex-shrink-0 w-3 text-center"></i>
                            <div>
                                <span class="text-gray-400 block font-medium">Bidan Pelaksana:</span>
                                <strong class="text-gray-700 font-semibold"><?php echo htmlspecialchars($row['nama_bidan'] ?? 'Belum Ditentukan'); ?></strong>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-2 border-t border-gray-200/50">
                            <i class="far fa-calendar-alt text-gray-400 flex-shrink-0 w-3 text-center"></i>
                            <span class="text-gray-700 font-medium"><?php echo formatTanggalIndonesia($row['tanggal']); ?></span>
                        </div>
                    </div>

                   <div class="bg-white border border-gray-100 rounded-xl p-3 text-xs text-gray-500">
                        <span class="text-gray-400 block font-medium mb-1">
                            <i class="fas fa-info-circle mr-1 text-green-500"></i>Info Vaksin:
                        </span>
                        <p class="leading-relaxed"><?php echo nl2br(htmlspecialchars($row['deskripsi'])); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="p-4 pt-0">
                <a href="daftar_imunisasi.php?jadwal_id=<?php echo $row['id_jadwal']; ?>" 
                   class="block w-full text-center bg-gradient-to-r from-green-600 to-emerald-500 text-white font-medium py-2 rounded-xl text-xs hover:bg-green-700 hover:shadow transition flex items-center justify-center gap-1">
                    <i class="fas fa-calendar-plus"></i> Daftar Sekarang
                </a>
            </div>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
        <div class="col-span-full bg-white rounded-2xl shadow-lg p-12 text-center">
            <i class="fas fa-calendar-times text-6xl text-gray-300 mb-3"></i>
            <h3 class="text-xl font-bold text-gray-600 mb-1">Jadwal Tidak Ditemukan</h3>
            <p class="text-gray-400 text-sm">Saat ini belum ada jadwal imunisasi aktif yang cocok dengan kata kunci tersebut.</p>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if($total_pages > 1): ?>
    <div class="mt-6">
        <?php echo paginate($page, $total_pages, "index.php"); ?>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>