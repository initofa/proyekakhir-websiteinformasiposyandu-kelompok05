<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_bidan.php';
$id = $_GET['id'];

// PROSES UPDATE STATUS KEHAMILAN
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])){
    $status_baru = $_POST['status_kehamilan'];
    $updated_by = $_SESSION['nik'];
    
    $query = "UPDATE ibu_hamil SET status_kehamilan='$status_baru', updated_by='$updated_by' WHERE id_kehamilan=$id";
    
    if(mysqli_query($conn, $query)){
        $_SESSION['success'] = "Status kehamilan berhasil diubah!";
    } else {
        $_SESSION['error'] = "Gagal mengubah status kehamilan!";
    }
    header("Location: detail_ibu_hamil.php?id=$id");
    exit();
}

$ibu = mysqli_fetch_assoc(mysqli_query($conn, "SELECT ih.*, u.nama_lengkap, u.no_wa, u.alamat FROM ibu_hamil ih JOIN users u ON ih.nik_ibu=u.nik WHERE ih.id_kehamilan=$id"));
$pemeriksaan = mysqli_query($conn, "SELECT * FROM pemeriksaan_kehamilan WHERE id_kehamilan=$id ORDER BY tanggal_pemeriksaan DESC");
$title = 'Detail Ibu Hamil';
include __DIR__ . '/../../templates/sidebar.php';

// Fungsi untuk mendapatkan warna badge status
function getStatusColor($status) {
    switch($status) {
        case 'aktif':
            return 'bg-green-100 text-green-700';
        case 'melahirkan':
            return 'bg-blue-100 text-blue-700';
        case 'keguguran':
            return 'bg-red-100 text-red-700';
        case 'pindah':
            return 'bg-orange-100 text-orange-700';
        default:
            return 'bg-gray-100 text-gray-700';
    }
}

// Fungsi untuk mendapatkan icon status
function getStatusIcon($status) {
    switch($status) {
        case 'aktif':
            return '<i class="fas fa-check-circle mr-1"></i>';
        case 'melahirkan':
            return '<i class="fas fa-baby-carriage mr-1"></i>';
        case 'keguguran':
            return '<i class="fas fa-heart-broken mr-1"></i>';
        case 'pindah':
            return '<i class="fas fa-exchange-alt mr-1"></i>';
        default:
            return '<i class="fas fa-circle mr-1"></i>';
    }
}

// Fungsi untuk mendapatkan teks status
function getStatusText($status) {
    switch($status) {
        case 'aktif':
            return 'Aktif';
        case 'melahirkan':
            return 'Sudah Melahirkan';
        case 'keguguran':
            return 'Keguguran';
        case 'pindah':
            return 'Pindah Posyandu';
        default:
            return ucfirst($status);
    }
}
?>

