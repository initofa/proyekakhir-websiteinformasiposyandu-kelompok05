<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';

$nik = $_GET['nik'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE nik='$nik'"));

// CEK USER - REDIRECT DULU SEBELUM SIDEBAR
if(!$user) {
    $_SESSION['error'] = "User tidak ditemukan!";
    header("Location: list_users.php");
    exit();
}

// PROSES UPDATE - REDIRECT DULU SEBELUM SIDEBAR
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $no_wa = $_POST['no_wa'];
    // Format no_wa dengan +62
    if(!str_starts_with($no_wa, '+62')) {
        $no_wa = '+62' . ltrim($no_wa, '0');
    }
    $nama_lengkap = $_POST['nama_lengkap'];
    $alamat = $_POST['alamat'];
    $role = $_POST['role'];
    $status = $_POST['status'];
    $updated_by = $_SESSION['nik'];
    
    // PERBAIKAN: Hanya update password jika diisi
    $password_query = "";
    if(!empty($_POST['password'])) {
        $password_query = ", password='".md5($_POST['password'])."'";
    }
    
    $query = "UPDATE users SET no_wa='$no_wa', nama_lengkap='$nama_lengkap', alamat='$alamat', role='$role', status='$status', updated_by='$updated_by' $password_query WHERE nik='$nik'";
    
    if(mysqli_query($conn, $query)) {
        $_SESSION['success'] = "User berhasil diupdate!";
        header("Location: list_users.php?tab=" . ($role == 'ibu' ? 'ibu' : 'bidan'));
        exit();
    } else {
        $_SESSION['error'] = "Gagal mengupdate user!";
        header("Location: list_users.php");
        exit();
    }
}

$title = 'Edit User';
include __DIR__ . '/../../templates/sidebar.php';
?>

<div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg p-6 fade-in">
    <h1 class="text-2xl font-bold text-green-800 mb-6">Edit Users</h1>
    
    <form method="POST" id="editUserForm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            
            <!-- NIK (Disabled) -->
            <div class="md:col-span-2">
                <label class="block text-gray-700 font-semibold mb-2">NIK</label>
                <div class="relative">
                    <i class="fas fa-id-card absolute left-3 top-1/2 -translate-y-1/2 text-green-400"></i>
                    <input type="text" value="<?php echo $user['nik']; ?>" disabled 
                           class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-100 text-gray-500">
                </div>
            </div>
            
            <!-- Nama Lengkap -->
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Nama Lengkap</label>
                <div class="relative">
                    <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-green-400"></i>
                    <input type="text" name="nama_lengkap" value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>" required 
                           class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition">
                </div>
            </div>
            
            <!-- Role -->
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Role</label>
                <div class="relative">
                    <i class="fas fa-user-tag absolute left-3 top-1/2 -translate-y-1/2 text-green-400"></i>
                    <select name="role" class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition appearance-none bg-white">
                        <option value="ibu" <?php echo $user['role']=='ibu'?'selected':''; ?>>Ibu</option>
                        <option value="bidan" <?php echo $user['role']=='bidan'?'selected':''; ?>>Bidan</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            
            <!-- Password (Opsional) -->
            <div class="md:col-span-2">
                <label class="block text-gray-700 font-semibold mb-2">Password <span class="text-gray-400 text-sm">(kosongkan jika tidak diubah)</span></label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-green-400"></i>
                    <input type="password" name="password" id="password" 
                           class="w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition"
                           placeholder="Masukkan password baru">
                    <button type="button" onclick="togglePassword('password','eye1')" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-green-600">
                        <i id="eye1" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <!-- Konfirmasi Password - Hanya tampil jika password diisi -->
            <div class="md:col-span-2" id="confirmPasswordDiv" style="display: none;">
                <label class="block text-gray-700 font-semibold mb-2">Konfirmasi Password</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-green-400"></i>
                    <input type="password" name="confirm_password" id="confirm_password" 
                           class="w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition"
                           placeholder="Ulangi password baru">
                    <button type="button" onclick="togglePassword('confirm_password','eye2')" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-green-600">
                        <i id="eye2" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <!-- WhatsApp -->
            <div>
                <label class="block text-gray-700 font-semibold mb-2">No. WhatsApp</label>
                <div class="flex">
                    <span class="inline-flex items-center px-4 rounded-l-xl border border-r-0 border-gray-200 bg-gray-100 text-gray-600 font-medium">
                        +62
                    </span>
                    <?php 
                    $no_wa_clean = preg_replace('/^\+62/', '', $user['no_wa']);
                    ?>
                    <input type="text" name="no_wa" id="no_wa" value="<?php echo $no_wa_clean; ?>" required maxlength="13"
                           placeholder="81234567890"
                           class="w-full px-4 py-3 border border-gray-200 rounded-r-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition">
                </div>
            </div>
            
            <!-- Status -->
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Status</label>
                <div class="relative">
                    <i class="fas fa-circle-info absolute left-3 top-1/2 -translate-y-1/2 text-green-400"></i>
                    <select name="status" class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition appearance-none bg-white">
                        <option value="active" <?php echo $user['status']=='active'?'selected':''; ?>>Aktif</option>
                        <option value="pending" <?php echo $user['status']=='pending'?'selected':''; ?>>Pending</option>
                        <option value="inactive" <?php echo $user['status']=='inactive'?'selected':''; ?>>Tidak Aktif</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            
            <!-- Alamat -->
            <div class="md:col-span-2">
                <label class="block text-gray-700 font-semibold mb-2">Alamat</label>
                <div class="relative">
                    <i class="fas fa-map-marker-alt absolute left-3 top-3 text-green-400"></i>
                    <textarea name="alamat" rows="3" 
                              class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition"
                              placeholder="Masukkan alamat lengkap"><?php echo htmlspecialchars($user['alamat']); ?></textarea>
                </div>
            </div>
            
        </div>
        
        <div class="flex gap-3 mt-6">
            <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-500 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition duration-300 transform hover:scale-105">
                <i class="fas fa-save mr-2"></i> Update
            </button>
            <a href="list_users.php" class="flex-1 bg-gray-200 text-gray-700 text-center py-3 rounded-xl font-semibold hover:bg-gray-300 transition duration-300">
                <i class="fas fa-times mr-2"></i> Batal
            </a>
        </div>
    </form>
