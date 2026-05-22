<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_bidan.php';

$id_pemeriksaan = $_GET['id'];
$kehamilan_id = $_GET['kehamilan_id'];
$pemeriksaan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pemeriksaan_kehamilan WHERE id_pemeriksaan=$id_pemeriksaan"));

// Validasi jika data tidak ditemukan
if(!$pemeriksaan){
    $_SESSION['error'] = "Data pemeriksaan tidak ditemukan!";
    header("Location: detail_ibu_hamil.php?id=$kehamilan_id");
    exit();
}

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // Ambil data
    $usia = trim($_POST['usia_kehamilan']);
    $tanggal = $_POST['tanggal_pemeriksaan'];
    $berat = trim($_POST['berat_badan']);
    $tekanan = trim($_POST['tekanan_darah']);
    $lingkar = trim($_POST['lingkar_perut']);
    $tfu = trim($_POST['tinggi_fundus']);
    $djj = trim($_POST['detak_jantung_janin']);
    $keluhan = trim($_POST['keluhan']);
    $tindakan = trim($_POST['tindakan']);
    
    // Validasi
    if(empty($usia) || $usia < 0){
        $error = "Usia kehamilan tidak valid!";
    } elseif(empty($tanggal)){
        $error = "Tanggal pemeriksaan harus diisi!";
    } elseif(empty($berat) || $berat < 0){
        $error = "Berat badan tidak valid!";
    } elseif(empty($tekanan)){
        $error = "Tekanan darah harus diisi!";
    } elseif(!preg_match('/^\d{2,3}\/\d{2,3}$/', $tekanan)){
        $error = "Format tekanan darah tidak valid! Contoh: 120/80";
    } elseif(!empty($djj) && ($djj < 60 || $djj > 200)){
        $error = "Detak jantung janin tidak normal (60-200)";
    } else {
        $updated_by = $_SESSION['nik'];
        
        $query = "UPDATE pemeriksaan_kehamilan SET 
            usia_kehamilan='$usia', 
            tanggal_pemeriksaan='$tanggal', 
            berat_badan='$berat', 
            tekanan_darah='$tekanan', 
            lingkar_perut=" . ($lingkar ? "'$lingkar'" : "NULL") . ", 
            tinggi_fundus=" . ($tfu ? "'$tfu'" : "NULL") . ", 
            detak_jantung_janin=" . ($djj ? "'$djj'" : "NULL") . ", 
            keluhan=" . ($keluhan ? "'$keluhan'" : "NULL") . ", 
            tindakan=" . ($tindakan ? "'$tindakan'" : "NULL") . ", 
            updated_by='$updated_by' 
            WHERE id_pemeriksaan=$id_pemeriksaan";
        
        if(mysqli_query($conn, $query)){
            $_SESSION['success'] = "Pemeriksaan berhasil diupdate!";
            header("Location: detail_ibu_hamil.php?id=$kehamilan_id");
            exit();
        } else {
            $error = "Gagal mengupdate data: " . mysqli_error($conn);
        }
    }
}

$title = 'Edit Pemeriksaan';
include __DIR__ . '/../../templates/sidebar.php';
?>

