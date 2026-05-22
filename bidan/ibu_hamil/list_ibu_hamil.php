<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_bidan.php';
$title = 'Data Ibu Hamil';
include __DIR__ . '/../../templates/sidebar.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'semua';
$limit = 15;
$offset = ($page - 1) * $limit;

// HITUNG TOTAL SEMUA STATUS
$total_aktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM ibu_hamil WHERE status_kehamilan='aktif'"))['total'];
$total_melahirkan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM ibu_hamil WHERE status_kehamilan='melahirkan'"))['total'];
$total_keguguran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM ibu_hamil WHERE status_kehamilan='keguguran'"))['total'];
$total_pindah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM ibu_hamil WHERE status_kehamilan='pindah'"))['total'];
$total_semua = $total_aktif + $total_melahirkan + $total_keguguran + $total_pindah;

// Filter berdasarkan tab
if($tab == 'aktif') {
    $where = "WHERE status_kehamilan='aktif'";
} elseif($tab == 'melahirkan') {
    $where = "WHERE status_kehamilan='melahirkan'";
} elseif($tab == 'keguguran') {
    $where = "WHERE status_kehamilan='keguguran'";
} elseif($tab == 'pindah') {
    $where = "WHERE status_kehamilan='pindah'";
} else {
    $where = "";
}

// Hitung total data untuk pagination
if($tab == 'semua') {
    $total_data = $total_semua;
} elseif($tab == 'aktif') {
    $total_data = $total_aktif;
} elseif($tab == 'melahirkan') {
    $total_data = $total_melahirkan;
} elseif($tab == 'keguguran') {
    $total_data = $total_keguguran;
} else {
    $total_data = $total_pindah;
}
$total_pages = ceil($total_data / $limit);

