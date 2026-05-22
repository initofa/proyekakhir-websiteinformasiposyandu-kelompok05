<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_ibu.php';

$nik = $_SESSION['nik'];
$id_pemeriksaan = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$kehamilan_id = isset($_GET['kehamilan_id']) ? (int)$_GET['kehamilan_id'] : 0;

if($id_pemeriksaan == 0) {
    $_SESSION['error'] = "Data pemeriksaan tidak ditemukan!";
    header("Location: riwayat_hamil.php");
    exit();
}

// Ambil data pemeriksaan dengan JOIN yang benar
$query_pemeriksaan = "SELECT p.*, ih.nik_ibu, u.nama_lengkap as nama_bidan 
    FROM pemeriksaan_kehamilan p 
    INNER JOIN ibu_hamil ih ON p.id_kehamilan = ih.id_kehamilan 
    LEFT JOIN users u ON p.petugas_nik = u.nik 
    WHERE p.id_pemeriksaan = $id_pemeriksaan AND ih.nik_ibu = '$nik'";

$result_pemeriksaan = mysqli_query($conn, $query_pemeriksaan);

if(!$result_pemeriksaan){
    die("Query Error: " . mysqli_error($conn));
}

$pemeriksaan = mysqli_fetch_assoc($result_pemeriksaan);

if(!$pemeriksaan){
    $_SESSION['error'] = "Data pemeriksaan tidak ditemukan!";
    header("Location: riwayat_hamil.php");
    exit();
}

// PERBAIKAN: Hitung urutan pemeriksaan berdasarkan tanggal (diurutkan ASC)
$query_urutan = "SELECT 
    (SELECT COUNT(*) FROM pemeriksaan_kehamilan 
     WHERE id_kehamilan = $kehamilan_id 
     AND tanggal_pemeriksaan < '{$pemeriksaan['tanggal_pemeriksaan']}') 
     + 1 as urutan";
$result_urutan = mysqli_query($conn, $query_urutan);
$urutan_data = mysqli_fetch_assoc($result_urutan);
$urutan_ke = $urutan_data['urutan'];

$title = 'Detail Pemeriksaan';
include __DIR__ . '/../../templates/sidebar.php';
?>

<div class="max-w-3xl mx-auto fade-in">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-500 p-6 text-white">
            <h1 class="text-2xl font-bold">Detail Pemeriksaan Kehamilan</h1>
        </div>
        
        <div class="p-6">
            <!-- Badge nomor pemeriksaan -->
            <div class="flex justify-center mb-4">
                <div class="bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-sm font-semibold">
                    <i class="fas fa-stethoscope mr-2"></i> 
                    Pemeriksaan ke-<?php echo $urutan_ke; ?> dari kehamilan ini
                </div>
            </div>
            
            <!-- Tanggal Pemeriksaan -->
            <div class="bg-blue-50 rounded-xl p-4 mb-6 text-center">
                <p class="text-sm text-gray-500">Tanggal Pemeriksaan</p>
                <p class="text-2xl font-bold text-blue-600"><?php echo date('d F Y', strtotime($pemeriksaan['tanggal_pemeriksaan'])); ?></p>
                <p class="text-sm text-gray-500 mt-1">Usia kehamilan: <strong><?php echo $pemeriksaan['usia_kehamilan']; ?> minggu</strong></p>
            </div>
            
            <!-- Hasil Pemeriksaan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-green-50 rounded-xl p-4">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-weight-scale text-green-500"></i> Antropometri
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Berat Badan</span><span class="font-semibold"><?php echo $pemeriksaan['berat_badan']; ?> kg</span></div>
                        <?php if($pemeriksaan['lingkar_perut'] && $pemeriksaan['lingkar_perut'] > 0): ?>
                        <div class="flex justify-between"><span class="text-gray-500">Lingkar Perut</span><span class="font-semibold"><?php echo $pemeriksaan['lingkar_perut']; ?> cm</span></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="bg-purple-50 rounded-xl p-4">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-heartbeat text-purple-500"></i> Vital Sign & Janin
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Tekanan Darah</span><span class="font-semibold"><?php echo $pemeriksaan['tekanan_darah']; ?></span></div>
                        <?php if($pemeriksaan['tinggi_fundus'] && $pemeriksaan['tinggi_fundus'] > 0): ?>
                        <div class="flex justify-between"><span class="text-gray-500">Tinggi Fundus (TFU)</span><span class="font-semibold"><?php echo $pemeriksaan['tinggi_fundus']; ?> cm</span></div>
                        <?php endif; ?>
                        <?php if($pemeriksaan['detak_jantung_janin'] && $pemeriksaan['detak_jantung_janin'] > 0): ?>
                        <div class="flex justify-between"><span class="text-gray-500">Detak Jantung Janin (DJJ)</span><span class="font-semibold"><?php echo $pemeriksaan['detak_jantung_janin']; ?> x/menit</span></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Keluhan -->
            <?php if($pemeriksaan['keluhan']): ?>
            <div class="bg-yellow-50 rounded-xl p-4 mb-6">
                <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                    <i class="fas fa-comment-dots text-yellow-500"></i> Keluhan
                </h3>
                <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($pemeriksaan['keluhan'])); ?></p>
            </div>
            <?php endif; ?>
            
            <!-- Tindakan / Edukasi -->
            <?php if($pemeriksaan['tindakan']): ?>
            <div class="bg-green-50 rounded-xl p-4 mb-6">
                <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                    <i class="fas fa-chalkboard-teacher text-green-500"></i> Tindakan / Edukasi
                </h3>
                <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($pemeriksaan['tindakan'])); ?></p>
            </div>
            <?php endif; ?>
            
            <!-- Informasi Tambahan -->
            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                    <i class="fas fa-info-circle text-gray-500"></i> Informasi Tambahan
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-500">Tanggal Input</span>
                        <p class="font-semibold"><?php echo date('d/m/Y H:i', strtotime($pemeriksaan['created_at'])); ?></p>
                    </div>
                    <div>
                        <span class="text-gray-500">Diperiksa oleh</span>
                        <p class="font-semibold flex items-center gap-1">
                            <i class="fas fa-user-nurse text-green-600"></i>
                            <?php 
                            if(!empty($pemeriksaan['nama_bidan'])) {
                                echo htmlspecialchars($pemeriksaan['nama_bidan']);
                            } else {
                                echo 'Bidan';
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Tombol Aksi -->
            <div class="flex gap-3">
                <a href="detail_hamil.php?id=<?php echo $kehamilan_id; ?>" class="flex-1 bg-gray-200 text-gray-700 text-center py-2 rounded-xl font-semibold hover:bg-gray-300 transition">
                Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>