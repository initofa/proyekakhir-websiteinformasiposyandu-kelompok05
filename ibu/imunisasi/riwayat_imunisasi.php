<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_ibu.php';
$title = 'Riwayat Imunisasi';
include __DIR__ . '/../../templates/sidebar.php';

$nik = $_SESSION['nik'];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$anak_filter = isset($_GET['anak_id']) ? (int)$_GET['anak_id'] : 0;
$limit = 12;
$offset = ($page - 1) * $limit;

// Ambil semua anak ibu
$anak_list = mysqli_query($conn, "SELECT * FROM anak WHERE nik_ibu='$nik' ORDER BY created_at DESC");
$jumlah_anak = mysqli_num_rows($anak_list);

// Jika hanya 1 anak dan tidak ada filter, otomatis pilih anak tersebut
if($jumlah_anak == 1 && $anak_filter == 0){
    $first_anak = mysqli_fetch_assoc($anak_list);
    $anak_filter = $first_anak['id_anak'];
    mysqli_data_seek($anak_list, 0);
}

// Query dengan filter anak
$where_anak = "";
if($anak_filter > 0){
    $where_anak = " AND a.id_anak = $anak_filter";
}

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran_imunisasi pi 
    JOIN anak a ON pi.id_anak = a.id_anak 
    WHERE a.nik_ibu='$nik' $where_anak"))['total'];
$total_pages = ceil($total / $limit);

