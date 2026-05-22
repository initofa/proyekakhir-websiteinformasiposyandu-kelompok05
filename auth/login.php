<?php
require_once __DIR__ . '/../config/database.php';
$base_url = "/posyandu";

if (isLoggedIn()) {
    $user = getCurrentUser();
    if ($user['role'] == 'admin') header("Location: ../admin/dashboard.php");
    elseif ($user['role'] == 'bidan') header("Location: ../bidan/dashboard.php");
    else header("Location: ../ibu/dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $password = md5($_POST['password']);
    
    $query = "SELECT * FROM users WHERE nik = '$nik' AND password = '$password'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        if ($user['status'] == 'pending') {
            $error = "Akun Anda belum dikonfirmasi. Silakan hubungi admin.";
        } elseif ($user['status'] == 'inactive') {
            $error = "Akun Anda tidak aktif. Silakan hubungi admin.";
        } else {
            $_SESSION['nik'] = $user['nik'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];
            
            if ($user['role'] == 'admin') header("Location: ../admin/dashboard.php");
            elseif ($user['role'] == 'bidan') header("Location: ../bidan/dashboard.php");
            else header("Location: ../ibu/dashboard.php");
            exit();
        }
    } else {
        $error = "NIK atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIPANDA</title>
    <link rel="icon" type="image/png" href="/posyandu/img/sipanda.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 min-h-screen flex items-center justify-center">
        <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-md w-full mx-4">
       <div class="w-20 h-20 mx-auto">
        <img src="<?= $base_url ?>/img/sipanda.png" 
         alt="SIPANDA Logo" 
         class="w-full h-full object-cover">
        </div>

        <?php if ($error): ?>
        <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '<?php echo addslashes($error); ?>',
            confirmButtonColor: '#dc2626'
        });
        </script>
        <?php endif; ?>
        
        <?php if ($error == "Akun Anda belum dikonfirmasi. Silakan hubungi admin."): ?>
        <script>
        Swal.fire({
            icon: 'warning',
            title: 'Akun Belum Aktif',
            text: '<?php echo addslashes($error); ?>',
            confirmButtonColor: '#f59e0b',
            showCancelButton: true,
            confirmButtonText: 'Tutup',
            cancelButtonText: 'Hubungi Admin WhatsApp',
            cancelButtonColor: '#25D366',
            reverseButtons: true
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel) {

                let pesan =
                    "Halo Admin SIPANDA%0A%0A" +
                    "Saya ingin meminta konfirmasi akun bidan saya.%0A%0A" +
                    "Mohon bantuannya untuk aktivasi akun saya agar bisa mengakses sistem SIPANDA.%0A%0A" +
                    "Terima kasih";

                window.open(
                    "https://wa.me/6281999925324?text=" + pesan,
                    "_blank"
                );
            }
        });
        </script>
        <?php endif; ?>
                
       <form method="POST">
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2"> NIK</label>
                <div class="relative">
                    <i class="fas fa-id-card absolute left-3 top-1/2 transform -translate-y-1/2 text-green-400"></i>
                    <input type="text" name="nik" id="nik" maxlength="16" required placeholder="Masukkan 16 digit NIK" class="w-full pl-10 pr-16 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition">
                    <span id="nikCounter"class="absolute right-3 top-1/2 transform -translate-y-1/2 text-xs text-gray-400">
                        0/16
                    </span>
                </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-green-400"></i>
                        <input type="password" name="password" id="password" required placeholder="Masukkan password"  class="w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition" >
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-green-600">
                            <i id="eyeIcon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-emerald-500 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition duration-300 transform hover:scale-105">
                    Login
                </button>

            </form>
            <div class="mt-6 text-center">
                <a href="register.php" class="text-green-600 hover:text-green-700">
                    Belum punya akun? Daftar
                </a>
            </div>
    </div>

<script>
const nikInput = document.getElementById('nik');
const nikCounter = document.getElementById('nikCounter');

nikInput.addEventListener('input', function () {

    // hanya angka
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

function togglePassword() {

    const password = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    if (password.type === 'password') {

        password.type = 'text';

        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');

    } else {

        password.type = 'password';

        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}
</script>
</body>
</html>