<div class="max-w-4xl mx-auto fade-in">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <!-- Header dengan warna sesuai status -->
        <?php 
            $header_color = [
                'aktif' => 'from-green-600 to-emerald-500',
                'melahirkan' => 'from-blue-600 to-blue-500',
                'keguguran' => 'from-red-600 to-red-500',
                'pindah' => 'from-orange-600 to-orange-500'
            ];
            $header_class = $header_color[$ibu['status_kehamilan']] ?? 'from-green-600 to-emerald-500';
        ?>
        <div class="bg-gradient-to-r <?php echo $header_class; ?> p-6 text-white">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div>
                    <h1 class="text-2xl font-bold"><?php echo $ibu['nama_lengkap']; ?></h1>
                    <p>NIK: <?php echo $ibu['nik_ibu']; ?></p>
                </div>
                <div class="bg-white/20 rounded-xl px-4 py-2">
                    <p class="text-sm">Status Kehamilan</p>
                    <p class="font-semibold text-lg">
                        <?php echo getStatusIcon($ibu['status_kehamilan']); ?> <?php echo getStatusText($ibu['status_kehamilan']); ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <!-- Form Ubah Status Kehamilan -->
            <div class="bg-yellow-50 rounded-xl p-4 mb-6 border border-yellow-200">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-exchange-alt text-yellow-600"></i> Ubah Status Kehamilan
                </h3>
                <form method="POST" class="flex flex-col sm:flex-row gap-3 items-end">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Status Saat Ini</label>
                        <select name="status_kehamilan" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400">
                            <option value="aktif" <?php echo $ibu['status_kehamilan'] == 'aktif' ? 'selected' : ''; ?>>Aktif (masih hamil)</option>
                            <option value="melahirkan" <?php echo $ibu['status_kehamilan'] == 'melahirkan' ? 'selected' : ''; ?>>Sudah Melahirkan</option>
                            <option value="keguguran" <?php echo $ibu['status_kehamilan'] == 'keguguran' ? 'selected' : ''; ?>>Keguguran</option>
                            <option value="pindah" <?php echo $ibu['status_kehamilan'] == 'pindah' ? 'selected' : ''; ?>>Pindah Posyandu</option>
                        </select>
                    </div>
                    <button type="submit" name="update_status" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg transition">
                        <i class="fas fa-save mr-2"></i> Update Status
                    </button>
                </form>
                <p class="text-xs text-gray-500 mt-2">
                    <i class="fas fa-info-circle mr-1"></i> Ubah status jika ibu sudah melahirkan, keguguran, atau pindah posyandu.
                </p>
            </div>
            
            <!-- Data Ibu -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div><label class="text-gray-500 text-sm">Usia Kehamilan</label><p class="font-semibold"><?php echo $ibu['usia_kehamilan']; ?> minggu</p></div>
                <div><label class="text-gray-500 text-sm">HPL</label><p class="font-semibold"><?php echo date('d/m/Y', strtotime($ibu['hpl'])); ?></p></div>
                <div><label class="text-gray-500 text-sm">HPHT</label><p class="font-semibold"><?php echo date('d/m/Y', strtotime($ibu['hpht'])); ?></p></div>
                <div><label class="text-gray-500 text-sm">Berat Badan</label><p class="font-semibold"><?php echo $ibu['berat_badan_ibu']; ?> kg</p></div>
                <div><label class="text-gray-500 text-sm">Tekanan Darah</label><p class="font-semibold"><?php echo $ibu['tekanan_darah']; ?></p></div>
                <div class="col-span-2"><label class="text-gray-500 text-sm">Alamat</label><p><?php echo $ibu['alamat']; ?></p></div>
            </div>
            
            <!-- Riwayat Pemeriksaan -->
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 Riwayat Pemeriksaan</h3>
            <div class="space-y-3">
                <?php if(mysqli_num_rows($pemeriksaan) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($pemeriksaan)): ?>
                    <div class="border rounded-xl p-3 hover:shadow-md transition">
                        <div class="flex flex-wrap justify-between items-center gap-2">
                            <div>
                                <span class="font-semibold"><?php echo date('d/m/Y', strtotime($row['tanggal_pemeriksaan'])); ?></span>
                                <span class="text-sm text-gray-500 ml-2">Usia: <?php echo $row['usia_kehamilan']; ?> minggu</span>
                            </div>
                            <a href="edit_pemeriksaan.php?id=<?php echo $row['id_pemeriksaan']; ?>&kehamilan_id=<?php echo $id; ?>" 
                               class="text-blue-500 hover:text-blue-700" title="Edit Pemeriksaan">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-2 text-sm">
                            <div><span class="text-gray-500">Berat:</span> <?php echo $row['berat_badan']; ?> kg</div>
                            <div><span class="text-gray-500">TD:</span> <?php echo $row['tekanan_darah']; ?></div>
                            <?php if($row['lingkar_perut']): ?>
                            <div><span class="text-gray-500">Lingkar Perut:</span> <?php echo $row['lingkar_perut']; ?> cm</div>
                            <?php endif; ?>
                            <?php if($row['tinggi_fundus']): ?>
                            <div><span class="text-gray-500">TFU:</span> <?php echo $row['tinggi_fundus']; ?> cm</div>
                            <?php endif; ?>
                            <?php if($row['detak_jantung_janin']): ?>
                            <div><span class="text-gray-500">DJJ:</span> <?php echo $row['detak_jantung_janin']; ?> x/m</div>
                            <?php endif; ?>
                        </div>
                        <?php if($row['keluhan']): ?>
                        <div class="text-sm text-yellow-600 mt-2">Keluhan: <?php echo $row['keluhan']; ?></div>
                        <?php endif; ?>
                        <?php if($row['tindakan']): ?>
                        <div class="text-sm text-green-600 mt-1">Edukasi: <?php echo $row['tindakan']; ?></div>
                        <?php endif; ?>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-xl">
                        <i class="fas fa-stethoscope text-4xl mb-2 text-gray-300"></i>
                        <p>Belum ada data pemeriksaan</p>
                        <a href="pemeriksaan.php?id=<?php echo $id; ?>" class="text-green-600 text-sm mt-2 inline-block">Tambah Pemeriksaan</a>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="flex gap-3 mt-6">
                <a href="list_ibu_hamil.php" class="flex-1 bg-gray-200 text-gray-700 text-center py-2 rounded-xl hover:bg-gray-300 transition">
                Kembali
                </a>
                <?php if($ibu['status_kehamilan'] == 'aktif'): ?>
                <a href="pemeriksaan.php?id=<?php echo $id; ?>" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-500 text-white text-center py-2 rounded-xl hover:shadow-lg transition">
                Pemeriksaan
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>