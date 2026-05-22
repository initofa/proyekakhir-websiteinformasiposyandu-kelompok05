<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_ibu.php';
$title = 'Detail Artikel';
include __DIR__ . '/../../templates/sidebar.php';

$id = $_GET['id'];

// Ambil data artikel
$query = "SELECT a.*, k.nama_kategori, u.nama_lengkap as penulis 
          FROM artikel a 
          LEFT JOIN kategori_artikel k ON a.id_kategori = k.id_kategori 
          LEFT JOIN users u ON a.penulis_nik = u.nik 
          WHERE a.id_artikel = $id";
$artikel = mysqli_fetch_assoc(mysqli_query($conn, $query));

if(!$artikel){
    $_SESSION['error'] = "Artikel tidak ditemukan!";
    header("Location: list_artikel.php");
    exit();
}

// Ambil artikel terkait (kategori sama)
$related = mysqli_query($conn, "SELECT id_artikel, judul, created_at, thumbnail 
                                FROM artikel 
                                WHERE id_kategori = '{$artikel['id_kategori']}' AND id_artikel != $id 
                                ORDER BY created_at DESC 
                                LIMIT 3");
?>

<div class="max-w-4xl mx-auto fade-in">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex items-center text-sm text-gray-500">
            <a href="list_artikel.php" class="hover:text-green-600 transition">
                <i class="fas fa-home mr-1"></i> Artikel
            </a>
            <i class="fas fa-chevron-right mx-2 text-xs"></i>
            <span class="text-gray-700 font-medium"><?php echo $artikel['judul']; ?></span>
        </nav>
    </div>
    
    <!-- Main Article Card -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <!-- Thumbnail -->
        <div class="relative h-80 bg-gradient-to-r from-green-400 to-emerald-400 overflow-hidden cursor-pointer" 
             onclick="openImageModal('<?php echo $artikel['thumbnail'] ? "../../uploads/artikel/" . $artikel['thumbnail'] : ''; ?>', '<?php echo $artikel['judul']; ?>')">
            <?php if($artikel['thumbnail'] && file_exists("../../uploads/artikel/" . $artikel['thumbnail'])): ?>
            <img src="../../uploads/artikel/<?php echo $artikel['thumbnail']; ?>" 
                 alt="<?php echo $artikel['judul']; ?>" 
                 class="w-full h-full object-cover">
            <?php else: ?>
            <div class="w-full h-full flex items-center justify-center">
                <i class="fas fa-newspaper text-white text-8xl opacity-50"></i>
            </div>
            <?php endif; ?>
            
            <!-- Kategori Badge -->
            <div class="absolute bottom-4 left-6">
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-white/90 backdrop-blur-sm text-green-700 text-sm font-semibold rounded-lg shadow-md">
                    <i class="fas fa-tag"></i> <?php echo $artikel['nama_kategori']; ?>
                </span>
            </div>
        </div>
        
        <!-- Content -->
        <div class="p-8">
            <!-- Judul -->
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4 leading-tight">
                <?php echo $artikel['judul']; ?>
            </h1>
            
            <!-- Meta Info -->
            <div class="flex flex-wrap items-center gap-4 mb-6 pb-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full flex items-center justify-center text-white">
                        <i class="fas fa-user text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Penulis</p>
                        <p class="text-sm font-medium text-gray-700"><?php echo $artikel['penulis']; ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="far fa-calendar-alt text-gray-500"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Tanggal Publikasi</p>
                        <p class="text-sm font-medium text-gray-700"><?php echo date('d F Y', strtotime($artikel['created_at'])); ?></p>
                    </div>
                </div>
                <?php if($artikel['updated_at'] != $artikel['created_at']): ?>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-edit text-gray-500"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Terakhir Diupdate</p>
                        <p class="text-sm font-medium text-gray-700"><?php echo date('d F Y', strtotime($artikel['updated_at'])); ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Konten Artikel -->
            <div class="prose max-w-none">
                <div class="text-gray-700 leading-relaxed whitespace-pre-line text-base">
                    <?php echo nl2br(htmlspecialchars($artikel['konten'])); ?>
                </div>
            </div>
            
            <!-- Share Section -->
            <div class="mt-8 pt-6 border-t border-gray-100">
                <p class="text-gray-600 mb-3 flex items-center gap-2">
                    <i class="fas fa-share-alt text-green-500"></i> Bagikan artikel ini:
                </p>
                <div class="flex gap-3">
                    <a href="https://wa.me/?text=<?php echo urlencode($artikel['judul'] . ' - ' . BASE_URL . 'ibu/artikel/detail_artikel.php?id=' . $id); ?>" 
                       target="_blank" 
                       class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white hover:bg-green-600 transition transform hover:scale-110">
                        <i class="fab fa-whatsapp text-lg"></i>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(BASE_URL . 'ibu/artikel/detail_artikel.php?id=' . $id); ?>" 
                       target="_blank" 
                       class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white hover:bg-blue-700 transition transform hover:scale-110">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($artikel['judul']); ?>" 
                       target="_blank" 
                       class="w-10 h-10 bg-sky-500 rounded-full flex items-center justify-center text-white hover:bg-sky-600 transition transform hover:scale-110">
                        <i class="fab fa-twitter"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Articles -->
    <?php if(mysqli_num_rows($related) > 0): ?>
    <div class="mt-10">
        <div class="flex items-center gap-2 mb-5">
            <div class="w-1 h-6 bg-green-500 rounded-full"></div>
            <h3 class="text-xl font-bold text-gray-800">Artikel Terkait</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <?php while($rel = mysqli_fetch_assoc($related)): ?>
            <a href="detail_artikel.php?id=<?php echo $rel['id_artikel']; ?>" 
               class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                <div class="h-36 bg-gradient-to-r from-green-400 to-emerald-400 relative overflow-hidden">
                    <?php if($rel['thumbnail'] && file_exists("../../uploads/artikel/" . $rel['thumbnail'])): ?>
                    <img src="../../uploads/artikel/<?php echo $rel['thumbnail']; ?>" 
                         alt="<?php echo $rel['judul']; ?>" 
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center">
                        <i class="fas fa-newspaper text-white text-4xl opacity-50"></i>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="p-4">
                    <h4 class="font-semibold text-gray-800 mb-2 line-clamp-2 group-hover:text-green-600 transition">
                        <?php echo $rel['judul']; ?>
                    </h4>
                    <p class="text-gray-400 text-xs flex items-center gap-1">
                        <i class="far fa-calendar-alt"></i>
                        <?php echo date('d M Y', strtotime($rel['created_at'])); ?>
                    </p>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Tombol Kembali -->
    <div class="mt-8 text-center">
        <a href="list_artikel.php" class="inline-flex items-center gap-2 text-green-600 hover:text-green-700 transition">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Artikel
        </a>
    </div>
</div>

<!-- Modal Fullscreen Image Sederhana -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-30 hidden items-center justify-center z-50" onclick="closeImageModal()">
    <div class="relative max-w-5xl w-full max-h-[90vh] mx-4 flex items-center justify-center">
        <img id="modalImage" src="" alt="" 
             class="max-w-full max-h-[85vh] object-contain cursor-pointer transition-transform duration-200"
             ondblclick="toggleZoom()">
    </div>
    <p id="modalCaption" class="absolute bottom-4 left-0 right-0 text-center text-white text-sm"></p>
</div>

<script>
let currentZoom = 1;
let modalImg = null;

function openImageModal(imgSrc, imgCaption) {
    modalImg = document.getElementById('modalImage');
    if(imgSrc && imgSrc !== '') {
        modalImg.src = imgSrc;
    } else {
        modalImg.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23ffffff"%3E%3Cpath d="M4 4h16v16H4zM4 4l16 16m0-16L4 20" stroke="%23ffffff" stroke-width="2"/%3E%3C/svg%3E';
    }
    document.getElementById('modalCaption').innerText = imgCaption || 'Gambar Artikel';
    document.getElementById('imageModal').classList.remove('hidden');
    document.getElementById('imageModal').classList.add('flex');
    resetZoom();
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.getElementById('imageModal').classList.remove('flex');
    resetZoom();
}

function toggleZoom() {
    if(currentZoom === 1) {
        currentZoom = 2;
        modalImg.style.transform = `scale(${currentZoom})`;
    } else {
        resetZoom();
    }
}

function resetZoom() {
    if(modalImg) {
        currentZoom = 1;
        modalImg.style.transform = `scale(1)`;
    }
}

// Escape key untuk menutup modal
document.addEventListener('keydown', function(e) {
    if(e.key === 'Escape') {
        const modal = document.getElementById('imageModal');
        if(modal && !modal.classList.contains('hidden')) {
            closeImageModal();
        }
    }
});
</script>

<style>
#modalImage {
    cursor: zoom-in;
    transition: transform 0.2s ease;
}

#imageModal {
    backdrop-filter: blur(5px);
}
</style>

<?php include __DIR__ . '/../../templates/footer.php'; ?>