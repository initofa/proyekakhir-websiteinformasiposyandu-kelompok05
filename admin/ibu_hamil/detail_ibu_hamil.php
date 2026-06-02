<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';
$title = 'Detail Ibu Hamil';
include __DIR__ . '/../../templates/sidebar.php';

$id = $_GET['id'];
$ibu = mysqli_fetch_assoc(mysqli_query($conn, "SELECT ih.*, u.nama_lengkap, u.no_wa, u.alamat FROM ibu_hamil ih JOIN users u ON ih.nik_ibu=u.nik WHERE ih.id_kehamilan=$id"));
$pemeriksaan = mysqli_query($conn, "SELECT * FROM pemeriksaan_kehamilan WHERE id_kehamilan=$id ORDER BY tanggal_pemeriksaan DESC");
?>

<div class="max-w-4xl mx-auto fade-in">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-emerald-500 p-6 text-white">
            <h1 class="text-2xl font-bold"><?php echo $ibu['nama_lengkap']; ?></h1>
            <p>NIK: <?php echo $ibu['nik_ibu']; ?></p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div><label class="text-gray-500 text-sm">Usia Kehamilan</label><p class="font-semibold"><?php echo $ibu['usia_kehamilan']; ?> minggu</p></div>
                <div><label class="text-gray-500 text-sm">HPL</label><p class="font-semibold"><?php echo date('d/m/Y', strtotime($ibu['hpl'])); ?></p></div>
                <div><label class="text-gray-500 text-sm">HPHT</label><p class="font-semibold"><?php echo date('d/m/Y', strtotime($ibu['hpht'])); ?></p></div>
                <div><label class="text-gray-500 text-sm">Berat Badan</label><p class="font-semibold"><?php echo $ibu['berat_badan_ibu']; ?> kg</p></div>
                <div><label class="text-gray-500 text-sm">Tekanan Darah</label><p class="font-semibold"><?php echo $ibu['tekanan_darah']; ?></p></div>
                <div class="col-span-2"><label class="text-gray-500 text-sm">Alamat</label><p><?php echo $ibu['alamat']; ?></p></div>
            </div>
            
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Pemeriksaan</h3>
            <div class="space-y-3">
                <?php while($row = mysqli_fetch_assoc($pemeriksaan)): ?>
                <div class="border rounded-xl p-3 hover:shadow-md transition">
                    <div class="flex flex-wrap justify-between items-center gap-2">
                        <div><span class="font-semibold"><?php echo date('d/m/Y', strtotime($row['tanggal_pemeriksaan'])); ?></span><span class="text-sm text-gray-500 ml-2">Usia: <?php echo $row['usia_kehamilan']; ?> m</span></div>
                        <div class="text-sm">Berat: <?php echo $row['berat_badan']; ?> kg | TD: <?php echo $row['tekanan_darah']; ?></div>
                    </div>
                    <?php if($row['tinggi_fundus']): ?>
                    <div class="text-sm text-gray-600 mt-1">TFU: <?php echo $row['tinggi_fundus']; ?> cm | DJJ: <?php echo $row['detak_jantung_janin']; ?> x/m</div>
                    <?php endif; ?>
                    <?php if($row['keluhan']): ?>
                    <div class="text-sm text-yellow-600 mt-1">Keluhan: <?php echo $row['keluhan']; ?></div>
                    <?php endif; ?>
                </div>
                <?php endwhile; ?>
            </div>
            <a href="index.php" class="mt-6 w-full text-center inline-block bg-gray-200 text-gray-700 px-6 py-2 rounded-xl hover:bg-gray-300 transition">Kembali</a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>