<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_ibu.php';

$nik = $_SESSION['nik'];
$jadwal_id = isset($_GET['jadwal_id']) ? (int)$_GET['jadwal_id'] : 0;
$anak_id = isset($_GET['anak_id']) ? (int)$_GET['anak_id'] : 0;

if($jadwal_id == 0){
    $_SESSION['error'] = "Jadwal imunisasi tidak ditemukan!";
    header("Location: jadwal_imunisasi.php");
    exit();
}

function hitungUsiaBulan($tanggal_lahir) {
    $lahir = new DateTime($tanggal_lahir);
    $sekarang = new DateTime();
    $diff = $lahir->diff($sekarang);
    return ($diff->y * 12) + $diff->m;
}

// Proses pendaftaran
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $anak_id = (int)$_POST['anak_id'];
    
    $anak_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM anak WHERE id_anak = $anak_id AND nik_ibu = '$nik'"));
    $jadwal_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT j.*, v.usia_rekomendasi FROM jadwal_imunisasi j JOIN vaksin v ON j.id_vaksin=v.id_vaksin WHERE j.id_jadwal = $jadwal_id"));
    
    if($anak_data && $jadwal_data){
        $usia_anak_bulan = hitungUsiaBulan($anak_data['tanggal_lahir']);
        $usia_rekomendasi = (int)$jadwal_data['usia_rekomendasi'];
        
        // Logika Mutlak: Tidak boleh lebih atau kurang dari 12 bulan (1 tahun)
        $batas_bawah = $usia_rekomendasi - 12;
        $batas_atas = $usia_rekomendasi + 12;
        
        if($usia_anak_bulan < $batas_bawah){
            $_SESSION['error'] = "Usia anak kurang dari batas minimal yang diperbolehkan (minimal " . ($batas_bawah < 0 ? 0 : $batas_bawah) . " bulan).";
        } elseif($usia_anak_bulan > $batas_atas){
            $_SESSION['error'] = "Usia anak melebihi batas maksimal yang diperbolehkan (maksimal " . $batas_atas . " bulan).";
        } else {
            // Cek apakah sudah terdaftar
            $check = mysqli_query($conn, "SELECT id_pendaftaran FROM pendaftaran_imunisasi WHERE id_jadwal = $jadwal_id AND id_anak = $anak_id AND STATUS != 'batal'");
            
            if(mysqli_num_rows($check) > 0){ 
                $_SESSION['error'] = "Anak sudah terdaftar atau sedang mengantre untuk jadwal ini!"; 
            } else { 
                $created_by = $_SESSION['nik']; 
                $query = "INSERT INTO pendaftaran_imunisasi (id_jadwal, id_anak, STATUS, created_by) VALUES ($jadwal_id, $anak_id, 'pending', '$created_by')";
                
                if(mysqli_query($conn, $query)){
                    $_SESSION['success'] = "Pendaftaran berhasil! Silakan tunggu konfirmasi dari petugas."; 
                    header("Location: riwayat_imunisasi.php"); 
                    exit();
                } else {
                    $_SESSION['error'] = "Gagal melakukan pendaftaran!";
                }
            }
        }
    } else {
        $_SESSION['error'] = "Data tidak valid!";
    }
}

$title = 'Daftar Imunisasi';
include __DIR__ . '/../../templates/sidebar.php';

$anak_list = mysqli_query($conn, "SELECT * FROM anak WHERE nik_ibu='$nik' ORDER BY created_at DESC");
$jadwal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT j.*, v.nama_vaksin, v.usia_rekomendasi 
    FROM jadwal_imunisasi j 
    JOIN vaksin v ON j.id_vaksin=v.id_vaksin 
    WHERE j.id_jadwal = $jadwal_id"));

if(!$jadwal){
    $_SESSION['error'] = "Jadwal imunisasi tidak ditemukan!";
    header("Location: jadwal_imunisasi.php");
    exit();
}

$usia_rekomendasi = (int)$jadwal['usia_rekomendasi'];
if($usia_rekomendasi == 0){
    $usia_text = "0 bulan (Baru Lahir)";
} elseif($usia_rekomendasi >= 12){
    $tahun = floor($usia_rekomendasi / 12);
    $sisa_bulan = $usia_rekomendasi % 12;
    $usia_text = ($sisa_bulan > 0) ? $tahun . " tahun " . $sisa_bulan . " bulan" : $tahun . " tahun";
} else {
    $usia_text = $usia_rekomendasi . " bulan";
}
?>

