<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_ibu.php';
$title = 'Daftar Kehamilan';
include __DIR__ . '/../../templates/sidebar.php';

$nik = $_SESSION['nik'];

$cek_aktif = mysqli_query($conn, "SELECT id_kehamilan FROM ibu_hamil WHERE nik_ibu = '$nik' AND status_kehamilan = 'aktif'");
$ada_kehamilan_aktif = mysqli_num_rows($cek_aktif) > 0;

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST' && !$ada_kehamilan_aktif){
    $hpht = $_POST['hpht'];
    $usia_kehamilan = $_POST['usia_kehamilan'];
    $berat_badan = $_POST['berat_badan_ibu'];
    $tinggi_badan = $_POST['tinggi_badan_ibu'];
    $tekanan_darah = $_POST['tekanan_darah'];
    $catatan = $_POST['catatan_kesehatan'];
    $created_by = $nik;
    
    $hpl_date = date('Y-m-d', strtotime($hpht . ' + 280 days'));
    
    $query = "INSERT INTO ibu_hamil (nik_ibu, usia_kehamilan, hpht, hpl, berat_badan_ibu, tinggi_badan_ibu, tekanan_darah, status_kehamilan, catatan_kesehatan, created_by) 
              VALUES ('$nik', '$usia_kehamilan', '$hpht', '$hpl_date', '$berat_badan', '$tinggi_badan', '$tekanan_darah', 'aktif', '$catatan', '$created_by')";
    
    if(mysqli_query($conn, $query)){
        $_SESSION['success'] = "Pendaftaran kehamilan berhasil!";
        header("Location: index.php");
        exit();
    } else {
        $error = "Gagal mendaftarkan kehamilan: " . mysqli_error($conn);
    }
}
?>

