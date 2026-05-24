<?php
// index.php - Landing Page SIPANDA
require_once __DIR__ . '/config/database.php';

// Ambil 4 artikel terbaru
$query_artikel = "SELECT a.*, k.nama_kategori, u.nama_lengkap as penulis 
    FROM artikel a 
    LEFT JOIN kategori_artikel k ON a.id_kategori = k.id_kategori 
    LEFT JOIN users u ON a.penulis_nik = u.nik 
    WHERE a.id_kategori IS NOT NULL
    ORDER BY a.created_at DESC LIMIT 4";
$artikel_terbaru = mysqli_query($conn, $query_artikel);

$title = 'SIPANDA - Solusi Kesehatan Ibu dan Anak';
include __DIR__ . '/templates/header_public.php';
?>

<!-- ========== HERO SECTION MODERN & KECIL ========== -->
<section class="relative py-16 lg:py-20 overflow-hidden">
    <!-- Background Soft Gradient -->
    <div class="absolute inset-0 bg-gradient-to-br from-green-50 via-white to-pink-50"></div>
    
    <!-- Dekorasi Simple -->
    <div class="absolute top-10 right-10 w-40 h-40 bg-green-200 rounded-full opacity-20 blur-2xl"></div>
    <div class="absolute bottom-10 left-10 w-40 h-40 bg-pink-200 rounded-full opacity-20 blur-2xl"></div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            
            <!-- Left Content - Lebih Ringkas -->
            <div class="text-center lg:text-left fade-in">
                <div class="inline-flex items-center gap-2 bg-white shadow-sm px-4 py-2 rounded-full text-sm mb-5 mx-auto lg:mx-auto xl:mx-0">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <span class="text-gray-600">Aplikasi Posyandu Terpercaya</span>
                </div>
                
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-800 leading-tight mb-2">
                    <span class="bg-gradient-to-r from-green-600 to-green-500 bg-clip-text text-transparent">SIPANDA</span>
                </h1>
                
                <!-- Kepanjangan SIPANDA -->
                <div class="text-sm text-gray-500 mb-3 font-medium">
                    Sistem Informasi Posyandu <span class="text-green-600">Anak</span> & <span class="text-pink-500">Bunda</span>
                </div>
                
                <p class="text-gray-600 text-base mb-6 max-w-md mx-auto lg:mx-0">
                    SIPANDA membantu ibu memantau imunisasi anak, tumbuh kembang, dan kesehatan kehamilan dengan mudah.
                </p>
                
                <div class="flex gap-3 justify-center lg:justify-start flex-wrap">
                    <a href="auth/register.php" class="bg-gradient-to-r from-green-600 to-green-500 text-white px-6 py-2.5 rounded-full font-semibold text-sm hover:shadow-lg transition-all hover:scale-105 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        Daftar Gratis
                    </a>
                    <a href="#layanan" class="border border-gray-300 text-gray-700 px-6 py-2.5 rounded-full font-semibold text-sm hover:bg-gray-50 transition-all">
                        Lihat Layanan
                    </a>
                </div>
                
                <!-- Trust Badge -->
                <div class="flex items-center gap-4 justify-center lg:justify-start mt-6">
                    <div class="flex -space-x-2">
                        <div class="w-7 h-7 bg-green-100 rounded-full flex items-center justify-center text-xs font-bold text-green-600 border-2 border-white">S</div>
                        <div class="w-7 h-7 bg-pink-100 rounded-full flex items-center justify-center text-xs font-bold text-pink-600 border-2 border-white">A</div>
                        <div class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center text-xs font-bold text-blue-600 border-2 border-white">R</div>
                    </div>
                    <p class="text-xs text-gray-500">💚 1.000+ Ibu percaya SIPANDA</p>
                </div>
            </div>
            
            <!-- Right Content - Card Ringkas & Manis -->
            <div class="relative">
                <div class="bg-white rounded-2xl shadow-xl p-5 border border-gray-100">
                    <!-- Header dengan kepanjangan -->
                    <div class="flex items-center gap-3 mb-4">
                        <div>
                            <p class="font-bold text-gray-800 text-sm">Ada Apa aja sih di SIPANDA?</p>
                            <p class="text-gray-500 text-xs">beberapa fitur unggulan</p>
                        </div>
                    </div>
                    
                    <!-- Fitur Grid 2x2 -->
                    <div class="grid grid-cols-2 gap-3">
                        <!-- Fitur 1 -->
                        <div class="bg-green-50 rounded-xl p-3 text-center group hover:bg-green-100 transition cursor-pointer">
                            <div class="w-10 h-10 bg-green-200 rounded-full flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="font-semibold text-gray-800 text-xs">Imunisasi</p>
                            <p class="text-gray-500 text-xs">Jadwal lengkap</p>
                        </div>
                        
                        <!-- Fitur 2 -->
                        <div class="bg-pink-50 rounded-xl p-3 text-center group hover:bg-pink-100 transition cursor-pointer">
                            <div class="w-10 h-10 bg-pink-200 rounded-full flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
                                <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                            <p class="font-semibold text-gray-800 text-xs">Kehamilan</p>
                            <p class="text-gray-500 text-xs">Pantau HPL</p>
                        </div>
                        
                        <!-- Fitur 3 -->
                        <div class="bg-blue-50 rounded-xl p-3 text-center group hover:bg-blue-100 transition cursor-pointer">
                            <div class="w-10 h-10 bg-blue-200 rounded-full flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                </svg>
                            </div>
                            <p class="font-semibold text-gray-800 text-xs">Pengingat</p>
                            <p class="text-gray-500 text-xs">Notifikasi WA</p>
                        </div>
                        
                        <!-- Fitur 4 -->
                        <div class="bg-purple-50 rounded-xl p-3 text-center group hover:bg-purple-100 transition cursor-pointer">
                            <div class="w-10 h-10 bg-purple-200 rounded-full flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <p class="font-semibold text-gray-800 text-xs">Edukasi</p>
                            <p class="text-gray-500 text-xs">Artikel sehat</p>
                        </div>
                    </div>
                    
                    <!-- Call to Action Card -->
                    <div class="mt-4 bg-gradient-to-r from-green-600 to-green-500 rounded-xl p-3 text-center">
                        <p class="text-white font-semibold text-sm">👋 Yuk daftar sekarang, gratis!</p>
                    </div>
                </div>
                
                <!-- Floating Badge -->
                <div class="absolute -top-3 -right-3 bg-white rounded-full shadow-lg px-3 py-1.5 flex items-center gap-2 animate-bounce">
                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                    <span class="text-xs font-semibold text-gray-700">⭐ Baru!</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== PROFIL SECTION ========== -->
