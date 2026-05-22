<?php
// templates/footer_public.php - Footer untuk halaman publik
$current_year = date('Y');
?>

<!-- ========== CTA SECTION MODERN ========== -->
<section class="py-20 relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-green-700 via-green-600 to-green-500"></div>
    <div class="absolute top-0 -left-20 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 -right-20 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
    <div class="container mx-auto px-4 text-center relative z-10">
        <div class="max-w-3xl mx-auto">
            <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full text-white/90 text-sm mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                <span>Bergabung dengan ribuan keluarga sehat</span>
            </div>
            <h3 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4">Siap Menjaga Kesehatan<br>Keluarga Anda?</h3>
            <p class="text-green-100 text-lg mb-8 max-w-xl mx-auto">Daftar sekarang dan nikmati kemudahan akses layanan kesehatan secara online</p>
            <div class="flex gap-4 justify-center flex-wrap">
                <a href="auth/register.php" class="group bg-white text-green-600 px-8 py-3.5 rounded-full font-bold hover:shadow-2xl transition-all duration-300 hover:scale-105 flex items-center gap-2">
                    <span>Daftar Sekarang</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
                <a href="index.php#layanan" class="border-2 border-white text-white px-8 py-3.5 rounded-full font-bold hover:bg-white hover:text-green-600 transition-all duration-300">
                    Lihat Layanan
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ========== FOOTER MODERN DENGAN ID KONTAK ========== -->
<footer id="kontak" class="bg-gray-900 pt-16 pb-8">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
            
        <!-- Brand Column -->
        <div class="lg:col-span-1">
            <div class="flex items-center space-x-3 mb-4">
                <img src="img/sipanda.png" alt="SIPANDA" class="w-10 h-10 object-contain">
                <span class="text-2xl font-bold bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent">SIPANDA</span>
            </div>
            <p class="text-gray-400 text-sm leading-relaxed mb-4">
                Sistem Informasi Pemantauan Anak dan Bunda. Solusi digital untuk memantau tumbuh kembang anak dan kesehatan ibu hamil.
            </p>
            <div class="flex space-x-3">
                <a href="#" class="w-9 h-9 bg-gray-800 rounded-xl flex items-center justify-center text-gray-400 hover:bg-green-600 hover:text-white transition-all duration-300 group">
                    <svg class="w-4 h-4 group-hover:scale-110 transition" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                    </svg>
                </a>
                <a href="#" class="w-9 h-9 bg-gray-800 rounded-xl flex items-center justify-center text-gray-400 hover:bg-pink-600 hover:text-white transition-all duration-300 group">
                    <svg class="w-4 h-4 group-hover:scale-110 transition" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                    </svg>
                </a>
                <a href="#" class="w-9 h-9 bg-gray-800 rounded-xl flex items-center justify-center text-gray-400 hover:bg-blue-600 hover:text-white transition-all duration-300 group">
                    <svg class="w-4 h-4 group-hover:scale-110 transition" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 0021.467-11.444 7.904 7.904 0 002.161-2.321 7.984 7.984 0 01-2.495.682 4.382 4.382 0 001.909-2.41z"/>
                    </svg>
                </a>
                <a href="#" class="w-9 h-9 bg-gray-800 rounded-xl flex items-center justify-center text-gray-400 hover:bg-red-600 hover:text-white transition-all duration-300 group">
                    <svg class="w-4 h-4 group-hover:scale-110 transition" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/>
                    </svg>
                </a>
            </div>
        </div>
            <!-- Explore Column -->
            <div>
                <h4 class="text-white font-semibold text-lg mb-5">Jelajahi</h4>
                <ul class="space-y-3">
                    <li><a href="index.php#profil" class="text-gray-400 hover:text-green-400 transition duration-200 flex items-center gap-2 group">Tentang Kami</a></li>
                    <li><a href="index.php#layanan" class="text-gray-400 hover:text-green-400 transition duration-200 flex items-center gap-2 group">Layanan</a></li>
                    <li><a href="artikel.php" class="text-gray-400 hover:text-green-400 transition duration-200 flex items-center gap-2 group">Artikel Kesehatan</a></li>
                    <li><a href="auth/register.php" class="text-gray-400 hover:text-green-400 transition duration-200 flex items-center gap-2 group">Pendaftaran</a></li>
                </ul>
            </div>
            
            <!-- Layanan Column -->
            <div>
                <h4 class="text-white font-semibold text-lg mb-5">Layanan Kami</h4>
                <ul class="space-y-3">
                    <li class="text-gray-400 flex items-center gap-3">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        Imunisasi Anak
                    </li>
                    <li class="text-gray-400 flex items-center gap-3">
                        <span class="w-2 h-2 bg-pink-500 rounded-full"></span>
                        Pemeriksaan Kehamilan
                    </li>
                    <li class="text-gray-400 flex items-center gap-3">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        Pantau Tumbuh Kembang
                    </li>
                    <li class="text-gray-400 flex items-center gap-3">
                        <span class="w-2 h-2 bg-pink-500 rounded-full"></span>
                        Edukasi Kesehatan
                    </li>
                </ul>
            </div>
            
            <!-- Kontak Column (highlighted) -->
            <div id="kontak-section">
                <h4 class="text-white font-semibold text-lg mb-5">Kontak Kami</h4>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3 text-gray-400">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-sm">Telang, Bangkalan</span>
                    </li>
                    <li class="flex items-center gap-3 text-gray-400">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span>0819-9992-5324</span>
                    </li>
                    <li class="flex items-center gap-3 text-gray-400">
                        <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span>sipanda@posyandu.id</span>
                    </li>
                    <li class="flex items-center gap-3 text-gray-400">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Senin - Sabtu, 08:00 - 15:00</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="border-t border-gray-800 my-8"></div>
        
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-gray-500 text-sm">&copy; <?php echo $current_year; ?> SIPANDA. All rights reserved.</p>
            <div class="flex gap-6">
                <a href="#" class="text-gray-500 text-xs hover:text-green-400 transition">Kebijakan Privasi</a>
                <a href="#" class="text-gray-500 text-xs hover:text-green-400 transition">Syarat & Ketentuan</a>
                <a href="#" class="text-gray-500 text-xs hover:text-green-400 transition">Bantuan</a>
            </div>
        </div>
    </div>
</footer>

<!-- ========== BACK TO TOP BUTTON MODERN ========== -->
<button id="backToTop" class="fixed bottom-8 right-8 w-12 h-12 bg-gradient-to-r from-green-600 to-green-500 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 opacity-0 invisible hover:scale-110 z-50 flex items-center justify-center group">
    <svg class="w-5 h-5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
    </svg>
</button>

<script>
    // Back to Top Button
    const backToTop = document.getElementById('backToTop');
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            backToTop.classList.remove('opacity-0', 'invisible');
            backToTop.classList.add('opacity-100', 'visible');
        } else {
            backToTop.classList.add('opacity-0', 'invisible');
            backToTop.classList.remove('opacity-100', 'visible');
        }
    });
    
    backToTop.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
</script>

</body>
</html>