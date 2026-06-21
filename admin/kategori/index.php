<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';
$title = 'Kategori Artikel';
include __DIR__ . '/../../templates/sidebar.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

$where = "WHERE 1=1";
if ($search) {
    $where .= " AND nama_kategori LIKE '%$search%'";
}

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM kategori_artikel $where"))['total'];
$total_pages = ceil($total / $limit);

$result = mysqli_query($conn, "SELECT * FROM kategori_artikel $where ORDER BY created_at DESC LIMIT $offset, $limit");
?>

<form id="formEditKategoriPost" action="edit_kategori.php" method="POST" style="display:none;">
    <input type="hidden" name="id_kategori" id="idKategoriEditPost">
</form>

<div class="fade-in">
    <div class="flex flex-wrap justify-between items-center mb-4 gap-3">
        <h1 class="text-2xl font-bold text-green-800">Kategori Artikel</h1>
        <a href="tambah_kategori.php" class="bg-gradient-to-r from-green-600 to-emerald-500 text-white px-4 py-2 rounded-xl hover:shadow-lg transition text-sm font-semibold">
            <i class="fas fa-plus mr-2"></i>Kategori
        </a>
    </div>
    

    <div class="bg-white rounded-2xl shadow-lg p-4 mb-6">
        <form method="GET" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari kategori artikel..." 
                       class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 text-sm">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 md:flex-none bg-green-600 text-white px-6 py-2 rounded-xl hover:bg-green-700 transition text-sm font-semibold shadow-sm">
                    <i class="fas fa-search mr-2"></i> Cari
                </button>
                <?php if($search): ?>
                <a href="index.php" class="flex-1 md:flex-none bg-gray-500 text-white px-6 py-2 rounded-xl hover:bg-gray-600 transition text-center flex items-center justify-center text-sm font-semibold shadow-sm">
                    <i class="fas fa-times mr-2"></i> Reset
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php if(mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <div class="bg-white rounded-2xl shadow-lg p-4 hover:shadow-xl transition flex flex-col justify-between border border-gray-100">
            <div class="flex justify-between items-start">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-tag text-green-600"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-bold text-gray-800 break-all truncate"><?php echo htmlspecialchars($row['nama_kategori']); ?></h3>
                        <p class="text-xs text-gray-400 mt-0.5">Dibuat: <?php echo formatTanggalIndonesia($row['created_at']); ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <button type="button" onclick="kirimEditKategoriPost('<?php echo $row['id_kategori']; ?>')" 
                            class="text-blue-500 hover:text-blue-700 p-1 transition" title="Edit">
                        <i class="fas fa-edit text-lg"></i>
                    </button>
                    
                    <a href="hapus_kategori.php?id=<?php echo $row['id_kategori']; ?>" 
                       class="text-red-500 hover:text-red-700 p-1 transition" title="Hapus"
                       onclick="confirmDelete(event, this.href)">
                        <i class="fas fa-trash text-lg"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
        <div class="col-span-full bg-white rounded-2xl shadow-lg p-12 text-center">
            <i class="fas fa-folder-open text-6xl text-gray-300 mb-4 opacity-70"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-1">Data Tidak Ditemukan</h3>
            <p class="text-gray-400 text-sm">Tidak ada kategori artikel yang cocok dengan kata kunci tersebut.</p>
        </div>
        <?php endif; ?>
    </div>

    <?php if($total_pages > 1): ?>
    <div class="mt-8">
        <?php echo paginate($page, $total_pages, 'index.php', ['search' => $search]); ?>
    </div>
    <?php endif; ?>
</div>

<script>
function kirimEditKategoriPost(idKategori) {
    document.getElementById('idKategoriEditPost').value = idKategori;
    document.getElementById('formEditKategoriPost').submit();
}

function confirmDelete(event, url) {
    event.preventDefault();
    Swal.fire({
        title: 'Yakin hapus kategori?',
        text: "Menghapus kategori dapat memengaruhi artikel yang terkait!",
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