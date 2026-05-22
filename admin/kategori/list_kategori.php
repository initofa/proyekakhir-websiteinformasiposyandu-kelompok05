<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';
$title = 'Kategori Artikel';
include __DIR__ . '/../../templates/sidebar.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM kategori_artikel"))['total'];
$total_pages = ceil($total / $limit);
$result = mysqli_query($conn, "SELECT * FROM kategori_artikel ORDER BY created_at DESC LIMIT $offset, $limit");
?>

<div class="fade-in">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-green-800">Kategori Artikel</h1>
        <a href="tambah_kategori.php" class="bg-gradient-to-r from-green-600 to-emerald-500 text-white px-4 py-2 rounded-xl hover:shadow-lg transition">
            <i class="fas fa-plus mr-2"></i>Tambah Kategori
        </a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <div class="bg-white rounded-2xl shadow-lg p-4 hover:shadow-xl transition">
            <div class="flex justify-between items-start">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-tag text-green-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800"><?php echo $row['nama_kategori']; ?></h3>
                        <p class="text-xs text-gray-400">Dibuat: <?php echo date('d/m/Y', strtotime($row['created_at'])); ?></p>
                    </div>
                </div>
                <div>
                    <a href="edit_kategori.php?id=<?php echo $row['id_kategori']; ?>" class="text-blue-500 mr-2 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                    <a href="hapus_kategori.php?id=<?php echo $row['id_kategori']; ?>" class="text-red-500 hover:text-red-700" onclick="confirmDelete(event, this.href)"><i class="fas fa-trash"></i></a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php if($total_pages > 1) echo paginate($page, $total_pages, "list_kategori.php"); ?>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>