<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/cek_ibu.php';
$title = 'Dashboard Ibu';
include __DIR__ . '/../templates/sidebar.php';

$nik = $_SESSION['nik'];

$total_anak = 0;
$query_total_anak = mysqli_query($conn, "SELECT COUNT(*) as total FROM anak WHERE nik_ibu='$nik'");
if($query_total_anak) {
    $total_anak = mysqli_fetch_assoc($query_total_anak)['total'];
}

$jadwal_aktif = 0;
$query_jadwal_aktif = mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran_imunisasi pi 
    JOIN anak a ON pi.id_anak = a.id_anak 
    WHERE a.nik_ibu='$nik' AND pi.status = 'pending'");
if($query_jadwal_aktif) {
    $jadwal_aktif = mysqli_fetch_assoc($query_jadwal_aktif)['total'];
}

$riwayat = 0;
$query_riwayat = mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran_imunisasi pi 
    JOIN anak a ON pi.id_anak = a.id_anak 
    WHERE a.nik_ibu='$nik' AND pi.status = 'selesai'");
if($query_riwayat) {
    $riwayat = mysqli_fetch_assoc($query_riwayat)['total'];
}

$kehamilan = 0;
$kehamilan_data = null;
$query_kehamilan = mysqli_query($conn, "SELECT * FROM ibu_hamil WHERE nik_ibu='$nik' AND status_kehamilan='aktif' LIMIT 1");
if($query_kehamilan) {
    $kehamilan = mysqli_num_rows($query_kehamilan);
    if($kehamilan > 0) {
        $kehamilan_data = mysqli_fetch_assoc($query_kehamilan);
    }
}

$anak_terbaru = mysqli_query($conn, "SELECT * FROM anak WHERE nik_ibu='$nik' ORDER BY created_at DESC LIMIT 3");

