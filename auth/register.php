<?php
require_once __DIR__ . '/../config/database.php';

$base_url = "/posyandu";
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $password_plain = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $password = md5($password_plain);
    $no_wa = '+62' . mysqli_real_escape_string($conn, $_POST['no_wa']);
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $status = ($role == 'bidan') ? 'pending' : 'active';

    if ($password_plain != $confirm_password) {
        $error = "Password tidak cocok!";
    } elseif (strlen($password_plain) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        $check = mysqli_query($conn, "SELECT nik FROM users WHERE nik='$nik'");
        if (mysqli_num_rows($check) > 0) {
            $error = "NIK sudah terdaftar!";
        } else {
            $insert = "INSERT INTO users (nik, PASSWORD, no_wa, nama_lengkap, alamat, ROLE, STATUS) 
                       VALUES ('$nik', '$password', '$no_wa', '$nama_lengkap', '$alamat', '$role', '$status')";
            
            if (mysqli_query($conn, $insert)) {
                $success = "Pendaftaran berhasil!";
            } else {
                $error = "Pendaftaran gagal: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SIPANDA</title>
    <link rel="icon" type="image/png" href="/posyandu/img/sipanda.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-2xl w-full">
        
        <div class="flex justify-center mb-6">
            <img src="<?= $base_url ?>/img/sipanda.png" alt="SIPANDA Logo" class="w-20 h-20 object-contain">
        </div>
        
        <?php if ($success): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '<?= addslashes($success); ?>',
                confirmButtonColor: '#10b981'
            }).then(() => {
                window.location.href = 'login.php';
            });
        </script>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '<?= addslashes($error); ?>',
                confirmButtonColor: '#dc2626'
            });
        </script>
        <?php endif; ?>
        
        <form method="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
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
                
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" required
                           placeholder="Masukkan nama lengkap"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition">
                </div>
                
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Daftar Sebagai</label>
                    <select name="role" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition">
                        <option value="ibu">Ibu</option>
                        <option value="bidan">Bidan</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-green-400"></i>
                        <input type="password" name="password" id="password" required
                               placeholder="Masukkan password (min 6 karakter)"
                               class="w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition">
                        <button type="button" onclick="togglePassword('password', 'eye1')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-green-600">
                            <i id="eye1" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-green-400"></i>
                        <input type="password" name="confirm_password" id="confirm_password" required
                               placeholder="Ulangi password"
                               class="w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition">
                        <button type="button" onclick="togglePassword('confirm_password', 'eye2')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-green-600">
                            <i id="eye2" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
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
                
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2">Alamat</label>
                    <textarea name="alamat" rows="3" placeholder="Masukkan alamat lengkap"
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition"></textarea>
                </div>
            </div>
            
            <button type="submit"
                    class="w-full bg-gradient-to-r from-green-600 to-emerald-500 text-white py-3 rounded-xl font-semibold mt-6 hover:shadow-lg transition duration-300 transform hover:scale-105 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Daftar Sekarang
            </button>

            <a href="/posyandu/index.php" 
               class="w-full flex items-center justify-center gap-2 mt-3 py-3 rounded-xl font-semibold text-green-600 border-2 border-green-500 hover:bg-green-600 hover:text-white hover:border-green-600 transition duration-300">
                Kembali
            </a>
        </form>
        
        <div class="mt-5 text-center">
            <a href="login.php" class="text-green-600 hover:text-green-700">
                Sudah punya akun? Login
            </a>
        </div>
    </div>

    <script>
        const nikInput = document.getElementById('nik');
        const nikCounter = document.getElementById('nikCounter');
        
        nikInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            let length = this.value.length;
            nikCounter.textContent = length + '/16';
            
            if (length == 16) {
                nikCounter.classList.remove('text-gray-400');
                nikCounter.classList.add('text-green-600');
            } else {
                nikCounter.classList.remove('text-green-600');
                nikCounter.classList.add('text-gray-400');
            }
        });
        
        const waInput = document.getElementById('no_wa');
        waInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.startsWith('0')) {
                this.value = this.value.substring(1);
            }
            if (this.value.length > 13) {
                this.value = this.value.substring(0, 13);
            }
        });
        
        function togglePassword(inputId, eyeId) {
            const input = document.getElementById(inputId);
            const eye = document.getElementById(eyeId);
            
            if (input.type === 'password') {
                input.type = 'text';
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>