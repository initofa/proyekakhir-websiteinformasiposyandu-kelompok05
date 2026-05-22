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

$result = mysqli_query($conn, "SELECT pi.*, a.nama_anak, v.nama_vaksin, j.tanggal, hi.berat_badan, hi.tinggi_badan, hi.status_gizi, hi.nafsu_makan
    FROM pendaftaran_imunisasi pi 
    JOIN anak a ON pi.id_anak = a.id_anak 
    JOIN jadwal_imunisasi j ON pi.id_jadwal = j.id_jadwal 
    JOIN vaksin v ON j.id_vaksin = v.id_vaksin 
    LEFT JOIN hasil_imunisasi hi ON pi.id_pendaftaran = hi.id_pendaftaran 
    WHERE a.nik_ibu = '$nik' $where_anak
    ORDER BY j.tanggal DESC 
    LIMIT $offset, $limit");
?>

<div class="fade-in">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-green-800">Riwayat Imunisasi</h1>
        <a href="jadwal_imunisasi.php" class="text-green-600 hover:text-green-700 text-sm">
            <i class="fas fa-calendar-plus mr-1"></i> Jadwal Imunisasi
        </a>
    </div>
    
    <!-- Filter Anak (Hanya muncul jika lebih dari 1 anak) -->
    <?php if($jumlah_anak > 1): ?>
    <div class="bg-white rounded-2xl shadow-lg p-4 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-3 items-end">
            <div class="flex-1">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Filter Berdasarkan Anak</label>
                <select name="anak_id" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400" onchange="this.form.submit()">
                    <option value="0">Semua Anak</option>
                    <?php mysqli_data_seek($anak_list, 0); while($a = mysqli_fetch_assoc($anak_list)): ?>
                    <option value="<?php echo $a['id_anak']; ?>" <?php echo $anak_filter == $a['id_anak'] ? 'selected' : ''; ?>>
                        <?php echo $a['nama_anak']; ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <?php if($anak_filter > 0): ?>
            <a href="riwayat_imunisasi.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition text-center">
                <i class="fas fa-times mr-1"></i> Reset Filter
            </a>
            <?php endif; ?>
        </form>
    </div>
    <?php endif; ?>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php while($row = mysqli_fetch_assoc($result)): 
            // Status hanya: pending, selesai, batal
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
            
            // Default jika status tidak dikenal
            $color = isset($status_color[$row['status']]) ? $status_color[$row['status']] : 'bg-gray-100 text-gray-700 border-l-4 border-gray-500';
            $icon = isset($status_icon[$row['status']]) ? $status_icon[$row['status']] : 'fa-question-circle';
            $text = isset($status_text[$row['status']]) ? $status_text[$row['status']] : ucfirst($row['status']);
        ?>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition transform hover:-translate-y-1 duration-300">
            <!-- Header Card -->
            <div class="<?php echo $color; ?> p-4">
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm">
                            <i class="fas fa-syringe text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800"><?php echo $row['nama_vaksin']; ?></h3>
                            <p class="text-xs text-gray-500"><?php echo $row['nama_anak']; ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium 
                            <?php echo $row['status'] == 'pending' ? 'bg-yellow-200 text-yellow-800' : 
                                ($row['status'] == 'selesai' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800'); ?>">
                            <i class="fas <?php echo $icon; ?> text-xs"></i>
                            <?php echo $text; ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Body Card -->
            <div class="p-4">
                <div class="flex items-center gap-2 text-gray-600 mb-3">
                    <i class="fas fa-calendar-alt text-green-500 w-4"></i>
                    <span class="text-sm"><?php echo date('d F Y', strtotime($row['tanggal'])); ?></span>
                </div>
                
                <?php if($row['status'] == 'selesai' && $row['berat_badan']): ?>
                <div class="bg-gray-50 rounded-lg p-3 mb-3">
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <p class="text-gray-500 text-xs">Berat Badan</p>
                            <p class="font-semibold text-gray-800"><?php echo $row['berat_badan']; ?> kg</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs">Tinggi Badan</p>
                            <p class="font-semibold text-gray-800"><?php echo $row['tinggi_badan']; ?> cm</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-gray-500 text-xs">Status Gizi</p>
                            <p class="font-semibold <?php echo $row['status_gizi'] == 'Normal' ? 'text-green-600' : 'text-yellow-600'; ?>">
                                <?php echo $row['status_gizi']; ?>
                                <?php if($row['status_gizi'] != 'Normal'): ?>
                                <i class="fas fa-exclamation-triangle ml-1 text-xs"></i>
                                <?php endif; ?>
                            </p>
                        </div>
                        <?php if($row['nafsu_makan']): ?>
                        <div class="col-span-2">
                            <p class="text-gray-500 text-xs">Nafsu Makan</p>
                            <p class="font-semibold text-gray-800"><?php echo ucfirst($row['nafsu_makan']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php elseif($row['status'] == 'pending'): ?>
                <div class="bg-yellow-50 rounded-lg p-3 mb-3 text-center">
                    <i class="fas fa-hourglass-half text-yellow-500 mr-1"></i>
                    <span class="text-xs text-yellow-700">Menunggu jadwal imunisasi</span>
                </div>
                <?php elseif($row['status'] == 'batal'): ?>
                <div class="bg-red-50 rounded-lg p-3 mb-3 text-center">
                    <i class="fas fa-ban text-red-500 mr-1"></i>
                    <span class="text-xs text-red-700">Pendaftaran dibatalkan</span>
                </div>
                <?php endif; ?>
                
                <!-- Tombol Detail -->
                <a href="detail_imunisasi.php?id=<?php echo $row['id_pendaftaran']; ?>" 
                   class="flex items-center justify-center gap-2 w-full bg-gradient-to-r from-green-600 to-emerald-500 text-white py-2 rounded-lg text-sm font-semibold hover:shadow-md transition">
                    <i class="fas fa-info-circle"></i> Lihat Detail
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
        <?php endwhile; ?>
        
        <?php if(mysqli_num_rows($result) == 0): ?>
        <div class="col-span-full">
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <i class="fas fa-syringe text-6xl text-gray-300 mb-4"></i>
                <i class="fas fa-times-circle text-6xl text-gray-300 -ml-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Riwayat Imunisasi</h3>
                <p class="text-gray-500">
                    <?php if($anak_filter > 0): ?>
                    Anak ini belum memiliki riwayat imunisasi
                    <?php else: ?>
                    Silakan daftar imunisasi terlebih dahulu
                    <?php endif; ?>
                </p>
                <a href="jadwal_imunisasi.php" class="inline-block mt-4 bg-green-600 text-white px-6 py-2 rounded-xl hover:bg-green-700 transition">
                    <i class="fas fa-calendar-plus mr-2"></i> Lihat Jadwal Imunisasi
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if($total_pages > 1): ?>
    <div class="mt-6">
        <?php echo paginate($page, $total_pages, "riwayat_imunisasi.php" . ($anak_filter > 0 ? "?anak_id=$anak_filter" : "")); ?>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>