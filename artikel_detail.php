<?php
// artikel_detail.php - Halaman detail artikel (publik)
require_once __DIR__ . '/config/database.php';

$id_artikel = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "SELECT a.*, k.nama_kategori, u.nama_lengkap as penulis 
    FROM artikel a 
    LEFT JOIN kategori_artikel k ON a.id_kategori = k.id_kategori 
    LEFT JOIN users u ON a.penulis_nik = u.nik 
    WHERE a.id_artikel = $id_artikel";
$result = mysqli_query($conn, $query);
$artikel = mysqli_fetch_assoc($result);

if(!$artikel) {
    header("Location: artikel.php");
    exit();
}

// Ambil artikel terkait (same category)
$query_terkait = "SELECT a.*, k.nama_kategori 
    FROM artikel a 
    LEFT JOIN kategori_artikel k ON a.id_kategori = k.id_kategori 
    WHERE a.id_kategori = {$artikel['id_kategori']} AND a.id_artikel != $id_artikel 
    ORDER BY a.created_at DESC LIMIT 3";
$artikel_terkait = mysqli_query($conn, $query_terkait);

$title = htmlspecialchars($artikel['judul']) . ' - SIPANDA';
include __DIR__ . '/templates/header_public.php';
?>

<!-- ========== BREADCRUMB ========== -->
<div class="bg-white border-b border-gray-100 sticky top-16 z-40 shadow-sm">
    <div class="container mx-auto px-4 py-3">
        <div class="flex items-center gap-2 text-sm flex-wrap">
            <a href="index.php" class="text-gray-500 hover:text-green-600 transition flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Home
            </a>
            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <a href="artikel.php" class="text-gray-500 hover:text-green-600 transition">Artikel</a>
            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="text-gray-700 line-clamp-1 font-medium"><?php echo htmlspecialchars($artikel['judul']); ?></span>
        </div>
    </div>
</div>

