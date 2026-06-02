<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_bidan.php';

$title = 'Data Anak';
include __DIR__ . '/../../templates/sidebar.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$jk_filter = isset($_GET['jk']) ? $_GET['jk'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;

$where = "WHERE 1=1";
if (!empty($search)) {
    $where .= " AND (a.nama_anak LIKE '%$search%' OR u.nama_lengkap LIKE '%$search%')";
}
if (!empty($jk_filter)) {
    $where .= " AND a.jenis_kelamin = '$jk_filter'";
}

$total_query = "SELECT COUNT(*) as total FROM anak a LEFT JOIN users u ON a.nik_ibu = u.nik $where";
$total = mysqli_fetch_assoc(mysqli_query($conn, $total_query))['total'];
$total_pages = ceil($total / $limit);

$query_anak = "SELECT a.*, u.nama_lengkap as nama_ibu 
               FROM anak a 
               LEFT JOIN users u ON a.nik_ibu = u.nik 
               $where 
               ORDER BY a.tanggal_lahir DESC 
               LIMIT $offset, $limit";
$result = mysqli_query($conn, $query_anak);
?>

<form id="formDetailAnakPost" action="detail_perkembangan.php" method="POST" style="display:none;">
    <input type="hidden" name="id_anak" id="idAnakDetailPost">
</form>

<div class="fade-in">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-green-800">Data Anak</h1>
    </div>
    
    <div class="bg-white rounded-2xl shadow-lg p-4 mb-6">
        <form method="GET" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari nama anak atau nama ibu..." 
                       class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200">
            </div>
            
            <select name="jk" class="px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 bg-white">
                <option value="">Semua Jenis Kelamin</option>
                <option value="L" <?php echo $jk_filter == 'L' ? 'selected' : ''; ?>>Laki-laki (L)</option>
                <option value="P" <?php echo $jk_filter == 'P' ? 'selected' : ''; ?>>Perempuan (P)</option>
            </select>
            
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-xl hover:bg-green-700 transition flex items-center justify-center gap-2">
                <i class="fas fa-filter"></i> Filter
            </button>
            
            <?php if($search || $jk_filter): ?>
            <a href="index.php" class="bg-gray-500 text-white px-6 py-2 rounded-xl hover:bg-gray-600 transition text-center flex items-center justify-center">
                <i class="fas fa-times mr-2"></i> Reset
            </a>
            <?php endif; ?>
        </form>
    </div>
    
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-green-600 to-emerald-500 text-white">
                        <th class="p-4 text-left text-sm font-semibold">No</th>
                        <th class="p-4 text-left text-sm font-semibold">Nama Anak</th>
                        <th class="p-4 text-left text-sm font-semibold">Jenis Kelamin</th>
                        <th class="p-4 text-left text-sm font-semibold">Tanggal Lahir</th>
                        <th class="p-4 text-left text-sm font-semibold">Nama Ibu</th>
                        <th class="p-4 text-center text-sm font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php $no = $offset + 1; while($row = mysqli_fetch_assoc($result)): ?>
                    <tr class="hover:bg-gray-50 transition duration-200">
                        <td class="p-4 text-sm text-gray-600"><?php echo $no++; ?></td>
                        <td class="p-4 font-semibold text-gray-800"><?php echo htmlspecialchars($row['nama_anak']); ?></td>
                        <td class="p-4 text-sm">
                            <?php if($row['jenis_kelamin'] == 'L'): ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                <i class="fas fa-mars mr-1"></i> Laki-laki
                            </span>
                            <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-pink-100 text-pink-700">
                                <i class="fas fa-venus mr-1"></i> Perempuan
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 text-sm text-gray-600"><?php echo formatTanggalIndonesia($row['tanggal_lahir']); ?></td>
                        <td class="p-4 text-sm font-medium text-gray-700"><?php echo htmlspecialchars($row['nama_ibu']); ?></td>
                        <td class="p-4 text-center">
                            <button type="button" onclick="kirimDetailAnakPost('<?php echo $row['id_anak']; ?>')" 
                                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-1.5 rounded-xl text-xs font-medium transition inline-flex items-center gap-1 shadow-sm">
                                <i class="fas fa-chart-line"></i> Perkembangan
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" class="p-12 text-center text-gray-500">
                            <i class="fas fa-child text-5xl mb-3 text-gray-300"></i>
                            <p>Data anak tidak ditemukan atau belum diinput</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php if($total_pages > 1): ?>
    <div class="mt-6">
        <?php echo paginate($page, $total_pages, "index.php?search=" . urlencode($search) . "&jk=" . $jk_filter); ?>
    </div>
    <?php endif; ?>
</div>

<script>
function kirimDetailAnakPost(idAnak) {
    document.getElementById('idAnakDetailPost').value = idAnak;
    document.getElementById('formDetailAnakPost').submit();
}
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>