<div class="max-w-2xl mx-auto fade-in">
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h1 class="text-2xl font-bold text-green-800 mb-6">Daftar Kehamilan</h1>
        
        <?php if($ada_kehamilan_aktif): ?>
        <div class="bg-yellow-100 border-l-4 border-yellow-500 p-4 rounded-r-lg mb-6">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                <div>
                    <p class="font-semibold text-yellow-800">Anda sudah memiliki kehamilan aktif!</p>
                    <p class="text-sm text-yellow-700">Tidak dapat mendaftarkan kehamilan baru sebelum kehamilan saat ini selesai.</p>
                    <a href="index.php" class="inline-block mt-2 text-sm text-green-600 hover:text-green-700">
                        <i class="fas fa-arrow-left mr-1"></i> Lihat riwayat kehamilan
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if($error): ?>
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" id="formKehamilan">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block font-semibold text-gray-700 mb-2">
                        HPHT 
                        <button type="button" id="btnInfoHpht" class="text-blue-500 hover:text-blue-700">
                            <i class="fas fa-question-circle"></i>
                        </button>
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="hpht" id="hpht" required value=""
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400">
                    
                    <div class="text-sm text-green-700 bg-green-50 p-3 rounded-md mt-2 border border-green-200">
                        <i class="fas fa-female mr-1"></i> 
                        <strong>Apa itu HPHT?</strong> Hari Pertama Haid Terakhir, yaitu <span class="font-semibold">tanggal pertama kali Anda mengalami menstruasi sebelum hamil</span>.
                        <br>
                        <span class="text-xs text-gray-600">📌 Contoh: Jika haid terakhir Anda mulai tanggal 10 Januari 2026, maka HPHT = 10 Januari 2026.</span>
                    </div>
                </div>
                
                <div>
                    <label class="block font-semibold text-gray-700 mb-2">
                        Usia Kehamilan (minggu)
                    </label>
                    <input type="number" name="usia_kehamilan" id="usia_kehamilan" readonly 
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-100">
                    <p class="text-xs text-gray-400 mt-1">✅ Dihitung otomatis dari HPHT</p>
                </div>
                
                <div>
                    <label class="block font-semibold text-gray-700 mb-2">
                        HPL
                        <button type="button" id="btnInfoHpl" class="text-blue-500 hover:text-blue-700">
                            <i class="fas fa-question-circle"></i>
                        </button>
                    </label>
                    <input type="text" id="hpl_display" readonly 
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-100">
                    <p class="text-xs text-gray-400 mt-1">
                        HPL = HPHT + 280 hari (perkiraan, bukan tanggal pasti lahiran)
                    </p>
                </div>
                
                <div>
                    <label class="block font-semibold text-gray-700 mb-2">
                        Berat Badan (kg) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="0.1" name="berat_badan_ibu" id="berat_badan_ibu" required 
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400"
                           placeholder="Contoh: 55.5">
                </div>
                
                <div>
                    <label class="block font-semibold text-gray-700 mb-2">
                        Tinggi Badan (cm) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="0.1" name="tinggi_badan_ibu" id="tinggi_badan_ibu" required 
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400"
                           placeholder="Contoh: 158">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block font-semibold text-gray-700 mb-2">
                        Tekanan Darah <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="tekanan_darah" id="tekanan" required 
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400"
                           placeholder="120/80">
                    <p class="text-xs text-gray-400 mt-1">Format: sistolik/diastolik (contoh: 120/80)</p>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block font-semibold text-gray-700 mb-2">
                        📝 Catatan Kesehatan
                    </label>
                    <textarea name="catatan_kesehatan" rows="3" 
                              class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400"
                              placeholder="Catatan khusus (jika ada)"></textarea>
                </div>
            </div>
            
            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-500 text-white py-2 rounded-xl font-semibold hover:shadow-lg transition" 
                        <?php echo $ada_kehamilan_aktif ? 'disabled' : ''; ?>>
                    <i class="fas fa-save mr-2"></i> Daftar Kehamilan
                </button>
                <a href="index.php" class="flex-1 bg-gray-200 text-gray-700 text-center py-2 rounded-xl font-semibold hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function hitungUsiaKehamilan() {
    const hpht = document.getElementById('hpht').value;
    if(hpht) {
        const hphtDate = new Date(hpht);
        const today = new Date();
        
        if(hphtDate > today) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Tanggal HPHT tidak boleh lebih dari hari ini!',
                confirmButtonColor: '#dc2626'
            });
            document.getElementById('hpht').value = '';
            document.getElementById('usia_kehamilan').value = '';
            document.getElementById('hpl_display').value = '';
            return;
        }
        
        const diffTime = today - hphtDate;
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
        const minggu = Math.floor(diffDays / 7);
        
        if(minggu >= 0 && minggu <= 42) {
            document.getElementById('usia_kehamilan').value = minggu;
        } else if(minggu > 42) {
            document.getElementById('usia_kehamilan').value = minggu;
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: 'Usia kehamilan melebihi 42 minggu. Silakan konsultasi dengan bidan!',
                confirmButtonColor: '#eab308'
            });
        } else if(minggu < 0) {
            document.getElementById('usia_kehamilan').value = 0;
        }
        
        const hplDate = new Date(hphtDate);
        hplDate.setDate(hplDate.getDate() + 280);
        const day = String(hplDate.getDate()).padStart(2, '0');
        const month = String(hplDate.getMonth() + 1).padStart(2, '0');
        const year = hplDate.getFullYear();
        document.getElementById('hpl_display').value = day + '/' + month + '/' + year;
    } else {
        document.getElementById('usia_kehamilan').value = '';
        document.getElementById('hpl_display').value = '';
    }
}

