<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_bidan.php';
$title = 'Data Vaksin';
include __DIR__ . '/../../templates/sidebar.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 9;
$offset = ($page - 1) * $limit;

$search_condition = "";
if($search) {
    $search_condition = "WHERE nama_vaksin LIKE '%$search%' OR deskripsi LIKE '%$search%'";
}

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM vaksin $search_condition"))['total'];
$total_pages = ceil($total / $limit);

$result = mysqli_query($conn, "SELECT v.* FROM vaksin v 
                               $search_condition 
                               ORDER BY v.usia_rekomendasi ASC 
                               LIMIT $offset, $limit");

function formatUsia($bulan) {
    if($bulan == 0) {
        return '0 bulan (baru lahir)';
    }
    
    $tahun = floor($bulan / 12);
    $sisa_bulan = $bulan % 12;
    
    if($tahun > 0 && $sisa_bulan > 0) {
        return $tahun . ' tahun ' . $sisa_bulan . ' bulan';
    } elseif($tahun > 0) {
        return $tahun . ' tahun';
    } else {
        return $sisa_bulan . ' bulan';
    }
}

?>

<div class="fade-in">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-green-800">Data Vaksin</h1>
    </div>
    
    <div class="bg-white rounded-2xl shadow-lg p-4 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Cari vaksin (nama atau deskripsi)..." 
                       class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200">
            </div>
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-xl hover:bg-green-700 transition flex items-center justify-center gap-2">
                <i class="fas fa-search"></i> Cari
            </button>
            <?php if($search): ?>
            <a href="index.php" class="bg-gray-500 text-white px-6 py-2 rounded-xl hover:bg-gray-600 transition flex items-center justify-center gap-2">
                <i class="fas fa-times"></i> Reset
            </a>
            <?php endif; ?>
        </form>
    </div>
    
    <?php if(mysqli_num_rows($result) > 0): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <div class="bg-white rounded-2xl shadow-lg p-4 hover:shadow-xl transition flex flex-col h-full">
            <div class="flex justify-between items-start">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-syringe text-green-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800"><?php echo $row['nama_vaksin']; ?></h3>
                        <p class="text-xs text-green-600">
                            <i class="fas fa-child mr-1"></i> 
                            Usia: <?php echo formatUsia($row['usia_rekomendasi']); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="mt-3 flex-1">
                <?php 
                $deskripsi_singkat = strip_tags($row['deskripsi']);
                if(strlen($deskripsi_singkat) > 120) {
                    $deskripsi_singkat = substr($deskripsi_singkat, 0, 120) . '...';
                }
                ?>
                <p class="text-gray-600 text-sm leading-relaxed"><?php echo nl2br(htmlspecialchars($deskripsi_singkat)); ?></p>
            </div>
            
            <div class="mt-4 pt-3 border-t border-gray-100">
                <button onclick='openDetailModal(<?php echo json_encode($row); ?>)' 
                        class="text-green-600 hover:text-green-700 text-sm font-medium flex items-center gap-1 transition">
                    <i class="fas fa-info-circle"></i> Lihat Detail Lengkap
                    <i class="fas fa-arrow-right text-xs"></i>
                </button>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    
    <div class="mt-6">
        <?php echo paginate($page, $total_pages, 'index.php', ['search' => $search]); ?>
    </div>
    
    <?php else: ?>
    <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
        <i class="fas fa-syringe text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak Ada Data Vaksin</h3>
        <p class="text-gray-500">Tidak ada data vaksin dengan kriteria pencarian "<?php echo htmlspecialchars($search); ?>"</p>
        <?php if($search): ?>
        <a href="index.php" class="inline-block mt-4 text-green-600 hover:text-green-700">
            <i class="fas fa-arrow-left mr-1"></i> Lihat semua vaksin
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4" onclick="closeDetailModal(event)">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="sticky top-0 bg-gradient-to-r from-green-600 to-emerald-500 p-4 rounded-t-2xl flex justify-between items-center">
            <h3 class="text-xl font-bold text-white" id="modalTitle"></h3>
            <button onclick="closeDetailModal()" class="text-white hover:text-gray-200 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="mb-4">
                <label class="block text-gray-500 text-sm mb-1">Nama Vaksin</label>
                <p class="font-semibold text-gray-800 text-lg" id="modalNama"></p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-500 text-sm mb-1">Usia Rekomendasi</label>
                <p class="text-gray-700" id="modalUsia"></p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-500 text-sm mb-1">Deskripsi Lengkap</label>
                <div class="text-gray-700 leading-relaxed whitespace-pre-line" id="modalDeskripsi"></div>
            </div>
            <div class="mb-4">
                <label class="block text-gray-500 text-sm mb-1">Tanggal Dibuat</label>
                <p class="text-gray-700" id="modalCreatedAt"></p>
            </div>
            <div class="flex justify-end mt-6">
                <button onclick="closeDetailModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openDetailModal(vaksin) {
    let usiaBulan = vaksin.usia_rekomendasi;
    let usiaText = '';
    
    if(usiaBulan == 0) {
        usiaText = '0 bulan (baru lahir)';
    } else {
        let tahun = Math.floor(usiaBulan / 12);
        let sisaBulan = usiaBulan % 12;
        
        if(tahun > 0 && sisaBulan > 0) {
            usiaText = tahun + ' tahun ' + sisaBulan + ' bulan';
        } else if(tahun > 0) {
            usiaText = tahun + ' tahun';
        } else {
            usiaText = sisaBulan + ' bulan';
        }
    }
    
    document.getElementById('modalTitle').innerText = vaksin.nama_vaksin;
    document.getElementById('modalNama').innerText = vaksin.nama_vaksin;
    document.getElementById('modalUsia').innerHTML = '<i class="fas fa-child mr-1 text-green-500"></i> ' + usiaText;
    document.getElementById('modalDeskripsi').innerHTML = vaksin.deskripsi || '<span class="text-gray-400">Tidak ada deskripsi</span>';
    
    if (vaksin.created_at) {
        let date = new Date(vaksin.created_at);
        let formattedDate = date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        document.getElementById('modalCreatedAt').innerHTML = formattedDate;
    } else {
        document.getElementById('modalCreatedAt').innerHTML = '-';
    }
    
    document.getElementById('detailModal').classList.remove('hidden');
    document.getElementById('detailModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeDetailModal(event) {
    if (event && event.target !== event.currentTarget && event.target.closest('.bg-white')) return;
    document.getElementById('detailModal').classList.add('hidden');
    document.getElementById('detailModal').classList.remove('flex');
    document.body.style.overflow = 'auto';
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDetailModal();
    }
});
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>