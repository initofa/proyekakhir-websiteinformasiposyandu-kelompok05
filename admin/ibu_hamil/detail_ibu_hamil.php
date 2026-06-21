<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';
$title = 'Detail Ibu Hamil';
include __DIR__ . '/../../templates/sidebar.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    $_SESSION['error'] = "Akses tidak sah!";
    header("Location: index.php");
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
                      WHERE pk.id_kehamilan = $id 
                      ORDER BY pk.tanggal_pemeriksaan DESC";

$pemeriksaan = mysqli_query($conn, $query_pemeriksaan);
?>

<div class="max-w-4xl mx-auto fade-in">
    <div class="mb-4">
        <a href="index.php" class="inline-flex items-center gap-2 text-green-600 hover:text-green-700 transition">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Ibu Hamil
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-emerald-500 p-6 text-white">
            <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($ibu['nama_lengkap']); ?></h1>
            <p class="text-green-100 text-sm mt-1">NIK: <?php echo htmlspecialchars($ibu['nik_ibu']); ?></p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100">
                <div><label class="text-gray-400 text-xs font-bold uppercase">Usia Kehamilan</label><p class="font-semibold text-gray-800"><?php echo htmlspecialchars($ibu['usia_kehamilan']); ?> minggu</p></div>
                <div><label class="text-gray-400 text-xs font-bold uppercase">HPL (Perkiraan)</label><p class="font-semibold text-gray-800"><?php echo date('d/m/Y', strtotime($ibu['hpl'])); ?></p></div>
                <div><label class="text-gray-400 text-xs font-bold uppercase">HPHT</label><p class="font-semibold text-gray-800"><?php echo date('d/m/Y', strtotime($ibu['hpht'])); ?></p></div>
                <div><label class="text-gray-400 text-xs font-bold uppercase">Berat Awal</label><p class="font-semibold text-gray-800"><?php echo htmlspecialchars($ibu['berat_badan_ibu']); ?> kg</p></div>
                <div><label class="text-gray-400 text-xs font-bold uppercase">Tekanan Darah</label><p class="font-semibold text-gray-800"><?php echo htmlspecialchars($ibu['tekanan_darah']); ?></p></div>
                <div class="col-span-2"><label class="text-gray-400 text-xs font-bold uppercase">Alamat</label><p class="text-gray-700 text-sm mt-0.5"><?php echo htmlspecialchars($ibu['alamat']); ?></p></div>
            </div>
            
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-history text-green-600"></i> Riwayat Pemeriksaan Kehamilan
            </h3>
            
            <div class="space-y-3">
                <?php if(mysqli_num_rows($pemeriksaan) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($pemeriksaan)): ?>
                <div class="border border-gray-100 rounded-xl p-4 hover:shadow-md transition bg-white">
                    <div class="flex flex-wrap justify-between items-center gap-2 pb-2 border-b border-gray-50">
                        <div>
                            <span class="font-bold text-gray-800"><?php echo date('d/m/Y', strtotime($row['tanggal_pemeriksaan'])); ?></span>
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-md ml-2 font-semibold">Usia: <?php echo $row['usia_kehamilan']; ?> minggu</span>
                        </div>
                        <div class="text-xs text-gray-500 font-medium bg-gray-100 px-3 py-1 rounded-lg">
                            BB: <?php echo $row['berat_badan']; ?> kg | TD: <?php echo $row['tekanan_darah']; ?>
                        </div>
                    </div>
                    
                    <?php if($row['tinggi_fundus']): ?>
                    <div class="text-xs text-gray-600 mt-2 flex gap-4">
                        <span><strong>TFU:</strong> <?php echo $row['tinggi_fundus']; ?> cm</span>
                        <span><strong>DJJ:</strong> <?php echo $row['detak_jantung_janin']; ?> x/menit</span>
                        <?php if($row['lingkar_perut']): ?>
                        <span><strong>LP:</strong> <?php echo $row['lingkar_perut']; ?> cm</span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($row['keluhan'] || $row['tindakan']): ?>
                    <div class="mt-2 text-xs grid grid-cols-1 md:grid-cols-2 gap-2 bg-yellow-50/50 p-2 rounded-lg border border-yellow-100/40">
                        <?php if($row['keluhan']): ?>
                        <div class="text-yellow-700"><strong>Keluhan:</strong> <?php echo htmlspecialchars($row['keluhan']); ?></div>
                        <?php endif; ?>
                        <?php if($row['tindakan']): ?>
                        <div class="text-green-700"><strong>Tindakan:</strong> <?php echo htmlspecialchars($row['tindakan']); ?></div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <div class="mt-3 pt-2 border-t border-gray-50 flex items-center justify-between text-[11px] text-gray-400">
                        <div class="flex items-center gap-1">
                            <i class="fas fa-user-md text-emerald-600"></i>
                            <span>Pemeriksa: <strong class="text-gray-600"><?php echo htmlspecialchars($row['nama_bidan'] ?? 'Tidak Tercatat'); ?></strong></span>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
                <?php else: ?>
                <div class="text-center py-8 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                    <i class="fas fa-notes-medical text-3xl text-gray-300 mb-2"></i>
                    <p class="text-sm text-gray-400">Belum ada riwayat pemeriksaan kehamilan.</p>
                </div>
                <?php endif; ?>
            </div>
            
            <a href="index.php" class="mt-6 w-full text-center inline-block bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2.5 rounded-xl font-semibold transition shadow-sm">Kembali</a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>