document.getElementById('btnInfoHpht').addEventListener('click', function() {
    Swal.fire({
        title: '📅 Apa itu HPHT?',
        html: `
            <div class="text-left">
                <p><strong>HPHT</strong> = <strong>H</strong>ari <strong>P</strong>ertama <strong>H</strong>aid <strong>T</strong>erakhir</p>
                <p class="mt-2">Yaitu <span class="text-green-600 font-semibold">tanggal pertama</span> Anda mengalami menstruasi <span class="text-green-600 font-semibold">sebelum hamil</span>.</p>
                <hr class="my-2">
                <p class="text-sm text-gray-600">📌 <strong>Contoh:</strong></p>
                <p class="text-sm">Jika haid terakhir Anda mulai tanggal <strong>10 Januari 2026</strong>, maka HPHT = <strong>10 Januari 2026</strong>.</p>
                <hr class="my-2">
                <p class="text-sm text-yellow-600">⚠️ <strong>Catatan:</strong> HPHT tidak boleh lebih dari hari ini.</p>
                <p class="text-sm text-blue-600 mt-2">💡 <strong>Tips:</strong> Jika lupa tanggal pasti, perkirakan semampu Anda atau konsultasi dengan bidan.</p>
            </div>
        `,
        icon: 'question',
        confirmButtonText: 'Mengerti',
        confirmButtonColor: '#22c55e'
    });
});

document.getElementById('btnInfoHpl').addEventListener('click', function() {
    Swal.fire({
        title: '🤰 Apa itu HPL?',
        html: `
            <div class="text-left">
                <p><strong>HPL</strong> = <strong>H</strong>ari <strong>P</strong>erkiraan <strong>L</strong>ahir</p>
                <p class="mt-2">Ini adalah <span class="text-green-600 font-semibold">perkiraan tanggal lahir</span> bayi Anda.</p>
                <hr class="my-2">
                <p class="text-sm text-gray-600">📌 <strong>Cara hitung:</strong></p>
                <p class="text-sm">HPHT + 280 hari (sekitar 40 minggu).</p>
                <hr class="my-2">
                <p class="text-sm text-yellow-600">⚠️ <strong>Catatan Penting:</strong></p>
                <p class="text-sm">HPL hanya <strong>perkiraan</strong>. Bayi bisa lahir lebih cepat atau lebih lambat 1-2 minggu dari tanggal ini.</p>
                <p class="text-sm text-blue-600 mt-2">💡 Yang terpenting adalah rutin memeriksakan kehamilan ke bidan!</p>
            </div>
        `,
        icon: 'info',
        confirmButtonText: 'Mengerti',
        confirmButtonColor: '#22c55e'
    });
});

document.getElementById('hpht').addEventListener('change', function() {
    hitungUsiaKehamilan();
});

document.getElementById('hpht').addEventListener('input', function() {
    if(this.value) {
        hitungUsiaKehamilan();
    }
});

const tekananInput = document.getElementById('tekanan');
tekananInput.addEventListener('input', function(e) {
    let value = this.value.replace(/[^0-9]/g, '');
    if(value.length >= 3) {
        this.value = value.slice(0, -2) + '/' + value.slice(-2);
    }
});

document.getElementById('formKehamilan').addEventListener('submit', function(e) {
    const hpht = document.getElementById('hpht').value;
    const usia = document.getElementById('usia_kehamilan').value;
    const berat = document.getElementById('berat_badan_ibu').value;
    const tinggi = document.getElementById('tinggi_badan_ibu').value;
    const tekanan = document.getElementById('tekanan').value;
    
    if(!hpht) {
        e.preventDefault();
        Swal.fire({ icon: 'error', title: 'Gagal!', text: 'HPHT harus diisi!', confirmButtonColor: '#dc2626' });
        return false;
    }
    
    if(!usia || usia < 0) {
        e.preventDefault();
        Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Usia kehamilan tidak valid!', confirmButtonColor: '#dc2626' });
        return false;
    }
    
    if(!berat || berat < 30 || berat > 150) {
        e.preventDefault();
        Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Berat badan tidak valid (30-150 kg)!', confirmButtonColor: '#dc2626' });
        return false;
    }
    
    if(!tinggi || tinggi < 100 || tinggi > 200) {
        e.preventDefault();
        Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Tinggi badan tidak valid (100-200 cm)!', confirmButtonColor: '#dc2626' });
        return false;
    }
    
    if(!tekanan || !/^\d{2,3}\/\d{2,3}$/.test(tekanan)) {
        e.preventDefault();
        Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Format tekanan darah salah! Contoh: 120/80', confirmButtonColor: '#dc2626' });
        return false;
    }
    
    return true;
});
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>