<section id="profil" class="py-24 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <span class="text-pink-600 font-semibold text-sm uppercase tracking-wider">Tentang Kami</span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-2 mb-3">Mengapa Memilih SIPANDA?</h2>
            <div class="w-20 h-1 bg-gradient-to-r from-green-600 to-pink-600 mx-auto rounded-full"></div>
            <p class="text-gray-500 mt-4 max-w-2xl mx-auto">Platform digital terintegrasi untuk kemudahan akses layanan kesehatan ibu dan anak</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <!-- Card 1 -->
            <div class="group bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                <div class="w-16 h-16 bg-gradient-to-br from-green-100 to-green-200 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Imunisasi Lengkap</h3>
                <p class="text-gray-500 leading-relaxed">Jadwal imunisasi lengkap untuk anak usia 0-24 bulan dengan sistem pengingat otomatis.</p>
            </div>
            
            <!-- Card 2 -->
            <div class="group bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                <div class="w-16 h-16 bg-gradient-to-br from-pink-100 to-pink-200 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition">
                    <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Pemantauan Kehamilan</h3>
                <p class="text-gray-500 leading-relaxed">Pantau usia kehamilan, HPL, dan riwayat pemeriksaan ke bidan dengan mudah.</p>
            </div>
            
            <!-- Card 3 -->
            <div class="group bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                <div class="w-16 h-16 bg-gradient-to-br from-green-100 to-green-200 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Edukasi Kesehatan</h3>
                <p class="text-gray-500 leading-relaxed">Artikel dan tips kesehatan dari para ahli untuk keluarga sehat dan bahagia.</p>
            </div>
        </div>
        
        <!-- ========== GALERI KEGIATAN DENGAN ANIMASI SLIDE INFINITE ========== -->
        <div class="mt-20">
            <div class="text-center mb-10">
                <h3 class="text-2xl font-bold text-gray-800 mb-2 flex items-center justify-center gap-2">
                    <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Galeri Kegiatan
                </h3>
                <p class="text-gray-500">Dokumentasi kegiatan di SIPANDA</p>
            </div>
            
            <!-- Slider Infinite dari Kanan ke Kiri dengan Efek Fade -->
            <div class="relative">
                <!-- Efek Fade Kiri dan Kanan -->
                <div class="absolute left-0 top-0 bottom-0 w-20 md:w-32 bg-gradient-to-r from-white to-transparent z-10 pointer-events-none"></div>
                <div class="absolute right-0 top-0 bottom-0 w-20 md:w-32 bg-gradient-to-l from-white to-transparent z-10 pointer-events-none"></div>
                
                <!-- Slider Container -->
                <div class="overflow-hidden">
                    <div class="slider-infinite flex gap-5 py-4">
                        <!-- Slide 1 -->
                        <div class="flex-shrink-0 w-72 md:w-80 group">
                            <div class="relative overflow-hidden rounded-2xl shadow-lg bg-white">
                                <img src="img/foto1.jpg" alt="Imunisasi" class="w-full h-56 object-cover transition duration-500 group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-4">
                                    <div>
                                        <p class="text-white font-semibold text-lg">Imunisasi Anak</p>
                                        <p class="text-white/80 text-sm">Vaksin lengkap untuk si kecil</p>
                                    </div>
                                </div>
                                <div class="absolute top-3 left-3 bg-green-500 text-white text-xs px-2 py-1 rounded-full opacity-0 group-hover:opacity-100 transition">
                                    Imunisasi
                                </div>
                            </div>
                        </div>
                        
                        <!-- Slide 2 -->
                        <div class="flex-shrink-0 w-72 md:w-80 group">
                            <div class="relative overflow-hidden rounded-2xl shadow-lg bg-white">
                                <img src="img/foto4.jpg" alt="Pemeriksaan Kehamilan" class="w-full h-56 object-cover transition duration-500 group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-4">
                                    <div>
                                        <p class="text-white font-semibold text-lg">Periksa Kehamilan</p>
                                        <p class="text-white/80 text-sm">Pantau kesehatan ibu & janin</p>
                                    </div>
                                </div>
                                <div class="absolute top-3 left-3 bg-pink-500 text-white text-xs px-2 py-1 rounded-full opacity-0 group-hover:opacity-100 transition">
                                    Kehamilan
                                </div>
                            </div>
                        </div>
                        
                        <!-- Slide 3 -->
                        <div class="flex-shrink-0 w-72 md:w-80 group">
                            <div class="relative overflow-hidden rounded-2xl shadow-lg bg-white">
                                <img src="img/foto3.jpg" alt="Penyuluhan Gizi" class="w-full h-56 object-cover transition duration-500 group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-4">
                                    <div>
                                        <p class="text-white font-semibold text-lg">Penyuluhan Gizi</p>
                                        <p class="text-white/80 text-sm">Edukasi makanan sehat</p>
                                    </div>
                                </div>
                                <div class="absolute top-3 left-3 bg-green-500 text-white text-xs px-2 py-1 rounded-full opacity-0 group-hover:opacity-100 transition">
                                    Edukasi
                                </div>
                            </div>
                        </div>
                        
                        <!-- Slide 4 -->
                        <div class="flex-shrink-0 w-72 md:w-80 group">
                            <div class="relative overflow-hidden rounded-2xl shadow-lg bg-white">
                                <img src="img/foto2.jpeg" alt="Suasana Posyandu" class="w-full h-56 object-cover transition duration-500 group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-4">
                                    <div>
                                        <p class="text-white font-semibold text-lg">Suasana Posyandu</p>
                                        <p class="text-white/80 text-sm">Ramah dan menyenangkan</p>
                                    </div>
                                </div>
                                <div class="absolute top-3 left-3 bg-blue-500 text-white text-xs px-2 py-1 rounded-full opacity-0 group-hover:opacity-100 transition">
                                    Posyandu
                                </div>
                            </div>
                        </div>
                        
                        <!-- Slide 5 -->
                        <div class="flex-shrink-0 w-72 md:w-80 group">
                            <div class="relative overflow-hidden rounded-2xl shadow-lg bg-white">
                                <img src="img/foto5.jpg" alt="Timbang Berat Badan" class="w-full h-56 object-cover transition duration-500 group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-4">
                                    <div>
                                        <p class="text-white font-semibold text-lg">Timbang Berat Badan</p>
                                        <p class="text-white/80 text-sm">Pantau tumbuh kembang</p>
                                    </div>
                                </div>
                                <div class="absolute top-3 left-3 bg-orange-500 text-white text-xs px-2 py-1 rounded-full opacity-0 group-hover:opacity-100 transition">
                                    Pemantauan
                                </div>
                            </div>
                        </div>
                        
                        <!-- DUPLIKAT untuk efek infinite seamless -->
                        <div class="flex-shrink-0 w-72 md:w-80 group">
                            <div class="relative overflow-hidden rounded-2xl shadow-lg bg-white">
                                <img src="img/foto1.jpg" alt="Imunisasi" class="w-full h-56 object-cover transition duration-500 group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-4">
                                    <div>
                                        <p class="text-white font-semibold text-lg">Imunisasi Anak</p>
                                        <p class="text-white/80 text-sm">Vaksin lengkap untuk si kecil</p>
                                    </div>
                                </div>
                                <div class="absolute top-3 left-3 bg-green-500 text-white text-xs px-2 py-1 rounded-full opacity-0 group-hover:opacity-100 transition">
                                    Imunisasi
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex-shrink-0 w-72 md:w-80 group">
                            <div class="relative overflow-hidden rounded-2xl shadow-lg bg-white">
                                <img src="img/foto4.jpg" alt="Pemeriksaan Kehamilan" class="w-full h-56 object-cover transition duration-500 group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-4">
                                    <div>
                                        <p class="text-white font-semibold text-lg">Periksa Kehamilan</p>
                                        <p class="text-white/80 text-sm">Pantau kesehatan ibu & janin</p>
                                    </div>
                                </div>
                                <div class="absolute top-3 left-3 bg-pink-500 text-white text-xs px-2 py-1 rounded-full opacity-0 group-hover:opacity-100 transition">
                                    Kehamilan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== LAYANAN SECTION ========== -->
