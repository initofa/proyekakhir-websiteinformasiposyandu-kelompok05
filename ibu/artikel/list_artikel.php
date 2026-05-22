<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_ibu.php';
$title = 'Artikel Kesehatan';
include __DIR__ . '/../../templates/sidebar.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$kategori_id = isset($_GET['kategori']) ? (int)$_GET['kategori'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Perbaiki WHERE clause dengan alias tabel yang jelas
$where = "WHERE 1=1";
if($search) $where .= " AND (a.judul LIKE '%$search%' OR a.konten LIKE '%$search%')";
if($kategori_id) $where .= " AND a.id_kategori = $kategori_id";

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM artikel a $where"))['total'];
$total_pages = ceil($total / $limit);
$result = mysqli_query($conn, "SELECT a.*, k.nama_kategori 
    FROM artikel a 
    LEFT JOIN kategori_artikel k ON a.id_kategori = k.id_kategori 
    $where 
    ORDER BY a.created_at DESC 
    LIMIT $offset, $limit");
$kategori = mysqli_query($conn, "SELECT * FROM kategori_artikel ORDER BY nama_kategori");
?>

<div class="fade-in">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-green-800">Artikel Kesehatan</h1>
        <p class="text-gray-500 mt-1">Informasi dan edukasi untuk kesehatan ibu dan anak</p>
    </div>
    
    <!-- Search & Filter -->
    <div class="bg-white rounded-2xl shadow-md p-5 mb-6">
        <form method="GET" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" value="<?php echo $search; ?>" 
                           placeholder="Cari artikel..." 
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-100 transition">
                </div>
            </div>
            <div class="w-full md:w-48">
                <select name="kategori" onchange="this.form.submit()" 
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 bg-white">
                    <option value="">Semua Kategori</option>
                    <?php while($cat = mysqli_fetch_assoc($kategori)): ?>
                    <option value="<?php echo $cat['id_kategori']; ?>" <?php echo $kategori_id == $cat['id_kategori'] ? 'selected' : ''; ?>>
                        <?php echo $cat['nama_kategori']; ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="bg-green-600 text-white px-6 py-2.5 rounded-xl hover:bg-green-700 transition flex items-center justify-center gap-2">
                <i class="fas fa-search"></i> Cari
            </button>
            <?php if($search || $kategori_id): ?>
            <a href="list_artikel.php" class="bg-gray-500 text-white px-6 py-2.5 rounded-xl hover:bg-gray-600 transition flex items-center justify-center gap-2">
                <i class="fas fa-times"></i> Reset
            </a>
            <?php endif; ?>
        </form>
    </div>
    
    <!-- Result Info -->
    <?php if($search || $kategori_id): ?>
    <div class="mb-4 text-sm text-gray-500">
        <i class="fas fa-search mr-1"></i> Menampilkan hasil 
        <?php if($search): ?>untuk "<strong><?php echo htmlspecialchars($search); ?></strong>" <?php endif; ?>
        <?php if($kategori_id): ?>di kategori <strong><?php 
            $cat_name = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_kategori FROM kategori_artikel WHERE id_kategori=$kategori_id"));
            echo $cat_name['nama_kategori'];
        ?></strong><?php endif; ?>
        (<?php echo number_format($total); ?> artikel)
    </div>
    <?php endif; ?>
    
    <!-- Articles Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 group">
            <!-- Thumbnail -->
            <div class="h-44 bg-gradient-to-r from-green-400 to-emerald-400 relative overflow-hidden">
                <?php if($row['thumbnail'] && file_exists("../../uploads/artikel/" . $row['thumbnail'])): ?>
                <img src="../../uploads/artikel/<?php echo $row['thumbnail']; ?>" 
                     alt="<?php echo $row['judul']; ?>" 
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <?php else: ?>
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-r from-green-400 to-emerald-400">
                    <i class="fas fa-newspaper text-white text-5xl opacity-50"></i>
                </div>
                <?php endif; ?>
                
                <!-- Kategori Badge -->
                <div class="absolute top-3 left-3">
                    <span class="px-2 py-1 bg-white/90 backdrop-blur-sm text-green-700 text-xs font-semibold rounded-lg shadow-sm">
                        <?php echo $row['nama_kategori']; ?>
                    </span>
                </div>
            </div>
            
            <!-- Content -->
            <div class="p-4">
                <h3 class="font-bold text-gray-800 text-md mb-2 line-clamp-2 group-hover:text-green-600 transition">
                    <?php echo $row['judul']; ?>
                </h3>
                <p class="text-gray-500 text-xs mb-3 line-clamp-3">
                    <?php echo substr(strip_tags($row['konten']), 0, 100); ?>...
                </p>
                
                <!-- Meta Info -->
                <div class="flex items-center text-xs text-gray-400 mb-3">
                    <div class="flex items-center gap-1">
                        <i class="far fa-calendar-alt"></i>
                        <span><?php echo date('d M Y', strtotime($row['created_at'])); ?></span>
                    </div>
                </div>
                
                <!-- Read More Button -->
                <a href="detail_artikel.php?id=<?php echo $row['id_artikel']; ?>" 
                   class="flex items-center justify-center gap-1 w-full bg-gray-50 hover:bg-green-50 text-gray-600 hover:text-green-600 py-2 rounded-xl text-sm font-medium transition border border-gray-100">
                    Baca Artikel <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
        <?php endwhile; ?>
        
        <!-- Empty State -->
        <?php if(mysqli_num_rows($result) == 0): ?>
        <div class="col-span-full">
            <div class="bg-white rounded-2xl shadow-md p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-newspaper text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak Ada Artikel</h3>
                <p class="text-gray-500">
                    <?php if($search || $kategori_id): ?>
                    Artikel tidak ditemukan untuk pencarian Anda.
                    <?php else: ?>
                    Belum ada artikel yang tersedia.
                    <?php endif; ?>
                </p>
                <?php if($search || $kategori_id): ?>
                <a href="list_artikel.php" class="inline-block mt-4 bg-green-600 text-white px-6 py-2 rounded-xl hover:bg-green-700 transition">
                    <i class="fas fa-times mr-2"></i> Reset Filter
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Pagination -->
    <?php if($total_pages > 1): ?>
    <div class="mt-8">
        <?php echo paginate($page, $total_pages, "list_artikel.php?search=" . urlencode($search) . "&kategori=$kategori_id"); ?>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>