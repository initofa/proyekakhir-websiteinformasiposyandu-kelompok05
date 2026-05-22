<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_ibu.php';
$title = 'Riwayat Kehamilan';
include __DIR__ . '/../../templates/sidebar.php';

$nik = $_SESSION['nik'];

// Cek apakah ada kehamilan aktif
$cek_aktif = mysqli_query($conn, "SELECT id_kehamilan FROM ibu_hamil WHERE nik_ibu = '$nik' AND status_kehamilan = 'aktif'");
$ada_kehamilan_aktif = mysqli_num_rows($cek_aktif) > 0;

// Ambil semua data kehamilan ibu
$result = mysqli_query($conn, "SELECT ih.*, 
    (SELECT COUNT(*) FROM pemeriksaan_kehamilan WHERE id_kehamilan = ih.id_kehamilan) as total_pemeriksaan
    FROM ibu_hamil ih 
    WHERE ih.nik_ibu = '$nik' 
    ORDER BY 
        CASE ih.status_kehamilan 
            WHEN 'aktif' THEN 0 
            WHEN 'melahirkan' THEN 1 
            WHEN 'keguguran' THEN 2 
            ELSE 3 
        END,
        ih.created_at DESC");

// Fungsi untuk mendapatkan badge status
function getStatusBadge($status) {
    switch($status) {
        case 'aktif':
            return '<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-green-100 text-green-700"><i class="fas fa-check-circle"></i> Aktif</span>';
        case 'melahirkan':
            return '<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700"><i class="fas fa-baby-carriage"></i> Sudah Melahirkan</span>';
        case 'keguguran':
            return '<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-red-100 text-red-700"><i class="fas fa-heart-broken"></i> Keguguran</span>';
        case 'pindah':
            return '<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-orange-100 text-orange-700"><i class="fas fa-exchange-alt"></i> Pindah Posyandu</span>';
        default:
            return '<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">-</span>';
    }
}

// Fungsi untuk mendapatkan warna card berdasarkan status dan kondisi kesehatan
function getCardColor($status, $catatan_kesehatan) {
    // Cek apakah ada indikasi tidak sehat
    $tidak_sehat = false;
    if($status == 'aktif' && !empty($catatan_kesehatan)) {
        $keywords = ['kurang', 'lemah', 'pusing', 'mual', 'darah tinggi', 'diabetes', 'anemia', 'komplikasi'];
        foreach($keywords as $keyword) {
            if(stripos($catatan_kesehatan, $keyword) !== false) {
                $tidak_sehat = true;
                break;
            }
        }
    }
    
    switch($status) {
        case 'aktif':
            if($tidak_sehat) {
                return 'bg-orange-50 border-l-4 border-orange-500'; // Tidak sehat - Orange
            }
            return 'bg-pink-50 border-l-4 border-pink-500'; // Sehat - Pink
        case 'melahirkan':
            return 'bg-blue-50 border-l-4 border-blue-500'; // Biru
        case 'keguguran':
            return 'bg-red-50 border-l-4 border-red-500'; // Merah
        case 'pindah':
            return 'bg-gray-50 border-l-4 border-gray-500'; // Abu-abu
        default:
            return 'bg-white border-l-4 border-gray-300';
    }
}
?>

<div class="fade-in">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-green-800">Riwayat Kehamilan</h1>
        
        <!-- Tombol Daftar Kehamilan hanya muncul jika TIDAK ada kehamilan aktif -->
        <?php if(!$ada_kehamilan_aktif): ?>
        <a href="daftar_hamil.php" class="bg-gradient-to-r from-green-600 to-emerald-500 text-white px-4 py-2 rounded-xl hover:shadow-lg transition">
            <i class="fas fa-plus mr-2"></i>Kehamilan
        </a>
        <?php endif; ?>
    </div>
    
    <?php if(mysqli_num_rows($result) > 0): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php while($row = mysqli_fetch_assoc($result)): 
            $hpl = new DateTime($row['hpl']);
            $today = new DateTime();
            $sisa_hari = $hpl > $today ? $today->diff($hpl)->days : 0;
            
            $usia_minggu = $row['usia_kehamilan'];
            $usia_bulan = floor($usia_minggu / 4);
            $sisa_minggu = $usia_minggu % 4;
            $usia_text = $usia_bulan . ' bulan ' . $sisa_minggu . ' minggu';
            
            $card_color = getCardColor($row['status_kehamilan'], $row['catatan_kesehatan']);
            
            // Tentukan ikon status
            $status_icon = '';
            if($row['status_kehamilan'] == 'aktif') {
                $tidak_sehat = false;
                $keywords = ['kurang', 'lemah', 'pusing', 'mual', 'darah tinggi', 'diabetes', 'anemia', 'komplikasi'];
                foreach($keywords as $keyword) {
                    if(stripos($row['catatan_kesehatan'], $keyword) !== false) {
                        $tidak_sehat = true;
                        break;
                    }
                }
                $status_icon = $tidak_sehat ? '⚠️ Perlu Perhatian' : '❤️ Sehat';
            } elseif($row['status_kehamilan'] == 'melahirkan') {
                $status_icon = '👶 Telah Melahirkan';
            } elseif($row['status_kehamilan'] == 'keguguran') {
                $status_icon = '💔 Keguguran';
            } else {
                $status_icon = '📦 Pindah';
            }
        ?>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition">
            <div class="<?php echo $card_color; ?>">
                <div class="p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <i class="fas fa-female text-green-600"></i>
                                <h3 class="font-bold text-gray-800">Kehamilan <?php echo date('Y', strtotime($row['created_at'])); ?></h3>
                            </div>
                            <div class="flex flex-wrap gap-2 items-center">
                                <?php echo getStatusBadge($row['status_kehamilan']); ?>
                                <span class="text-xs text-gray-500"><?php echo $status_icon; ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                        <div class="flex items-center gap-1">
                            <i class="fas fa-calendar-alt w-4 text-green-500"></i>
                            <span>HPL: <?php echo date('d/m/Y', strtotime($row['hpl'])); ?></span>
                        </div>
                        <div class="flex items-center gap-1">
                            <i class="fas fa-hourglass-half w-4 text-green-500"></i>
                            <span>Sisa: <?php echo $sisa_hari; ?> hari</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <i class="fas fa-clock w-4 text-green-500"></i>
                            <span>Usia: <?php echo $usia_text; ?></span>
                        </div>
                        <div class="flex items-center gap-1">
                            <i class="fas fa-stethoscope w-4 text-green-500"></i>
                            <span>Pemeriksaan: <?php echo $row['total_pemeriksaan']; ?>x</span>
                        </div>
                    </div>
                    
                    <?php if($row['status_kehamilan'] == 'aktif' && !empty($row['catatan_kesehatan'])): ?>
                    <div class="mt-2 mb-2 p-2 bg-yellow-50 rounded-lg text-xs text-yellow-700">
                        <i class="fas fa-info-circle mr-1"></i> <?php echo substr($row['catatan_kesehatan'], 0, 100); ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="flex gap-2 mt-3 pt-3 border-t border-gray-100">
                        <a href="detail_hamil.php?id=<?php echo $row['id_kehamilan']; ?>" 
                           class="flex-1 text-center bg-green-600 text-white py-1 rounded-lg text-sm hover:bg-green-700 transition">
                            <i class="fas fa-eye mr-1"></i> Periksa Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
        <i class="fas fa-baby-carriage text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Data Kehamilan</h3>
        <p class="text-gray-500 mb-4">Silakan daftarkan kehamilan Anda</p>
        <?php if(!$ada_kehamilan_aktif): ?>
        <a href="daftar_hamil.php" class="inline-block bg-green-600 text-white px-6 py-2 rounded-xl hover:bg-green-700 transition">
            <i class="fas fa-plus mr-2"></i> Daftar Kehamilan
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>