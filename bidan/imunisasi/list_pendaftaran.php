<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_bidan.php';

$nik = $_SESSION['nik'];

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6; 
$offset = ($page - 1) * $limit;

$title = 'Jadwal Imunisasi';
include __DIR__ . '/../../templates/sidebar.php';

$search_condition = "";
if ($search !== '') {
    $search_condition = " AND (v.nama_vaksin LIKE '%$search%' OR j.lokasi LIKE '%$search%')";
}

$total_query = "SELECT COUNT(*) as total 
                FROM jadwal_imunisasi j 
                JOIN vaksin v ON j.id_vaksin=v.id_vaksin 
                WHERE j.petugas_nik = '$nik' $search_condition";
$total_data = mysqli_fetch_assoc(mysqli_query($conn, $total_query))['total'];
$total_pages = ceil($total_data / $limit);

$query_base = "SELECT j.*, v.nama_vaksin, u.nama_lengkap as nama_bidan,
    (SELECT COUNT(*) FROM pendaftaran_imunisasi WHERE id_jadwal=j.id_jadwal AND STATUS != 'batal') as total_daftar,
    (SELECT COUNT(*) FROM pendaftaran_imunisasi WHERE id_jadwal=j.id_jadwal AND STATUS='pending') as total_pending,
    (SELECT COUNT(*) FROM pendaftaran_imunisasi WHERE id_jadwal=j.id_jadwal AND STATUS='selesai') as total_selesai,
    (SELECT COUNT(*) FROM pendaftaran_imunisasi WHERE id_jadwal=j.id_jadwal AND STATUS='batal') as total_batal
    FROM jadwal_imunisasi j 
    JOIN vaksin v ON j.id_vaksin=v.id_vaksin 
    LEFT JOIN users u ON j.petugas_nik = u.nik
    WHERE j.petugas_nik = '$nik' $search_condition
    ORDER BY 
        CASE WHEN j.tanggal = CURDATE() THEN 0 ELSE 1 END,
        j.tanggal ASC
    LIMIT $offset, $limit";

$result = mysqli_query($conn, $query_base);
?>

<form id="formCetakLaporanPdf" action="cetak_laporan.php" method="POST" style="display:none;">
    <input type="hidden" name="id_jadwal" id="idJadwalCetakPost">
</form>

