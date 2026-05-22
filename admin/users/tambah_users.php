<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';

// ============================================
// PROSES FORM - HARUS SEBELUM SIDEBAR
// ============================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nik = $_POST['nik'];
    $password_plain = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $password = md5($password_plain);
    
    // Format no_wa dengan +62
    $no_wa = $_POST['no_wa'];
    if(!str_starts_with($no_wa, '+62')) {
        $no_wa = '+62' . ltrim($no_wa, '0');
    }
    
    $nama_lengkap = $_POST['nama_lengkap'];
    $alamat = $_POST['alamat'];
    $role = $_POST['role'];
    $status = $_POST['status'];
    $created_by = $_SESSION['nik'];
    
    // Validasi password
    if($password_plain != $confirm_password) {
        $_SESSION['error'] = "Password tidak cocok!";
    } elseif(strlen($password_plain) < 6) {
        $_SESSION['error'] = "Password minimal 6 karakter!";
    } else {
        $check = mysqli_query($conn, "SELECT nik FROM users WHERE nik='$nik'");
        if(mysqli_num_rows($check) > 0) {
            $_SESSION['error'] = "NIK sudah terdaftar!";
        } else {
            $query = "INSERT INTO users (nik, password, no_wa, nama_lengkap, alamat, role, status, created_by) 
                      VALUES ('$nik', '$password', '$no_wa', '$nama_lengkap', '$alamat', '$role', '$status', '$created_by')";
            if(mysqli_query($conn, $query)) {
                $_SESSION['success'] = "User berhasil ditambahkan!";
                header("Location: list_users.php?tab=" . ($role == 'ibu' ? 'ibu' : 'bidan'));
                exit();
            } else {
                $_SESSION['error'] = "Gagal menambahkan user!";
            }
        }
    }
    header("Location: list_users.php");
    exit();
}

$title = 'Tambah User';
include __DIR__ . '/../../templates/sidebar.php';
?>

<div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg p-6 fade-in">
    <h1 class="text-2xl font-bold text-green-800 mb-6">Tambah Users</h1>
    
    <form method="POST">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            
            <!-- NIK -->
            <div class="md:col-span-2">
                <label class="block text-gray-700 font-semibold mb-2">NIK</label>
                <div class="relative">
                    <i class="fas fa-id-card absolute left-3 top-1/2 -translate-y-1/2 text-green-400"></i>
                    <input type="text" name="nik" id="nik" maxlength="16" required 
                           placeholder="Masukkan 16 digit NIK"
                           class="w-full pl-10 pr-16 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition">
                    <span id="nikCounter" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">0/16</span>
                </div>
            </div>
            
            <!-- Nama Lengkap -->
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Nama Lengkap</label>
                <div class="relative">
                    <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-green-400"></i>
                    <input type="text" name="nama_lengkap" required 
                           placeholder="Masukkan nama lengkap"
                           class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition">
                </div>
            </div>
            
            <!-- Role -->
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Role</label>
                <div class="relative">
                    <i class="fas fa-user-tag absolute left-3 top-1/2 -translate-y-1/2 text-green-400"></i>
                    <select name="role" required 
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition appearance-none bg-white">
                        <option value="ibu">Ibu</option>
                        <option value="bidan">Bidan</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            
            <!-- Password -->
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Password</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-green-400"></i>
                    <input type="password" name="password" id="password" required 
                           placeholder="Masukkan password"
                           class="w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition">
                    <button type="button" onclick="togglePassword('password','eye1')" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-green-600">
                        <i id="eye1" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <!-- Konfirmasi Password -->
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Konfirmasi Password</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-green-400"></i>
                    <input type="password" name="confirm_password" id="confirm_password" required 
                           placeholder="Ulangi password"
                           class="w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition">
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
                    <input type="text" name="no_wa" id="no_wa" required maxlength="13"
                           placeholder="81234567890"
                           class="w-full px-4 py-3 border border-gray-200 rounded-r-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition">
                </div>
            </div>
            
            <!-- Status -->
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Status</label>
                <div class="relative">
                    <i class="fas fa-circle-info absolute left-3 top-1/2 -translate-y-1/2 text-green-400"></i>
                    <select name="status" required 
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition appearance-none bg-white">
                        <option value="active">Aktif</option>
                        <option value="pending">Pending</option>
                        <option value="inactive">Tidak Aktif</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            
            <!-- Alamat -->
            <div class="md:col-span-2">
                <label class="block text-gray-700 font-semibold mb-2">Alamat</label>
                <div class="relative">
                    <i class="fas fa-map-marker-alt absolute left-3 top-3 text-green-400"></i>
                    <textarea name="alamat" rows="3" required 
                              placeholder="Masukkan alamat lengkap"
                              class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition"></textarea>
                </div>
            </div>
            
        </div>
        
        <div class="flex gap-3 mt-6">
            <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-500 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition duration-300 transform hover:scale-105">
                <i class="fas fa-save mr-2"></i> Simpan
            </button>
            <a href="list_users.php" class="flex-1 bg-gray-200 text-gray-700 text-center py-3 rounded-xl font-semibold hover:bg-gray-300 transition duration-300">
                <i class="fas fa-times mr-2"></i> Batal
            </a>
        </div>
    </form>
</div>

<script>
// Counter NIK
const nikInput = document.getElementById('nik');
const nikCounter = document.getElementById('nikCounter');

if(nikInput) {
    nikInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        let length = this.value.length;
        nikCounter.textContent = length + '/16';
        if(length == 16) {
            nikCounter.classList.remove('text-gray-400');
            nikCounter.classList.add('text-green-600');
        } else {
            nikCounter.classList.remove('text-green-600');
            nikCounter.classList.add('text-gray-400');
        }
    });
}

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
    const nik = document.getElementById('nik').value;
    
    if(nik.length !== 16) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'NIK harus 16 digit!',
            confirmButtonColor: '#dc2626'
        });
        return false;
    }
    
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
    
    // Format no_wa sebelum submit
    let no_wa = document.getElementById('no_wa').value;
    if(no_wa && !no_wa.startsWith('+62')) {
        document.getElementById('no_wa').value = '+62' + no_wa;
    }
    
    return true;
});
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>