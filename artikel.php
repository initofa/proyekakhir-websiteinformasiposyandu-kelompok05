<?php
// artikel.php - Halaman semua artikel (publik)
require_once __DIR__ . '/config/database.php';

// Pagination
$limit = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$kategori_id = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;
$search_keyword = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$where = "";
if($kategori_id > 0) {
    $where = "WHERE a.id_kategori = $kategori_id";
}
if($search_keyword != '') {
    if($where == "") {
        $where = "WHERE (a.judul LIKE '%$search_keyword%' OR a.konten LIKE '%$search_keyword%')";
    } else {
        $where .= " AND (a.judul LIKE '%$search_keyword%' OR a.konten LIKE '%$search_keyword%')";
    }
}

$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM artikel a $where");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $limit);

$query_artikel = "SELECT a.*, k.nama_kategori, u.nama_lengkap as penulis 
    FROM artikel a 
    LEFT JOIN kategori_artikel k ON a.id_kategori = k.id_kategori 
    LEFT JOIN users u ON a.penulis_nik = u.nik 
    $where
    ORDER BY a.created_at DESC LIMIT $offset, $limit";
$artikel = mysqli_query($conn, $query_artikel);

$query_kategori = "SELECT * FROM kategori_artikel ORDER BY nama_kategori";
$kategori_list = mysqli_query($conn, $query_kategori);

$title = 'Artikel Kesehatan - SIPANDA';
include __DIR__ . '/templates/header_public.php';
?>

<!-- ========== HERO SECTION MODERN ========== -->
<section class="relative py-16 md:py-20 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-green-600 to-green-500"></div>
    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
    <div class="container mx-auto px-4 text-center relative z-10">
        <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full text-white/90 text-sm mb-6">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
            </svg>
            <span>Edukasi Kesehatan</span>
        </div>
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4">Artikel Kesehatan</h1>
        <p class="text-green-100 text-base md:text-lg max-w-2xl mx-auto">Informasi terpercaya untuk kesehatan ibu dan anak dari para ahli</p>
        
        <!-- Search Bar - Menggunakan Form GET -->
        <div class="max-w-md mx-auto mt-8">
            <form method="GET" action="" id="searchForm">
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" name="search" id="searchArtikel" value="<?php echo htmlspecialchars($search_keyword); ?>" placeholder="Cari artikel..." class="w-full pl-12 pr-24 py-3 rounded-xl bg-white/90 backdrop-blur-sm border-0 focus:ring-2 focus:ring-white focus:outline-none text-gray-800 placeholder-gray-400">
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-green-600 text-white px-4 py-1.5 rounded-lg text-sm font-semibold hover:bg-green-700 transition">Cari</button>
                </div>
                <?php if($search_keyword): ?>
                <a href="artikel.php" class="inline-block mt-2 text-white/80 text-sm hover:text-white transition">✕ Hapus filter</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
</section>

