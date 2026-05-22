<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_ibu.php';
$title = 'Detail Perkembangan Anak';
include __DIR__ . '/../../templates/sidebar.php';

$nik = $_SESSION['nik'];
$anak_id = isset($_GET['anak_id']) ? $_GET['anak_id'] : '';
$anak_list = mysqli_query($conn, "SELECT * FROM anak WHERE nik_ibu='$nik' ORDER BY created_at DESC");
$jumlah_anak = mysqli_num_rows($anak_list);

// Jika hanya 1 anak dan tidak ada parameter anak_id, otomatis pilih anak tersebut
if($jumlah_anak == 1 && empty($anak_id)){
    $first_anak = mysqli_fetch_assoc($anak_list);
    $anak_id = $first_anak['id_anak'];
    // Reset query karena sudah diambil
    mysqli_data_seek($anak_list, 0);
}

// Data anak terpilih
$anak_data = null;
if($anak_id){
    $anak_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM anak WHERE id_anak = $anak_id AND nik_ibu = '$nik'"));
}

// Data perkembangan (hasil imunisasi)
$perkembangan = [];
$chart_berat = [];
$chart_tinggi = [];
$chart_labels = [];

if($anak_id){
    $perkembangan = mysqli_query($conn, "SELECT hi.*, v.nama_vaksin, j.tanggal 
        FROM hasil_imunisasi hi 
        JOIN pendaftaran_imunisasi pi ON hi.id_pendaftaran = pi.id_pendaftaran 
        JOIN jadwal_imunisasi j ON pi.id_jadwal = j.id_jadwal 
        JOIN vaksin v ON j.id_vaksin = v.id_vaksin 
        WHERE pi.id_anak = $anak_id 
        ORDER BY hi.tgl_imunisasi ASC");
    
    // Data untuk chart
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

// Data riwayat imunisasi lengkap
$riwayat_imunisasi = [];
if($anak_id){
    $riwayat_imunisasi = mysqli_query($conn, "SELECT pi.*, v.nama_vaksin, j.tanggal, 
        hi.berat_badan, hi.tinggi_badan, hi.status_gizi, hi.tgl_imunisasi as tgl_hasil
        FROM pendaftaran_imunisasi pi 
        JOIN jadwal_imunisasi j ON pi.id_jadwal = j.id_jadwal 
        JOIN vaksin v ON j.id_vaksin = v.id_vaksin 
        LEFT JOIN hasil_imunisasi hi ON pi.id_pendaftaran = hi.id_pendaftaran 
        WHERE pi.id_anak = $anak_id 
        ORDER BY j.tanggal DESC");
}
?>

<div class="max-w-6xl mx-auto fade-in">
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h1 class="text-2xl font-bold text-green-800 mb-6">Detail Perkembangan Anak</h1>
        
        <!-- Pilih Anak (Hanya muncul jika lebih dari 1 anak) -->
        <?php if($jumlah_anak > 1): ?>
        <div class="mb-6">
            <form method="GET" class="flex gap-4">
                <select name="anak_id" class="flex-1 px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400" onchange="this.form.submit()">
                    <option disabled selected value="">Pilih Anak</option>
                    <?php mysqli_data_seek($anak_list, 0); while($a = mysqli_fetch_assoc($anak_list)): ?>
                    <option value="<?php echo $a['id_anak']; ?>" <?php echo $anak_id == $a['id_anak'] ? 'selected' : ''; ?>>
                        <?php echo $a['nama_anak']; ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </form>
        </div>
        <?php endif; ?>
        
        <?php if($anak_id && $anak_data): ?>
        
        <!-- Info Anak -->
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-child text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800"><?php echo $anak_data['nama_anak']; ?></h2>
                    <p class="text-gray-600">
                        Lahir: <?php echo date('d/m/Y', strtotime($anak_data['tanggal_lahir'])); ?> | 
                        <?php echo $anak_data['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Grafik Perkembangan -->
        <?php if(!empty($chart_berat) && !empty($chart_tinggi)): ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white border rounded-xl p-4 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-700 mb-4"><i class="fas fa-weight-scale text-green-600 mr-2"></i> Grafik Berat Badan</h3>
                <canvas id="beratChart" height="250"></canvas>
            </div>
            <div class="bg-white border rounded-xl p-4 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-700 mb-4"><i class="fas fa-ruler text-blue-600 mr-2"></i> Grafik Tinggi Badan</h3>
                <canvas id="tinggiChart" height="250"></canvas>
            </div>
        </div>
        <?php else: ?>
        <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-xl mb-8">
            <i class="fas fa-chart-line text-4xl mb-2 text-gray-300"></i>
            <p>Belum ada data grafik untuk anak ini</p>
        </div>
        <?php endif; ?>
        
        <!-- Tabel Perkembangan (Hasil Imunisasi) -->
        <h3 class="text-lg font-semibold text-gray-800 mb-4"><i class="fas fa-table text-green-600 mr-2"></i> Data Perkembangan (Hasil Imunisasi)</h3>
        <?php if(mysqli_num_rows($perkembangan) > 0): ?>
        <div class="overflow-x-auto mb-8">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left text-sm font-semibold text-gray-600">Tanggal</th>
                        <th class="p-3 text-left text-sm font-semibold text-gray-600">Vaksin</th>
                        <th class="p-3 text-left text-sm font-semibold text-gray-600">Berat (kg)</th>
                        <th class="p-3 text-left text-sm font-semibold text-gray-600">Tinggi (cm)</th>
                        <th class="p-3 text-left text-sm font-semibold text-gray-600">Lingkar Kepala</th>
                        <th class="p-3 text-left text-sm font-semibold text-gray-600">Status Gizi</th>
                        <th class="p-3 text-left text-sm font-semibold text-gray-600">Nafsu Makan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php while($row = mysqli_fetch_assoc($perkembangan)): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 text-sm"><?php echo date('d/m/Y', strtotime($row['tgl_imunisasi'])); ?></td>
                        <td class="p-3 text-sm font-semibold text-gray-800"><?php echo $row['nama_vaksin']; ?></td>
                        <td class="p-3 text-sm font-semibold text-green-600"><?php echo $row['berat_badan']; ?> kg</td>
                        <td class="p-3 text-sm font-semibold text-blue-600"><?php echo $row['tinggi_badan']; ?> cm</td>
                        <td class="p-3 text-sm"><?php echo $row['lingkar_kepala']; ?> cm</td>
                        <td class="p-3"><span class="px-2 py-1 rounded-full text-xs <?php echo $row['status_gizi']=='Normal'?'bg-green-100 text-green-700':'bg-yellow-100 text-yellow-700'; ?>"><?php echo $row['status_gizi']; ?></span></td>
                        <td class="p-3 text-sm"><?php echo ucfirst($row['nafsu_makan']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-xl mb-8">
            <i class="fas fa-database text-4xl mb-2 text-gray-300"></i>
            <p>Belum ada data hasil imunisasi untuk anak ini</p>
        </div>
        <?php endif; ?>
        
        <!-- Riwayat Imunisasi (Semua Pendaftaran) -->
        <h3 class="text-lg font-semibold text-gray-800 mb-4"><i class="fas fa-history text-blue-600 mr-2"></i> Riwayat Imunisasi</h3>
        <?php if(mysqli_num_rows($riwayat_imunisasi) > 0): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left text-sm font-semibold text-gray-600">Tanggal Jadwal</th>
                        <th class="p-3 text-left text-sm font-semibold text-gray-600">Vaksin</th>
                        <th class="p-3 text-left text-sm font-semibold text-gray-600">Status</th>
                        <th class="p-3 text-left text-sm font-semibold text-gray-600">Tanggal Imunisasi</th>
                        <th class="p-3 text-left text-sm font-semibold text-gray-600">Berat</th>
                        <th class="p-3 text-left text-sm font-semibold text-gray-600">Status Gizi</th>
                        <th class="p-3 text-left text-sm font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php while($row = mysqli_fetch_assoc($riwayat_imunisasi)): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 text-sm"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                        <td class="p-3 text-sm font-semibold text-gray-800"><?php echo $row['nama_vaksin']; ?></td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded-full text-xs <?php 
                                echo $row['status'] == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                    ($row['status'] == 'selesai' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'); 
                            ?>">
                                <?php echo $row['status'] == 'pending' ? 'Menunggu' : ($row['status'] == 'selesai' ? 'Selesai' : 'Batal'); ?>
                            </span>
                        </td>
                        <td class="p-3 text-sm"><?php echo $row['tgl_hasil'] ? date('d/m/Y', strtotime($row['tgl_hasil'])) : '-'; ?></td>
                        <td class="p-3 text-sm"><?php echo $row['berat_badan'] ? $row['berat_badan'] . ' kg' : '-'; ?></td>
                        <td class="p-3">
                            <?php if($row['status_gizi']): ?>
                            <span class="px-2 py-1 rounded-full text-xs <?php echo $row['status_gizi']=='Normal'?'bg-green-100 text-green-700':'bg-yellow-100 text-yellow-700'; ?>">
                                <?php echo $row['status_gizi']; ?>
                            </span>
                            <?php else: ?>-<?php endif; ?>
                        </td>
                        <td class="p-3">
                            <a href="../imunisasi/detail_imunisasi.php?id=<?php echo $row['id_pendaftaran']; ?>" 
                               class="text-blue-500 hover:text-blue-700" title="Lihat Detail">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-xl">
            <i class="fas fa-calendar-times text-4xl mb-2 text-gray-300"></i>
            <p>Belum ada riwayat imunisasi untuk anak ini</p>
        </div>
        <?php endif; ?>
        
        <!-- Tombol Aksi -->
        <div class="flex gap-3 mt-6">
            <a href="../imunisasi/riwayat_imunisasi.php" class="flex-1 bg-gray-200 text-gray-700 text-center py-2 rounded-xl hover:bg-gray-300 transition">
            Kembali
            </a>
        </div>
        
        <?php elseif($anak_id): ?>
        <div class="text-center py-12 text-gray-500 bg-gray-50 rounded-xl">
            <i class="fas fa-child text-6xl text-gray-300 mb-4"></i>
            <p class="text-lg">Data anak tidak ditemukan</p>
            <a href="list_anak.php" class="inline-block mt-4 text-green-600 hover:text-green-700">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Data Anak
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if($anak_id && !empty($chart_berat) && !empty($chart_tinggi)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Grafik Berat Badan
const beratCtx = document.getElementById('beratChart').getContext('2d');
new Chart(beratCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($chart_labels); ?>,
        datasets: [{
            label: 'Berat Badan (kg)',
            data: <?php echo json_encode($chart_berat); ?>,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
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
        scales: { y: { beginAtZero: true, title: { display: true, text: 'Berat (kg)' } }, x: { title: { display: true, text: 'Tanggal' } } }
    }
});

// Grafik Tinggi Badan
const tinggiCtx = document.getElementById('tinggiChart').getContext('2d');
new Chart(tinggiCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($chart_labels); ?>,
        datasets: [{
            label: 'Tinggi Badan (cm)',
            data: <?php echo json_encode($chart_tinggi); ?>,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
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
        scales: { y: { beginAtZero: true, title: { display: true, text: 'Tinggi (cm)' } }, x: { title: { display: true, text: 'Tanggal' } } }
    }
});
</script>
<?php endif; ?>

<?php include __DIR__ . '/../../templates/footer.php'; ?>