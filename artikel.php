<?php
require_once __DIR__ . '/config/database.php';

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

$total_mutlak_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM artikel");
$total_mutlak = mysqli_fetch_assoc($total_mutlak_query)['total'];

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

<section class="relative py-12 md:py-16 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-green-700 via-green-600 to-green-500"></div>
    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
    <div class="container mx-auto px-4 text-center relative z-10">
        <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-1.5 rounded-full text-white/95 text-xs mb-4 font-medium tracking-wide">
            <svg class="w-4 h-4 text-emerald-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
            </svg>
            <span>Media Edukasi & Informasi Kesehatan</span>
        </div>
        <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-3 tracking-tight">Artikel Kesehatan</h1>
        <p class="text-green-50 text-sm md:text-base max-w-2xl mx-auto opacity-90">Informasi terpercaya untuk kesehatan ibu, janin, dan tumbuh kembang anak langsung dari para ahli medis.</p>
        
        <div class="max-w-md mx-auto mt-6">
            <form method="GET" action="" id="searchForm">
                <div class="relative shadow-sm rounded-xl overflow-hidden">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" name="search" id="searchArtikel" value="<?php echo htmlspecialchars($search_keyword); ?>" placeholder="Cari topik kesehatan atau kata kunci..." class="w-full pl-12 pr-24 py-3 rounded-xl bg-white/95 border-0 focus:ring-2 focus:ring-green-400 focus:outline-none text-gray-800 text-sm placeholder-gray-400">
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-green-600 text-white px-5 py-1.5 rounded-lg text-xs font-bold hover:bg-green-700 transition">Cari</button>
                </div>
            </form>
        </div>
    </div>
</section>

