<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'ibu';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Handle konfirmasi bidan / ibu (terima / aktifkan)
if (isset($_GET['confirm'])) {
    $nik = $_GET['confirm'];
    mysqli_query($conn, "UPDATE users SET STATUS='active', updated_by='{$_SESSION['nik']}' WHERE nik='$nik'");
    $_SESSION['success'] = "Akun berhasil dikonfirmasi!";
    header("Location: list_users.php?tab=" . $tab); 
    exit();
}

// Handle tolak
if (isset($_GET['reject'])) {
    $nik = $_GET['reject'];
    mysqli_query($conn, "UPDATE users SET STATUS='inactive', updated_by='{$_SESSION['nik']}' WHERE nik='$nik'");
    $_SESSION['warning'] = "Pendaftaran ditolak!";
    header("Location: list_users.php?tab=" . $tab);
    exit();
}

// Handle aktifkan kembali bidan atau ibu
if (isset($_GET['activate'])) {
    $nik = $_GET['activate'];
    mysqli_query($conn, "UPDATE users SET STATUS='active', updated_by='{$_SESSION['nik']}' WHERE nik='$nik'");
    $_SESSION['success'] = "Akun berhasil diaktifkan kembali!";
    header("Location: list_users.php?tab=" . $tab);
    exit();
}

// Handle nonaktifkan bidan atau ibu
if (isset($_GET['deactivate'])) {
    $nik = $_GET['deactivate'];
    mysqli_query($conn, "UPDATE users SET STATUS='inactive', updated_by='{$_SESSION['nik']}' WHERE nik='$nik'");
    $_SESSION['warning'] = "Akun berhasil dinonaktifkan!";
    header("Location: list_users.php?tab=" . $tab);
    exit();
}

$total_ibu = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE ROLE='ibu'"))['total'];
$total_bidan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE ROLE='bidan'"))['total'];

$title = 'Manajemen Users';
include __DIR__ . '/../../templates/sidebar.php';