// PERUBAHAN: Memastikan pemanggilan pi.STATUS alias status_pendaftaran agar tidak rancu dan terhindar dari error Undefined index
$result = mysqli_query($conn, "SELECT pi.*, pi.STATUS as status_pendaftaran, a.nama_anak, v.nama_vaksin, j.tanggal, hi.berat_badan, hi.tinggi_badan, hi.status_gizi, hi.nafsu_makan
    FROM pendaftaran_imunisasi pi 
    JOIN anak a ON pi.id_anak = a.id_anak 
    JOIN jadwal_imunisasi j ON pi.id_jadwal = j.id_jadwal 
    JOIN vaksin v ON j.id_vaksin = v.id_vaksin 
    LEFT JOIN hasil_imunisasi hi ON pi.id_pendaftaran = hi.id_pendaftaran 
    WHERE a.nik_ibu = '$nik' $where_anak
    ORDER BY j.tanggal DESC 
    LIMIT $offset, $limit");
?>

<form id="formDetailImunisasiPost" action="detail_imunisasi.php" method="POST" style="display:none;">
    <input type="hidden" name="id_pendaftaran" id="idPendaftaranPost">
</form>

<div class="fade-in">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-green-800">Riwayat Imunisasi</h1>
       
    </div>
    
    <?php if($jumlah_anak > 1): ?>
    <div class="bg-white rounded-2xl shadow-lg p-4 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-3 items-end">
            <div class="flex-1">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Filter Berdasarkan Anak</label>
                <select name="anak_id" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 bg-white text-sm" onchange="this.form.submit()">
                    <option value="0">Semua Anak</option>
                    <?php mysqli_data_seek($anak_list, 0); while($a = mysqli_fetch_assoc($anak_list)): ?>
                    <option value="<?php echo $a['id_anak']; ?>" <?php echo $anak_filter == $a['id_anak'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($a['nama_anak']); ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <?php if($anak_filter > 0): ?>
            <a href="riwayat_imunisasi.php" class="bg-gray-500 text-white px-6 py-2 rounded-xl hover:bg-gray-600 transition text-center text-sm font-semibold shadow-sm">
                <i class="fas fa-times mr-1"></i> Reset Filter
            </a>
            <?php endif; ?>
        </form>
    </div>
    <?php endif; ?>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if(mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)): 
            $status_curr = $row['status_pendaftaran']; // Menggunakan alias hasil query baru

            $status_color = [
                'pending' => 'bg-yellow-100 text-yellow-700 border-l-4 border-yellow-500',
                'selesai' => 'bg-green-100 text-green-700 border-l-4 border-green-500',
                'batal' => 'bg-red-100 text-red-700 border-l-4 border-red-500'
            ];
            $status_icon = [
                'pending' => 'fa-clock',
                'selesai' => 'fa-check-double',
                'batal' => 'fa-times-circle'
            ];
            $status_text = [
                'pending' => 'Menunggu',
                'selesai' => 'Selesai',
                'batal' => 'Dibatalkan'
            ];
            
            $color = isset($status_color[$status_curr]) ? $status_color[$status_curr] : 'bg-gray-100 text-gray-700 border-l-4 border-gray-500';
            $icon = isset($status_icon[$status_curr]) ? $status_icon[$status_curr] : 'fa-question-circle';
            $text = isset($status_text[$status_curr]) ? $status_text[$status_curr] : ucfirst($status_curr);
        ?>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition transform hover:-translate-y-1 duration-300 flex flex-col justify-between border border-gray-100">
            <div>
                <div class="<?php echo $color; ?> p-4 bg-opacity-40">
                    <div class="flex justify-between items-start gap-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm flex-shrink-0">
                                <i class="fas fa-syringe text-green-600 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 text-sm leading-tight"><?php echo htmlspecialchars($row['nama_vaksin']); ?></h3>
                                <p class="text-xs text-gray-500 mt-0.5"><?php echo htmlspecialchars($row['nama_anak']); ?></p>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold shadow-sm
                                <?php echo $status_curr == 'pending' ? 'bg-yellow-200 text-yellow-800' : 
                                    ($status_curr == 'selesai' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800'); ?>">
                                <i class="fas <?php echo $icon; ?> text-[10px]"></i>
                                <?php echo $text; ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 space-y-3">
                    <div class="flex items-center gap-2 text-gray-600 bg-gray-50 p-2.5 rounded-xl border border-gray-100 text-xs">
                        <i class="fas fa-calendar-alt text-green-500 w-4 text-center"></i>
                        <span class="font-medium"><?php echo formatTanggalIndonesia($row['tanggal']); ?></span>
                    </div>
                    
                    <?php if($status_curr == 'selesai' && $row['berat_badan']): ?>
                    <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm space-y-2">
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <p class="text-gray-400 font-medium">Berat Badan</p>
                                <p class="font-bold text-gray-700 text-sm mt-0.5"><?php echo number_format($row['berat_badan'], 2); ?> kg</p>
                            </div>
                            <div>
                                <p class="text-gray-400 font-medium">Tinggi Badan</p>
                                <p class="font-bold text-gray-700 text-sm mt-0.5"><?php echo number_format($row['tinggi_badan'], 2); ?> cm</p>
                            </div>
                            <div class="col-span-2 pt-1 border-t border-gray-100">
                                <p class="text-gray-400 font-medium">Status Gizi</p>
                                <p class="font-bold text-sm mt-0.5 <?php echo $row['status_gizi'] == 'Normal' ? 'text-green-600' : 'text-yellow-600'; ?>">
                                    <?php echo htmlspecialchars($row['status_gizi'] ?: 'Normal'); ?>
                                    <?php if($row['status_gizi'] != 'Normal' && $row['status_gizi'] != ''): ?>
                                    <i class="fas fa-exclamation-triangle ml-1 text-xs"></i>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <?php if($row['nafsu_makan']): ?>
                            <div class="col-span-2 pt-1 border-t border-gray-100">
                                <p class="text-gray-400 font-medium">Nafsu Makan Balita</p>
                                <p class="font-semibold text-gray-700 mt-0.5"><?php echo ucfirst($row['nafsu_makan']); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php elseif($status_curr == 'pending'): ?>
                    <div class="bg-yellow-50/50 border border-yellow-100 rounded-xl p-4 text-center">
                        <i class="fas fa-hourglass-half text-yellow-500 text-lg mb-1 block animate-spin" style="animation-duration: 3s;"></i>
                        <span class="text-xs text-yellow-700 font-medium">Menunggu kedatangan dan pelayanan di lokasi posyandu</span>
                    </div>
                    <?php elseif($status_curr == 'batal'): ?>
                    <div class="bg-red-50/50 border border-red-100 rounded-xl p-4 text-center">
                        <i class="fas fa-ban text-red-400 text-lg mb-1 block"></i>
                        <span class="text-xs text-red-700 font-medium">Sesi pendaftaran imunisasi ini telah dibatalkan</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="p-4 pt-0">
                <button type="button" onclick="bukaDetailImunisasiPost('<?php echo $row['id_pendaftaran']; ?>')" 
                        class="w-full bg-gradient-to-r from-green-600 to-emerald-500 text-white py-2 rounded-xl text-xs font-bold hover:shadow-lg transition flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="fas fa-info-circle text-sm"></i> Lihat Detail
                </button>
            </div>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
        <div class="col-span-full bg-white rounded-2xl shadow-lg p-12 text-center">
            <i class="fas fa-history text-6xl text-gray-300 mb-3"></i>
            <h3 class="text-xl font-bold text-gray-600 mb-1">Belum Ada Riwayat</h3>
            <p class="text-gray-400 text-sm">
                <?php if($anak_filter > 0): ?>
                Anak yang dipilih belum memiliki riwayat pendaftaran imunisasi.
                <?php else: ?>
                Anda belum memiliki riwayat pendaftaran pelayanan imunisasi di posyandu.
                <?php endif; ?>
            </p>
            <a href="jadwal_imunisasi.php" class="inline-block mt-4 bg-green-600 text-white px-5 py-2 rounded-xl text-xs font-semibold hover:bg-green-700 transition shadow-sm">
                <i class="fas fa-calendar-plus mr-1"></i> Lihat Jadwal Aktif
            </a>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if($total_pages > 1): ?>
    <div class="mt-8 flex justify-center">
        <?php echo paginate($page, $total_pages, "riwayat_imunisasi.php" . ($anak_filter > 0 ? "?anak_id=$anak_filter" : "")); ?>
    </div>
    <?php endif; ?>
</div>

<script>
// Fungsi pemicu form POST tersembunyi demi menjaga kebersihan url parameter
function bukaDetailImunisasiPost(idPendaftaran) {
    document.getElementById('idPendaftaranPost').value = idPendaftaran;
    document.getElementById('formDetailImunisasiPost').submit();
}
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>