<div class="container mx-auto px-4 py-10">
    <div class="flex flex-col lg:flex-row gap-8">
        
        <div class="w-full lg:w-72 flex-shrink-0">
            <div class="bg-white rounded-2xl shadow-md p-5 sticky top-24 border border-gray-100">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                    <div class="w-7 h-7 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center text-white shadow-sm">
                        <i class="fas fa-folder-open text-xs"></i>
                    </div>
                    <h3 class="font-bold text-gray-800 text-base">Kategori Pilihan</h3>
                </div>
                
                <ul class="space-y-1 text-sm">
                    <li>
                        <a href="artikel.php" class="flex items-center justify-between py-2 px-3.5 rounded-xl <?php echo $kategori_id == 0 && $search_keyword == '' ? 'bg-green-50 text-green-700 font-bold border-l-4 border-green-500' : 'text-gray-600 hover:bg-gray-50'; ?> transition">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-globe-asia text-[13px] opacity-70"></i> Semua Artikel
                            </span>
                            <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full font-semibold"><?php echo $total_mutlak; ?></span>
                        </a>
                    </li>
                    <?php while($kat = mysqli_fetch_assoc($kategori_list)): 
                        $count_kat_query = "SELECT COUNT(*) as total FROM artikel WHERE id_kategori = {$kat['id_kategori']}";
                        if($search_keyword) {
                            $count_kat_query .= " AND (judul LIKE '%$search_keyword%' OR konten LIKE '%$search_keyword%')";
                        }
                        $count_kat = mysqli_fetch_assoc(mysqli_query($conn, $count_kat_query))['total'];
                    ?>
                    <li>
                        <a href="?kategori=<?php echo $kat['id_kategori']; ?><?php echo $search_keyword ? '&search='.urlencode($search_keyword) : ''; ?>" class="flex items-center justify-between py-2 px-3.5 rounded-xl <?php echo $kategori_id == $kat['id_kategori'] ? 'bg-green-50 text-green-700 font-bold border-l-4 border-green-500' : 'text-gray-600 hover:bg-gray-50'; ?> transition">
                            <span class="flex items-center gap-2 truncate">
                                <i class="fas fa-bookmark text-[12px] opacity-60"></i> <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                            </span>
                            <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full font-semibold"><?php echo $count_kat; ?></span>
                        </a>
                    </li>
                    <?php endwhile; ?>
                </ul>
                
                <div class="mt-6 p-4 bg-gradient-to-br from-green-50/60 to-emerald-50/60 rounded-xl border border-green-100/40">
                    <div class="flex items-center gap-2 mb-1.5">
                        <i class="fas fa-lightbulb text-green-600 text-sm"></i>
                        <p class="font-bold text-gray-700 text-xs uppercase tracking-wider">Tips Edukasi</p>
                    </div>
                    <p class="text-[11px] text-gray-500 leading-relaxed font-medium">Informasi diperbarui berkala secara valid oleh tim Bidan dan Petugas Kesehatan Posyandu SIPANDA.</p>
                </div>
            </div>
        </div>
        
        <div class="flex-1">    
            <?php if(mysqli_num_rows($artikel) > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php while($row = mysqli_fetch_assoc($artikel)): ?>
                    <div class="group bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1.5 border border-gray-100 flex flex-col justify-between">
                        <div>
                            <div class="relative h-48 overflow-hidden cursor-pointer" onclick="window.location.href='artikel_detail.php?id=<?php echo $row['id_artikel']; ?>'">
                                <?php if($row['thumbnail'] && file_exists("uploads/artikel/" . $row['thumbnail'])): ?>
                                <img src="uploads/artikel/<?php echo $row['thumbnail']; ?>" 
                                     alt="<?php echo htmlspecialchars($row['judul']); ?>" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br from-green-50 to-emerald-100 flex items-center justify-center">
                                    <i class="fas fa-image text-4xl text-green-200 group-hover:scale-110 transition duration-300"></i>
                                </div>
                                <?php endif; ?>
                                
                                <div class="absolute top-3 left-3">
                                    <span class="text-[10px] bg-white/90 backdrop-blur-sm text-green-800 px-2.5 py-0.5 rounded-md font-bold shadow-sm uppercase tracking-wider">
                                        <?php echo htmlspecialchars($row['nama_kategori'] ?? 'Kesehatan'); ?>
                                    </span>
                                </div>
                                
                                <div class="absolute bottom-3 right-3">
                                    <span class="text-[10px] bg-black/60 backdrop-blur-sm text-white px-2.5 py-1 rounded-md font-medium tracking-wide">
                                        <?php echo formatTanggalIndonesia($row['created_at']); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="p-5">
                                <h3 class="font-bold text-base text-gray-800 mb-2 line-clamp-2 group-hover:text-green-600 transition duration-200 leading-snug cursor-pointer" onclick="window.location.href='artikel_detail.php?id=<?php echo $row['id_artikel']; ?>'">
                                    <?php echo htmlspecialchars($row['judul']); ?>
                                </h3>
                                
                                <div class="flex items-center gap-3 text-[11px] text-gray-400 mb-3 font-medium">
                                    <?php if($row['penulis']): ?>
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-user text-[10px] text-gray-300"></i>
                                        <?php echo htmlspecialchars(substr($row['penulis'], 0, 15)); ?>
                                    </span>
                                    <?php endif; ?>
                                    <span class="flex items-center gap-1">
                                        <i class="far fa-clock text-[10px] text-gray-300"></i>
                                        <?php echo ceil(str_word_count(strip_tags($row['konten'])) / 200); ?> mnt baca
                                    </span>
                                </div>
                                
                                <p class="text-gray-500 text-xs mb-1 line-clamp-3 leading-relaxed font-medium">
                                    <?php echo htmlspecialchars(substr(strip_tags($row['konten']), 0, 120)) . '...'; ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="px-5 pb-5 pt-1">
                            <a href="artikel_detail.php?id=<?php echo $row['id_artikel']; ?>" class="inline-flex items-center gap-1.5 text-green-600 font-bold text-xs group-hover:gap-2.5 transition-all duration-200">
                                <span>Baca Selengkapnya</span>
                                <i class="fas fa-arrow-right text-[10px] group-hover:translate-x-0.5 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                
                <?php if($total_pages > 1): ?>
                <div class="flex justify-center gap-1.5 mt-10">
                    <?php if($page > 1): ?>
                    <a href="?page=<?php echo $page-1; ?><?php echo $kategori_id ? '&kategori='.$kategori_id : ''; ?><?php echo $search_keyword ? '&search='.urlencode($search_keyword) : ''; ?>" class="w-9 h-9 rounded-xl bg-gray-50 border border-gray-100 text-gray-500 flex items-center justify-center hover:bg-green-600 hover:text-white transition duration-200">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php 
                    $start = max(1, $page - 2);
                    $end = min($total_pages, $page + 2);
                    for($i = $start; $i <= $end; $i++): 
                    ?>
                    <a href="?page=<?php echo $i; ?><?php echo $kategori_id ? '&kategori='.$kategori_id : ''; ?><?php echo $search_keyword ? '&search='.urlencode($search_keyword) : ''; ?>" class="w-9 h-9 rounded-xl <?php echo $i == $page ? 'bg-gradient-to-r from-green-600 to-emerald-500 text-white font-bold shadow-sm' : 'bg-gray-50 text-gray-600 hover:bg-gray-200/60 border border-gray-100'; ?> flex items-center justify-center transition text-xs font-semibold">
                        <?php echo $i; ?>
                    </a>
                    <?php endfor; ?>
                    
                    <?php if($page < $total_pages): ?>
                    <a href="?page=<?php echo $page+1; ?><?php echo $kategori_id ? '&kategori='.$kategori_id : ''; ?><?php echo $search_keyword ? '&search='.urlencode($search_keyword) : ''; ?>" class="w-9 h-9 rounded-xl bg-gray-50 border border-gray-100 text-gray-500 flex items-center justify-center hover:bg-green-600 hover:text-white transition duration-200">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="text-center py-16 bg-white rounded-2xl shadow-md border border-gray-100">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-300">
                        <i class="fas fa-newspaper text-3xl"></i>
                    </div>
                    <p class="text-gray-700 font-bold text-base">Artikel Tidak Ditemukan</p>
                    <?php if($search_keyword): ?>
                    <p class="text-gray-400 text-xs mt-1">Kata kunci "<?php echo htmlspecialchars($search_keyword); ?>" tidak cocok dengan artikel apa pun.</p>
                    <a href="artikel.php" class="inline-flex items-center gap-1.5 mt-4 text-green-600 hover:text-green-700 font-bold text-xs bg-green-50 px-4 py-2 rounded-xl transition">
                        <i class="fas fa-sync-alt text-[10px]"></i> Lihat Semua Artikel
                    </a>
                    <?php else: ?>
                    <p class="text-gray-400 text-xs mt-1">Belum ada unggahan edukasi kesehatan di dalam kategori ini.</p>
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
        line-clamp: 2;
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-clamp: 3;
    }
</style>

<?php include __DIR__ . '/templates/footer_public.php'; ?>