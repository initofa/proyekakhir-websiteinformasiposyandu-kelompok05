<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/cek_admin.php';
$title = 'Dashboard Admin';
include __DIR__ . '/../templates/sidebar.php';


$total_ibu = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='ibu'"))['total'];
$total_anak = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM anak"))['total'];
$total_bidan_aktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='bidan' AND status='active'"))['total'];
$total_jadwal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM jadwal_imunisasi"))['total'];
$total_ibu_hamil = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM ibu_hamil WHERE status_kehamilan='aktif'"))['total'];
$total_artikel = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM artikel"))['total'];
$total_vaksin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM vaksin"))['total'];
$total_kategori = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM kategori_artikel"))['total'];

$labels = [];
$data_imunisasi = [];
for ($i = 6; $i >= 0; $i--) {
    $tanggal = date('Y-m-d', strtotime("-$i days"));
    $labels[] = formatTanggalIndonesia($tanggal);
    $count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran_imunisasi WHERE DATE(created_at) = '$tanggal'"))['total'];
    $data_imunisasi[] = $count;
}

$pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran_imunisasi WHERE status='pending'"))['total'];
$selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran_imunisasi WHERE status='selesai'"))['total'];
$batal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran_imunisasi WHERE status='batal'"))['total'];
?>

<div class="fade-in">
    <div class="bg-gradient-to-r from-green-600 to-emerald-500 rounded-2xl p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">Halo, <?php echo $_SESSION['nama_lengkap']; ?>!</h1>
                <p class="text-green-100">Selamat datang di dashboard SIPANDA</p>
                <div class="mt-2 flex items-center gap-2 text-sm text-green-100">
                    <i class="fas fa-calendar-alt"></i>
                    <span><?php echo formatTanggalIndonesia(date('Y-m-d')); ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition group">
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-12 h-12 bg-rose-100 rounded-xl flex items-center justify-center group-hover:bg-rose-200 transition">
                        <i class="fas fa-heartbeat text-rose-500 text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-gray-800"><?php echo number_format($total_ibu_hamil); ?></span>
                </div>
                <p class="text-gray-500 text-sm">Ibu Hamil Aktif</p>
                <div class="mt-2 text-xs text-rose-600">
                    <i class="fas fa-heartbeat mr-1"></i> Dalam masa kehamilan
                </div>
            </div>
            <div class="h-1 bg-rose-500 w-full"></div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition group">
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition">
                        <i class="fas fa-child text-blue-500 text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-gray-800"><?php echo number_format($total_anak); ?></span>
                </div>
                <p class="text-gray-500 text-sm">Total Anak</p>
                <div class="mt-2 text-xs text-blue-600">
                    <i class="fas fa-baby-carriage mr-1"></i> Data balita & anak
                </div>
            </div>
            <div class="h-1 bg-blue-500 w-full"></div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition group">
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center group-hover:bg-teal-200 transition">
                        <i class="fas fa-user-md text-teal-500 text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-gray-800"><?php echo number_format($total_bidan_aktif); ?></span>
                </div>
                <p class="text-gray-500 text-sm">Bidan Aktif</p>
                <div class="mt-2 text-xs text-teal-600">
                    <i class="fas fa-check-circle mr-1"></i> Status aktif
                </div>
            </div>
            <div class="h-1 bg-teal-500 w-full"></div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition group">
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center group-hover:bg-purple-200 transition">
                        <i class="fas fa-calendar-alt text-purple-500 text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-gray-800"><?php echo number_format($total_jadwal); ?></span>
                </div>
                <p class="text-gray-500 text-sm">Jadwal Imunisasi</p>
                <div class="mt-2 text-xs text-purple-600">
                    <i class="fas fa-clock mr-1"></i> Jadwal tersedia
                </div>
            </div>
            <div class="h-1 bg-purple-500 w-full"></div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition group">
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center group-hover:bg-pink-200 transition">
                        <i class="fas fa-female text-pink-500 text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-gray-800"><?php echo number_format($total_ibu); ?></span>
                </div>
                <p class="text-gray-500 text-sm">Total Ibu</p>
                <div class="mt-2 text-xs text-green-600">
                    <i class="fas fa-check-circle mr-1"></i> Terdaftar di sistem
                </div>
            </div>
            <div class="h-1 bg-pink-500 w-full"></div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition group">
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-200 transition">
                        <i class="fas fa-syringe text-green-500 text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-gray-800"><?php echo number_format($total_vaksin); ?></span>
                </div>
                <p class="text-gray-500 text-sm">Jenis Vaksin</p>
                <div class="mt-2 text-xs text-green-600">
                    <i class="fas fa-list mr-1"></i> Tersedia di sistem
                </div>
            </div>
            <div class="h-1 bg-green-500 w-full"></div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition group">
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center group-hover:bg-orange-200 transition">
                        <i class="fas fa-newspaper text-orange-500 text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-gray-800"><?php echo number_format($total_artikel); ?></span>
                </div>
                <p class="text-gray-500 text-sm">Total Artikel</p>
                <div class="mt-2 text-xs text-orange-600">
                    <i class="fas fa-book-open mr-1"></i> Informasi kesehatan
                </div>
            </div>
            <div class="h-1 bg-orange-500 w-full"></div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition group">
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center group-hover:bg-indigo-200 transition">
                        <i class="fas fa-tags text-indigo-500 text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-gray-800"><?php echo number_format($total_kategori); ?></span>
                </div>
                <p class="text-gray-500 text-sm">Kategori Artikel</p>
                <div class="mt-2 text-xs text-indigo-600">
                    <i class="fas fa-folder-open mr-1"></i> Topik artikel
                </div>
            </div>
            <div class="h-1 bg-indigo-500 w-full"></div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-700">
                    <i class="fas fa-chart-line text-green-500 mr-2"></i> Pendaftar Imunisasi
                </h3>
                <span class="text-xs text-gray-400">7 hari terakhir</span>
            </div>
            <div style="height: 300px;">
                <canvas id="weeklyChart"></canvas>
            </div>
        </div>
        
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

<script>
const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
new Chart(weeklyCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            label: 'Jumlah Pendaftar',
            data: <?php echo json_encode($data_imunisasi); ?>,
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
                        return context.raw + ' pendaftar'; 
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

const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Menunggu', 'Selesai', 'dibatalkan'],
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
                        const total = <?php echo $pending + $selesai + $batal; ?>;
                        const persen = ((context.raw / total) * 100).toFixed(1);
                        return context.label + ': ' + context.raw + ' data (' + persen + '%)'; 
                    } 
                } 
            }
        },
        cutout: '60%'
    }
});
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>