// Query untuk Ibu
if ($tab == 'ibu') {
    $where = "WHERE ROLE='ibu'";
    if ($search) $where .= " AND (nama_lengkap LIKE '%$search%' OR nik LIKE '%$search%')";
    $total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users $where"))['total'];
    $total_pages = ceil($total / $limit);
    $result = mysqli_query($conn, "SELECT * FROM users $where ORDER BY CASE STATUS WHEN 'active' THEN 0 ELSE 1 END, created_at DESC LIMIT $offset, $limit");
} 
// Query untuk Bidan
else {
    $where = "WHERE ROLE='bidan'";
    if ($search) $where .= " AND (nama_lengkap LIKE '%$search%' OR nik LIKE '%$search%')";
    $total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users $where"))['total'];
    $total_pages = ceil($total / $limit);
    $result = mysqli_query($conn, "SELECT * FROM users $where ORDER BY 
        CASE STATUS 
            WHEN 'pending' THEN 0 
            WHEN 'active' THEN 1 
            ELSE 2 
        END, 
        created_at DESC LIMIT $offset, $limit");
}
?>

<!-- Form Tersembunyi Global untuk Mengirim NIK via POST ke Halaman Edit -->
<form id="formEditPost" action="edit_users.php" method="POST" style="display:none;">
    <input type="hidden" name="nik" id="nikEditPost">
</form>

<div class="fade-in">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-green-800">Users</h1>
        <a href="tambah_users.php" class="bg-gradient-to-r from-green-600 to-emerald-500 text-white px-4 py-2 rounded-xl hover:shadow-lg transition">
            <i class="fas fa-plus mr-2"></i>Users
        </a>
    </div>
    
    <!-- Tab Navigation dengan Total Terpisah -->
    <div class="flex gap-2 mb-6">
        <a href="?tab=ibu&page=1<?php echo $search ? '&search='.$search : ''; ?>" 
           class="px-6 py-2 rounded-xl font-medium transition-all duration-300 <?php echo $tab == 'ibu' ? 'bg-green-600 text-white shadow-lg' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200'; ?>">
            <i class="fas fa-female mr-2"></i> Ibu 
            <span class="ml-1 text-sm <?php echo $tab == 'ibu' ? 'text-green-200' : 'text-gray-400'; ?>">(<?php echo number_format($total_ibu); ?>)</span>
        </a>
        <a href="?tab=bidan&page=1<?php echo $search ? '&search='.$search : ''; ?>" 
           class="px-6 py-2 rounded-xl font-medium transition-all duration-300 <?php echo $tab == 'bidan' ? 'bg-green-600 text-white shadow-lg' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200'; ?>">
            <i class="fas fa-user-md mr-2"></i> Bidan 
            <span class="ml-1 text-sm <?php echo $tab == 'bidan' ? 'text-green-200' : 'text-gray-400'; ?>">(<?php echo number_format($total_bidan); ?>)</span>
        </a>
    </div>
    
    <!-- Search Bar -->
    <div class="bg-white rounded-2xl shadow-lg p-4 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <input type="hidden" name="tab" value="<?php echo $tab; ?>">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari user (NIK atau Nama)..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200">
            </div>
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-xl hover:bg-green-700 transition flex items-center justify-center gap-2">
                <i class="fas fa-search"></i> Cari
            </button>
            <?php if($search): ?>
            <a href="list_users.php?tab=<?php echo $tab; ?>&page=1" class="bg-gray-500 text-white px-6 py-2 rounded-xl hover:bg-gray-600 transition flex items-center justify-center gap-2">
                <i class="fas fa-times"></i> Reset
            </a>
            <?php endif; ?>
        </form>
    </div>
    
    <!-- ==================== TABEL IBU ==================== -->
    <?php if ($tab == 'ibu'): ?>
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-green-600 to-emerald-500 text-white">
                        <th class="p-4 text-left text-sm font-semibold">No</th>
                        <th class="p-4 text-left text-sm font-semibold">NIK</th>
                        <th class="p-4 text-left text-sm font-semibold">Nama Lengkap</th>
                        <th class="p-4 text-left text-sm font-semibold">No. WhatsApp</th>
                        <th class="p-4 text-left text-sm font-semibold">Alamat</th>
                        <th class="p-4 text-left text-sm font-semibold">Status</th>
                        <th class="p-4 text-center text-sm font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php $no = $offset + 1; while($row = mysqli_fetch_assoc($result)): ?>
                    <tr class="hover:bg-gray-50 transition duration-200">
                        <td class="p-4 text-sm text-gray-600"><?php echo $no++; ?></td>
                        <td class="p-4 text-sm font-mono"><?php echo htmlspecialchars($row['nik']); ?></td>
                        <td class="p-4 font-semibold text-gray-800"><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                        <td class="p-4 text-sm"><?php echo htmlspecialchars($row['no_wa']); ?></td>
                        <td class="p-4 text-sm text-gray-600 max-w-xs break-words"><?php echo htmlspecialchars($row['alamat']); ?></td>
                        <td class="p-4">
                            <?php if($row['STATUS'] == 'active'): ?>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <i class="fas fa-check-circle mr-1 text-xs"></i> Aktif
                            </span>
                            <?php else: ?>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                <i class="fas fa-times-circle mr-1 text-xs"></i> Tidak Aktif
                            </span>
                            <?php endif; ?>
                        </td>

                        <td class="p-4 text-center">
                            <?php if($row['STATUS'] == 'inactive'): ?>
                            <div class="flex justify-center gap-2">
                                <a href="?activate=<?php echo $row['nik']; ?>&tab=ibu" class="bg-green-500 text-white px-3 py-1 rounded-lg text-xs hover:bg-green-600 transition flex items-center gap-1" onclick="return confirmAction(event, this.href, 'Aktifkan Akun', 'Aktifkan kembali akun ibu ini?', 'Ya, Aktifkan!')">
                                    <i class="fas fa-check-circle"></i> Aktifkan
                                </a>
                                <a href="hapus_users.php?nik=<?php echo $row['nik']; ?>" class="text-red-500 hover:text-red-700 p-1" title="Hapus" onclick="confirmDelete(event, this.href, 'Data user akan dihapus permanen!')">
                                    <i class="fas fa-trash-alt text-lg"></i>
                                </a>
                            </div>
                            <?php else: ?>
                            <div class="flex justify-center gap-2">
                                <!-- PERUBAHAN DI SINI: Menggunakan kirimEditPost() via JavaScript untuk metode POST -->
                                <button type="button" onclick="kirimEditPost('<?php echo $row['nik']; ?>')" class="text-blue-500 hover:text-blue-700 transition p-1" title="Edit">
                                    <i class="fas fa-edit text-lg"></i>
                                </button>
                                <a href="?deactivate=<?php echo $row['nik']; ?>&tab=ibu" class="text-orange-500 hover:text-orange-700 p-1" title="Nonaktifkan" onclick="return confirmAction(event, this.href, 'Nonaktifkan Akun', 'Nonaktifkan akun ibu ini?', 'Ya, Nonaktifkan!')">
                                    <i class="fas fa-ban text-lg"></i>
                                </a>
                                <a href="hapus_users.php?nik=<?php echo $row['nik']; ?>" class="text-red-500 hover:text-red-700 transition p-1" title="Hapus" onclick="confirmDelete(event, this.href, 'Data user akan dihapus permanen!')">
                                    <i class="fas fa-trash-alt text-lg"></i>
                                </a>
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="7" class="p-12 text-center text-gray-500">
                            <i class="fas fa-folder-open text-5xl mb-3 text-gray-300"></i>
                            <p>Tidak ada data ibu</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination Ibu -->
    <?php if($total_pages > 1): ?>
    <div class="mt-6">
        <?php echo paginate($page, $total_pages, "list_users.php?tab=ibu&search=" . urlencode($search)); ?>
    </div>
    <?php endif; ?>
    
    <!-- ==================== TABEL BIDAN ==================== -->
    <?php else: ?>
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-green-600 to-emerald-500 text-white">
                        <th class="p-4 text-left text-sm font-semibold">No</th>
                        <th class="p-4 text-left text-sm font-semibold">NIK</th>
                        <th class="p-4 text-left text-sm font-semibold">Nama Lengkap</th>
                        <th class="p-4 text-left text-sm font-semibold">No. WhatsApp</th>
                        <th class="p-4 text-left text-sm font-semibold">Alamat</th>
                        <th class="p-4 text-left text-sm font-semibold">Status</th>
                        <th class="p-4 text-center text-sm font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php $no = $offset + 1; while($row = mysqli_fetch_assoc($result)): ?>
                    <tr class="hover:bg-gray-50 transition duration-200">
                        <td class="p-4 text-sm text-gray-600"><?php echo $no++; ?></td>
                        <td class="p-4 text-sm font-mono"><?php echo htmlspecialchars($row['nik']); ?></td>
                        <td class="p-4 font-semibold text-gray-800"><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                        <td class="p-4 text-sm"><?php echo htmlspecialchars($row['no_wa']); ?></td>
                        <td class="p-4 text-sm text-gray-600 max-w-xs break-words"><?php echo htmlspecialchars($row['alamat']); ?></td>
                        <td class="p-4">
                            <?php if($row['STATUS'] == 'pending'): ?>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                <i class="fas fa-clock mr-1 text-xs"></i> Menunggu
                            </span>
                            <?php elseif($row['STATUS'] == 'active'): ?>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <i class="fas fa-check-circle mr-1 text-xs"></i> Aktif
                            </span>
                            <?php else: ?>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                <i class="fas fa-times-circle mr-1 text-xs"></i> Tidak Aktif
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 text-center">
                            <?php if($row['STATUS'] == 'pending'): ?>
                            <div class="flex justify-center gap-2">
                                <a href="?confirm=<?php echo $row['nik']; ?>&tab=bidan" class="bg-green-500 text-white px-3 py-1 rounded-lg text-xs hover:bg-green-600 transition flex items-center gap-1" onclick="return confirmAction(event, this.href, 'Konfirmasi Akun', 'Terima pendaftaran bidan ini?', 'Ya, Terima!')">
                                    <i class="fas fa-check"></i> Terima
                                </a>
                                <a href="?reject=<?php echo $row['nik']; ?>&tab=bidan" class="bg-red-500 text-white px-3 py-1 rounded-lg text-xs hover:bg-red-600 transition flex items-center gap-1" onclick="return confirmAction(event, this.href, 'Tolak Pendaftaran', 'Tolak pendaftaran bidan ini?', 'Ya, Tolak!')">
                                    <i class="fas fa-times"></i> Tolak
                                </a>
                            </div>
                            <?php elseif($row['STATUS'] == 'inactive'): ?>
                            <div class="flex justify-center gap-2">
                                <a href="?activate=<?php echo $row['nik']; ?>&tab=bidan" class="bg-green-500 text-white px-3 py-1 rounded-lg text-xs hover:bg-green-600 transition flex items-center gap-1" onclick="return confirmAction(event, this.href, 'Aktifkan Akun', 'Aktifkan kembali akun bidan ini?', 'Ya, Aktifkan!')">
                                    <i class="fas fa-check-circle"></i> Aktifkan
                                </a>
                                <a href="hapus_users.php?nik=<?php echo $row['nik']; ?>" class="text-red-500 hover:text-red-700 p-1" onclick="confirmDelete(event, this.href, 'Hapus akun bidan ini?')">
                                    <i class="fas fa-trash-alt text-lg"></i>
                                </a>
                            </div>
                            <?php else: ?>
                            <div class="flex justify-center gap-2">
                                <!-- PERUBAHAN DI SINI: Menggunakan kirimEditPost() via JavaScript untuk metode POST -->
                                <button type="button" onclick="kirimEditPost('<?php echo $row['nik']; ?>')" class="text-blue-500 hover:text-blue-700 p-1" title="Edit">
                                    <i class="fas fa-edit text-lg"></i>
                                </button>
                                <a href="?deactivate=<?php echo $row['nik']; ?>&tab=bidan" class="text-orange-500 hover:text-orange-700 p-1" title="Nonaktifkan" onclick="return confirmAction(event, this.href, 'Nonaktifkan Akun', 'Nonaktifkan akun bidan ini?', 'Ya, Nonaktifkan!')">
                                    <i class="fas fa-ban text-lg"></i>
                                </a>
                                <a href="hapus_users.php?nik=<?php echo $row['nik']; ?>" class="text-red-500 hover:text-red-700 p-1" title="Hapus" onclick="confirmDelete(event, this.href, 'Hapus akun bidan ini?')">
                                    <i class="fas fa-trash-alt text-lg"></i>
                                </a>
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="7" class="p-12 text-center text-gray-500">
                            <i class="fas fa-folder-open text-5xl mb-3 text-gray-300"></i>
                            <p>Tidak ada data bidan</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination Bidan -->
    <?php if($total_pages > 1): ?>
    <div class="mt-6">
        <?php echo paginate($page, $total_pages, "list_users.php?tab=bidan&search=" . urlencode($search)); ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<script>
// PERUBAHAN DI SINI: Fungsi JavaScript untuk mengisi form hidden lalu men-submit-nya secara POST
function kirimEditPost(nik) {
    document.getElementById('nikEditPost').value = nik;
    document.getElementById('formEditPost').submit();
}

function confirmAction(event, url, title, text, confirmButtonText) {
    event.preventDefault();
    Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22c55e',
        cancelButtonColor: '#ef4444',
        confirmButtonText: confirmButtonText,
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
    return false;
}

function confirmDelete(event, url, text) {
    event.preventDefault();
    Swal.fire({
        title: 'Yakin hapus?',
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
    return false;
}
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>