// AMBIL DATA
$result = mysqli_query($conn, "SELECT ih.*, u.nama_lengkap, u.no_wa, u.alamat,
    (SELECT MAX(tanggal_pemeriksaan) FROM pemeriksaan_kehamilan WHERE id_kehamilan=ih.id_kehamilan) as tgl_periksa_terakhir
    FROM ibu_hamil ih 
    JOIN users u ON ih.nik_ibu=u.nik 
    $where
    ORDER BY 
        CASE ih.status_kehamilan 
            WHEN 'aktif' THEN 0 
            WHEN 'melahirkan' THEN 1 
            WHEN 'keguguran' THEN 2 
            ELSE 3 
        END,
        ih.hpl ASC 
    LIMIT $offset, $limit");

// Fungsi konversi minggu ke bulan
function mingguKeBulan($minggu) {
    $bulan = floor($minggu / 4);
    $sisa_minggu = $minggu % 4;
    if($bulan > 0 && $sisa_minggu > 0) {
        return $bulan . ' bulan ' . $sisa_minggu . ' minggu';
    } elseif($bulan > 0) {
        return $bulan . ' bulan';
    } else {
        return $minggu . ' minggu';
    }
}

// Fungsi cek lama tidak periksa (lebih dari 2 bulan)
function cekLamaTidakPeriksa($tgl_periksa_terakhir) {
    if(empty($tgl_periksa_terakhir)) return true;
    $last_periksa = new DateTime($tgl_periksa_terakhir);
    $today = new DateTime();
    $diff = $today->diff($last_periksa);
    $bulan_selisih = $diff->m + ($diff->y * 12);
    return $bulan_selisih >= 2;
}

// Fungsi untuk mendapatkan warna card berdasarkan status
function getCardColor($status, $need_attention) {
    if($need_attention && $status == 'aktif') {
        return 'bg-pink-50 border-2 border-pink-300';
    }
    
    switch($status) {
        case 'aktif':
            return 'bg-white border-l-4 border-green-500';
        case 'melahirkan':
            return 'bg-blue-50 border-l-4 border-blue-500';
        case 'keguguran':
            return 'bg-red-50 border-l-4 border-red-500';
        case 'pindah':
            return 'bg-orange-50 border-l-4 border-orange-500';
        default:
            return 'bg-white';
    }
}

// Fungsi untuk mendapatkan badge status
function getStatusBadge($status) {
    switch($status) {
        case 'aktif':
            return '<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700"><i class="fas fa-check-circle mr-1"></i> Aktif</span>';
        case 'melahirkan':
            return '<span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700"><i class="fas fa-baby-carriage mr-1"></i> Sudah Melahirkan</span>';
        case 'keguguran':
            return '<span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700"><i class="fas fa-heart-broken mr-1"></i> Keguguran</span>';
        case 'pindah':
            return '<span class="px-2 py-1 rounded-full text-xs bg-orange-100 text-orange-700"><i class="fas fa-exchange-alt mr-1"></i> Pindah Posyandu</span>';
        default:
            return '<span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">-</span>';
    }
}
?>

<div class="fade-in">
    <!-- Header -->
    <h1 class="text-2xl font-bold text-green-800 mb-4">Data Ibu Hamil</h1>
    
    <!-- Tab Navigation -->
    <div class="flex flex-wrap gap-2 mb-6 border-b border-gray-200 pb-2">
        <a href="?tab=semua&page=1" 
           class="px-4 py-2 rounded-t-lg text-sm font-medium transition-all duration-300 <?php echo $tab == 'semua' ? 'bg-green-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">
            <i class="fas fa-chart-pie mr-1"></i> Semua (<?php echo $total_semua; ?>)
        </a>
        <a href="?tab=aktif&page=1" 
           class="px-4 py-2 rounded-t-lg text-sm font-medium transition-all duration-300 <?php echo $tab == 'aktif' ? 'bg-green-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">
            <i class="fas fa-check-circle text-green-600 mr-1"></i> Aktif (<?php echo $total_aktif; ?>)
        </a>
        <a href="?tab=melahirkan&page=1" 
           class="px-4 py-2 rounded-t-lg text-sm font-medium transition-all duration-300 <?php echo $tab == 'melahirkan' ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">
            <i class="fas fa-baby-carriage text-blue-600 mr-1"></i> Melahirkan (<?php echo $total_melahirkan; ?>)
        </a>
        <a href="?tab=keguguran&page=1" 
           class="px-4 py-2 rounded-t-lg text-sm font-medium transition-all duration-300 <?php echo $tab == 'keguguran' ? 'bg-red-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">
            <i class="fas fa-heart-broken text-red-600 mr-1"></i> Keguguran (<?php echo $total_keguguran; ?>)
        </a>
        <a href="?tab=pindah&page=1" 
           class="px-4 py-2 rounded-t-lg text-sm font-medium transition-all duration-300 <?php echo $tab == 'pindah' ? 'bg-orange-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">
            <i class="fas fa-exchange-alt text-orange-600 mr-1"></i> Pindah (<?php echo $total_pindah; ?>)
        </a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php while($row = mysqli_fetch_assoc($result)): 
            $hpl = new DateTime($row['hpl']);
            $today = new DateTime();
            $sisa = $hpl > $today ? $today->diff($hpl)->days : 0;
            
            $usia_minggu = $row['usia_kehamilan'];
            $usia_display = $usia_minggu . ' minggu (' . mingguKeBulan($usia_minggu) . ')';
            
            $need_attention = ($row['status_kehamilan'] == 'aktif') ? cekLamaTidakPeriksa($row['tgl_periksa_terakhir']) : false;
            
            $card_color = getCardColor($row['status_kehamilan'], $need_attention);
            $status_badge = getStatusBadge($row['status_kehamilan']);
            
            $wa_message = "Halo Ibu " . $row['nama_lengkap'] . ",\n\n";
            if($row['status_kehamilan'] == 'aktif'){
                $wa_message .= "Kami mengingatkan untuk melakukan pemeriksaan kehamilan rutin.\n\n";
                $wa_message .= "Usia Kehamilan: " . $usia_display . "\n";
                $wa_message .= "HPL: " . date('d/m/Y', strtotime($row['hpl'])) . " (sisa $sisa hari)\n\n";
                $wa_message .= "Silakan datang ke Posyandu Ceria untuk pemeriksaan.\n\n";
            } else {
                $wa_message .= "Bagaimana kondisi kesehatan Anda?\n\n";
                $wa_message .= "Jika ada keluhan, silakan konsultasi ke Posyandu Ceria.\n\n";
            }
            $wa_message .= "Terima kasih.\n\n- Petugas Posyandu Ceria";
            
            $wa_url = "https://wa.me/" . preg_replace('/[^0-9]/', '', $row['no_wa']) . "?text=" . urlencode($wa_message);
        ?>
        <div class="<?php echo $card_color; ?> rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition">
            <div class="bg-gradient-to-r from-green-500 to-emerald-500 p-4 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <i class="fas fa-female text-2xl"></i>
                        <h3 class="text-lg font-bold"><?php echo $row['nama_lengkap']; ?></h3>
                    </div>
                    <div class="text-right">
                        <span class="bg-white/20 px-2 py-1 rounded-full text-xs"><?php echo $usia_display; ?></span>
                    </div>
                </div>
            </div>
            <div class="p-4">
                <div class="mb-2">
                    <?php echo $status_badge; ?>
                </div>
                <p><i class="fas fa-calendar w-4 text-gray-500"></i> HPL: <?php echo date('d/m/Y', strtotime($row['hpl'])); ?> (<?php echo $sisa; ?> hari)</p>
                <p><i class="fas fa-weight-scale w-4 text-gray-500"></i> BB: <?php echo $row['berat_badan_ibu']; ?> kg</p>
                <p><i class="fas fa-heartbeat w-4 text-gray-500"></i> TD: <?php echo $row['tekanan_darah']; ?></p>
                <p><i class="fas fa-clock w-4 text-gray-500"></i> Usia: <?php echo $usia_display; ?></p>
                
                <?php if($need_attention): ?>
                <div class="mt-2 p-2 bg-pink-100 border border-pink-300 rounded-lg">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-pink-600"></i>
                        <span class="text-xs text-pink-700 font-semibold">Sudah 2 bulan tidak periksa!</span>
                    </div>
                    <?php if($row['tgl_periksa_terakhir']): ?>
                    <p class="text-xs text-pink-600 mt-1">Terakhir periksa: <?php echo date('d/m/Y', strtotime($row['tgl_periksa_terakhir'])); ?></p>
                    <?php else: ?>
                    <p class="text-xs text-pink-600 mt-1">Belum pernah melakukan pemeriksaan</p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <div class="flex gap-2 mt-3">
                    <a href="detail_ibu_hamil.php?id=<?php echo $row['id_kehamilan']; ?>" class="flex-1 text-center bg-green-600 text-white py-1 rounded-lg text-sm hover:bg-green-700 transition">
                        Detail
                    </a>
                    <?php if($row['status_kehamilan'] == 'aktif'): ?>
                    <a href="pemeriksaan.php?id=<?php echo $row['id_kehamilan']; ?>" class="flex-1 text-center bg-blue-600 text-white py-1 rounded-lg text-sm hover:bg-blue-700 transition">
                        Pemeriksaan
                    </a>
                    <?php endif; ?>
                    <a href="<?php echo $wa_url; ?>" target="_blank" class="flex-1 text-center bg-green-500 text-white py-1 rounded-lg text-sm hover:bg-green-600 transition flex items-center justify-center gap-1">
                        <i class="fab fa-whatsapp"></i> WA
                    </a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
        
        <?php if(mysqli_num_rows($result) == 0): ?>
        <div class="col-span-full">
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <i class="fas fa-folder-open text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak Ada Data</h3>
                <p class="text-gray-500">Tidak ada data ibu hamil dengan status ini</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if($total_pages > 1): ?>
    <div class="mt-6">
        <?php echo paginate($page, $total_pages, "list_ibu_hamil.php?tab=$tab"); ?>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>