<section id="layanan" class="py-24 bg-gradient-to-b from-gray-50 to-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <span class="text-pink-600 font-semibold text-sm uppercase tracking-wider">Layanan Kami</span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-2 mb-3">Solusi Lengkap Kesehatan</h2>
            <div class="w-20 h-1 bg-gradient-to-r from-green-600 to-pink-600 mx-auto rounded-full"></div>
            <p class="text-gray-500 mt-4">Dua layanan utama untuk kesehatan keluarga Anda</p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 max-w-5xl mx-auto">
            <!-- Imunisasi -->
            <div class="group bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-green-100">
                <div class="bg-gradient-to-r from-green-600 to-green-500 p-6 text-white">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold">Imunisasi Anak</h3>
                            <p class="text-green-100">Lindungi si kecil sejak dini</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span>Jadwal imunisasi lengkap 0-24 bulan</span></li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span>Notifikasi pengingat jadwal</span></li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span>Riwayat imunisasi anak</span></li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span>Daftar imunisasi online</span></li>
                    </ul>
                    <div class="bg-green-50 rounded-xl p-4 text-center">
                        <p class="text-sm text-gray-600">💉 Vaksin: BCG, Hepatitis B, Polio, DPT, Campak, dan lainnya</p>
                    </div>
                </div>
            </div>
            
            <!-- Kehamilan -->
            <div class="group bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-pink-100">
                <div class="bg-gradient-to-r from-pink-600 to-pink-500 p-6 text-white">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold">Pemeriksaan Kehamilan</h3>
                            <p class="text-pink-100">Pantau kesehatan ibu dan janin</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span>Pantau usia kehamilan & HPL</span></li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span>Riwayat pemeriksaan kehamilan</span></li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span>Catatan kesehatan ibu hamil</span></li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span>Jadwal periksa ke bidan</span></li>
                    </ul>
                    <div class="bg-pink-50 rounded-xl p-4 text-center">
                        <p class="text-sm text-gray-600">🤰 Pemeriksaan rutin setiap bulan untuk ibu hamil</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== ARTIKEL SECTION DENGAN GAMBAR ========== -->
