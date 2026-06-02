<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_bidan.php';

$nik_login = $_SESSION['nik']; 

$title = 'Artikel';
include __DIR__ . '/../../templates/sidebar.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$kategori_id = isset($_GET['kategori']) ? (int)$_GET['kategori'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

$where = "WHERE 1=1";
if($search) $where .= " AND (a.judul LIKE '%$search%' OR a.konten LIKE '%$search%')";
if($kategori_id) $where .= " AND a.id_kategori = $kategori_id";

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM artikel a $where"))['total'];
$total_pages = ceil($total / $limit);

$query_artikel = "SELECT a.*, k.nama_kategori, u.nama_lengkap AS nama_penulis 
                  FROM artikel a 
                  LEFT JOIN kategori_artikel k ON a.id_kategori = k.id_kategori 
                  LEFT JOIN users u ON a.penulis_nik = u.nik 
                  $where 
                  ORDER BY a.created_at DESC 
                  LIMIT $offset, $limit";

$result = mysqli_query($conn, $query_artikel);

$kategori_query = mysqli_query($conn, "SELECT * FROM kategori_artikel");
$list_kategori = [];
while($cat = mysqli_fetch_assoc($kategori_query)) {
    $list_kategori[] = $cat;
}
?>

<form id="formAksiArtikelPost" method="POST" style="display:none;">
    <input type="hidden" name="id_artikel" id="idArtikelAksiPost">
</form>

<div class="fade-in">
    <div class="flex flex-wrap justify-between items-center mb-4 gap-3">
        <div>
            <h1 class="text-2xl font-bold text-green-800">Artikel Kesehatan</h1>
        </div>
        <a href="tambah_artikel.php" class="bg-gradient-to-r from-green-600 to-emerald-500 text-white px-4 py-2 rounded-xl hover:shadow-lg transition text-sm font-semibold">
            <i class="fas fa-plus mr-2"></i>Artikel
        </a>
    </div>
    
    <div class="bg-white rounded-2xl shadow-lg p-4 mb-6">
        <form method="GET" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari judul atau konten artikel..." 
                       class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 text-sm">
            </div>
            <select name="kategori" class="px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 bg-white text-sm">
                <option value="">Semua Kategori</option>
                <?php foreach($list_kategori as $cat): ?>
                <option value="<?php echo $cat['id_kategori']; ?>" <?php echo $kategori_id == $cat['id_kategori'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['nama_kategori']); ?>
                </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-xl hover:bg-green-700 transition text-sm font-semibold shadow-sm">
                <i class="fas fa-search mr-2"></i> Filter
            </button>
            <?php if($search || $kategori_id): ?>
            <a href="index.php" class="bg-gray-500 text-white px-6 py-2 rounded-xl hover:bg-gray-600 transition text-center flex items-center justify-center text-sm font-semibold shadow-sm">
                <i class="fas fa-times mr-2"></i> Reset
            </a>
            <?php endif; ?>
        </form>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php if(mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)): 
            $is_my_article = ($row['penulis_nik'] === $nik_login);
        ?>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition transform hover:-translate-y-1 duration-300 flex flex-col justify-between border border-gray-100">
            <div>
                <div class="h-48 bg-gradient-to-r from-green-400 to-emerald-400 relative overflow-hidden">
                    <?php if($row['thumbnail'] && file_exists("../../uploads/artikel/" . $row['thumbnail'])): ?>
                    <img src="../../uploads/artikel/<?php echo $row['thumbnail']; ?>" 
                         alt="<?php echo htmlspecialchars($row['judul']); ?>" 
                         class="w-full h-full object-cover">
                    <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-r from-green-400 to-emerald-400">
                        <i class="fas fa-newspaper text-white text-5xl opacity-50"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="absolute top-3 left-3">
                        <span class="px-2 py-1 bg-white/90 backdrop-blur-sm text-green-700 text-xs font-semibold rounded-lg shadow-sm">
                            <?php echo htmlspecialchars($row['nama_kategori'] ?? 'Tanpa Kategori'); ?>
                        </span>
                    </div>
                </div>
                
                <div class="p-4">
                    <h3 class="font-bold text-gray-800 text-lg mb-2 line-clamp-2"><?php echo htmlspecialchars($row['judul']); ?></h3>
                    <p class="text-gray-500 text-sm mb-3 line-clamp-3"><?php echo htmlspecialchars(substr(strip_tags($row['konten']), 0, 100)); ?>...</p>
                </div>
            </div>
            
            <div class="p-4 pt-0">
                <div class="flex items-center justify-between text-xs text-gray-400 mb-4 bg-gray-50 p-2 rounded-xl border border-gray-100/60">
                    <div class="flex items-center gap-1 min-w-0">
                        <i class="fas fa-user flex-shrink-0 text-green-600"></i>
                        <span class="truncate font-medium <?php echo $is_my_article ? 'text-green-700 font-bold' : 'text-gray-600'; ?>">
                            <?php echo $is_my_article ? 'Saya' : htmlspecialchars($row['nama_penulis'] ?? 'Anonim'); ?>
                        </span>
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <i class="far fa-calendar-alt"></i>
                        <span><?php echo formatTanggalIndonesia($row['created_at']); ?></span>
                    </div>
                </div>
                
                <div class="flex gap-2 pt-3 border-t border-gray-100">
                    <button type="button" onclick="kirimAksiArtikelPost('detail_artikel.php', '<?php echo $row['id_artikel']; ?>')" 
                       class="flex-1 text-center bg-green-500 hover:bg-green-600 text-white py-2 rounded-xl text-xs font-semibold transition flex items-center justify-center gap-1 shadow-sm">
                        <i class="fas fa-eye text-[11px]"></i> Detail
                    </button>
                    
                    <?php if($is_my_article): ?>
                        <button type="button" onclick="kirimAksiArtikelPost('edit_artikel.php', '<?php echo $row['id_artikel']; ?>')" 
                                class="flex-1 text-center bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-xl text-xs font-semibold transition flex items-center justify-center gap-1 shadow-sm">
                            <i class="fas fa-edit text-[11px]"></i> Edit
                        </button>
                        
                        <a href="hapus_artikel.php?id=<?php echo $row['id_artikel']; ?>" 
                           class="flex-1 text-center bg-red-500 hover:bg-red-600 text-white py-2 rounded-xl text-xs font-semibold transition flex items-center justify-center gap-1 shadow-sm"
                           onclick="confirmDelete(event, this.href)">
                            <i class="fas fa-trash text-[11px]"></i> Hapus
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
        <div class="col-span-full bg-white rounded-2xl shadow-lg p-12 text-center mt-2">
            <i class="fas fa-newspaper text-6xl text-gray-300 mb-4 opacity-70"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-1">Data Tidak Ditemukan</h3>
            <p class="text-gray-400 text-sm">Tidak ada artikel yang cocok dengan kata kunci atau filter terpilih.</p>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if($total_pages > 1): ?>
    <div class="mt-8">
        <?php echo paginate($page, $total_pages, 'index.php', ['search' => $search, 'kategori' => $kategori_id]); ?>
    </div>
    <?php endif; ?>
</div>

<script>
function kirimAksiArtikelPost(urlTujuan, idArtikel) {
    const form = document.getElementById('formAksiArtikelPost');
    form.action = urlTujuan;
    document.getElementById('idArtikelAksiPost').value = idArtikel;
    form.submit();
}

function confirmDelete(event, url) {
    event.preventDefault();
    Swal.fire({
        title: 'Yakin hapus artikel?',
        text: "Artikel yang dihapus tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
    return false;
}
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>