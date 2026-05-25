<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/cek_bidan.php';
$title = 'Dashboard Bidan';
include __DIR__ . '/../templates/sidebar.php';

$nik = $_SESSION['nik'];
$nama_bidan = $_SESSION['nama_lengkap'];

// ============================================
// DATA STATISTIK PER BIDAN
// ============================================

// Jadwal Aktif (jadwal yang petugasnya bidan ini)
$jadwal_aktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM jadwal_imunisasi WHERE petugas_nik='$nik'"))['total'];

// Total Pendaftaran (dari jadwal yang petugasnya bidan ini)
$total_pendaftaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran_imunisasi pi 
    JOIN jadwal_imunisasi j ON pi.id_jadwal=j.id_jadwal 
    WHERE j.petugas_nik='$nik'"))['total'];

// Ibu Hamil Aktif (SEMUA BIDAN - data global)
$total_ibu_hamil_aktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM ibu_hamil WHERE status_kehamilan='aktif'"))['total'];

// Imunisasi Selesai (dari pendaftaran yang petugasnya bidan ini)
$imunisasi_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran_imunisasi pi 
    JOIN jadwal_imunisasi j ON pi.id_jadwal=j.id_jadwal 
    WHERE j.petugas_nik='$nik' AND pi.status='selesai'"))['total'];

// Data untuk chart pendaftaran (7 hari terakhir) - per bidan
$labels = [];
$data_pendaftaran = [];
for ($i = 6; $i >= 0; $i--) {
    $tanggal = date('Y-m-d', strtotime("-$i days"));
    $labels[] = formatTanggalIndonesia($tanggal);
    $count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran_imunisasi pi 
        JOIN jadwal_imunisasi j ON pi.id_jadwal=j.id_jadwal 
        WHERE j.petugas_nik='$nik' AND DATE(pi.created_at) = '$tanggal'"))['total'];
    $data_pendaftaran[] = $count;
}

// Data status imunisasi (pending, selesai, batal) - per bidan
$pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran_imunisasi pi 
    JOIN jadwal_imunisasi j ON pi.id_jadwal=j.id_jadwal 
    WHERE j.petugas_nik='$nik' AND pi.status='pending'"))['total'];
$selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran_imunisasi pi 
    JOIN jadwal_imunisasi j ON pi.id_jadwal=j.id_jadwal 
    WHERE j.petugas_nik='$nik' AND pi.status='selesai'"))['total'];
$batal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran_imunisasi pi 
    JOIN jadwal_imunisasi j ON pi.id_jadwal=j.id_jadwal 
    WHERE j.petugas_nik='$nik' AND pi.status='batal'"))['total'];
?>

<div class="fade-in">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-green-600 to-emerald-500 rounded-2xl p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">Selamat Datang, Bidan <?php echo $nama_bidan; ?>!</h1>
                <p class="text-green-100">Pantau jadwal imunisasi dan layanan kesehatan</p>
                <div class="mt-2 flex items-center gap-2 text-sm text-green-100">
                    <i class="fas fa-calendar-alt"></i>
                    <span><?php echo formatTanggalIndonesia(date('Y-m-d')); ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards - 4 Card Saja -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Jadwal Aktif -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition group">
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-200 transition">
                        <i class="fas fa-calendar-alt text-green-500 text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-gray-800"><?php echo number_format($jadwal_aktif); ?></span>
                </div>
                <p class="text-gray-500 text-sm">Jadwal Aktif</p>
                <div class="mt-2 text-xs text-green-600">
                    <i class="fas fa-clock mr-1"></i> Jadwal imunisasi tersedia
                </div>
            </div>
            <div class="h-1 bg-green-500 w-full"></div>
        </div>
        
        <!-- Total Pendaftaran -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition group">
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition">
                        <i class="fas fa-clipboard-list text-blue-500 text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-gray-800"><?php echo number_format($total_pendaftaran); ?></span>
                </div>
                <p class="text-gray-500 text-sm">Total Pendaftaran</p>
                <div class="mt-2 text-xs text-blue-600">
                    <i class="fas fa-users mr-1"></i> Pendaftaran imunisasi
                </div>
            </div>
            <div class="h-1 bg-blue-500 w-full"></div>
        </div>
        
        <!-- Ibu Hamil Aktif -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition group">
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center group-hover:bg-pink-200 transition">
                        <i class="fas fa-female text-pink-500 text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-gray-800"><?php echo number_format($total_ibu_hamil_aktif); ?></span>
                </div>
                <p class="text-gray-500 text-sm">Ibu Hamil Aktif</p>
                <div class="mt-2 text-xs text-pink-600">
                    <i class="fas fa-heartbeat mr-1"></i> Dalam masa kehamilan
                </div>
            </div>
            <div class="h-1 bg-pink-500 w-full"></div>
        </div>
        
        <!-- Imunisasi Selesai -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition group">
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center group-hover:bg-teal-200 transition">
                        <i class="fas fa-check-circle text-teal-500 text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-gray-800"><?php echo number_format($imunisasi_selesai); ?></span>
                </div>
                <p class="text-gray-500 text-sm">Imunisasi Selesai</p>
                <div class="mt-2 text-xs text-teal-600">
                    <i class="fas fa-syringe mr-1"></i> Imunisasi telah selesai
                </div>
            </div>
            <div class="h-1 bg-teal-500 w-full"></div>
        </div>
    </div>
    
    <!-- Charts Section - Ukuran sama -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Grafik Pendaftaran 7 Hari Terakhir -->
        <div class="bg-white rounded-2xl shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-700">
                    <i class="fas fa-chart-line text-green-500 mr-2"></i> Pendaftaran Imunisasi
                </h3>
                <span class="text-xs text-gray-400">7 hari terakhir</span>
            </div>
            <div style="height: 300px;">
                <canvas id="weeklyChart"></canvas>
            </div>
        </div>
        
        <!-- Grafik Status Imunisasi -->
        <div class="bg-white rounded-2xl shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-700">
                    <i class="fas fa-chart-pie text-green-500 mr-2"></i> Status Imunisasi
                </h3>
                <span class="text-xs text-gray-400">Keseluruhan</span>
            </div>
            <div style="height: 300px;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Grafik 7 Hari Terakhir
const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
new Chart(weeklyCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            label: 'Jumlah Pendaftaran',
            data: <?php echo json_encode($data_pendaftaran); ?>,
            backgroundColor: 'rgba(16, 185, 129, 0.7)',
            borderColor: '#10b981',
            borderWidth: 1,
            borderRadius: 8,
            barPercentage: 0.65
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top' },
            tooltip: { 
                callbacks: { 
                    label: function(context) { 
                        return context.raw + ' pendaftaran'; 
                    } 
                } 
            }
        },
        scales: {
            y: { 
                beginAtZero: true, 
                ticks: { stepSize: 1, precision: 0 }, 
                title: { display: true, text: 'Jumlah' } 
            },
            x: { title: { display: true, text: 'Tanggal' } }
        }
    }
});

// Grafik Status Imunisasi (Doughnut)
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'Selesai', 'Batal'],
        datasets: [{
            data: [<?php echo $pending; ?>, <?php echo $selesai; ?>, <?php echo $batal; ?>],
            backgroundColor: ['#eab308', '#10b981', '#ef4444'],
            borderWidth: 0,
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' },
            tooltip: { 
                callbacks: { 
                    label: function(context) { 
                        return context.label + ': ' + context.raw + ' data'; 
                    } 
                } 
            }
        },
        cutout: '60%'
    }
});
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>