<!-- ========== ARTIKEL DETAIL ========== -->
<div class="container mx-auto px-4 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- ========== MAIN CONTENT ========== -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                
                <!-- Header dengan Thumbnail -->
                <div class="relative h-72 md:h-96 overflow-hidden cursor-pointer bg-gradient-to-r from-green-600 to-green-500" 
                     onclick="openImageModal('<?php echo $artikel['thumbnail'] ? '../posyandu/uploads/artikel/' . $artikel['thumbnail'] : ''; ?>', '<?php echo htmlspecialchars($artikel['judul']); ?>')">
                    
                    <!-- Gambar Thumbnail -->
                    <?php if($artikel['thumbnail'] && file_exists("../posyandu/uploads/artikel/" . $artikel['thumbnail'])): ?>
                    <img src="../posyandu/uploads/artikel/<?php echo $artikel['thumbnail']; ?>" 
                         alt="<?php echo htmlspecialchars($artikel['judul']); ?>" 
                         class="w-full h-full object-cover hover:scale-105 transition duration-500">
                    <?php else: ?>
                    <div class="w-full h-full flex flex-col items-center justify-center">
                        <svg class="w-24 h-24 text-white/40" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2a10 10 0 0110 10 10 10 0 01-10 10 10 10 0 01-10-10 10 10 0 0110-10m0 2a8 8 0 00-8 8 8 8 0 008 8 8 8 0 008-8 8 8 0 00-8-8z"/>
                            <path d="M12 6v6l4 2"/>
                        </svg>
                        <p class="text-white/60 mt-2">Tidak ada thumbnail</p>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Overlay Gradient -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                    
                    <!-- Kategori Badge -->
                    <div class="absolute bottom-4 left-6 z-10">
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-white/90 backdrop-blur-sm text-green-700 text-sm font-semibold rounded-xl shadow-md">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4 6h16v2H4V6zm2-4h12v2H6V2zm16 8H2v12h20V10zm-2 10H4v-8h16v8z"></path>
                            </svg>
                            <?php echo htmlspecialchars($artikel['nama_kategori'] ?? 'Kesehatan'); ?>
                        </span>
                    </div>
                    
                    <!-- Zoom Hint -->
                    <div class="absolute bottom-4 right-4 z-10 bg-black/50 backdrop-blur-sm rounded-full p-2">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                        </svg>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="p-6 md:p-8">
                    <!-- Judul -->
                    <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-800 mb-4 leading-tight">
                        <?php echo htmlspecialchars($artikel['judul']); ?>
                    </h1>
                    
                    <!-- Meta Info -->
                    <div class="flex flex-wrap items-center gap-4 mb-6 pb-4 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full flex items-center justify-center text-white">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Penulis</p>
                                <p class="text-sm font-medium text-gray-700"><?php echo htmlspecialchars($artikel['penulis'] ?? 'Admin SIPANDA'); ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Tanggal Publikasi</p>
                                <p class="text-sm font-medium text-gray-700"><?php echo date('d F Y', strtotime($artikel['created_at'])); ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Estimasi Baca</p>
                                <p class="text-sm font-medium text-gray-700"><?php echo ceil(str_word_count(strip_tags($artikel['konten'])) / 200); ?> menit</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Konten Artikel -->
                    <div class="prose prose-green max-w-none">
                        <?php echo nl2br(htmlspecialchars_decode($artikel['konten'])); ?>
                    </div>
                    
                    <!-- Share Section -->
                    <div class="border-t border-gray-100 mt-8 pt-6">
                        <p class="text-gray-600 mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                            </svg>
                            Bagikan artikel ini
                        </p>
                        <div class="flex gap-3 flex-wrap">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>" target="_blank" class="group w-10 h-10 bg-[#1877F2] text-white rounded-xl flex items-center justify-center hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                                </svg>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($artikel['judul']); ?>" target="_blank" class="group w-10 h-10 bg-[#1DA1F2] text-white rounded-xl flex items-center justify-center hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 0021.467-11.444 7.904 7.904 0 002.161-2.321 7.984 7.984 0 01-2.495.682 4.382 4.382 0 001.909-2.41z"/>
                                </svg>
                            </a>
                            <a href="https://wa.me/?text=<?php echo urlencode($artikel['judul'] . ' - http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>" target="_blank" class="group w-10 h-10 bg-[#25D366] text-white rounded-xl flex items-center justify-center hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.588 2.014.89 3.149.891h.002c3.18 0 5.767-2.587 5.768-5.766.001-3.18-2.585-5.767-5.766-5.767zm0 10.536c-1.136 0-2.233-.349-3.151-.979l-.475-.288-1.532.401.414-1.489-.303-.468a4.785 4.785 0 01-.92-2.787c.001-2.614 2.128-4.742 4.743-4.742 1.265 0 2.456.493 3.352 1.389a4.712 4.712 0 011.389 3.352c-.001 2.615-2.128 4.743-4.743 4.743zm2.528-4.27c-.138-.074-.862-.426-.995-.473-.133-.047-.23-.074-.327.074-.097.148-.378.473-.463.572-.085.099-.17.111-.308.037-.138-.074-.585-.216-1.114-.689-.411-.367-.688-.82-.768-.958-.085-.138-.009-.212.064-.281.064-.062.149-.16.213-.24.064-.08.085-.137.128-.229.043-.092.021-.172-.01-.241-.031-.069-.327-.79-.449-1.081-.118-.284-.239-.245-.327-.255-.085-.01-.182-.009-.28-.009s-.256.037-.39.184c-.134.148-.51.497-.51 1.213s.522 1.407.595 1.504c.073.097 1.027 1.569 2.489 2.2.349.15.62.24.832.307.35.111.668.095.92.058.281-.042.862-.352.984-.693.122-.34.122-.632.085-.693-.037-.062-.137-.099-.275-.172z"/>
                                </svg>
                            </a>
                            <button onclick="copyToClipboard()" class="group w-10 h-10 bg-gray-600 text-white rounded-xl flex items-center justify-center hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ========== SIDEBAR ========== -->
            <div class="lg:col-span-1">
            <!-- Kartu Profil SIPANDA -->
            <div class="bg-gradient-to-r from-green-600 to-green-500 rounded-2xl p-6 text-white mb-6 relative overflow-hidden group">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition"></div>
                <div class="relative z-10">
                    <!-- Logo SIPANDA -->
                    <div class="flex justify-center mb-4">
                        <img src="img/sipanda.png" alt="SIPANDA" class="w-16 h-16 object-contain drop-shadow-lg">
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-center">SIPANDA</h3>
                    <p class="text-green-100 text-sm mb-4 text-center">Sistem Informasi Pemantauan Anak dan Bunda</p>
                    <a href="auth/register.php" class="inline-flex items-center justify-center gap-2 bg-white text-green-600 px-4 py-2 rounded-xl text-sm font-semibold hover:shadow-lg transition-all hover:gap-3 w-full">
                        Daftar Sekarang
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                </div>
            </div>
            
            <!-- Artikel Terkait -->
            <?php if(mysqli_num_rows($artikel_terkait) > 0): ?>
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 border border-gray-100">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4 6h16v2H4V6zm2-4h12v2H6V2zm16 8H2v12h20V10zm-2 10H4v-8h16v8z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800">Artikel Terkait</h3>
                </div>
                <div class="space-y-4">
                    <?php while($terkait = mysqli_fetch_assoc($artikel_terkait)): ?>
                    <a href="artikel_detail.php?id=<?php echo $terkait['id_artikel']; ?>" class="group flex gap-3 p-3 rounded-xl hover:bg-green-50 transition-all duration-300">
                        <div class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 bg-gradient-to-br from-green-100 to-pink-100">
                            <?php if($terkait['thumbnail'] && file_exists("../posyandu/uploads/artikel/" . $terkait['thumbnail'])): ?>
                            <img src="../posyandu/uploads/artikel/<?php echo $terkait['thumbnail']; ?>" alt="<?php echo htmlspecialchars($terkait['judul']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition">
                            <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2a10 10 0 0110 10 10 10 0 01-10 10 10 10 0 01-10-10 10 10 0 0110-10m0 2a8 8 0 00-8 8 8 8 0 008 8 8 8 0 008-8 8 8 0 00-8-8z"/>
                                </svg>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-400 mb-1"><?php echo date('d M Y', strtotime($terkait['created_at'])); ?></p>
                            <h4 class="font-semibold text-gray-800 group-hover:text-green-600 transition line-clamp-2 text-sm">
                                <?php echo htmlspecialchars($terkait['judul']); ?>
                            </h4>
                        </div>
                    </a>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ========== MODAL IMAGE FULLSCREEN ========== -->
<div id="imageModal" class="fixed inset-0 bg-black/90 hidden items-center justify-center z-50" onclick="closeImageModal()">
    <div class="relative max-w-5xl w-full max-h-[90vh] mx-4 flex items-center justify-center">
        <img id="modalImage" src="" alt="" class="max-w-full max-h-[85vh] object-contain cursor-pointer transition-transform duration-200" ondblclick="toggleZoom()">
        <button onclick="closeImageModal()" class="absolute -top-12 right-0 text-white hover:text-gray-300 transition">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    <p id="modalCaption" class="absolute bottom-6 left-0 right-0 text-center text-white/80 text-sm"></p>
</div>

<script>
// Copy to clipboard function
function copyToClipboard() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Link artikel telah disalin ke clipboard',
            confirmButtonColor: '#22c55e',
            timer: 2000,
            showConfirmButton: false
        });
    }).catch(() => {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Gagal menyalin link',
            confirmButtonColor: '#dc2626'
        });
    });
}

// Image Modal
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

// Escape key
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
    .prose {
        color: #374151;
        line-height: 1.8;
        font-size: 1.05rem;
    }
    .prose p {
        margin-bottom: 1.25rem;
    }
    .prose h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 1rem;
        color: #1f2937;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e5e7eb;
    }
    .prose h3 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        color: #374151;
    }
    .prose ul, .prose ol {
        margin-bottom: 1rem;
        padding-left: 1.5rem;
    }
    .prose li {
        margin-bottom: 0.5rem;
    }
    .prose blockquote {
        border-left: 4px solid #22c55e;
        padding-left: 1rem;
        font-style: italic;
        color: #6b7280;
        margin: 1.5rem 0;
    }
    
    #modalImage {
        cursor: zoom-in;
        transition: transform 0.2s ease;
    }
</style>

<?php include __DIR__ . '/templates/footer_public.php'; ?>