<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';

$title = 'Detail & Riwayat Perkembangan Anak';
include __DIR__ . '/../../templates/sidebar.php';

$id_anak = isset($_POST['id_anak']) ? (int)$_POST['id_anak'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);

if($id_anak === 0){
    $_SESSION['error'] = "Akses tidak sah atau ID Anak tidak ditemukan!";
    header("Location: list_anak.php");
    exit();
}

$query_profil = "SELECT a.*, u.nama_lengkap as nama_ibu, u.no_wa, u.alamat 
                 FROM anak a 
                 LEFT JOIN users u ON a.nik_ibu = u.nik 
                 WHERE a.id_anak = $id_anak";
$result_profil = mysqli_query($conn, $query_profil);
$anak_data = mysqli_fetch_assoc($result_profil);

if(!$anak_data) {
    $_SESSION['error'] = "Data profil anak tidak ditemukan dalam sistem!";
    header("Location: list_anak.php");
    exit();
}

// Hitung usia riil anak dari tanggal lahir hingga hari ini
$usia = date_diff(date_create($anak_data['tanggal_lahir']), date_create('today'));

// 2. Ambil data rekam pertumbuhan (hasil pemeriksaan medis selesai) untuk Tabel Perkembangan
$perkembangan = mysqli_query($conn, "SELECT hi.*, v.nama_vaksin, j.tanggal 
    FROM hasil_imunisasi hi 
    JOIN pendaftaran_imunisasi pi ON hi.id_pendaftaran = pi.id_pendaftaran 
    JOIN jadwal_imunisasi j ON pi.id_jadwal = j.id_jadwal 
    JOIN vaksin v ON j.id_vaksin = v.id_vaksin 
    WHERE pi.id_anak = $id_anak 
    ORDER BY hi.tgl_imunisasi ASC");

// 3. Ambil data koordinat pertumbuhan khusus untuk pemetaan Chart.js
$chart_berat = [];
$chart_tinggi = [];
$chart_labels = [];

$chart_query = mysqli_query($conn, "SELECT hi.tgl_imunisasi, hi.berat_badan, hi.tinggi_badan 
    FROM hasil_imunisasi hi 
    JOIN pendaftaran_imunisasi pi ON hi.id_pendaftaran = pi.id_pendaftaran 
    WHERE pi.id_anak = $id_anak 
    ORDER BY hi.tgl_imunisasi ASC");

while($row = mysqli_fetch_assoc($chart_query)){
    $chart_labels[] = date('d/m/Y', strtotime($row['tgl_imunisasi']));
    $chart_berat[] = (float)$row['berat_badan'];
    $chart_tinggi[] = (float)$row['tinggi_badan'];
}

// 4. Ambil arsip data seluruh pendaftaran sesi imunisasi lengkap (pending, selesai, batal)
$riwayat_imunisasi = mysqli_query($conn, "SELECT pi.*, pi.STATUS AS status_pendaftaran, v.nama_vaksin, j.tanggal, 
    hi.berat_badan, hi.tinggi_badan, hi.status_gizi, hi.tgl_imunisasi as tgl_hasil
    FROM pendaftaran_imunisasi pi 
    JOIN jadwal_imunisasi j ON pi.id_jadwal = j.id_jadwal 
    JOIN vaksin v ON j.id_vaksin = v.id_vaksin 
    LEFT JOIN hasil_imunisasi hi ON pi.id_pendaftaran = hi.id_pendaftaran 
    WHERE pi.id_anak = $id_anak 
    ORDER BY j.tanggal DESC");
?>

<!-- Form Tersembunyi Global untuk Mengirim ID Pendaftaran via POST ke Halaman Detail Imunisasi Admin -->
<form id="formDetailImunisasiPost" action="detail_imunisasi.php" method="POST" style="display:none;">
    <input type="hidden" name="id_pendaftaran" id="idPendaftaranPost">
    <input type="hidden" name="id_anak_asal" value="<?php echo $id_anak; ?>">
</form>

<div class="max-w-6xl mx-auto fade-in">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <h1 class="text-2xl font-bold text-green-800 mb-6">Detail Perkembangan Anak</h1>
        
        <!-- Kartu Informasi Identitas Utama Anak -->
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-100 rounded-xl p-4 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-500 rounded-full flex items-center justify-center text-white shadow-sm flex-shrink-0">
                        <i class="fas fa-child text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 leading-tight"><?php echo htmlspecialchars($anak_data['nama_anak']); ?></h2>
                        <p class="text-xs text-gray-500 mt-1 font-medium">
                            Lahir: <strong><?php echo formatTanggalIndonesia($anak_data['tanggal_lahir']); ?></strong> | 
                            Jenis Kelamin: <strong><?php echo $anak_data['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></strong> | 
                            Usia Saat Ini: <strong class="text-green-700"><?php echo $usia->y; ?> tahun <?php echo $usia->m; ?> bulan</strong>
                        </p>
                    </div>
                </div>
                <div class="text-xs text-gray-500 font-medium bg-white/80 p-3 rounded-lg border border-green-100/40 space-y-1">
                    <p><i class="fas fa-female text-gray-400 w-4 text-center"></i> Ibu: <span class="text-gray-800 font-bold"><?php echo htmlspecialchars($anak_data['nama_ibu']); ?></span></p>
                    <p><i class="fab fa-whatsapp text-gray-400 w-4 text-center"></i> WA: <span class="text-gray-800 font-bold"><?php echo htmlspecialchars($anak_data['no_wa'] ?: '-'); ?></span></p>
                    <p><i class="fas fa-map-marker-alt text-gray-400 w-4 text-center"></i> Alamat: <span class="text-gray-800 font-bold"><?php echo htmlspecialchars($anak_data['alamat'] ?: '-'); ?></span></p>
                </div>
            </div>
        </div>
        
        <!-- Blok Pemetaan Grafik Pertumbuhan (Chart.js) -->
        <?php if(!empty($chart_berat) && !empty($chart_tinggi)): ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                <h3 class="text-sm font-bold text-gray-700 mb-4"><i class="fas fa-weight-scale text-green-600 mr-2"></i> Grafik Berat Badan (Klinik Admin)</h3>
                <canvas id="beratChart" height="250"></canvas>
            </div>
            <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                <h3 class="text-sm font-bold text-gray-700 mb-4"><i class="fas fa-ruler text-blue-600 mr-2"></i> Grafik Tinggi Badan (Klinik Admin)</h3>
                <canvas id="tinggiChart" height="250"></canvas>
            </div>
        </div>
        <?php else: ?>
        <div class="text-center py-8 text-gray-400 bg-gray-50/50 border border-dashed rounded-xl mb-8 text-sm">
            <i class="fas fa-chart-line text-4xl mb-2 text-gray-300 block"></i>
            <p>Belum memiliki riwayat rekam medis pertumbuhan untuk memuat pemetaan grafik visual.</p>
        </div>
        <?php endif; ?>
        
        <!-- Tabel Rekam Tumbuh Kembang (Hasil Tindakan) -->
        <h3 class="text-base font-bold text-gray-800 mb-3"><i class="fas fa-table text-green-600 mr-2"></i> Data Rekam Antropometri (Hasil Imunisasi)</h3>
        <?php if(mysqli_num_rows($perkembangan) > 0): ?>
        <div class="overflow-x-auto mb-8 border border-gray-100 rounded-xl shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-600 uppercase tracking-wider">
                    <tr>
                        <th class="p-3">Tanggal Periksa</th>
                        <th class="p-3">Vaksin / Tindakan</th>
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
                        <td class="p-3 font-bold text-blue-600"><?php echo number_format($row['tinggi_badan'], 1); ?> cm</td>
                        <td class="p-3 font-semibold text-gray-700"><?php echo number_format($row['lingkar_kepala'], 1); ?> cm</td>
                        <td class="p-3">
                            <span class="px-2.5 py-0.5 rounded-full font-semibold <?php echo $row['status_gizi']=='Normal'?'bg-green-100 text-green-700':'bg-yellow-100 text-yellow-700'; ?>">
                                <?php echo htmlspecialchars($row['status_gizi'] ?: 'Normal'); ?>
                            </span>
                        </td>
                        <td class="p-3 font-medium"><?php echo ucfirst($row['nafsu_makan'] ?: '-'); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-8 text-gray-400 bg-gray-50/50 border border-dashed rounded-xl mb-8 text-sm">
            <i class="fas fa-database text-4xl mb-2 text-gray-300 block"></i>
            <p>Belum ada berkas rekam medis hasil pemeriksaan pertumbuhan yang tercatat untuk anak ini.</p>
        </div>
        <?php endif; ?>
        
        <!-- Riwayat Pendaftaran Jadwal Sesi -->
        <h3 class="text-base font-bold text-gray-800 mb-3"><i class="fas fa-history text-blue-600 mr-2"></i> Riwayat Registrasi Kunjungan Imunisasi</h3>
        <?php if(mysqli_num_rows($riwayat_imunisasi) > 0): ?>
        <div class="overflow-x-auto border border-gray-100 rounded-xl shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-600 uppercase tracking-wider">
                    <tr>
                        <th class="p-3">Tanggal Agenda</th>
                        <th class="p-3">Vaksin</th>
                        <th class="p-3">Status Sesi</th>
                        <th class="p-3">Tanggal Pelaksanaan</th>
                        <th class="p-3">Berat Timbang</th>
                        <th class="p-3">Status Gizi</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs text-gray-700 bg-white">
                    <?php while($row = mysqli_fetch_assoc($riwayat_imunisasi)): 
                        $status_curr = $row['status_pendaftaran'];
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
                        <td class="p-3 font-medium"><?php echo $row['tgl_hasil'] ? formatTanggalIndonesia($row['tgl_hasil']) : '-'; ?></td>
                        <td class="p-3 font-semibold text-gray-700"><?php echo $row['berat_badan'] ? number_format($row['berat_badan'], 2) . ' kg' : '-'; ?></td>
                        <td class="p-3">
                            <?php if($row['status_gizi']): ?>
                            <span class="px-2.5 py-0.5 rounded-full font-semibold <?php echo $row['status_gizi']=='Normal'?'bg-green-100 text-green-700':'bg-yellow-100 text-yellow-700'; ?>">
                                <?php echo htmlspecialchars($row['status_gizi']); ?>
                            </span>
                            <?php else: ?>-<?php endif; ?>
                        </td>
                        <td class="p-3 text-center">
                            <!-- PERUBAHAN: Tombol memicu POST tersembunyi ke detail_imunisasi.php milik admin -->
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
            <p>Belum ada riwayat pendaftaran kunjungan imunisasi untuk anak balita ini.</p>
        </div>
        <?php endif; ?>
        
        <div class="flex gap-3 mt-6">
            <a href="list_anak.php" class="flex-1 bg-gray-100 text-gray-600 text-center py-2.5 rounded-xl font-bold hover:bg-gray-200 transition shadow-sm text-sm">
            Kembali
            </a>
        </div>
    </div>
</div>

<?php if($id_anak > 0 && !empty($chart_berat) && !empty($chart_tinggi)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Peta Grafik Timbang Berat Badan
const beratCtx = document.getElementById('beratChart').getContext('2d');
new Chart(beratCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($chart_labels); ?>,
        datasets: [{
            label: 'Berat Badan (kg)',
            data: <?php echo json_encode($chart_berat); ?>,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.04)',
            borderWidth: 2,
            tension: 0.25,
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
        scales: { y: { beginAtZero: true, title: { display: true, text: 'Massa Berat (kg)' } }, x: { title: { display: true, text: 'Tanggal Rekam Medis' } } }
    }
});

// Peta Grafik Ukur Tinggi Badan
const tinggiCtx = document.getElementById('tinggiChart').getContext('2d');
new Chart(tinggiCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($chart_labels); ?>,
        datasets: [{
            label: 'Tinggi Badan (cm)',
            data: <?php echo json_encode($chart_tinggi); ?>,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.04)',
            borderWidth: 2,
            tension: 0.25,
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
        scales: { y: { beginAtZero: true, title: { display: true, text: 'Tinggi Badan (cm)' } }, x: { title: { display: true, text: 'Tanggal Rekam Medis' } } }
    }
});
</script>
<?php endif; ?>

<script>
// Fungsi pemicu manipulasi DOM Form POST tersembunyi ke detail_imunisasi.php milik admin
function bukaDetailImunisasiPost(idPendaftaran) {
    document.getElementById('idPendaftaranPost').value = idPendaftaran;
    document.getElementById('formDetailImunisasiPost').submit();
}
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>