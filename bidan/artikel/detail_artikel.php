<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_bidan.php';
$title = 'Detail Artikel';
include __DIR__ . '/../../templates/sidebar.php';

$id = isset($_POST['id_artikel']) ? (int)$_POST['id_artikel'] : 0;

if($id === 0) {
    $_SESSION['error'] = "Akses tidak sah!";
    header("Location: list_artikel.php");
    exit();
}

$artikel = mysqli_fetch_assoc(mysqli_query($conn, "SELECT a.*, k.nama_kategori, u.nama_lengkap as penulis 
                                                    FROM artikel a 
                                                    LEFT JOIN kategori_artikel k ON a.id_kategori = k.id_kategori 
                                                    LEFT JOIN users u ON a.penulis_nik = u.nik 
                                                    WHERE a.id_artikel = $id"));

if(!$artikel) {
    $_SESSION['error'] = "Artikel tidak ditemukan!";
    header("Location: list_artikel.php");
    exit();
}
?>

<div class="max-w-4xl mx-auto fade-in">
    <div class="mb-4">
        <a href="list_artikel.php" class="inline-flex items-center gap-2 text-green-600 hover:text-green-700 transition">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Artikel
        </a>
    </div>
    
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="h-80 bg-gradient-to-r from-green-400 to-emerald-400 flex items-center justify-center overflow-hidden">
            <?php if($artikel['thumbnail'] && file_exists("../../uploads/artikel/" . $artikel['thumbnail'])): ?>
            <img src="../../uploads/artikel/<?php echo $artikel['thumbnail']; ?>" 
                 alt="<?php echo htmlspecialchars($artikel['judul']); ?>" 
                 class="w-full h-full object-cover">
            <?php else: ?>
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-r from-green-400 to-emerald-400">
                <i class="fas fa-newspaper text-white text-8xl opacity-50"></i>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="p-8">
            <div class="flex flex-wrap justify-between items-center mb-4 gap-2">
                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                    <i class="fas fa-tag mr-1"></i> <?php echo htmlspecialchars($artikel['nama_kategori'] ?? 'Tanpa Kategori'); ?>
                </span>
                <div class="flex flex-wrap gap-3 text-sm text-gray-500">
                    <span><i class="fas fa-user mr-1"></i> <?php echo htmlspecialchars($artikel['penulis'] ?? 'Anonim'); ?></span>
                    <span><i class="far fa-calendar-alt mr-1"></i> <?php echo formatTanggalIndonesia($artikel['created_at']); ?></span>
                </div>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-800 mb-6"><?php echo htmlspecialchars($artikel['judul']); ?></h1>
            
            <div class="prose max-w-none text-gray-700 leading-relaxed whitespace-pre-line mb-8">
                <?php echo nl2br(htmlspecialchars($artikel['konten'])); ?>
            </div>
            
            <div class="mt-8 pt-4 border-t border-gray-200">
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-info-circle text-green-500 mr-2"></i>
                        <span>Artikel ini ditulis oleh <strong class="text-gray-800"><?php echo htmlspecialchars($artikel['penulis'] ?? 'Anonim'); ?></strong> pada <?php echo formatTanggalIndonesia($artikel['created_at']); ?>.</span>
                    </div>
                </div>
            </div>
            
            <div class="flex mt-6">
                <a href="list_artikel.php" 
                   class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 py-2.5 rounded-xl transition text-center flex items-center justify-center gap-2 font-semibold">
                   Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>