<!-- ========== KONTEN ARTIKEL ========== -->
<div class="container mx-auto px-4 py-12">
    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- ========== SIDEBAR KATEGORI MODERN ========== -->
        <div class="w-full lg:w-80">
            <div class="bg-white rounded-2xl shadow-xl p-6 sticky top-24 border border-gray-100">
                <div class="flex items-center gap-2 mb-5 pb-3 border-b border-gray-100">
                    <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4 6h16v2H4V6zm2-4h12v2H6V2zm16 8H2v12h20V10zm-2 10H4v-8h16v8z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800 text-lg">Kategori Artikel</h3>
                </div>
                
                <ul class="space-y-2">
                    <li>
                        <a href="artikel.php" class="flex items-center justify-between py-2.5 px-4 rounded-xl <?php echo $kategori_id == 0 && $search_keyword == '' ? 'bg-gradient-to-r from-green-50 to-white text-green-600 font-semibold border-l-4 border-green-500' : 'text-gray-600 hover:bg-gray-50'; ?> transition-all duration-200">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2a10 10 0 0110 10 10 10 0 01-10 10 10 10 0 01-10-10 10 10 0 0110-10m0 2a8 8 0 00-8 8 8 8 0 008 8 8 8 0 008-8 8 8 0 00-8-8z"/>
                                </svg>
                                Semua Artikel
                            </span>
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full"><?php echo $total_data; ?></span>
                        </a>
                    </li>
                    <?php while($kat = mysqli_fetch_assoc($kategori_list)): 
                        // Hitung jumlah artikel per kategori dengan mempertimbangkan search
                        $count_kat_query = "SELECT COUNT(*) as total FROM artikel WHERE id_kategori = {$kat['id_kategori']}";
                        if($search_keyword) {
                            $count_kat_query .= " AND (judul LIKE '%$search_keyword%' OR konten LIKE '%$search_keyword%')";
                        }
                        $count_kat = mysqli_fetch_assoc(mysqli_query($conn, $count_kat_query))['total'];
                    ?>
                    <li>
                        <a href="?kategori=<?php echo $kat['id_kategori']; ?><?php echo $search_keyword ? '&search='.urlencode($search_keyword) : ''; ?>" class="flex items-center justify-between py-2.5 px-4 rounded-xl <?php echo $kategori_id == $kat['id_kategori'] ? 'bg-gradient-to-r from-green-50 to-white text-green-600 font-semibold border-l-4 border-green-500' : 'text-gray-600 hover:bg-gray-50'; ?> transition-all duration-200">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M4 6h16v2H4V6zm2-4h12v2H6V2zm16 8H2v12h20V10zm-2 10H4v-8h16v8z"></path>
                                </svg>
                                <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                            </span>
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full"><?php echo $count_kat; ?></span>
                        </a>
                    </li>
                    <?php endwhile; ?>
                </ul>
                
                <!-- Tips Card -->
                <div class="mt-6 p-4 bg-gradient-to-r from-green-50 to-pink-50 rounded-xl">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2a10 10 0 0110 10 10 10 0 01-10 10 10 10 0 01-10-10 10 10 0 0110-10m0 2a8 8 0 00-8 8 8 8 0 008 8 8 8 0 008-8 8 8 0 00-8-8z"></path>
                        </svg>
                        <p class="font-semibold text-gray-700 text-sm">💡 Tips Sehat</p>
                    </div>
                    <p class="text-xs text-gray-600">Baca artikel terbaru setiap minggu untuk informasi kesehatan terkini!</p>
                </div>
            </div>
        </div>
        
        <!-- ========== DAFTAR ARTIKEL MODERN DENGAN GAMBAR ========== -->
        <div class="flex-1">
            <!-- Result Info -->
            <?php if($search_keyword): ?>
            <div class="mb-4 px-4 py-2 bg-green-50 rounded-lg text-sm text-gray-600">
                <i class="fas fa-search mr-2"></i> Menampilkan hasil pencarian untuk "<strong><?php echo htmlspecialchars($search_keyword); ?></strong>" (<?php echo $total_data; ?> artikel)
            </div>
            <?php endif; ?>
            
            <?php if(mysqli_num_rows($artikel) > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php while($row = mysqli_fetch_assoc($artikel)): ?>
                    <div class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-gray-100">
                        <!-- Thumbnail Area dengan Gambar -->
                        <div class="relative h-52 overflow-hidden cursor-pointer" onclick="window.location.href='artikel_detail.php?id=<?php echo $row['id_artikel']; ?>'">
                            <?php if($row['thumbnail'] && file_exists("uploads/artikel/" . $row['thumbnail'])): ?>
                            <img src="uploads/artikel/<?php echo $row['thumbnail']; ?>" 
                                 alt="<?php echo htmlspecialchars($row['judul']); ?>" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                            <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-green-100 to-pink-100 flex items-center justify-center">
                                <svg class="w-16 h-16 text-green-300 group-hover:scale-110 transition duration-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2a10 10 0 0110 10 10 10 0 01-10 10 10 10 0 01-10-10 10 10 0 0110-10m0 2a8 8 0 00-8 8 8 8 0 008 8 8 8 0 008-8 8 8 0 00-8-8z"/>
                                </svg>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Overlay Gradient -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300"></div>
                            
                            <!-- Badge Kategori -->
                            <div class="absolute top-3 left-3">
                                <span class="text-xs bg-white/90 backdrop-blur-sm text-green-700 px-2.5 py-1 rounded-full font-medium shadow-sm">
                                    <?php echo htmlspecialchars($row['nama_kategori'] ?? 'Kesehatan'); ?>
                                </span>
                            </div>
                            
                            <!-- Badge Date -->
                            <div class="absolute bottom-3 right-3">
                                <span class="text-xs bg-black/50 backdrop-blur-sm text-white px-2.5 py-1 rounded-full">
                                    <?php echo date('d M Y', strtotime($row['created_at'])); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-5">
                            <h3 class="font-bold text-lg text-gray-800 mb-2 line-clamp-2 group-hover:text-green-600 transition duration-300">
                                <?php echo htmlspecialchars($row['judul']); ?>
                            </h3>
                            
                            <div class="flex items-center gap-3 text-xs text-gray-400 mb-3">
                                <?php if($row['penulis']): ?>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                    </svg>
                                    <?php echo htmlspecialchars(substr($row['penulis'], 0, 15)); ?>
                                </span>
                                <?php endif; ?>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <?php echo ceil(str_word_count(strip_tags($row['konten'])) / 200); ?> min
                                </span>
                            </div>
                            
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3 leading-relaxed">
                                <?php echo htmlspecialchars(substr(strip_tags($row['konten']), 0, 120)) . '...'; ?>
                            </p>
                            
                            <a href="artikel_detail.php?id=<?php echo $row['id_artikel']; ?>" class="inline-flex items-center gap-2 text-green-600 font-semibold text-sm group-hover:gap-3 transition-all duration-300">
                                <span>Baca Selengkapnya</span>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                
                <!-- ========== PAGINATION MODERN ========== -->
                <?php if($total_pages > 1): ?>
                <div class="flex justify-center gap-2 mt-12">
                    <?php if($page > 1): ?>
                    <a href="?page=<?php echo $page-1; ?><?php echo $kategori_id ? '&kategori='.$kategori_id : ''; ?><?php echo $search_keyword ? '&search='.urlencode($search_keyword) : ''; ?>" class="w-10 h-10 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center hover:bg-gradient-to-r hover:from-green-600 hover:to-green-500 hover:text-white transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <?php endif; ?>
                    
                    <?php 
                    $start = max(1, $page - 2);
                    $end = min($total_pages, $page + 2);
                    for($i = $start; $i <= $end; $i++): 
                    ?>
                    <a href="?page=<?php echo $i; ?><?php echo $kategori_id ? '&kategori='.$kategori_id : ''; ?><?php echo $search_keyword ? '&search='.urlencode($search_keyword) : ''; ?>" class="w-10 h-10 rounded-xl <?php echo $i == $page ? 'bg-gradient-to-r from-green-600 to-green-500 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?> flex items-center justify-center transition-all duration-300 font-medium">
                        <?php echo $i; ?>
                    </a>
                    <?php endfor; ?>
                    
                    <?php if($page < $total_pages): ?>
                    <a href="?page=<?php echo $page+1; ?><?php echo $kategori_id ? '&kategori='.$kategori_id : ''; ?><?php echo $search_keyword ? '&search='.urlencode($search_keyword) : ''; ?>" class="w-10 h-10 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center hover:bg-gradient-to-r hover:from-green-600 hover:to-green-500 hover:text-white transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="text-center py-20 bg-white rounded-2xl shadow-lg">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2a10 10 0 0110 10 10 10 0 01-10 10 10 10 0 01-10-10 10 10 0 0110-10m0 2a8 8 0 00-8 8 8 8 0 008 8 8 8 0 008-8 8 8 0 00-8-8z"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 text-lg">Tidak ada artikel ditemukan</p>
                    <?php if($search_keyword): ?>
                    <p class="text-gray-400 text-sm mt-1">Pencarian "<?php echo htmlspecialchars($search_keyword); ?>" tidak ditemukan</p>
                    <a href="artikel.php" class="inline-flex items-center gap-2 mt-4 text-green-600 hover:text-green-700 font-semibold">
                        Lihat semua artikel
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                    <?php else: ?>
                    <p class="text-gray-500 text-sm mt-1">Belum ada artikel dalam kategori ini</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<?php include __DIR__ . '/templates/footer_public.php'; ?>