<section id="artikel" class="py-24 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <span class="text-pink-600 font-semibold text-sm uppercase tracking-wider">Edukasi Kesehatan</span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-2 mb-3">Artikel Terbaru</h2>
            <div class="w-20 h-1 bg-gradient-to-r from-green-600 to-pink-600 mx-auto rounded-full"></div>
            <p class="text-gray-500 mt-4">Informasi kesehatan terkini dari para ahli</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-7xl mx-auto">
            <?php if(mysqli_num_rows($artikel_terbaru) > 0): ?>
                <?php while($artikel = mysqli_fetch_assoc($artikel_terbaru)): ?>
                <div class="group bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                    <!-- Thumbnail dengan Gambar -->
                    <div class="relative h-48 overflow-hidden cursor-pointer" onclick="window.location.href='artikel_detail.php?id=<?php echo $artikel['id_artikel']; ?>'">
                        <?php if($artikel['thumbnail'] && file_exists("uploads/artikel/" . $artikel['thumbnail'])): ?>
                        <img src="uploads/artikel/<?php echo $artikel['thumbnail']; ?>" 
                             alt="<?php echo htmlspecialchars($artikel['judul']); ?>" 
                             class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        <?php else: ?>
                        <div class="w-full h-full bg-gradient-to-br from-green-100 to-pink-100 flex items-center justify-center">
                            <svg class="w-16 h-16 text-green-300 group-hover:scale-110 transition duration-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2a10 10 0 0110 10 10 10 0 01-10 10 10 10 0 01-10-10 10 10 0 0110-10m0 2a8 8 0 00-8 8 8 8 0 008 8 8 8 0 008-8 8 8 0 00-8-8z"/>
                            </svg>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Badge Kategori -->
                        <div class="absolute top-3 left-3">
                            <span class="text-xs bg-white/90 backdrop-blur-sm text-green-700 px-2.5 py-1 rounded-full font-medium shadow-sm">
                                <?php echo htmlspecialchars($artikel['nama_kategori'] ?? 'Kesehatan'); ?>
                            </span>
                        </div>
                        
                        <!-- Overlay saat hover -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300"></div>
                    </div>
                    
                    <div class="p-5">
                        <h3 class="font-bold text-gray-800 mb-2 line-clamp-2 group-hover:text-green-600 transition">
                            <?php echo htmlspecialchars($artikel['judul']); ?>
                        </h3>
                        <div class="flex items-center gap-3 text-xs text-gray-400 mb-3">
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <?php echo date('d M Y', strtotime($artikel['created_at'])); ?>
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <?php echo ceil(str_word_count(strip_tags($artikel['konten'])) / 200); ?> min
                            </span>
                        </div>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                            <?php echo htmlspecialchars(substr(strip_tags($artikel['konten']), 0, 100)) . '...'; ?>
                        </p>
                        <a href="artikel_detail.php?id=<?php echo $artikel['id_artikel']; ?>" class="text-green-600 font-semibold text-sm inline-flex items-center gap-1 group-hover:gap-2 transition-all">
                            Baca Selengkapnya 
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-4 text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <p class="text-gray-500">Belum ada artikel</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-12">
            <a href="artikel.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-green-500 text-white px-8 py-3.5 rounded-full font-semibold hover:shadow-xl transition-all duration-300 hover:scale-105">
                Lihat Semua Artikel
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- ========== TESTIMONIAL SECTION ========== -->
<section class="py-24 bg-gradient-to-r from-green-50 to-pink-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <span class="text-pink-600 font-semibold text-sm uppercase tracking-wider">Testimonial</span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-2 mb-3">Apa Kata Mereka?</h2>
            <div class="w-20 h-1 bg-gradient-to-r from-green-600 to-pink-600 mx-auto rounded-full"></div>
            <p class="text-gray-500 mt-4">Pengalaman pengguna yang menggunakan SIPANDA</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path>
                    </svg>
                </div>
                <p class="text-gray-600 italic mb-5">"Sangat membantu! Saya jadi tidak lupa jadwal imunisasi anak."</p>
                <h4 class="font-bold text-gray-800">Siti Aisyah</h4>
                <p class="text-sm text-gray-400">Ibu dari Ahmad Fathir</p>
            </div>
            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 text-center">
                <div class="w-20 h-20 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-pink-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path>
                    </svg>
                </div>
                <p class="text-gray-600 italic mb-5">"Pantau kehamilan jadi lebih mudah. Rekomendasi untuk ibu hamil!"</p>
                <h4 class="font-bold text-gray-800">Nurul Hidayah</h4>
                <p class="text-sm text-gray-400">Ibu Hamil 24 Minggu</p>
            </div>
            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path>
                    </svg>
                </div>
                <p class="text-gray-600 italic mb-5">"Artikel-artikelnya sangat informatif dan mudah dipahami."</p>
                <h4 class="font-bold text-gray-800">Rizka Amalia</h4>
                <p class="text-sm text-gray-400">Ibu dari 2 anak</p>
            </div>
        </div>
    </div>
</section>

<style>
/* Animasi slide infinite dari kanan ke kiri */
.slider-infinite {
    animation: slideRightToLeft 25s linear infinite;
    width: fit-content;
}

.slider-infinite:hover {
    animation-play-state: paused;
}

@keyframes slideRightToLeft {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-50%);
    }
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<?php include __DIR__ . '/templates/footer_public.php'; ?>