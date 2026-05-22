<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';
$title = 'Data Ibu Hamil';
include __DIR__ . '/../../templates/sidebar.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM ibu_hamil"))['total'];
$total_pages = ceil($total / $limit);
$result = mysqli_query($conn, "SELECT ih.*, u.nama_lengkap, u.no_wa FROM ibu_hamil ih JOIN users u ON ih.nik_ibu=u.nik ORDER BY ih.hpl ASC LIMIT $offset, $limit");
?>

<div class="fade-in">
    <h1 class="text-2xl font-bold text-green-800 mb-4">Data Ibu Hamil</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php while($row = mysqli_fetch_assoc($result)): 
            $hpl = new DateTime($row['hpl']);
            $today = new DateTime();
            $sisa = $hpl > $today ? $today->diff($hpl)->days : 0;
        ?>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition">
            <div class="bg-gradient-to-r from-green-500 to-emerald-500 p-4 text-white">
                <div class="flex justify-between items-center">
                    <div><i class="fas fa-female text-2xl"></i><h3 class="text-lg font-bold"><?php echo $row['nama_lengkap']; ?></h3></div>
                    <span class="bg-white/20 px-2 py-1 rounded-full text-xs"><?php echo $row['usia_kehamilan']; ?> minggu</span>
                </div>
            </div>
            <div class="p-4">
                <p><i class="fas fa-calendar w-4 text-gray-500"></i> HPL: <?php echo date('d/m/Y', strtotime($row['hpl'])); ?> (<?php echo $sisa; ?> hari)</p>
                <p><i class="fas fa-weight-scale w-4 text-gray-500"></i> BB: <?php echo $row['berat_badan_ibu']; ?> kg</p>
                <p><i class="fas fa-heartbeat w-4 text-gray-500"></i> TD: <?php echo $row['tekanan_darah']; ?></p>
                <a href="detail_ibu_hamil.php?id=<?php echo $row['id_kehamilan']; ?>" class="mt-3 inline-block text-green-600 text-sm hover:text-green-700">Detail <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php if($total_pages > 1) echo paginate($page, $total_pages, "list_ibu_hamil.php"); ?>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>