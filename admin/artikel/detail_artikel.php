<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';
$title = 'Detail Artikel';
include __DIR__ . '/../../templates/sidebar.php';

$id = $_GET['id'];
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
    <!-- Tombol Kembali -->
    <div class="mb-4">
        <a href="list_artikel.php" class="inline-flex items-center gap-2 text-green-600 hover:text-green-700 transition">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Artikel
        </a>
    </div>
    
    <!-- Card Detail Artikel -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <!-- Thumbnail -->
        <div class="h-80 bg-gradient-to-r from-green-400 to-emerald-400 flex items-center justify-center overflow-hidden">
            <?php if($artikel['thumbnail'] && file_exists("../../uploads/artikel/" . $artikel['thumbnail'])): ?>
            <img src="../../uploads/artikel/<?php echo $artikel['thumbnail']; ?>" 
                 alt="<?php echo $artikel['judul']; ?>" 
                 class="w-full h-full object-cover">
            <?php else: ?>
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-r from-green-400 to-emerald-400">
                <i class="fas fa-newspaper text-white text-8xl opacity-50"></i>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Content -->
        <div class="p-8">
            <!-- Kategori & Meta -->
            <div class="flex flex-wrap justify-between items-center mb-4">
                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                    <i class="fas fa-tag mr-1"></i> <?php echo $artikel['nama_kategori']; ?>
                </span>
                <div class="flex gap-3 text-sm text-gray-500">
                    <span><i class="fas fa-user mr-1"></i> <?php echo $artikel['penulis']; ?></span>
                    <span><i class="far fa-calendar-alt mr-1"></i> <?php echo date('d F Y H:i', strtotime($artikel['created_at'])); ?></span>
                    <?php if($artikel['updated_at'] != $artikel['created_at']): ?>
                    <span class="text-xs text-gray-400"><i class="fas fa-edit mr-1"></i> Diupdate: <?php echo date('d/m/Y', strtotime($artikel['updated_at'])); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Judul -->
            <h1 class="text-3xl font-bold text-gray-800 mb-6"><?php echo $artikel['judul']; ?></h1>
            
            <!-- Konten -->
            <div class="prose max-w-none text-gray-700 leading-relaxed whitespace-pre-line">
                <?php echo nl2br(htmlspecialchars($artikel['konten'])); ?>
            </div>
            
            <!-- Informasi Tambahan -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm beetween">
                        <div>
                            <span class="text-gray-500">Tanggal Dibuat:</span>
                            <span class="ml-2 font-semibold text-gray-700"><?php echo date('d/m/Y H:i:s', strtotime($artikel['created_at'])); ?></span>
                        </div>
                        <div>
                            <span class="text-gray-500">Terakhir Diupdate:</span>
                            <span class="ml-2 font-semibold text-gray-700"><?php echo date('d/m/Y H:i:s', strtotime($artikel['updated_at'])); ?></span>
                        </div>
                        <?php if($artikel['updated_by']): ?>
                        <div>
                            <span class="text-gray-500">Diupdate Oleh:</span>
                            <span class="ml-2 font-semibold text-gray-700"><?php echo getUserName($artikel['updated_by']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex gap-3 mt-6">
                <a href="edit_artikel.php?id=<?php echo $artikel['id_artikel']; ?>" 
                   class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-xl transition text-center flex items-center justify-center gap-2">
                    <i class="fas fa-edit"></i> Edit Artikel
                </a>
                <a href="hapus_artikel.php?id=<?php echo $artikel['id_artikel']; ?>" 
                   class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2 rounded-xl transition text-center flex items-center justify-center gap-2"
                   onclick="confirmDelete(event, this.href)">
                    <i class="fas fa-trash"></i> Hapus Artikel
                </a>
                <a href="list_artikel.php" 
                   class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 rounded-xl transition text-center flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>