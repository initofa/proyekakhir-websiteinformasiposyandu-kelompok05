<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_bidan.php';
$title = 'Detail Artikel';
include __DIR__ . '/../../templates/sidebar.php';

$id = isset($_POST['id_artikel']) ? (int)$_POST['id_artikel'] : 0;

if($id === 0) {
    $_SESSION['error'] = "Akses tidak sah!";
    header("Location: index.php");
    exit();
}

$artikel = mysqli_fetch_assoc(mysqli_query($conn, "SELECT a.*, k.nama_kategori, u.nama_lengkap as penulis 
                                                    FROM artikel a 
                                                    LEFT JOIN kategori_artikel k ON a.id_kategori = k.id_kategori 
                                                    LEFT JOIN users u ON a.penulis_nik = u.nik 
                                                    WHERE a.id_artikel = $id"));

if(!$artikel) {
    $_SESSION['error'] = "Artikel tidak ditemukan!";
    header("Location: index.php");
    exit();
}
?>

<div class="max-w-4xl mx-auto fade-in">
    <div class="mb-4">
        <a href="index.php" class="inline-flex items-center gap-2 text-green-600 hover:text-green-700 transition">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Artikel
        </a>
    </div>
    
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="relative h-80 bg-gradient-to-r from-green-400 to-emerald-400 flex items-center justify-center overflow-hidden cursor-pointer group"
             onclick="openImageModal('<?php echo $artikel['thumbnail'] ? '../../uploads/artikel/' . $artikel['thumbnail'] : ''; ?>', '<?php echo htmlspecialchars($artikel['judul']); ?>')">
            
            <?php if($artikel['thumbnail'] && file_exists("../../uploads/artikel/" . $artikel['thumbnail'])): ?>
            <img src="../../uploads/artikel/<?php echo $artikel['thumbnail']; ?>" 
                 alt="<?php echo htmlspecialchars($artikel['judul']); ?>" 
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
            <?php else: ?>
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-r from-green-400 to-emerald-400">
                <i class="fas fa-newspaper text-white text-8xl opacity-50"></i>
            </div>
            <?php endif; ?>

            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition duration-300"></div>

            <div class="absolute bottom-4 right-4 bg-black/50 backdrop-blur-sm rounded-full p-2 opacity-0 group-hover:opacity-100 transition duration-300 z-10">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                </svg>
            </div>
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
                <a href="index.php" 
                   class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 py-2.5 rounded-xl transition text-center flex items-center justify-center gap-2 font-semibold">
                   Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div id="imageModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-50" onclick="closeImageModal()">
    <div class="relative max-w-7xl w-full max-h-[95vh] mx-4 md:mx-12 flex items-center justify-center">
        <img id="modalImage" src="" alt="" class="max-w-full max-h-[90vh] object-contain cursor-zoom-in transition-transform duration-200" ondblclick="toggleZoom()">
        <button onclick="closeImageModal()" class="absolute -top-12 right-2 text-white hover:text-gray-300 transition-all scale-125">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    <p id="modalCaption" class="absolute bottom-6 left-0 right-0 text-center text-white/90 text-base font-bold px-4 tracking-wide bg-black/30 py-2 backdrop-blur-sm"></p>
</div>

<script>
let currentZoom = 1;
let modalImg = null;

function openImageModal(imgSrc, imgCaption) {
    modalImg = document.getElementById('modalImage');
    if(imgSrc && imgSrc !== '' && !imgSrc.includes('undefined')) {
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
        modalImg.style.transform = 'scale(1)';
    }
}

// Menutup modal dengan tombol Escape keyboard
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
</style>

<?php include __DIR__ . '/../../templates/footer.php'; ?>