<div class="max-w-md mx-auto fade-in">
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h1 class="text-2xl font-bold text-green-800 mb-6">Daftar Imunisasi</h1>
        
        <div class="bg-green-50 p-4 rounded-lg mb-6 border border-green-100">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-r from-green-600 to-emerald-500 rounded-full flex items-center justify-center text-white flex-shrink-0">
                    <i class="fas fa-syringe text-xl"></i>
                </div>
                <div>
                    <p class="font-bold text-gray-800 leading-tight"><?php echo htmlspecialchars($jadwal['nama_vaksin']); ?></p>
                    <p class="text-xs text-gray-500 mt-0.5"><?php echo date('d F Y', strtotime($jadwal['tanggal'])); ?> - <?php echo htmlspecialchars($jadwal['lokasi']); ?></p>
                    <p class="text-xs text-green-700 font-medium mt-1">
                        <i class="fas fa-baby mr-1"></i> Usia Rekomendasi: <strong><?php echo $usia_text; ?></strong>
                    </p>
                </div>
            </div>
        </div>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="mb-4 p-3 bg-red-100 text-red-700 text-sm rounded-xl font-medium">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="formDaftarImunisasi">
            <div class="mb-5">
                <label class="block font-semibold text-gray-700 mb-2">Pilih Anak Yang Akan Diimunisasi</label>
                <select name="anak_id" id="anak_id" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 text-sm bg-white">
                    <option disabled selected value="">Pilih Anak</option>
                    <?php 
                    if(mysqli_num_rows($anak_list) > 0):
                        while($a = mysqli_fetch_assoc($anak_list)): 
                            $usia_anak = hitungUsiaBulan($a['tanggal_lahir']);
                            $usia_rekom = (int)$jadwal['usia_rekomendasi'];
                            
                            $bat_bawah = $usia_rekom - 12;
                            $bat_atas = $usia_rekom + 12;
                            $is_valid = ($usia_anak >= $bat_bawah && $usia_anak <= $bat_atas);
                        ?>
                        <option value="<?php echo $a['id_anak']; ?>" 
                                data-usia="<?php echo $usia_anak; ?>"
                                data-rekom="<?php echo $usia_rekom; ?>"
                                data-nama="<?php echo htmlspecialchars($a['nama_anak']); ?>"
                                class="<?php echo !$is_valid ? 'text-red-600 font-medium' : ''; ?>"
                                <?php echo $anak_id == $a['id_anak'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($a['nama_anak']); ?> (Usia: <?php echo floor($usia_anak / 12) . 'th ' . ($usia_anak % 12) . 'bln'; ?>)<?php echo !$is_valid ? ' - Tidak sesuai kriteria usia' : ''; ?>
                        </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
                <?php if(mysqli_num_rows($anak_list) == 0): ?>
                    <p class="text-xs text-red-500 mt-1.5">Anda belum mendaftarkan data anak. Silakan tambah data anak terlebih dahulu.</p>
                <?php endif; ?>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" id="btnDaftar" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-500 text-white py-2 rounded-xl font-semibold hover:shadow-lg transition text-center" <?php echo mysqli_num_rows($anak_list) == 0 ? 'disabled' : ''; ?>>
                    Daftar
                </button>
                <a href="jadwal_imunisasi.php" class="flex-1 bg-gray-100 text-gray-600 text-center py-2 rounded-xl font-semibold hover:bg-gray-200 transition text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
const anakSelect = document.getElementById('anak_id');
const btnDaftar = document.getElementById('btnDaftar');

anakSelect.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    if(!selectedOption.value) return;

    const usiaAnak = parseInt(selectedOption.dataset.usia);
    const usiaRekom = parseInt(selectedOption.dataset.rekom);
    
    const batasBawah = usiaRekom - 12;
    const batasAtas = usiaRekom + 12;
    
    if(usiaAnak < batasBawah || usiaAnak > batasAtas) {
        btnDaftar.disabled = true;
        btnDaftar.className = 'flex-1 bg-gray-300 text-gray-400 py-2 rounded-xl font-semibold cursor-not-allowed text-center';
    } else {
        btnDaftar.disabled = false;
        btnDaftar.className = 'flex-1 bg-gradient-to-r from-green-600 to-emerald-500 text-white py-2 rounded-xl font-semibold hover:shadow-lg transition text-center';
    }
});

document.addEventListener('DOMContentLoaded', function() {
    if(anakSelect.value !== "") {
        anakSelect.dispatchEvent(new Event('change'));
    }
});
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>