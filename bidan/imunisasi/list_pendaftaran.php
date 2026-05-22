<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_bidan.php';

$nik = $_SESSION['nik'];

$title = 'Imunisasi';
include __DIR__ . '/../../templates/sidebar.php';

// Query untuk mendapatkan jadwal beserta statistik pendaftaran
// Urutan: tanggal hari ini paling atas, lalu tanggal terbaru berikutnya
$result = mysqli_query($conn, "SELECT j.*, v.nama_vaksin, 
    (SELECT COUNT(*) FROM pendaftaran_imunisasi WHERE id_jadwal=j.id_jadwal AND status != 'batal') as total_daftar,
    (SELECT COUNT(*) FROM pendaftaran_imunisasi WHERE id_jadwal=j.id_jadwal AND status='pending') as total_pending,
    (SELECT COUNT(*) FROM pendaftaran_imunisasi WHERE id_jadwal=j.id_jadwal AND status='selesai') as total_selesai
    FROM jadwal_imunisasi j 
    JOIN vaksin v ON j.id_vaksin=v.id_vaksin 
    WHERE j.created_by='$nik'
    ORDER BY 
        CASE WHEN j.tanggal = CURDATE() THEN 0 ELSE 1 END,
        j.tanggal DESC, 
        j.created_at DESC");
?>

<div class="fade-in">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-green-800">Imunisasi</h1>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php while($row = mysqli_fetch_assoc($result)): 
            $tanggal = new DateTime($row['tanggal']);
            $hari_ini = new DateTime();
            $is_today = $row['tanggal'] == date('Y-m-d');
            $status_jadwal = $tanggal < $hari_ini ? 'Selesai' : 'Akan Datang';
            $status_color = $tanggal < $hari_ini ? 'bg-gray-100' : 'bg-green-50';
        ?>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition relative">
            <!-- Label Hari Ini -->
            <?php if($is_today): ?>
            <div class="absolute top-0 right-0">
                <div class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg rounded-tr-lg shadow-md">
                    <i class="fas fa-calendar-day mr-1"></i> HARI INI
                </div>
            </div>
            <?php endif; ?>
            
            <div class="<?php echo $status_color; ?> p-4 border-b">
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full flex items-center justify-center text-white">
                            <i class="fas fa-syringe"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800"><?php echo $row['nama_vaksin']; ?></h3>
                            <span class="text-xs <?php echo $tanggal < $hari_ini ? 'text-gray-500' : 'text-green-600'; ?>">
                                <i class="fas fa-clock mr-1"></i> <?php echo $status_jadwal; ?>
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-700"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="p-4">
                <div class="grid grid-cols-3 gap-2 pt-2">
                    <div class="text-center">
                        <p class="text-lg font-bold text-blue-600"><?php echo $row['total_daftar']; ?></p>
                        <p class="text-xs text-gray-500">Total Daftar</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold text-yellow-600"><?php echo $row['total_pending']; ?></p>
                        <p class="text-xs text-gray-500">Menunggu</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold text-green-600"><?php echo $row['total_selesai']; ?></p>
                        <p class="text-xs text-gray-500">Selesai</p>
                    </div>
                </div>
                
                <div class="flex gap-2 mt-4">
                    <button onclick="openPesertaModal(<?php echo $row['id_jadwal']; ?>, '<?php echo $row['nama_vaksin']; ?>', '<?php echo date('d/m/Y', strtotime($row['tanggal'])); ?>')" 
                            class="flex-1 text-center bg-blue-500 text-white py-1 rounded-lg text-sm hover:bg-blue-600 transition">
                        <i class="fas fa-users mr-1"></i> Lihat Peserta
                    </button>
                    <button onclick="openRiwayatModal(<?php echo $row['id_jadwal']; ?>, '<?php echo $row['nama_vaksin']; ?>', '<?php echo date('d/m/Y', strtotime($row['tanggal'])); ?>')" 
                            class="flex-1 text-center bg-green-500 text-white py-1 rounded-lg text-sm hover:bg-green-600 transition">
                        <i class="fas fa-check-circle mr-1"></i> Selesai
                    </button>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    
    <?php if(mysqli_num_rows($result) == 0): ?>
    <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
        <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Jadwal</h3>
        <p class="text-gray-500">Silakan buat jadwal imunisasi terlebih dahulu</p>
        <a href="../jadwal/list_jadwal.php" class="inline-block mt-4 bg-green-600 text-white px-6 py-2 rounded-xl hover:bg-green-700 transition">
            <i class="fas fa-plus mr-2"></i> Buat Jadwal
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Peserta -->
<div id="pesertaModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4" onclick="closeModal(event, 'pesertaModal')">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-blue-500 p-4 rounded-t-2xl flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-white" id="pesertaModalTitle"></h3>
                <p class="text-blue-100 text-sm" id="pesertaModalSubtitle"></p>
            </div>
            <button onclick="closeModal(null, 'pesertaModal')" class="text-white hover:text-gray-200 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6" id="pesertaModalContent">
            <div class="text-center py-8">Memuat data...</div>
        </div>
    </div>
</div>

<!-- Modal Riwayat Selesai -->
<div id="riwayatModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4" onclick="closeModal(event, 'riwayatModal')">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="sticky top-0 bg-gradient-to-r from-green-600 to-green-500 p-4 rounded-t-2xl flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-white" id="riwayatModalTitle"></h3>
                <p class="text-green-100 text-sm" id="riwayatModalSubtitle"></p>
            </div>
            <button onclick="closeModal(null, 'riwayatModal')" class="text-white hover:text-gray-200 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6" id="riwayatModalContent">
            <div class="text-center py-8">Memuat data...</div>
        </div>
    </div>
</div>

<script>
function openPesertaModal(jadwalId, vaksinNama, tanggal) {
    document.getElementById('pesertaModalTitle').innerText = 'Daftar Peserta - ' + vaksinNama;
    document.getElementById('pesertaModalSubtitle').innerText = 'Tanggal: ' + tanggal;
    document.getElementById('pesertaModalContent').innerHTML = '<div class="text-center py-8">Memuat data...</div>';
    
    fetch('get_peserta.php?id=' + jadwalId + '&type=peserta')
        .then(response => response.text())
        .then(data => {
            document.getElementById('pesertaModalContent').innerHTML = data;
        })
        .catch(error => {
            document.getElementById('pesertaModalContent').innerHTML = '<div class="text-center py-8 text-red-500">Gagal memuat数据</div>';
        });
    
    document.getElementById('pesertaModal').classList.remove('hidden');
    document.getElementById('pesertaModal').classList.add('flex');
}

function openRiwayatModal(jadwalId, vaksinNama, tanggal) {
    document.getElementById('riwayatModalTitle').innerText = 'Imunisasi Selesai - ' + vaksinNama;
    document.getElementById('riwayatModalSubtitle').innerText = 'Tanggal: ' + tanggal;
    document.getElementById('riwayatModalContent').innerHTML = '<div class="text-center py-8">Memuat data...</div>';
    
    fetch('get_peserta.php?id=' + jadwalId + '&type=selesai')
        .then(response => response.text())
        .then(data => {
            document.getElementById('riwayatModalContent').innerHTML = data;
        })
        .catch(error => {
            document.getElementById('riwayatModalContent').innerHTML = '<div class="text-center py-8 text-red-500">Gagal memuat data</div>';
        });
    
    document.getElementById('riwayatModal').classList.remove('hidden');
    document.getElementById('riwayatModal').classList.add('flex');
}

function closeModal(event, modalId) {
    if (event && event.target !== event.currentTarget && event.target.closest('.bg-white')) return;
    document.getElementById(modalId).classList.add('hidden');
    document.getElementById(modalId).classList.remove('flex');
}
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>