<div class="fade-in">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-green-800">Jadwal Imunisasi</h1>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-4 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Cari jadwal (Vaksin atau Lokasi Posyandu)..." 
                       class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 text-sm">
            </div>
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-xl hover:bg-green-700 transition flex items-center justify-center gap-2 text-sm font-semibold shadow-sm">
                <i class="fas fa-search text-xs"></i> Cari
            </button>
            <?php if($search): ?>
            <a href="?" class="bg-gray-500 text-white px-6 py-2 rounded-xl hover:bg-gray-600 transition flex items-center justify-center gap-2 text-sm font-semibold shadow-sm">
                <i class="fas fa-times text-xs"></i> Reset
            </a>
            <?php endif; ?>
        </form>
    </div>
    
    <?php if(mysqli_num_rows($result) > 0): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php while($row = mysqli_fetch_assoc($result)): 
            $tanggal_eval = new DateTime($row['tanggal']);
            $hari_ini = new DateTime(date('Y-m-d'));
            $is_today = $row['tanggal'] == date('Y-m-d');
            
            $status_jadwal = $tanggal_eval < $hari_ini ? 'Selesai Pelaksanaan' : 'Akan Datang';
            $status_color = $tanggal_eval < $hari_ini ? 'bg-gray-50 border-gray-100' : 'bg-green-50/60 border-green-100';
        ?>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition flex flex-col justify-between border border-gray-100 relative">
            <?php if($is_today): ?>
            <div class="absolute top-0 right-0 z-10">
                <span class="bg-red-500 text-white text-[10px] tracking-wider font-extrabold px-3 py-1 rounded-bl-xl shadow-sm block animate-pulse">
                    <i class="fas fa-calendar-day mr-1"></i> HARI INI
                </span>
            </div>
            <?php endif; ?>
            
            <div>
                <div class="<?php echo $status_color; ?> p-4 border-b">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center text-white shadow-sm flex-shrink-0">
                                <i class="fas fa-syringe"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 leading-tight"><?php echo htmlspecialchars($row['nama_vaksin']); ?></h3>
                                <span class="text-[11px] font-medium <?php echo $tanggal_eval < $hari_ini ? 'text-gray-400' : 'text-green-600'; ?>">
                                    <i class="fas fa-dot-circle mr-1 text-[9px]"></i><?php echo $status_jadwal; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 space-y-3">
                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 text-xs text-gray-600 space-y-2.5">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-map-marker-alt text-red-500 mt-0.5 flex-shrink-0 w-3 text-center"></i>
                            <div>
                                <span class="text-gray-400 block font-medium">Tempat Pelaksanaan:</span>
                                <strong class="text-gray-700 font-semibold break-words"><?php echo htmlspecialchars($row['lokasi']); ?></strong>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-2 border-t border-gray-200/50">
                            <i class="far fa-calendar-alt text-gray-400 flex-shrink-0 w-3 text-center"></i>
                            <span class="text-gray-700 font-medium"><?php echo formatTanggalIndonesia($row['tanggal']); ?></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-1 text-center bg-white border border-gray-100 rounded-xl py-2 shadow-sm">
                        <div>
                            <p class="text-sm font-bold text-blue-600"><?php echo $row['total_daftar']; ?></p>
                            <p class="text-[9px] font-medium text-gray-400 uppercase tracking-tight">Terdaftar</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-yellow-600"><?php echo $row['total_pending']; ?></p>
                            <p class="text-[9px] font-medium text-gray-400 uppercase tracking-tight">Menunggu</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-green-600"><?php echo $row['total_selesai']; ?></p>
                            <p class="text-[9px] font-medium text-gray-400 uppercase tracking-tight">Selesai</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-red-500"><?php echo $row['total_batal']; ?></p>
                            <p class="text-[9px] font-medium text-gray-400 uppercase tracking-tight">Batal</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-4 pt-0">
                <div class="flex items-center justify-between pt-3 border-t border-gray-100 gap-2">
                    <button type="button" onclick="openPesertaMasterModal(<?php echo $row['id_jadwal']; ?>, '<?php echo htmlspecialchars($row['nama_vaksin']); ?>', '<?php echo formatTanggalIndonesia($row['tanggal']); ?>')" 
                            class="flex-1 bg-green-600 text-white font-medium py-2 rounded-xl text-xs hover:bg-green-700 hover:shadow transition flex items-center justify-center gap-1">
                        <i class="fas fa-users"></i> Lihat Peserta
                    </button>
                    
                    <button type="button" onclick="bukaLaporanPdf('<?php echo $row['id_jadwal']; ?>')" 
                            class="bg-pink-50 text-pink-600 border border-pink-200 py-2 px-3 rounded-xl text-xs hover:bg-pink-100 hover:text-pink-700 font-semibold transition flex items-center justify-center gap-1" title="Cetak Laporan PDF">
                        <i class="fas fa-file-pdf text-sm"></i> Laporan
                    </button>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <?php if($total_pages > 1): ?>
    <div class="mt-8">
        <?php echo paginate($page, $total_pages, '', ['search' => $search]); ?>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
        <i class="fas fa-calendar-times text-6xl text-gray-300 mb-3"></i>
        <h3 class="text-xl font-bold text-gray-600 mb-1">Data Tidak Ditemukan</h3>
        <p class="text-gray-400 text-sm">Tidak ada jadwal yang cocok dengan kriteria atau pencarian Anda.</p>
        <?php if($search): ?>
        <a href="?" class="inline-block mt-4 text-sm font-bold text-green-600 hover:text-green-700 bg-green-50 px-4 py-2 rounded-xl transition">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Semua Jadwal
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<div id="masterPesertaModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4" onclick="closeMasterModal(event)">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[85vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="sticky top-0 bg-gradient-to-r from-green-600 to-emerald-500 p-4 rounded-t-2xl flex justify-between items-center z-20 shadow-sm">
            <div>
                <h3 class="text-lg font-bold text-white" id="modalJadwalTitle">Daftar Peserta Imunisasi</h3>
                <p class="text-green-100 text-xs mt-0.5" id="modalJadwalSubtitle"></p>
            </div>
            <button onclick="closeMasterModal(null)" class="text-white hover:text-gray-200 transition p-1">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="flex border-b border-gray-100 bg-gray-50 px-6 pt-2 gap-4">
            <button id="btnTabPending" onclick="switchSubTab('peserta')" class="px-4 py-2 text-sm font-semibold border-b-2 transition-all">
                <i class="fas fa-clock mr-1"></i> Menunggu
            </button>
            <button id="btnTabSelesai" onclick="switchSubTab('selesai')" class="px-4 py-2 text-sm font-semibold border-b-2 transition-all">
                <i class="fas fa-check-circle mr-1"></i> Selesai
            </button>
            <button id="btnTabBatal" onclick="switchSubTab('batal')" class="px-4 py-2 text-sm font-semibold border-b-2 transition-all">
                <i class="fas fa-times-circle mr-1"></i> Dibatalkan
            </button>
        </div>

        <div class="p-6" id="modalDynamicContent">
            <div class="text-center py-8 text-gray-400">Memuat data...</div>
        </div>
    </div>
</div>

<script>
let currentJadwalId = 0;

function bukaLaporanPdf(idJadwal) {
    document.getElementById('idJadwalCetakPost').value = idJadwal;
    document.getElementById('formCetakLaporanPdf').submit();
}

function openPesertaMasterModal(jadwalId, vaksinNama, tanggalText) {
    currentJadwalId = jadwalId;
    document.getElementById('modalJadwalTitle').innerText = 'Pelayanan - ' + vaksinNama;
    document.getElementById('modalJadwalSubtitle').innerHTML = '<i class="far fa-calendar-alt mr-1"></i> ' + tanggalText;
    
    document.getElementById('masterPesertaModal').classList.remove('hidden');
    document.getElementById('masterPesertaModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
    switchSubTab('peserta');
}

function switchSubTab(type) {
    const tabPending = document.getElementById('btnTabPending');
    const tabSelesai = document.getElementById('btnTabSelesai');
    const tabBatal = document.getElementById('btnTabBatal');
    const contentBox = document.getElementById('modalDynamicContent');
    
    contentBox.innerHTML = '<div class="text-center py-12 text-sm text-gray-400"><i class="fas fa-spinner animate-spin mr-2"></i>Memuat data...</div>';
    
    if (type === 'peserta') {
        tabPending.className = "px-4 py-2 text-sm font-bold border-b-2 border-green-600 text-green-700";
        tabSelesai.className = "px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-400 hover:text-gray-600";
        tabBatal.className = "px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-400 hover:text-gray-600";
    } else if (type === 'selesai') {
        tabSelesai.className = "px-4 py-2 text-sm font-bold border-b-2 border-green-600 text-green-700";
        tabPending.className = "px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-400 hover:text-gray-600";
        tabBatal.className = "px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-400 hover:text-gray-600";
    } else {
        tabBatal.className = "px-4 py-2 text-sm font-bold border-b-2 border-red-600 text-red-700";
        tabPending.className = "px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-400 hover:text-gray-600";
        tabSelesai.className = "px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-400 hover:text-gray-600";
    }

    fetch('get_peserta.php?id=' + currentJadwalId + '&type=' + type)
        .then(response => response.text())
        .then(data => { contentBox.innerHTML = data; })
        .catch(err => { contentBox.innerHTML = '<div class="text-center py-8 text-red-500">Gagal memuat data pelayanan.</div>'; });
}

function closeMasterModal(event) {
    if (event && event.target !== event.currentTarget && event.target.closest('.bg-white')) return;
    document.getElementById('masterPesertaModal').classList.add('hidden');
    document.getElementById('masterPesertaModal').classList.remove('flex');
    document.body.style.overflow = 'auto';
}

function konfirmasiBatalManual(idPendaftaran, idJadwal) {
    Swal.fire({
        title: 'Konfirmasi Pembatalan',
        text: 'Apakah Anda yakin ingin membatalkan peserta ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Batalkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang membatalkan pendaftaran',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            window.location.href = 'proses_batal.php?id=' + idPendaftaran + '&jadwal_id=' + idJadwal;
        }
    });
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        if (!document.getElementById('masterPesertaModal').classList.contains('hidden')) {
            closeMasterModal(null);
        }
    }
});
</script>

<?php if(isset($_GET['buka_jadwal'])): 
    $id_buka = (int)$_GET['buka_jadwal'];
    $q_inf = mysqli_query($conn, "SELECT v.nama_vaksin, j.tanggal FROM jadwal_imunisasi j JOIN vaksin v ON j.id_vaksin=v.id_vaksin WHERE j.id_jadwal=$id_buka");
    if($inf = mysqli_fetch_assoc($q_inf)):
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    openPesertaMasterModal(<?php echo $id_buka; ?>, '<?php echo htmlspecialchars($inf['nama_vaksin']); ?>', '<?php echo formatTanggalIndonesia($inf['tanggal']); ?>');
});
</script>
<?php endif; endif; ?>

<?php include __DIR__ . '/../../templates/footer.php'; ?>