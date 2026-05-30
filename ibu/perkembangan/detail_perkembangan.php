<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_ibu.php';
$title = 'Detail Perkembangan Anak';
include __DIR__ . '/../../templates/sidebar.php';

$nik = $_SESSION['nik'];

$anak_id = isset($_POST['anak_id']) ? (int)$_POST['anak_id'] : (isset($_GET['anak_id']) ? (int)$_GET['anak_id'] : 0);

$anak_list = mysqli_query($conn, "SELECT * FROM anak WHERE nik_ibu='$nik' ORDER BY created_at DESC");
$jumlah_anak = mysqli_num_rows($anak_list);

if($jumlah_anak == 1 && $anak_id == 0){
    $first_anak = mysqli_fetch_assoc($anak_list);
    $anak_id = $first_anak['id_anak'];
    mysqli_data_seek($anak_list, 0);
}

$anak_data = null;
if($anak_id > 0){
    $anak_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM anak WHERE id_anak = $anak_id AND nik_ibu = '$nik'"));
}

$perkembangan = [];
$chart_berat = [];
$chart_tinggi = [];
$chart_labels = [];

if($anak_id > 0 && $anak_data){
    $perkembangan = mysqli_query($conn, "SELECT hi.*, v.nama_vaksin, j.tanggal 
        FROM hasil_imunisasi hi 
        JOIN pendaftaran_imunisasi pi ON hi.id_pendaftaran = pi.id_pendaftaran 
        JOIN jadwal_imunisasi j ON pi.id_jadwal = j.id_jadwal 
        JOIN vaksin v ON j.id_vaksin = v.id_vaksin 
        WHERE pi.id_anak = $anak_id 
        ORDER BY hi.tgl_imunisasi ASC");
    
    $chart_query = mysqli_query($conn, "SELECT hi.tgl_imunisasi, hi.berat_badan, hi.tinggi_badan 
        FROM hasil_imunisasi hi 
        JOIN pendaftaran_imunisasi pi ON hi.id_pendaftaran = pi.id_pendaftaran 
        WHERE pi.id_anak = $anak_id 
        ORDER BY hi.tgl_imunisasi ASC");
    
    while($row = mysqli_fetch_assoc($chart_query)){
        $chart_labels[] = date('d/m/Y', strtotime($row['tgl_imunisasi']));
        $chart_berat[] = (float)$row['berat_badan'];
        $chart_tinggi[] = (float)$row['tinggi_badan'];
    }
}

$riwayat_imunisasi = [];
if($anak_id > 0 && $anak_data){
    $riwayat_imunisasi = mysqli_query($conn, "SELECT pi.*, pi.STATUS AS status_pendaftaran, v.nama_vaksin, j.tanggal, 
        hi.berat_badan, hi.tinggi_badan, hi.status_gizi, hi.tgl_imunisasi as tgl_hasil
        FROM pendaftaran_imunisasi pi 
        JOIN jadwal_imunisasi j ON pi.id_jadwal = j.id_jadwal 
        JOIN vaksin v ON j.id_vaksin = v.id_vaksin 
        LEFT JOIN hasil_imunisasi hi ON pi.id_pendaftaran = hi.id_pendaftaran 
        WHERE pi.id_anak = $anak_id 
        ORDER BY j.tanggal DESC");
}
?>

<form id="formDetailImunisasiPost" action="../imunisasi/detail_imunisasi.php" method="POST" style="display:none;">
    <input type="hidden" name="id_pendaftaran" id="idPendaftaranPost">
</form>

<div class="max-w-6xl mx-auto fade-in">
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h1 class="text-2xl font-bold text-green-800 mb-6">Detail Perkembangan Anak</h1>
        
        <?php if($jumlah_anak > 1): ?>
        <div class="mb-6">
            <form method="POST" class="flex gap-4">
                <select name="anak_id" class="flex-1 px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 bg-white text-sm" onchange="this.form.submit()">
                    <option disabled selected value="">Pilih Anak</option>
                    <?php mysqli_data_seek($anak_list, 0); while($a = mysqli_fetch_assoc($anak_list)): ?>
                    <option value="<?php echo $a['id_anak']; ?>" <?php echo $anak_id == $a['id_anak'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($a['nama_anak']); ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </form>
        </div>
        <?php endif; ?>
        
        <?php if($anak_id > 0 && $anak_data): 
            $usia = date_diff(date_create($anak_data['tanggal_lahir']), date_create('today'));
        ?>
        
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-100 rounded-xl p-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-500 rounded-full flex items-center justify-center text-white shadow-sm flex-shrink-0">
                    <i class="fas fa-child text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800 leading-tight"><?php echo htmlspecialchars($anak_data['nama_anak']); ?></h2>
                    <p class="text-xs text-gray-500 mt-1 font-medium">
                        Lahir: <?php echo formatTanggalIndonesia($anak_data['tanggal_lahir']); ?> | 
                        Jenis Kelamin: <?php echo $anak_data['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?> | 
                        Usia saat ini: <?php echo $usia->y; ?> tahun <?php echo $usia->m; ?> bulan
                    </p>
                </div>
            </div>
        </div>
        
        <?php if(!empty($chart_berat) && !empty($chart_tinggi)): ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                <h3 class="text-sm font-bold text-gray-700 mb-4"><i class="fas fa-weight-scale text-green-600 mr-2"></i> Grafik Berat Badan</h3>
                <canvas id="beratChart" height="250"></canvas>
            </div>
            <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                <h3 class="text-sm font-bold text-gray-700 mb-4"><i class="fas fa-ruler text-blue-600 mr-2"></i> Grafik Tinggi Badan</h3>
                <canvas id="tinggiChart" height="250"></canvas>
            </div>
        </div>
        <?php else: ?>
        <div class="text-center py-8 text-gray-400 bg-gray-50/50 border border-dashed rounded-xl mb-8 text-sm">
            <i class="fas fa-chart-line text-4xl mb-2 text-gray-300 block"></i>
            <p>Belum memiliki riwayat rekam medis pertumbuhan untuk memuat pemetaan grafik.</p>
        </div>
        <?php endif; ?>
        
        <h3 class="text-base font-bold text-gray-800 mb-3"><i class="fas fa-table text-green-600 mr-2"></i> Data Perkembangan (Hasil Imunisasi)</h3>
        <?php if(mysqli_num_rows($perkembangan) > 0): ?>
        <div class="overflow-x-auto mb-8 border border-gray-100 rounded-xl shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-600 uppercase tracking-wider">
                    <tr>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Vaksin</th>
                        <th class="p-3">Berat (kg)</th>
                        <th class="p-3">Tinggi (cm)</th>
                        <th class="p-3">Lingkar Kepala</th>
                        <th class="p-3">Status Gizi</th>
                        <th class="p-3">Nafsu Makan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs text-gray-700 bg-white">
                    <?php while($row = mysqli_fetch_assoc($perkembangan)): ?>
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="p-3 font-medium"><?php echo formatTanggalIndonesia($row['tgl_imunisasi']); ?></td>
                        <td class="p-3 font-bold text-gray-800"><?php echo htmlspecialchars($row['nama_vaksin']); ?></td>
                        <td class="p-3 font-bold text-green-600"><?php echo number_format($row['berat_badan'], 2); ?> kg</td>
                        <td class="p-3 font-bold text-blue-600"><?php echo number_format($row['tinggi_badan'], 2); ?> cm</td>
                        <td class="p-3"><?php echo number_format($row['lingkar_kepala'], 2); ?> cm</td>
                        <td class="p-3">
                            <span class="px-2.5 py-0.5 rounded-full font-semibold <?php echo $row['status_gizi']=='Normal'?'bg-green-100 text-green-700':'bg-yellow-100 text-yellow-700'; ?>">
                                <?php echo htmlspecialchars($row['status_gizi'] ?: 'Normal'); ?>
                            </span>
                        </td>
                        <td class="p-3 font-medium"><?php echo ucfirst($row['nafsu_makan']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-8 text-gray-400 bg-gray-50/50 border border-dashed rounded-xl mb-8 text-sm">
            <i class="fas fa-database text-4xl mb-2 text-gray-300 block"></i>
            <p>Belum ada rekapan riwayat perkembangan tumbuh kembang anak.</p>
        </div>
        <?php endif; ?>
        
        <h3 class="text-base font-bold text-gray-800 mb-3"><i class="fas fa-history text-blue-600 mr-2"></i> Riwayat Sesi Imunisasi</h3>
        <?php if(mysqli_num_rows($riwayat_imunisasi) > 0): ?>
        <div class="overflow-x-auto border border-gray-100 rounded-xl shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-600 uppercase tracking-wider">
                    <tr>
                        <th class="p-3">Tanggal Jadwal</th>
                        <th class="p-3">Vaksin</th>
                        <th class="p-3">Status Sesi</th>
                        <th class="p-3">Tanggal Tindakan</th>
                        <th class="p-3">Berat Periksa</th>
                        <th class="p-3">Status Gizi</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs text-gray-700 bg-white">
                    <?php while($row = mysqli_fetch_assoc($riwayat_imunisasi)): 
                        $status_curr = $row['status_pendaftaran']; // Menggunakan alias baru hasil perbaikan sensitivitas huruf
                    ?>
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="p-3 font-medium"><?php echo formatTanggalIndonesia($row['tanggal']); ?></td>
                        <td class="p-3 font-bold text-gray-800"><?php echo htmlspecialchars($row['nama_vaksin']); ?></td>
                        <td class="p-3">
                            <span class="px-2.5 py-0.5 rounded-full font-bold <?php 
                                echo $status_curr == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                    ($status_curr == 'selesai' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'); 
                            ?>">
                                <?php echo $status_curr == 'pending' ? 'Menunggu' : ($status_curr == 'selesai' ? 'Selesai' : 'Batal'); ?>
                            </span>
                        </td>
                        <td class="p-3"><?php echo $row['tgl_hasil'] ? formatTanggalIndonesia($row['tgl_hasil']) : '-'; ?></td>
                        <td class="p-3 font-semibold"><?php echo $row['berat_badan'] ? number_format($row['berat_badan'], 2) . ' kg' : '-'; ?></td>
                        <td class="p-3">
                            <?php if($row['status_gizi']): ?>
                            <span class="px-2.5 py-0.5 rounded-full font-semibold <?php echo $row['status_gizi']=='Normal'?'bg-green-100 text-green-700':'bg-yellow-100 text-yellow-700'; ?>">
                                <?php echo htmlspecialchars($row['status_gizi']); ?>
                            </span>
                            <?php else: ?>-<?php endif; ?>
                        </td>
                        <td class="p-3 text-center">
                            <button type="button" onclick="bukaDetailImunisasiPost('<?php echo $row['id_pendaftaran']; ?>')" 
                                    class="text-blue-600 hover:text-blue-800 font-bold inline-flex items-center gap-1 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-lg transition shadow-sm">
                                <i class="fas fa-eye text-[11px]"></i> Detail
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-8 text-gray-400 bg-gray-50/50 border border-dashed rounded-xl text-sm">
            <i class="fas fa-calendar-times text-4xl mb-2 text-gray-300 block"></i>
            <p>Belum ada riwayat keterlibatan pendaftaran jadwal imunisasi.</p>
        </div>
        <?php endif; ?>
        
        <!-- <div class="flex gap-3 mt-6">
            <a href="../imunisasi/riwayat_imunisasi.php" class="flex-1 bg-gray-100 text-gray-600 text-center py-2 rounded-xl font-semibold hover:bg-gray-200 transition shadow-sm text-sm">
                Kembali
            </a>
        </div> -->
        
        <?php elseif($anak_id > 0): ?>
        <div class="text-center py-12 text-gray-400 bg-gray-50/50 border border-dashed rounded-xl">
            <i class="fas fa-child text-6xl text-gray-300 mb-4 block"></i>
            <p class="text-base font-semibold">Data profil anak tidak terdaftar pada sistem Anda</p>
            <a href="list_anak.php" class="inline-block mt-3 text-sm text-green-600 hover:text-green-700 font-bold">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Data Anak
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if($anak_id > 0 && !empty($chart_berat) && !empty($chart_tinggi)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const beratCtx = document.getElementById('beratChart').getContext('2d');
new Chart(beratCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($chart_labels); ?>,
        datasets: [{
            label: 'Berat Badan (kg)',
            data: <?php echo json_encode($chart_berat); ?>,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.05)',
            borderWidth: 2,
            tension: 0.3,
            fill: true,
            pointBackgroundColor: '#10b981',
            pointBorderColor: '#fff',
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { position: 'top' }, tooltip: { callbacks: { label: function(context) { return context.raw + ' kg'; } } } },
        scales: { y: { beginAtZero: true, title: { display: true, text: 'Berat (kg)' } }, x: { title: { display: true, text: 'Tanggal Periksa' } } }
    }
});

const tinggiCtx = document.getElementById('tinggiChart').getContext('2d');
new Chart(tinggiCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($chart_labels); ?>,
        datasets: [{
            label: 'Tinggi Badan (cm)',
            data: <?php echo json_encode($chart_tinggi); ?>,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.05)',
            borderWidth: 2,
            tension: 0.3,
            fill: true,
            pointBackgroundColor: '#3b82f6',
            pointBorderColor: '#fff',
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { position: 'top' }, tooltip: { callbacks: { label: function(context) { return context.raw + ' cm'; } } } },
        scales: { y: { beginAtZero: true, title: { display: true, text: 'Tinggi (cm)' } }, x: { title: { display: true, text: 'Tanggal Periksa' } } }
    }
});
</script>
<?php endif; ?>

<script>
function bukaDetailImunisasiPost(idPendaftaran) {
    document.getElementById('idPendaftaranPost').value = idPendaftaran;
    document.getElementById('formDetailImunisasiPost').submit();
}
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>