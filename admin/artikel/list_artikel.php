<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';
$title = 'Artikel';
include __DIR__ . '/../../templates/sidebar.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$kategori_id = isset($_GET['kategori']) ? (int)$_GET['kategori'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

$where = "WHERE 1=1";
if($search) $where .= " AND (a.judul LIKE '%$search%' OR a.konten LIKE '%$search%')";
if($kategori_id) $where .= " AND a.id_kategori = $kategori_id";

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM artikel a $where"))['total'];
$total_pages = ceil($total / $limit);
$result = mysqli_query($conn, "SELECT a.*, k.nama_kategori FROM artikel a LEFT JOIN kategori_artikel k ON a.id_kategori=k.id_kategori $where ORDER BY a.created_at DESC LIMIT $offset, $limit");
$kategori = mysqli_query($conn, "SELECT * FROM kategori_artikel");
?>

<div class="fade-in">
    <div class="flex flex-wrap justify-between items-center mb-4 gap-3">
        <h1 class="text-2xl font-bold text-green-800">Artikel Kesehatan</h1>
        <a href="tambah_artikel.php" class="bg-gradient-to-r from-green-600 to-emerald-500 text-white px-4 py-2 rounded-xl hover:shadow-lg transition">
            <i class="fas fa-plus mr-2"></i>Tambah Artikel
        </a>
    </div>
    
    <!-- Search & Filter -->
    <div class="bg-white rounded-2xl shadow-lg p-4 mb-6">
        <form method="GET" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="<?php echo $search; ?>" placeholder="Cari artikel..." 
                       class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400">
            </div>
            <select name="kategori" class="px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 bg-white">
                <option value="">Semua Kategori</option>
                <?php while($cat = mysqli_fetch_assoc($kategori)): ?>
                <option value="<?php echo $cat['id_kategori']; ?>" <?php echo $kategori_id == $cat['id_kategori'] ? 'selected' : ''; ?>>
                    <?php echo $cat['nama_kategori']; ?>
                </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-xl hover:bg-green-700 transition">
                <i class="fas fa-search mr-2"></i> Filter
            </button>
            <?php if($search || $kategori_id): ?>
            <a href="list_artikel.php" class="bg-gray-500 text-white px-6 py-2 rounded-xl hover:bg-gray-600 transition text-center">
                <i class="fas fa-times mr-2"></i> Reset
            </a>
            <?php endif; ?>
        </form>
    </div>
    
    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition transform hover:-translate-y-1 duration-300">
            <!-- Thumbnail -->
            <div class="h-48 bg-gradient-to-r from-green-400 to-emerald-400 relative overflow-hidden">
                <?php if($row['thumbnail'] && file_exists("../../uploads/artikel/" . $row['thumbnail'])): ?>
                <img src="../../uploads/artikel/<?php echo $row['thumbnail']; ?>" 
                     alt="<?php echo $row['judul']; ?>" 
                     class="w-full h-full object-cover">
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
                <h3 class="font-bold text-gray-800 text-lg mb-2 line-clamp-2"><?php echo $row['judul']; ?></h3>
                <p class="text-gray-500 text-sm mb-3 line-clamp-3"><?php echo substr(strip_tags($row['konten']), 0, 100); ?>...</p>
                
                <!-- Meta Info -->
                <div class="flex items-center justify-between text-xs text-gray-400 mb-4">
                    <div class="flex items-center gap-1">
                        <i class="fas fa-user"></i>
                        <span><?php echo getUserName($row['penulis_nik']); ?></span>
                    </div>
                    <div class="flex items-center gap-1">
                        <i class="far fa-calendar-alt"></i>
                        <span><?php echo date('d M Y', strtotime($row['created_at'])); ?></span>
                    </div>
                </div>
                
               <!-- Action Buttons di dalam card, ganti dengan yang ini -->
                <div class="flex gap-2 pt-3 border-t border-gray-100">
                    <a href="detail_artikel.php?id=<?php echo $row['id_artikel']; ?>" 
                    class="flex-1 text-center bg-green-500 hover:bg-green-600 text-white py-2 rounded-xl text-sm transition flex items-center justify-center gap-1">
                        <i class="fas fa-eye"></i> Detail
                    </a>
                    <a href="edit_artikel.php?id=<?php echo $row['id_artikel']; ?>" 
                    class="flex-1 text-center bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-xl text-sm transition flex items-center justify-center gap-1">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="hapus_artikel.php?id=<?php echo $row['id_artikel']; ?>" 
                    class="flex-1 text-center bg-red-500 hover:bg-red-600 text-white py-2 rounded-xl text-sm transition flex items-center justify-center gap-1"
                    onclick="confirmDelete(event, this.href)">
                        <i class="fas fa-trash"></i> Hapus
                    </a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    
    <!-- Empty State -->
    <?php if(mysqli_num_rows($result) == 0): ?>
    <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
        <i class="fas fa-newspaper text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Artikel</h3>
        <p class="text-gray-500">Silakan tambahkan artikel baru</p>
        <a href="tambah_artikel.php" class="inline-block mt-4 bg-green-600 text-white px-6 py-2 rounded-xl hover:bg-green-700 transition">
            <i class="fas fa-plus mr-2"></i> Tambah Artikel
        </a>
    </div>
    <?php endif; ?>
    
    <!-- Pagination -->
    <?php if($total_pages > 1): ?>
    <div class="mt-8">
        <div class="flex justify-center items-center gap-2 flex-wrap">
            <?php if($page > 1): ?>
            <a href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&kategori=<?php echo $kategori_id; ?>" 
               class="px-4 py-2 bg-gray-200 rounded-xl hover:bg-gray-300 transition flex items-center gap-1">
                <i class="fas fa-chevron-left"></i> Prev
            </a>
            <?php endif; ?>
            
            <?php 
            $start = max(1, $page - 2);
            $end = min($total_pages, $page + 2);
            
            if($start > 1) echo '<span class="px-3 py-2 text-gray-500">...</span>';
            for($i = $start; $i <= $end; $i++): 
            ?>
            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&kategori=<?php echo $kategori_id; ?>" 
               class="w-10 h-10 flex items-center justify-center rounded-xl transition <?php echo $i == $page ? 'bg-green-600 text-white shadow-md' : 'bg-gray-200 hover:bg-gray-300 text-gray-700'; ?>">
                <?php echo $i; ?>
            </a>
            <?php endfor; ?>
            <?php if($end < $total_pages) echo '<span class="px-3 py-2 text-gray-500">...</span>'; ?>
            
            <?php if($page < $total_pages): ?>
            <a href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&kategori=<?php echo $kategori_id; ?>" 
               class="px-4 py-2 bg-gray-200 rounded-xl hover:bg-gray-300 transition flex items-center gap-1">
                Next <i class="fas fa-chevron-right"></i>
            </a>
            <?php endif; ?>
        </div>
        <div class="text-center text-gray-500 text-sm mt-4">
            <i class="fas fa-database mr-1"></i> Menampilkan <?php echo mysqli_num_rows($result); ?> dari <?php echo number_format($total); ?> artikel
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>