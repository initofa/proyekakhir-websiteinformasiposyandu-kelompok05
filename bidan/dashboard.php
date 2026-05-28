<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/cek_bidan.php';
$title = 'Dashboard Bidan';
include __DIR__ . '/../templates/sidebar.php';

$nik = $_SESSION['nik'];
$nama_bidan = $_SESSION['nama_lengkap'];

$jadwal_aktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM jadwal_imunisasi WHERE petugas_nik='$nik' AND tanggal >= CURDATE()"))['total'];


$total_pendaftaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran_imunisasi pi 
    JOIN jadwal_imunisasi j ON pi.id_jadwal=j.id_jadwal 
    WHERE j.petugas_nik='$nik'"))['total'];


$total_ibu_hamil_aktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM ibu_hamil WHERE status_kehamilan='aktif'"))['total'];
$total_anak = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM anak"))['total'];


$pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran_imunisasi pi 
    JOIN jadwal_imunisasi j ON pi.id_jadwal=j.id_jadwal 
    WHERE j.petugas_nik='$nik' AND pi.status='pending'"))['total'];
$selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran_imunisasi pi 
    JOIN jadwal_imunisasi j ON pi.id_jadwal=j.id_jadwal 
    WHERE j.petugas_nik='$nik' AND pi.status='selesai'"))['total'];
$batal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran_imunisasi pi 
    JOIN jadwal_imunisasi j ON pi.id_jadwal=j.id_jadwal 
    WHERE j.petugas_nik='$nik' AND pi.status='batal'"))['total'];


$jadwal_terdekat = mysqli_query($conn, "SELECT j.*, v.nama_vaksin 
    FROM jadwal_imunisasi j 
    JOIN vaksin v ON j.id_vaksin = v.id_vaksin 
    WHERE j.petugas_nik='$nik' AND j.tanggal >= CURDATE()
    ORDER BY j.tanggal ASC 
    LIMIT 5");
?>

<div class="fade-in">
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
    
  
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
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
        
        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition group">
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center group-hover:bg-purple-200 transition">
                        <i class="fas fa-clipboard-list text-purple-500 text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-gray-800"><?php echo number_format($total_pendaftaran); ?></span>
                </div>
                <p class="text-gray-500 text-sm">Total Pendaftar</p>
                <div class="mt-2 text-xs text-purple-600">
                    <i class="fas fa-users mr-1"></i> Pendaftar imunisasi
                </div>
            </div>
            <div class="h-1 bg-purple-500 w-full"></div>
        </div>
        
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
                    <i class="fas fa-baby mr-1"></i> Anak terdaftar
                </div>
            </div>
            <div class="h-1 bg-blue-500 w-full"></div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Jadwal Imunisasi Terdekat -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-emerald-500 px-5 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-white text-lg"></i>
                        <h3 class="text-white font-semibold">Jadwal Imunisasi Terdekat</h3>
                    </div>
                    <a href="imunisasi/list_pendaftaran.php" class="text-white/80 hover:text-white text-xs transition">
                        Lihat semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="p-4">
                <?php if(mysqli_num_rows($jadwal_terdekat) > 0): ?>
                    <div class="space-y-3">
                        <?php while($row = mysqli_fetch_assoc($jadwal_terdekat)): 
                            $tanggal = new DateTime($row['tanggal']);
                            $hari_ini = new DateTime();
                            $diff = $hari_ini->diff($tanggal);
                            $hari_selisih = $diff->days;
                            $is_today = $row['tanggal'] == date('Y-m-d');
                            
                            if($is_today) {
                                $badge_color = 'bg-red-500';
                                $badge_text = 'Hari Ini';
                            } elseif($hari_selisih <= 3) {
                                $badge_color = 'bg-orange-500';
                                $badge_text = $hari_selisih . ' hari lagi';
                            } elseif($hari_selisih <= 7) {
                                $badge_color = 'bg-yellow-500';
                                $badge_text = $hari_selisih . ' hari lagi';
                            } else {
                                $badge_color = 'bg-green-500';
                                $badge_text = $hari_selisih . ' hari lagi';
                            }
                        ?>
                        <div class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition border border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-syringe text-green-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($row['nama_vaksin']); ?></p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        <i class="fas fa-map-marker-alt mr-1"></i><?php echo htmlspecialchars($row['lokasi']); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-700"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></p>
                                <span class="inline-block text-xs px-2 py-0.5 rounded-full text-white <?php echo $badge_color; ?> mt-1">
                                    <?php echo $badge_text; ?>
                                </span>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-400">
                        <i class="fas fa-calendar-times text-4xl mb-2"></i>
                        <p class="text-sm">Tidak ada jadwal terdekat</p>
                    </div>
                <?php endif; ?>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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