</div>

<script>
// Tampilkan/sembunyikan konfirmasi password berdasarkan input password
const passwordInput = document.getElementById('password');
const confirmDiv = document.getElementById('confirmPasswordDiv');

passwordInput.addEventListener('input', function() {
    if(this.value.length > 0) {
        confirmDiv.style.display = 'block';
        document.getElementById('confirm_password').required = true;
    } else {
        confirmDiv.style.display = 'none';
        document.getElementById('confirm_password').required = false;
        document.getElementById('confirm_password').value = '';
    }
});

// Format nomor WA
const waInput = document.getElementById('no_wa');
if(waInput) {
    waInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if(this.value.startsWith('0')) {
            this.value = this.value.substring(1);
        }
    });
}

// Toggle password visibility
function togglePassword(inputId, eyeId) {
    const input = document.getElementById(inputId);
    const eye = document.getElementById(eyeId);
    
    if(input.type === 'password') {
        input.type = 'text';
        eye.classList.remove('fa-eye');
        eye.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        eye.classList.remove('fa-eye-slash');
        eye.classList.add('fa-eye');
    }
}

// Validasi form sebelum submit
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirm_password').value;
    
    // PERBAIKAN: Hanya validasi jika password diisi
    if(password !== '') {
        // Cek konfirmasi password tidak boleh kosong
        if(confirm === '') {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Harap isi konfirmasi password!',
                confirmButtonColor: '#dc2626'
            });
            return false;
        }
        
        // Cek kecocokan password
        if(password !== confirm) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Password tidak cocok!',
                confirmButtonColor: '#dc2626'
            });
            return false;
        }
        
        // Cek minimal panjang password
        if(password.length < 6) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Password minimal 6 karakter!',
                confirmButtonColor: '#dc2626'
            });
            return false;
        }
    }
    
    // Format no_wa sebelum submit (tambahkan +62 jika belum)
    let no_wa = document.getElementById('no_wa').value;
    if(no_wa && !no_wa.startsWith('+62')) {
        document.getElementById('no_wa').value = '+62' + no_wa;
    }
    
    return true;
});

// Inisialisasi: cek apakah password sudah terisi (misalnya dari sebelumnya)
if(passwordInput.value.length > 0) {
    confirmDiv.style.display = 'block';
}
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>