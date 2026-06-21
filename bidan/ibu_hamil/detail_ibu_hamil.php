<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_bidan.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    $_SESSION['error'] = "Akses tidak sah!";
    header("Location: index.php");
    exit();
}

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

if (!$ibu) {
    $_SESSION['error'] = "Data ibu hamil tidak ditemukan!";
    header("Location: index.php");
    exit();
}

$query_pemeriksaan = "SELECT pk.*, u.nama_lengkap AS nama_bidan 
                      FROM pemeriksaan_kehamilan pk 
                      LEFT JOIN users u ON pk.petugas_nik = u.nik 
                      WHERE pk.id_kehamilan=$id 
                      ORDER BY pk.tanggal_pemeriksaan DESC";
$pemeriksaan = mysqli_query($conn, $query_pemeriksaan);

$title = 'Detail Ibu Hamil';
include __DIR__ . '/../../templates/sidebar.php';

function getStatusColor($status) {
    switch($status) {
        case 'aktif': return 'bg-green-100 text-green-700';
        case 'melahirkan': return 'bg-blue-100 text-blue-700';
        case 'keguguran': return 'bg-red-100 text-red-700';
        case 'pindah': return 'bg-orange-100 text-orange-700';
        default: return 'bg-gray-100 text-gray-700';
    }
}

function getStatusIcon($status) {
    switch($status) {
        case 'aktif': return '<i class="fas fa-check-circle mr-1"></i>';
        case 'melahirkan': return '<i class="fas fa-baby-carriage mr-1"></i>';
        case 'keguguran': return '<i class="fas fa-heart-broken mr-1"></i>';
        case 'pindah': return '<i class="fas fa-exchange-alt mr-1"></i>';
        default: return '<i class="fas fa-circle mr-1"></i>';
    }
}

function getStatusText($status) {
    switch($status) {
        case 'aktif': return 'Aktif';
        case 'melahirkan': return 'Sudah Melahirkan';
        case 'keguguran': return 'Keguguran';
        case 'pindah': return 'Pindah Posyandu';
        default: return ucfirst($status);
    }
}
?>