<div class="max-w-2xl mx-auto fade-in">
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h1 class="text-2xl font-bold text-green-800 mb-6">Edit Pemeriksaan Kehamilan</h1>
        
        <?php if($error): ?>
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <div class="bg-blue-50 p-4 rounded-lg mb-6">
            <div class="flex items-center gap-2">
                <i class="fas fa-calendar-check text-blue-600"></i>
                <p class="text-gray-600">Edit data pemeriksaan tanggal <strong><?php echo date('d/m/Y', strtotime($pemeriksaan['tanggal_pemeriksaan'])); ?></strong></p>
            </div>
        </div>
        
        <form method="POST" id="formPemeriksaan">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-gray-700 mb-2">
                        Usia Kehamilan <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="usia_kehamilan" id="usia" value="<?php echo $pemeriksaan['usia_kehamilan']; ?>" required 
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400"
                           min="0" max="42">
                    <p class="text-xs text-gray-400 mt-1">Usia kehamilan dalam minggu (0-42 minggu)</p>
                </div>
                
                <div>
                    <label class="block font-semibold text-gray-700 mb-2">
                        Tanggal Pemeriksaan <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_pemeriksaan" id="tanggal" value="<?php echo $pemeriksaan['tanggal_pemeriksaan']; ?>" required 
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400">
                </div>
                
                <div>
                    <label class="block font-semibold text-gray-700 mb-2">
                        Berat Badan (kg) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="0.1" name="berat_badan" id="berat" value="<?php echo $pemeriksaan['berat_badan']; ?>" required 
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400"
                           min="30" max="150" placeholder="Contoh: 58.5">
                </div>
                
                <div>
                    <label class="block font-semibold text-gray-700 mb-2">
                        Tekanan Darah <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="tekanan_darah" id="tekanan" value="<?php echo $pemeriksaan['tekanan_darah']; ?>" placeholder="120/80" required 
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400">
                    <p class="text-xs text-gray-400 mt-1">Format: sistolik/diastolik (contoh: 120/80)</p>
                </div>
                
                <div>
                    <label class="block font-semibold text-gray-700 mb-2">Lingkar Perut (cm)</label>
                    <input type="number" step="0.1" name="lingkar_perut" id="lingkar" value="<?php echo $pemeriksaan['lingkar_perut']; ?>" 
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400"
                           min="50" max="150" placeholder="Contoh: 85.5">
                </div>
                
                <div>
                    <label class="block font-semibold text-gray-700 mb-2">Tinggi Fundus (cm)</label>
                    <input type="number" step="0.1" name="tinggi_fundus" id="tfu" value="<?php echo $pemeriksaan['tinggi_fundus']; ?>" 
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400"
                           min="10" max="50" placeholder="Contoh: 22">
                </div>
                
                <div>
                    <label class="block font-semibold text-gray-700 mb-2">Detak Jantung Janin (x/menit)</label>
                    <input type="number" name="detak_jantung_janin" id="djj" value="<?php echo $pemeriksaan['detak_jantung_janin']; ?>" 
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400"
                           min="60" max="200" placeholder="120-160">
                    <p class="text-xs text-gray-400 mt-1">Normal: 120-160 x/menit</p>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block font-semibold text-gray-700 mb-2">Keluhan</label>
                    <textarea name="keluhan" id="keluhan" rows="2" 
                              class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400"
                              placeholder="Keluhan ibu hamil (jika ada)"><?php echo $pemeriksaan['keluhan']; ?></textarea>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block font-semibold text-gray-700 mb-2">Tindakan / Edukasi</label>
                    <textarea name="tindakan" id="tindakan" rows="2" 
                              class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400"
                              placeholder="Tindakan yang diberikan atau edukasi untuk ibu"><?php echo $pemeriksaan['tindakan']; ?></textarea>
                </div>
            </div>
            
            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-500 text-white py-2 rounded-xl font-semibold hover:shadow-lg transition">
                    <i class="fas fa-save mr-2"></i> Update
                </button>
                <a href="detail_ibu_hamil.php?id=<?php echo $kehamilan_id; ?>" 
                   class="flex-1 bg-gray-200 text-gray-700 text-center py-2 rounded-xl font-semibold hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Validasi client-side
document.getElementById('formPemeriksaan').addEventListener('submit', function(e) {
    let usia = document.getElementById('usia').value;
    let berat = document.getElementById('berat').value;
    let tekanan = document.getElementById('tekanan').value;
    let djj = document.getElementById('djj').value;
    
    if(usia < 0 || usia > 42) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Usia kehamilan harus antara 0-42 minggu!',
            confirmButtonColor: '#dc2626'
        });
        return false;
    }
    
    if(berat < 30 || berat > 150) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Berat badan tidak realistis (30-150 kg)!',
            confirmButtonColor: '#dc2626'
        });
        return false;
    }
    
    if(!/^\d{2,3}\/\d{2,3}$/.test(tekanan)) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Format tekanan darah salah! Contoh: 120/80',
            confirmButtonColor: '#dc2626'
        });
        return false;
    }
    
    if(djj && (djj < 60 || djj > 200)) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Detak jantung janin harus antara 60-200 x/menit!',
            confirmButtonColor: '#dc2626'
        });
        return false;
    }
    
    return true;
});

// Format tekanan darah otomatis
const tekananInput = document.getElementById('tekanan');
tekananInput.addEventListener('input', function(e) {
    let value = this.value.replace(/[^0-9]/g, '');
    if(value.length >= 3) {
        this.value = value.slice(0, -2) + '/' + value.slice(-2);
    }
});
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>