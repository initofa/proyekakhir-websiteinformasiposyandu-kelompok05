<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_ibu.php';
$title = 'Detail Kehamilan';
include __DIR__ . '/../../templates/sidebar.php';

$nik = $_SESSION['nik'];

// Gunakan parameter 'id' atau 'kehamilan_id'
$id_kehamilan = isset($_GET['kehamilan_id']) ? (int)$_GET['kehamilan_id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);

if($id_kehamilan == 0) {
    $_SESSION['error'] = "Data kehamilan tidak ditemukan!";
    header("Location: riwayat_hamil.php");
    exit();
}

// Ambil data kehamilan
$query_kehamilan = "SELECT ih.* 
    FROM ibu_hamil ih 
    WHERE ih.id_kehamilan = $id_kehamilan AND ih.nik_ibu = '$nik'";
$result_kehamilan = mysqli_query($conn, $query_kehamilan);

if(!$result_kehamilan) {
    die("Query Error: " . mysqli_error($conn));
}

$kehamilan = mysqli_fetch_assoc($result_kehamilan);

if(!$kehamilan){
    $_SESSION['error'] = "Data kehamilan tidak ditemukan!";
    header("Location: riwayat_hamil.php");
    exit();
}

// PERBAIKAN: Ambil riwayat pemeriksaan diurutkan berdasarkan TANGGAL (ASC)
$query_pemeriksaan = "SELECT p.*, u.nama_lengkap as nama_bidan 
    FROM pemeriksaan_kehamilan p 
    LEFT JOIN users u ON p.petugas_nik = u.nik 
    WHERE p.id_kehamilan = $id_kehamilan 
    ORDER BY p.tanggal_pemeriksaan ASC, p.id_pemeriksaan ASC";
$pemeriksaan = mysqli_query($conn, $query_pemeriksaan);

// Hitung usia kehamilan
$usia_minggu = $kehamilan['usia_kehamilan'];
$usia_bulan = floor($usia_minggu / 4);
$sisa_minggu = $usia_minggu % 4;
$usia_text = $usia_bulan . ' bulan ' . $sisa_minggu . ' minggu';

// Hitung HPL
$hpl = new DateTime($kehamilan['hpl']);
$today = new DateTime();
$sisa_hari = $hpl > $today ? $today->diff($hpl)->days : 0;

// Fungsi badge status
function getStatusBadge($status) {
    switch($status) {
        case 'aktif': return '<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700"><i class="fas fa-check-circle mr-1"></i> Aktif</span>';
        case 'melahirkan': return '<span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700"><i class="fas fa-baby-carriage mr-1"></i> Sudah Melahirkan</span>';
        case 'keguguran': return '<span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700"><i class="fas fa-heart-broken mr-1"></i> Keguguran</span>';
        case 'pindah': return '<span class="px-2 py-1 rounded-full text-xs bg-orange-100 text-orange-700"><i class="fas fa-exchange-alt mr-1"></i> Pindah Posyandu</span>';
        default: return '<span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">-</span>';
    }
}
?>

<div class="max-w-4xl mx-auto fade-in">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-600 to-emerald-500 p-6 text-white">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Detail Kehamilan</h1>
                    <p class="text-green-100 mt-1">Informasi lengkap kehamilan Anda</p>
                </div>
                <div>
                    <?php echo getStatusBadge($kehamilan['status_kehamilan']); ?>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <!-- Informasi Kehamilan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-pink-50 rounded-xl p-4">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-pink-500"></i> Informasi Kehamilan
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Usia Kehamilan</span><span class="font-semibold"><?php echo $usia_text; ?> (<?php echo $usia_minggu; ?> minggu)</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">HPHT</span><span class="font-semibold"><?php echo date('d/m/Y', strtotime($kehamilan['hpht'])); ?></span></div>
                        <div class="flex justify-between"><span class="text-gray-500">HPL</span><span class="font-semibold"><?php echo date('d/m/Y', strtotime($kehamilan['hpl'])); ?> (<?php echo $sisa_hari; ?> hari lagi)</span></div>
                    </div>
                </div>
                <div class="bg-blue-50 rounded-xl p-4">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-heartbeat text-blue-500"></i> Data Kesehatan Ibu
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Berat Badan</span><span class="font-semibold"><?php echo $kehamilan['berat_badan_ibu']; ?> kg</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Tinggi Badan</span><span class="font-semibold"><?php echo $kehamilan['tinggi_badan_ibu']; ?> cm</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Tekanan Darah</span><span class="font-semibold"><?php echo $kehamilan['tekanan_darah']; ?></span></div>
                    </div>
                </div>
            </div>
            
            <!-- Catatan Kesehatan -->
            <?php if($kehamilan['catatan_kesehatan']): ?>
            <div class="bg-yellow-50 rounded-xl p-4 mb-8">
                <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                    <i class="fas fa-notes-medical text-yellow-500"></i> Catatan Kesehatan
                </h3>
                <p class="text-gray-700 text-sm"><?php echo nl2br(htmlspecialchars($kehamilan['catatan_kesehatan'])); ?></p>
            </div>
            <?php endif; ?>
            
            <!-- Riwayat Pemeriksaan -->
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-stethoscope text-green-500"></i> Riwayat Pemeriksaan Kehamilan
            </h3>
            
            <?php if(mysqli_num_rows($pemeriksaan) > 0): ?>
            <div class="space-y-3">
                <?php 
                $no = 1;
                while($row = mysqli_fetch_assoc($pemeriksaan)): 
                ?>
                <div class="border rounded-xl p-4 hover:shadow-md transition">
                    <div class="flex flex-wrap justify-between items-start gap-2">
                        <div>
                            <span class="font-semibold text-gray-800">Pemeriksaan ke-<?php echo $no; ?></span>
                            <span class="text-sm text-gray-500 ml-2"><?php echo date('d F Y', strtotime($row['tanggal_pemeriksaan'])); ?></span>
                            <span class="text-sm text-gray-500 ml-2">(Usia: <?php echo $row['usia_kehamilan']; ?> minggu)</span>
                        </div>
                        <a href="detail_periksa.php?id=<?php echo $row['id_pemeriksaan']; ?>&kehamilan_id=<?php echo $id_kehamilan; ?>" 
                           class="text-green-600 text-sm hover:text-green-700">
                            <i class="fas fa-eye mr-1"></i> Detail
                        </a>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-3 text-sm">
                        <div><span class="text-gray-500">Berat:</span> <?php echo $row['berat_badan']; ?> kg</div>
                        <div><span class="text-gray-500">TD:</span> <?php echo $row['tekanan_darah']; ?></div>
                        <?php if($row['tinggi_fundus'] && $row['tinggi_fundus'] > 0): ?>
                        <div><span class="text-gray-500">TFU:</span> <?php echo $row['tinggi_fundus']; ?> cm</div>
                        <?php endif; ?>
                        <?php if($row['detak_jantung_janin'] && $row['detak_jantung_janin'] > 0): ?>
                        <div><span class="text-gray-500">DJJ:</span> <?php echo $row['detak_jantung_janin']; ?> x/m</div>
                        <?php endif; ?>
                    </div>
                    <?php if($row['keluhan']): ?>
                    <div class="text-sm text-yellow-600 mt-2">Keluhan: <?php echo htmlspecialchars($row['keluhan']); ?></div>
                    <?php endif; ?>
                    <?php if($row['nama_bidan']): ?>
                    <div class="text-xs text-green-600 mt-2">
                        <i class="fas fa-user-nurse"></i> Bidan: <?php echo htmlspecialchars($row['nama_bidan']); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php 
                $no++;
                endwhile; 
                ?>
            </div>
            <?php else: ?>
            <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-xl">
                <i class="fas fa-stethoscope text-4xl mb-2 text-gray-300"></i>
                <p>Belum ada data pemeriksaan kehamilan</p>
            </div>
            <?php endif; ?>
            
            <!-- Tombol Kembali -->
            <div class="mt-6 flex gap-3">
                <a href="riwayat_hamil.php" class="flex-1 bg-gray-200 text-gray-700 text-center py-2 rounded-xl font-semibold hover:bg-gray-300 transition">
                Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>