<div class="max-w-4xl mx-auto fade-in">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
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
                    <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($ibu['nama_lengkap']); ?></h1>
                    <p class="text-sm opacity-90">NIK: <?php echo htmlspecialchars($ibu['nik_ibu']); ?></p>
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
            <div class="bg-yellow-50 rounded-xl p-4 mb-6 border border-yellow-200">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-exchange-alt text-yellow-600"></i> Ubah Status Kehamilan
                </h3>
                <form method="POST" class="flex flex-col sm:flex-row gap-3 items-end">
                    <div class="flex-1 w-full">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Status Saat Ini</label>
                        <select name="status_kehamilan" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400 bg-white">
                            <option value="aktif" <?php echo $ibu['status_kehamilan'] == 'aktif' ? 'selected' : ''; ?>>Aktif (masih hamil)</option>
                            <option value="melahirkan" <?php echo $ibu['status_kehamilan'] == 'melahirkan' ? 'selected' : ''; ?>>Sudah Melahirkan</option>
                            <option value="keguguran" <?php echo $ibu['status_kehamilan'] == 'keguguran' ? 'selected' : ''; ?>>Keguguran</option>
                            <option value="pindah" <?php echo $ibu['status_kehamilan'] == 'pindah' ? 'selected' : ''; ?>>Pindah Posyandu</option>
                        </select>
                    </div>
                    <button type="submit" name="update_status" class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg transition font-semibold shadow-sm">
                        <i class="fas fa-save mr-2"></i> Update Status
                    </button>
                </form>
                <p class="text-xs text-gray-500 mt-2">
                    <i class="fas fa-info-circle mr-1"></i> Ubah status jika ibu sudah melahirkan, keguguran, atau pindah posyandu.
                </p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100">
                <div><label class="text-gray-500 text-xs font-bold uppercase">Usia Kehamilan</label><p class="font-semibold text-gray-800"><?php echo $ibu['usia_kehamilan']; ?> minggu</p></div>
                <div><label class="text-gray-500 text-xs font-bold uppercase">HPL</label><p class="font-semibold text-gray-800"><?php echo date('d/m/Y', strtotime($ibu['hpl'])); ?></p></div>
                <div><label class="text-gray-500 text-xs font-bold uppercase">HPHT</label><p class="font-semibold text-gray-800"><?php echo date('d/m/Y', strtotime($ibu['hpht'])); ?></p></div>
                <div><label class="text-gray-500 text-xs font-bold uppercase">Berat Badan</label><p class="font-semibold text-gray-800"><?php echo $ibu['berat_badan_ibu']; ?> kg</p></div>
                <div><label class="text-gray-500 text-xs font-bold uppercase">Tekanan Darah</label><p class="font-semibold text-gray-800"><?php echo $ibu['tekanan_darah']; ?></p></div>
                <div class="col-span-2"><label class="text-gray-500 text-xs font-bold uppercase">Alamat</label><p class="text-gray-700 text-sm mt-0.5"><?php echo htmlspecialchars($ibu['alamat']); ?></p></div>
            </div>
            
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-history text-green-600"></i> Riwayat Pemeriksaan
            </h3>
            
            <div class="space-y-3">
                <?php if(mysqli_num_rows($pemeriksaan) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($pemeriksaan)): ?>
                    <div class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition bg-white">
                        <div class="flex flex-wrap justify-between items-center gap-2 pb-2 border-b border-gray-100">
                            <div>
                                <span class="font-bold text-gray-800"><?php echo date('d/m/Y', strtotime($row['tanggal_pemeriksaan'])); ?></span>
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-md ml-2 font-semibold">Usia: <?php echo $row['usia_kehamilan']; ?> minggu</span>
                            </div>
                            
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <?php if ($row['petugas_nik'] == $_SESSION['nik']): ?>
                                    <a href="edit_pemeriksaan.php?id=<?php echo $row['id_pemeriksaan']; ?>&kehamilan_id=<?php echo $id; ?>" 
                                       class="text-blue-500 hover:text-blue-700 p-1 transition" title="Edit Pemeriksaan">
                                        <i class="fas fa-edit text-lg"></i>
                                    </a>
                                <?php else: ?>
                                    <button type="button" disabled 
                                            class="text-gray-300 p-1 cursor-not-allowed" 
                                            title="Hanya bisa diedit oleh bidan pemeriksa">
                                        <i class="fas fa-edit text-lg"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-2 mt-2 text-xs font-medium text-gray-600 bg-gray-50/70 p-2 rounded-lg">
                            <div><span class="text-gray-400">Berat:</span> <?php echo $row['berat_badan']; ?> kg</div>
                            <div><span class="text-gray-400">TD:</span> <?php echo $row['tekanan_darah']; ?></div>
                            <div><span class="text-gray-400">L. Perut:</span> <?php echo $row['lingkar_perut'] ?? '-'; ?> cm</div>
                            <div><span class="text-gray-400">TFU:</span> <?php echo $row['tinggi_fundus'] ?? '-'; ?> cm</div>
                            <div><span class="text-gray-400">DJJ:</span> <?php echo $row['detak_jantung_janin'] ?? '-'; ?> x/m</div>
                        </div>
                        
                        <?php if($row['keluhan']): ?>
                        <div class="text-xs text-yellow-700 bg-yellow-50/50 p-2 rounded-lg border border-yellow-100/60 mt-2"><strong>Keluhan:</strong> <?php echo htmlspecialchars($row['keluhan']); ?></div>
                        <?php endif; ?>
                        <?php if($row['tindakan']): ?>
                        <div class="text-xs text-green-700 bg-green-50/50 p-2 rounded-lg border border-green-100/60 mt-1"><strong>Edukasi/Tindakan:</strong> <?php echo htmlspecialchars($row['tindakan']); ?></div>
                        <?php endif; ?>

                        <div class="mt-2 pt-2 border-t border-gray-100 text-[11px] text-gray-400 flex items-center gap-1">
                            <i class="fas fa-user-md text-emerald-600"></i>
                            <span>Bidan Pemeriksa: <strong class="text-gray-600"><?php echo htmlspecialchars($row['nama_bidan'] ?? 'Tidak Tercatat'); ?></strong></span>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        <i class="fas fa-stethoscope text-4xl mb-2 text-gray-300"></i>
                        <p class="text-sm">Belum ada data pemeriksaan</p>
                        <a href="pemeriksaan.php?id=<?php echo $id; ?>" class="text-green-600 text-sm mt-2 inline-block font-semibold hover:underline">Tambah Pemeriksaan</a>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="flex gap-3 mt-6">
                <a href="index.php" class="flex-1 bg-gray-200 text-gray-700 text-center py-2.5 rounded-xl hover:bg-gray-300 transition font-semibold shadow-sm">
                    Kembali
                </a>
                <?php if($ibu['status_kehamilan'] == 'aktif'): ?>
                <a href="pemeriksaan.php?id=<?php echo $id; ?>" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-500 text-white text-center py-2.5 rounded-xl hover:shadow-lg transition font-semibold shadow-sm">
                    <i class="fas fa-plus mr-1"></i> Pemeriksaan Baru
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>