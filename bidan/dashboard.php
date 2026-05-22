<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/cek_bidan.php';
$title = 'Dashboard Bidan';
include __DIR__ . '/../templates/sidebar.php';

$nik = $_SESSION['nik'];

// Statistik Imunisasi
$stats['jadwal'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM jadwal_imunisasi WHERE created_by='$nik'"))['total'];
$stats['pendaftaran'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran_imunisasi pi JOIN jadwal_imunisasi j ON pi.id_jadwal=j.id_jadwal WHERE j.created_by='$nik'"))['total'];
$stats['hasil'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM hasil_imunisasi hi JOIN pendaftaran_imunisasi pi ON hi.id_pendaftaran=pi.id_pendaftaran JOIN jadwal_imunisasi j ON pi.id_jadwal=j.id_jadwal WHERE j.created_by='$nik'"))['total'];

// Total Ibu Hamil Aktif
$total_ibu_hamil_aktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM ibu_hamil WHERE status_kehamilan='aktif'"))['total'];

// Data untuk chart (7 hari terakhir pendaftaran)
$labels = [];
$data_pendaftaran = [];
for ($i = 6; $i >= 0; $i--) {
    $tanggal = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('d/m', strtotime($tanggal));
    $count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran_imunisasi pi 
        JOIN jadwal_imunisasi j ON pi.id_jadwal=j.id_jadwal 
        WHERE j.created_by='$nik' AND DATE(pi.created_at) = '$tanggal'"))['total'];
    $data_pendaftaran[] = $count;
}
?>

<div class="fade-in">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-green-600 to-emerald-500 rounded-2xl p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">Selamat Datang, Bidan <?php echo $_SESSION['nama_lengkap']; ?>! </h1>
                <p class="text-green-100">Pantau jadwal imunisasi dan layanan kesehatan</p>
                <div class="mt-2 flex items-center gap-2 text-sm text-green-100">
                    <i class="fas fa-calendar-alt"></i>
                    <span><?php echo date('l, d F Y'); ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Jadwal Aktif -->
        <div class="bg-white rounded-2xl shadow-md p-4 hover:shadow-lg transition border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Jadwal Aktif</p>
                    <p class="text-2xl font-bold text-green-600"><?php echo $stats['jadwal']; ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-green-500 text-xl"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-400">
                <i class="fas fa-clock mr-1"></i> Jadwal imunisasi tersedia
            </div>
        </div>
        
        <!-- Total Pendaftaran -->
        <div class="bg-white rounded-2xl shadow-md p-4 hover:shadow-lg transition border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Pendaftaran</p>
                    <p class="text-2xl font-bold text-blue-600"><?php echo $stats['pendaftaran']; ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-blue-500 text-xl"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-400">
                <i class="fas fa-users mr-1"></i> Pendaftaran imunisasi
            </div>
        </div>
        
        <!-- Ibu Hamil Aktif (Ganti Menunggu Konfirmasi) -->
        <div class="bg-white rounded-2xl shadow-md p-4 hover:shadow-lg transition border-l-4 border-pink-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Ibu Hamil Aktif</p>
                    <p class="text-2xl font-bold text-pink-600"><?php echo $total_ibu_hamil_aktif; ?></p>
                </div>
                <div class="w-12 h-12 bg-pink-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-female text-pink-500 text-xl"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-400">
                <i class="fas fa-heartbeat mr-1"></i> Dalam masa kehamilan
            </div>
        </div>
        
        <!-- Imunisasi Selesai -->
        <div class="bg-white rounded-2xl shadow-md p-4 hover:shadow-lg transition border-l-4 border-teal-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Imunisasi Selesai</p>
                    <p class="text-2xl font-bold text-teal-600"><?php echo $stats['hasil']; ?></p>
                </div>
                <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-teal-500 text-xl"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-400">
                <i class="fas fa-syringe mr-1"></i> Imunisasi telah selesai
            </div>
        </div>
    </div>
    
    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Grafik Pendaftaran 7 Hari Terakhir -->
        <div class="bg-white rounded-2xl shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-700">
                    <i class="fas fa-chart-line text-green-500 mr-2"></i> Pendaftaran Imunisasi
                </h3>
                <span class="text-xs text-gray-400">7 hari terakhir</span>
            </div>
            <canvas id="weeklyChart" height="200"></canvas>
        </div>
        
        <!-- Info Cepat -->
        <div class="bg-white rounded-2xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">
                <i class="fas fa-info-circle text-blue-500 mr-2"></i> Informasi Cepat
            </h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-calendar-week text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Jadwal Hari Ini</p>
                            <p class="font-semibold text-gray-800"><?php 
                                $hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM jadwal_imunisasi WHERE created_by='$nik' AND tanggal=CURDATE()"))['total'];
                                echo $hari_ini;
                            ?> jadwal</p>
                        </div>
                    </div>
                    <a href="jadwal/list_jadwal.php" class="text-green-600 text-sm">Lihat <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-yellow-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-hourglass-half text-yellow-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Menunggu Imunisasi</p>
                            <p class="font-semibold text-gray-800"><?php 
                                $pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran_imunisasi pi JOIN jadwal_imunisasi j ON pi.id_jadwal=j.id_jadwal WHERE j.created_by='$nik' AND pi.status='pending'"))['total'];
                                echo $pending;
                            ?> peserta</p>
                        </div>
                    </div>
                    <a href="imunisasi/list_pendaftaran.php" class="text-yellow-600 text-sm">Proses <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Jadwal Hari Ini -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-calendar-day text-green-500 mr-2"></i> Jadwal Imunisasi Hari Ini
        </h3>
        <?php 
        $today = date('Y-m-d'); 
        $today_schedule = mysqli_query($conn, "SELECT j.*, v.nama_vaksin, COUNT(pi.id_pendaftaran) as pendaftar 
            FROM jadwal_imunisasi j 
            JOIN vaksin v ON j.id_vaksin=v.id_vaksin 
            LEFT JOIN pendaftaran_imunisasi pi ON j.id_jadwal=pi.id_jadwal 
            WHERE j.created_by='$nik' AND j.tanggal='$today' 
            GROUP BY j.id_jadwal");
        
        if(mysqli_num_rows($today_schedule) > 0): 
            while($jadwal = mysqli_fetch_assoc($today_schedule)): 
        ?>
        <div class="flex items-center justify-between p-4 bg-green-50 rounded-xl mb-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-syringe text-green-600"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-800"><?php echo $jadwal['nama_vaksin']; ?></p>
                    <p class="text-sm text-gray-600">Posyandu Ceria</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold text-green-600">Pendaftar: <?php echo $jadwal['pendaftar']; ?> anak</p>
                <a href="imunisasi/list_pendaftaran.php" class="text-xs text-green-600 hover:text-green-700">
                    Lihat Pendaftar <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        <?php 
            endwhile; 
        else: 
        ?>
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-calendar-day text-4xl mb-2"></i>
            <p>Tidak ada jadwal imunisasi hari ini</p>
            <a href="jadwal/tambah_jadwal.php" class="text-green-600 text-sm mt-2 inline-block">
                <i class="fas fa-plus mr-1"></i> Buat Jadwal
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

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
        maintainAspectRatio: true,
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
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>