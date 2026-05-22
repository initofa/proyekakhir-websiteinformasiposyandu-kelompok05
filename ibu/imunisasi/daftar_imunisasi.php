<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_ibu.php';

$nik = $_SESSION['nik'];
$jadwal_id = isset($_GET['jadwal_id']) ? (int)$_GET['jadwal_id'] : 0;
$anak_id = isset($_GET['anak_id']) ? (int)$_GET['anak_id'] : 0;

// Redirect jika tidak ada jadwal_id
if($jadwal_id == 0){
    $_SESSION['error'] = "Jadwal imunisasi tidak ditemukan!";
    header("Location: jadwal_imunisasi.php");
    exit();
}

// Fungsi untuk menghitung usia dalam bulan
function hitungUsiaBulan($tanggal_lahir) {
    $lahir = new DateTime($tanggal_lahir);
    $sekarang = new DateTime();
    $diff = $lahir->diff($sekarang);
    return ($diff->y * 12) + $diff->m;
}

// Proses pendaftaran
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $anak_id = (int)$_POST['anak_id'];
    
    // Ambil data anak dan vaksin
    $anak_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM anak WHERE id_anak = $anak_id AND nik_ibu = '$nik'"));
    $jadwal_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT j.*, v.usia_rekomendasi FROM jadwal_imunisasi j JOIN vaksin v ON j.id_vaksin=v.id_vaksin WHERE j.id_jadwal = $jadwal_id"));
    
    if($anak_data && $jadwal_data){
        $usia_anak_bulan = hitungUsiaBulan($anak_data['tanggal_lahir']);
        $usia_rekomendasi = $jadwal_data['usia_rekomendasi'];
        
        // Validasi usia (maksimal 12 bulan lebih dari rekomendasi)
        $batas_atas = $usia_rekomendasi + 12;
        
        if($usia_anak_bulan < $usia_rekomendasi){
            $selisih = $usia_rekomendasi - $usia_anak_bulan;
            $_SESSION['error'] = "Anak belum cukup usia untuk vaksin ini. Usia minimal " . $usia_rekomendasi . " bulan. (Kurang $selisih bulan)";
        } elseif($usia_anak_bulan > $batas_atas){
            $selisih = $usia_anak_bulan - $usia_rekomendasi;
            $_SESSION['error'] = "Usia anak sudah melebihi batas maksimal vaksin ini (maksimal " . $batas_atas . " bulan). Kelebihan $selisih bulan. Silakan konsultasi dengan bidan.";
        } else {
            // Cek apakah sudah terdaftar
            $check = mysqli_query($conn, "SELECT id_pendaftaran FROM pendaftaran_imunisasi WHERE id_jadwal = $jadwal_id AND id_anak = $anak_id");
            
            if(mysqli_num_rows($check) > 0){ 
                $_SESSION['error'] = "Anak sudah terdaftar untuk jadwal ini!"; 
            } else { 
                $created_by = $_SESSION['nik']; 
                $query = "INSERT INTO pendaftaran_imunisasi (id_jadwal, id_anak, status, created_by) VALUES ($jadwal_id, $anak_id, 'pending', '$created_by')";
                
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

// Ambil data anak
$anak_list = mysqli_query($conn, "SELECT * FROM anak WHERE nik_ibu='$nik' ORDER BY created_at DESC");

// Ambil data jadwal dengan info usia rekomendasi
$jadwal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT j.*, v.nama_vaksin, v.usia_rekomendasi 
    FROM jadwal_imunisasi j 
    JOIN vaksin v ON j.id_vaksin=v.id_vaksin 
    WHERE j.id_jadwal = $jadwal_id"));

// Jika jadwal tidak ditemukan
if(!$jadwal){
    $_SESSION['error'] = "Jadwal imunisasi tidak ditemukan!";
    header("Location: jadwal_imunisasi.php");
    exit();
}

// Format usia rekomendasi
$usia_rekomendasi = $jadwal['usia_rekomendasi'];
if($usia_rekomendasi == 0){
    $usia_text = "0 bulan (baru lahir)";
} elseif($usia_rekomendasi >= 12){
    $tahun = floor($usia_rekomendasi / 12);
    $sisa_bulan = $usia_rekomendasi % 12;
    if($sisa_bulan > 0){
        $usia_text = $tahun . " tahun " . $sisa_bulan . " bulan";
    } else {
        $usia_text = $tahun . " tahun";
    }
} else {
    $usia_text = $usia_rekomendasi . " bulan";
}
?>

<div class="max-w-md mx-auto fade-in">
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h1 class="text-2xl font-bold text-green-800 mb-6">Daftar Imunisasi</h1>
        
        <div class="bg-green-50 p-4 rounded-lg mb-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-r from-green-600 to-emerald-500 rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-syringe text-xl"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-800"><?php echo $jadwal['nama_vaksin']; ?></p>
                    <p class="text-sm text-gray-600"><?php echo date('d F Y', strtotime($jadwal['tanggal'])); ?> - Posyandu</p>
                    <p class="text-xs text-green-600 mt-1">
                        <i class="fas fa-child mr-1"></i> Usia rekomendasi: <?php echo $usia_text; ?>
                    </p>
                </div>
            </div>
        </div>
        
        <form method="POST">
            <div class="mb-4">
                <label class="block font-semibold text-gray-700 mb-2">Pilih Anak</label>
                <select name="anak_id" id="anak_id" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400">
                    <option disabled selected value="">Pilih Anak</option>
                    <?php while($a = mysqli_fetch_assoc($anak_list)): 
                        $usia_anak = hitungUsiaBulan($a['tanggal_lahir']);
                        $usia_rekom = $jadwal['usia_rekomendasi'];
                        $batas_atas = $usia_rekom + 2;
                        $is_valid = ($usia_anak >= $usia_rekom && $usia_anak <= $batas_atas);
                    ?>
                    <option value="<?php echo $a['id_anak']; ?>" 
                            data-usia="<?php echo $usia_anak; ?>"
                            data-rekom="<?php echo $usia_rekom; ?>"
                            data-nama="<?php echo $a['nama_anak']; ?>"
                            <?php echo $anak_id == $a['id_anak'] ? 'selected' : ''; ?>>
                        <?php echo $a['nama_anak']; ?> (Usia: <?php echo floor($usia_anak / 12) . 'th ' . ($usia_anak % 12) . 'bln'; ?>)
                        <?php if(!$is_valid): ?> - ⚠️ Tidak sesuai usia<?php endif; ?>
                    </option>
                    <?php endwhile; ?>
                </select>
                <?php if(mysqli_num_rows($anak_list) == 0): ?>
                <p class="text-xs text-red-500 mt-1">Belum ada data anak. Silakan tambah data anak terlebih dahulu.</p>
                <?php endif; ?>
            </div>
            
            <!-- Info Validasi Usia -->
            <div id="validasiInfo" class="hidden mb-4 p-3 rounded-lg text-sm"></div>
            
            <div class="flex gap-3">
                <button type="submit" id="btnDaftar" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-500 text-white py-2 rounded-xl font-semibold hover:shadow-lg transition" <?php echo mysqli_num_rows($anak_list) == 0 ? 'disabled' : ''; ?>>
                    <i class="fas fa-save mr-2"></i> Daftar
                </button>
                <a href="jadwal_imunisasi.php" class="flex-1 bg-gray-200 text-gray-700 text-center py-2 rounded-xl font-semibold hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Ambil data dari select
const anakSelect = document.getElementById('anak_id');
const validasiInfo = document.getElementById('validasiInfo');
const btnDaftar = document.getElementById('btnDaftar');

anakSelect.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const usiaAnak = parseInt(selectedOption.dataset.usia);
    const usiaRekom = parseInt(selectedOption.dataset.rekom);
    const namaAnak = selectedOption.dataset.nama;
    const batasAtas = usiaRekom + 2;
    
    if(usiaAnak && usiaRekom) {
        if(usiaAnak < usiaRekom) {
            const selisih = usiaRekom - usiaAnak;
            validasiInfo.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> <strong>' + namaAnak + '</strong> belum cukup usia untuk vaksin ini. Usia minimal ' + usiaRekom + ' bulan. (Kurang ' + selisih + ' bulan)';
            validasiInfo.className = 'mb-4 p-3 rounded-lg text-sm bg-yellow-100 text-yellow-700';
            validasiInfo.classList.remove('hidden');
            btnDaftar.disabled = true;
            btnDaftar.classList.add('opacity-50', 'cursor-not-allowed');
        } else if(usiaAnak > batasAtas) {
            const selisih = usiaAnak - usiaRekom;
            validasiInfo.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> <strong>' + namaAnak + '</strong> sudah melebihi batas usia vaksin ini (maksimal ' + batasAtas + ' bulan). Kelebihan ' + selisih + ' bulan. Silakan konsultasi dengan bidan.';
            validasiInfo.className = 'mb-4 p-3 rounded-lg text-sm bg-red-100 text-red-700';
            validasiInfo.classList.remove('hidden');
            btnDaftar.disabled = true;
            btnDaftar.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            validasiInfo.innerHTML = '<i class="fas fa-check-circle mr-2"></i> <strong>' + namaAnak + '</strong> sudah sesuai usia untuk vaksin ini.';
            validasiInfo.className = 'mb-4 p-3 rounded-lg text-sm bg-green-100 text-green-700';
            validasiInfo.classList.remove('hidden');
            btnDaftar.disabled = false;
            btnDaftar.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    } else {
        validasiInfo.classList.add('hidden');
        btnDaftar.disabled = false;
        btnDaftar.classList.remove('opacity-50', 'cursor-not-allowed');
    }
});
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>