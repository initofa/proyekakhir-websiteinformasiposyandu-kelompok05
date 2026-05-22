<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_bidan.php';

$id_pendaftaran = $_GET['id'];

// Ambil data hasil imunisasi
$hasil = mysqli_fetch_assoc(mysqli_query($conn, "SELECT hi.*, a.nama_anak, a.tanggal_lahir, a.berat_lahir, a.panjang_lahir, u.nama_lengkap as nama_ibu, u.no_wa, v.nama_vaksin, j.tanggal
    FROM hasil_imunisasi hi 
    JOIN pendaftaran_imunisasi pi ON hi.id_pendaftaran = pi.id_pendaftaran
    JOIN anak a ON pi.id_anak = a.id_anak
    JOIN users u ON a.nik_ibu = u.nik
    JOIN jadwal_imunisasi j ON pi.id_jadwal = j.id_jadwal
    JOIN vaksin v ON j.id_vaksin = v.id_vaksin
    WHERE pi.id_pendaftaran = $id_pendaftaran"));

// Ambil riwayat imunisasi anak
$riwayat = mysqli_query($conn, "SELECT pi.*, v.nama_vaksin, j.tanggal, hi.berat_badan, hi.tinggi_badan, hi.status_gizi, hi.tgl_imunisasi
    FROM pendaftaran_imunisasi pi 
    JOIN jadwal_imunisasi j ON pi.id_jadwal = j.id_jadwal 
    JOIN vaksin v ON j.id_vaksin = v.id_vaksin 
    LEFT JOIN hasil_imunisasi hi ON pi.id_pendaftaran = hi.id_pendaftaran
    WHERE pi.id_anak = (SELECT id_anak FROM pendaftaran_imunisasi WHERE id_pendaftaran = $id_pendaftaran)
    ORDER BY j.tanggal DESC");

// Ambil data perkembangan (grafik)
$perkembangan = mysqli_query($conn, "SELECT hi.tgl_imunisasi, hi.berat_badan, hi.tinggi_badan 
    FROM hasil_imunisasi hi 
    JOIN pendaftaran_imunisasi pi ON hi.id_pendaftaran = pi.id_pendaftaran
    WHERE pi.id_anak = (SELECT id_anak FROM pendaftaran_imunisasi WHERE id_pendaftaran = $id_pendaftaran)
    ORDER BY hi.tgl_imunisasi ASC");
$chart_data = [];
while($row = mysqli_fetch_assoc($perkembangan)){
    $chart_data[] = $row;
}
$title = 'Detail Hasil Imunisasi';
include __DIR__ . '/../../templates/sidebar.php';
?>

<div class="max-w-6xl mx-auto fade-in">
    <!-- Detail Hasil Imunisasi -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <h1 class="text-2xl font-bold text-green-800 mb-6">Detail Hasil Imunisasi</h1>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div><label class="text-gray-500 text-sm">Nama Anak</label><p class="font-semibold text-gray-800"><?php echo $hasil['nama_anak']; ?></p></div>
            <div><label class="text-gray-500 text-sm">Nama Ibu</label><p class="text-gray-800"><?php echo $hasil['nama_ibu']; ?></p></div>
            <div><label class="text-gray-500 text-sm">Vaksin</label><p class="text-gray-800"><?php echo $hasil['nama_vaksin']; ?></p></div>
            <div><label class="text-gray-500 text-sm">Tanggal Imunisasi</label><p class="text-gray-800"><?php echo date('d/m/Y', strtotime($hasil['tanggal'])); ?></p></div>
            <div><label class="text-gray-500 text-sm">Berat Badan</label><p class="text-gray-800"><?php echo $hasil['berat_badan']; ?> kg</p></div>
            <div><label class="text-gray-500 text-sm">Tinggi Badan</label><p class="text-gray-800"><?php echo $hasil['tinggi_badan']; ?> cm</p></div>
            <div><label class="text-gray-500 text-sm">Lingkar Kepala</label><p class="text-gray-800"><?php echo $hasil['lingkar_kepala']; ?> cm</p></div>
            <div><label class="text-gray-500 text-sm">Status Gizi</label><p class="font-semibold <?php echo $hasil['status_gizi'] == 'Normal' ? 'text-green-600' : 'text-yellow-600'; ?>"><?php echo $hasil['status_gizi']; ?></p></div>
            <div><label class="text-gray-500 text-sm">Nafsu Makan</label><p class="text-gray-800"><?php echo ucfirst($hasil['nafsu_makan']); ?></p></div>
            <div class="col-span-2"><label class="text-gray-500 text-sm">Catatan Kesehatan</label><p class="text-gray-800"><?php echo $hasil['catatan_kesehatan']; ?></p></div>
        </div>
        <div class="flex gap-3 mt-6">
            <a href="edit_hasil.php?id=<?php echo $id_pendaftaran; ?>" class="bg-blue-500 text-white px-4 py-2 rounded-xl hover:bg-blue-600 transition">Edit Hasil</a>
            <a href="list_pendaftaran.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-xl hover:bg-gray-300 transition">Kembali</a>
        </div>
    </div>
    
    <!-- Riwayat Imunisasi Anak -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-green-800 mb-4">📋 Riwayat Imunisasi <?php echo $hasil['nama_anak']; ?></h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left text-sm font-semibold text-gray-600">No</th>
                        <th class="p-3 text-left text-sm font-semibold text-gray-600">Vaksin</th>
                        <th class="p-3 text-left text-sm font-semibold text-gray-600">Tanggal</th>
                        <th class="p-3 text-left text-sm font-semibold text-gray-600">Berat</th>
                        <th class="p-3 text-left text-sm font-semibold text-gray-600">Tinggi</th>
                        <th class="p-3 text-left text-sm font-semibold text-gray-600">Status Gizi</th>
                        <th class="p-3 text-left text-sm font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $no = 1; while($row = mysqli_fetch_assoc($riwayat)): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 text-sm"><?php echo $no++; ?></td>
                        <td class="p-3 text-sm font-semibold"><?php echo $row['nama_vaksin']; ?></td>
                        <td class="p-3 text-sm"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                        <td class="p-3 text-sm"><?php echo $row['berat_badan']; ?> kg</td>
                        <td class="p-3 text-sm"><?php echo $row['tinggi_badan']; ?> cm</td>
                        <td class="p-3"><span class="px-2 py-1 rounded-full text-xs <?php echo $row['status_gizi'] == 'Normal' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>"><?php echo $row['status_gizi']; ?></span></td>
                        <td class="p-3"><span class="px-2 py-1 rounded-full text-xs <?php echo $row['status'] == 'selesai' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>"><?php echo $row['status'] == 'selesai' ? 'Selesai' : 'Pending'; ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Grafik Perkembangan -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-xl font-bold text-green-800 mb-4">📈 Grafik Perkembangan <?php echo $hasil['nama_anak']; ?></h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div><h3 class="text-md font-semibold text-gray-700 mb-2">Berat Badan (kg)</h3><canvas id="beratChart" height="200"></canvas></div>
            <div><h3 class="text-md font-semibold text-gray-700 mb-2">Tinggi Badan (cm)</h3><canvas id="tinggiChart" height="200"></canvas></div>
        </div>
    </div>
</div>

<script>
// Chart Berat Badan
const beratCtx = document.getElementById('beratChart').getContext('2d');
new Chart(beratCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($chart_data, 'tgl_imunisasi')); ?>.map(d => new Date(d).toLocaleDateString('id-ID')),
        datasets: [{ label: 'Berat Badan (kg)', data: <?php echo json_encode(array_column($chart_data, 'berat_badan')); ?>, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.1)', tension: 0.4, fill: true }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

// Chart Tinggi Badan
const tinggiCtx = document.getElementById('tinggiChart').getContext('2d');
new Chart(tinggiCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($chart_data, 'tgl_imunisasi')); ?>.map(d => new Date(d).toLocaleDateString('id-ID')),
        datasets: [{ label: 'Tinggi Badan (cm)', data: <?php echo json_encode(array_column($chart_data, 'tinggi_badan')); ?>, borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.1)', tension: 0.4, fill: true }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>