$jadwal_terdekat = mysqli_query($conn, "SELECT pi.*, a.nama_anak, v.nama_vaksin, j.tanggal 
    FROM pendaftaran_imunisasi pi 
    JOIN anak a ON pi.id_anak = a.id_anak 
    JOIN jadwal_imunisasi j ON pi.id_jadwal = j.id_jadwal 
    JOIN vaksin v ON j.id_vaksin = v.id_vaksin 
    WHERE a.nik_ibu='$nik' AND pi.status = 'pending' 
    ORDER BY j.tanggal ASC LIMIT 5");

$jadwal_lewat = 0;
$query_jadwal_lewat = mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran_imunisasi pi 
    JOIN anak a ON pi.id_anak = a.id_anak 
    JOIN jadwal_imunisasi j ON pi.id_jadwal = j.id_jadwal 
    WHERE a.nik_ibu='$nik' AND pi.status = 'pending' AND j.tanggal < CURDATE()");
if($query_jadwal_lewat) {
    $jadwal_lewat = mysqli_fetch_assoc($query_jadwal_lewat)['total'];
}

$artikel_terbaru = mysqli_query($conn, "SELECT * FROM artikel ORDER BY created_at DESC LIMIT 3");
?>

<div class="fade-in">
    <div class="bg-gradient-to-r from-green-600 to-emerald-500 rounded-2xl p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>! 👋</h1>
                <p class="text-green-100">Pantau tumbuh kembang buah hati Anda di sipanda</p>
                <div class="mt-2 flex items-center gap-2 text-sm text-green-100">
                    <i class="fas fa-calendar-alt"></i>
                    <span><?php echo formatTanggalIndonesia(date('Y-m-d')); ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <?php if($jadwal_lewat > 0): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
        <div class="flex items-center gap-3">
            <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
            <div>
                <p class="font-semibold">Perhatian!</p>
                <p class="text-sm">Anda memiliki <?php echo $jadwal_lewat; ?> jadwal imunisasi yang sudah lewat. Segera hubungi bidan!</p>
            </div>
            <a href="imunisasi/index.php" class="ml-auto bg-red-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-red-600">
                Lihat
            </a>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl shadow-lg p-4 border-l-4 border-green-500 hover:shadow-xl transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Anak</p>
                    <p class="text-2xl font-bold text-green-700"><?php echo $total_anak; ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-baby text-green-600 text-xl"></i>
                </div>
            </div>
            <a href="anak/index.php" class="text-green-600 text-sm mt-3 inline-block hover:text-green-700">
                Kelola <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg p-4 border-l-4 border-emerald-500 hover:shadow-xl transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Jadwal Mendatang</p>
                    <p class="text-2xl font-bold text-emerald-700"><?php echo $jadwal_aktif; ?></p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-emerald-600 text-xl"></i>
                </div>
            </div>
            <a href="imunisasi/index.php" class="text-emerald-600 text-sm mt-3 inline-block hover:text-emerald-700">
                Lihat <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg p-4 border-l-4 border-teal-500 hover:shadow-xl transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Riwayat Imunisasi</p>
                    <p class="text-2xl font-bold text-teal-700"><?php echo $riwayat; ?></p>
                </div>
                <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-teal-600 text-xl"></i>
                </div>
            </div>
            <a href="imunisasi/index.php" class="text-teal-600 text-sm mt-3 inline-block hover:text-teal-700">
                Riwayat <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg p-4 border-l-4 border-pink-500 hover:shadow-xl transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Status Kehamilan</p>
                    <p class="text-2xl font-bold text-pink-700">
                        <?php echo $kehamilan > 0 ? 'Aktif' : 'Tidak Aktif'; ?>
                    </p>
                    <?php if($kehamilan_data): ?>
                    <p class="text-xs text-gray-500 mt-1">Usia: <?php echo $kehamilan_data['usia_kehamilan']; ?> minggu</p>
                    <?php endif; ?>
                </div>
                <div class="w-12 h-12 bg-pink-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-female text-pink-600 text-xl"></i>
                </div>
            </div>
            <a href="kehamilan/index.php" class="text-pink-600 text-sm mt-3 inline-block hover:text-pink-700">
                Detail <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-calendar-check text-green-500 mr-2"></i> 
                    Jadwal Imunisasi Mendatang
                </h3>
                <a href="imunisasi/index.php" class="text-sm text-green-600 hover:text-green-700">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            <?php if(mysqli_num_rows($jadwal_terdekat) > 0): ?>
                <?php while($j = mysqli_fetch_assoc($jadwal_terdekat)): ?>
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-xl mb-3 hover:bg-green-100 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-syringe text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($j['nama_anak']); ?></p>
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($j['nama_vaksin']); ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-green-600">
                            <?php echo date('d/m/Y', strtotime($j['tanggal'])); ?>
                        </p>
                        <?php 
                        $tanggal_jadwal = strtotime($j['tanggal']);
                        $hari_ini = time();
                        $selisih_hari = ceil(($tanggal_jadwal - $hari_ini) / (60 * 60 * 24));
                        ?>
                        <p class="text-xs text-gray-500">
                            <?php if($selisih_hari == 0): ?>
                                Hari ini
                            <?php elseif($selisih_hari == 1): ?>
                                Besok
                            <?php elseif($selisih_hari < 7): ?>
                                <?php echo $selisih_hari; ?> hari lagi
                            <?php else: ?>
                                <?php echo ceil($selisih_hari/7); ?> minggu lagi
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-calendar-times text-4xl mb-2 text-gray-300"></i>
                    <p>Tidak ada jadwal imunisasi mendatang</p>
                    <a href="imunisasi/index.php" class="text-green-600 text-sm mt-3 inline-block">
                        Daftar Imunisasi
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-child text-blue-500 mr-2"></i> 
                    Data Anak
                </h3>
                <a href="anak/index.php" class="text-sm text-blue-600 hover:text-blue-700">
                    Kelola <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            <?php if(mysqli_num_rows($anak_terbaru) > 0): ?>
                <?php while($a = mysqli_fetch_assoc($anak_terbaru)): ?>
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-xl mb-3 hover:bg-blue-100 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-child text-blue-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($a['nama_anak']); ?></p>
                            <p class="text-xs text-gray-500">
                                Lahir: <?php echo date('d/m/Y', strtotime($a['tanggal_lahir'])); ?>
                                <?php
                                $lahir = new DateTime($a['tanggal_lahir']);
                                $sekarang = new DateTime();
                                $umur = $lahir->diff($sekarang);
                                if($umur->y > 0) {
                                    echo " • {$umur->y} tahun";
                                } elseif($umur->m > 0) {
                                    echo " • {$umur->m} bulan";
                                } else {
                                    echo " • {$umur->d} hari";
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                    <form action="/posyandu/ibu/perkembangan/index.php" method="POST" class="inline">
                        <input type="hidden" name="anak_id" value="<?php echo $a['id_anak']; ?>">
                        <button type="submit" class="text-blue-600 text-sm hover:text-blue-700">
                            Detail <i class="fas fa-arrow-right ml-1"></i>
                        </button>
                    </form>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-baby-carriage text-4xl mb-2 text-gray-300"></i>
                    <p>Belum ada data anak</p>
                    <a href="anak/tambah_anak.php" class="text-green-600 text-sm mt-3 inline-block">
                        Tambah Anak
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <?php if($kehamilan > 0 && $kehamilan_data): ?>
        <div class="bg-gradient-to-r from-pink-50 to-pink-100 rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-pink-700 mb-3">
                <i class="fas fa-heartbeat mr-2"></i> 
                Tips untuk Ibu Hamil (Trimester <?php echo ceil($kehamilan_data['usia_kehamilan'] / 13); ?>)
            </h3>
            <ul class="space-y-2 text-sm text-gray-700">
                <li class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                    <span>Rutin periksa kehamilan ke bidan minimal 1x per bulan</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                    <span>Konsumsi makanan bergizi dan vitamin penambah darah</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                    <span>Istirahat cukup dan hindari stres berlebihan</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                    <span>Segera ke bidan jika ada keluhan seperti pusing, perdarahan, atau gerakan janin berkurang</span>
                </li>
            </ul>
            <a href="kehamilan/index.php" class="inline-block mt-4 text-pink-600 text-sm hover:text-pink-700">
                Lihat Detail Kehamilan <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <?php else: ?>
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-blue-700 mb-3">
                <i class="fas fa-info-circle mr-2"></i> 
                Tips Kesehatan Anak
            </h3>
            <ul class="space-y-2 text-sm text-gray-700">
                <li class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                    <span>Pastikan imunisasi anak lengkap sesuai jadwal</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                    <span>Berikan makanan bergizi seimbang (4 bintang 5 sempurna)</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                    <span>Pantau tumbuh kembang anak setiap bulan di Posyandu</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                    <span>Ajak anak bermain dan stimulasi perkembangan motorik</span>
                </li>
            </ul>
        </div>
        <?php endif; ?>
        
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-newspaper text-orange-500 mr-2"></i> 
                    Artikel Terbaru
                </h3>
                <a href="/posyandu/artikel.php" class="text-sm text-orange-600 hover:text-orange-700">
                    Baca Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            <?php if(mysqli_num_rows($artikel_terbaru) > 0): ?>
                <div class="space-y-3">
                    <?php while($artikel = mysqli_fetch_assoc($artikel_terbaru)): ?>
                    <a href="/posyandu/artikel_detail.php?id=<?php echo $artikel['id_artikel']; ?>" class="block p-3 border rounded-xl hover:bg-gray-50 transition">
                        <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($artikel['judul']); ?></h4>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="far fa-calendar-alt"></i> <?php echo formatTanggalIndonesia($artikel['created_at']); ?>
                        </p>
                    </a>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-newspaper text-4xl mb-2 text-gray-300"></i>
                    <p>Belum ada artikel</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>