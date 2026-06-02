<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_ibu.php';

$nik = $_SESSION['nik'];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM anak WHERE nik_ibu='$nik'"))['total'];
$total_pages = ceil($total / $limit);

$query_anak = "SELECT a.*, 
    ((SELECT COUNT(*) FROM pendaftaran_imunisasi WHERE id_anak = a.id_anak) + 
     (SELECT COUNT(*) FROM hasil_imunisasi WHERE id_pendaftaran IN (SELECT id_pendaftaran FROM pendaftaran_imunisasi WHERE id_anak = a.id_anak))) AS total_riwayat_imunisasi
    FROM anak a 
    WHERE a.nik_ibu='$nik' 
    ORDER BY a.created_at DESC 
    LIMIT $offset, $limit";

$result = mysqli_query($conn, $query_anak);

$title = 'Data Anak';
include __DIR__ . '/../../templates/sidebar.php';
?>

<form id="formAksiAnakPost" method="POST" style="display:none;">
    <input type="hidden" name="id_anak" id="idAnakAksiPost">
</form>

<div class="fade-in">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-green-800">Data Anak</h1>
        <a href="tambah_anak.php" class="bg-gradient-to-r from-green-600 to-emerald-500 text-white px-4 py-2 rounded-xl hover:shadow-lg transition">
            <i class="fas fa-plus mr-2"></i>Anak
        </a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php while($anak = mysqli_fetch_assoc($result)): 
            $usia = date_diff(date_create($anak['tanggal_lahir']), date_create('today'));
            
            $punya_imunisasi = ((int)$anak['total_riwayat_imunisasi'] > 0);
        ?>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition">
            <div class="bg-gradient-to-r from-green-600 to-emerald-500 p-4 text-white">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-child text-xl"></i>
                        <h3 class="text-lg font-bold"><?php echo htmlspecialchars($anak['nama_anak']); ?></h3>
                    </div>
                    
                    <div class="flex gap-2">
                        <?php if(!$punya_imunisasi): ?>
                            <button type="button" onclick="kirimAksiAnakPost('edit_anak.php', '<?php echo $anak['id_anak']; ?>')" class="text-white hover:text-green-200" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            
                            <a href="hapus_anak.php?id=<?php echo $anak['id_anak']; ?>" 
                               onclick="confirmDelete(event, this.href)" 
                               class="text-white hover:text-red-200" 
                               title="Hapus">
                                <i class="fas fa-trash"></i>
                            </a>
                        <?php else: ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="p-4">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-calendar w-4 text-green-500"></i>
                        <span class="text-sm">Lahir: <?php echo date('d/m/Y', strtotime($anak['tanggal_lahir'])); ?></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-clock w-4 text-green-500"></i>
                        <span class="text-sm">Usia: <?php echo $usia->y; ?> tahun <?php echo $usia->m; ?> bulan</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-venus-mars w-4 text-green-500"></i>
                        <span class="text-sm"><?php echo $anak['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-weight-scale w-4 text-green-500"></i>
                        <span class="text-sm">Berat Lahir: <?php echo $anak['berat_lahir']; ?> kg</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-ruler w-4 text-green-500"></i>
                        <span class="text-sm">Panjang Lahir: <?php echo $anak['panjang_lahir']; ?> cm</span>
                    </div>
                </div>
                
                <div class="flex gap-2 mt-4 pt-3 border-t">
                    <button type="button" onclick="kirimAksiAnakPost('../perkembangan/index.php', '<?php echo $anak['id_anak']; ?>')" class="flex-1 text-center bg-green-600 text-white py-1 rounded-lg text-sm hover:bg-green-700 transition">
                        <i class="fas fa-child mr-1"></i> Perkembangan
                    </button>
                    <a href="../imunisasi/index.php" class="flex-1 text-center bg-blue-600 text-white py-1 rounded-lg text-sm hover:bg-blue-700 transition">
                        <i class="fas fa-syringe mr-1"></i> Daftar Imunisasi
                    </a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
        
        <?php if(mysqli_num_rows($result) == 0): ?>
        <div class="col-span-full">
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <i class="fas fa-baby-carriage text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Data Anak</h3>
                <p class="text-gray-500">Silakan tambahkan data anak Anda</p>
                <a href="tambah_anak.php" class="inline-block mt-4 bg-green-600 text-white px-6 py-2 rounded-xl hover:bg-green-700 transition">
                    <i class="fas fa-plus mr-2"></i> Tambah Anak Sekarang
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if($total_pages > 1): ?>
    <div class="mt-6">
        <?php echo paginate($page, $total_pages, "index.php"); ?>
    </div>
    <?php endif; ?>
</div>

<script>
function kirimAksiAnakPost(urlTujuan, idAnak) {
    const form = document.getElementById('formAksiAnakPost');
    if(urlTujuan.includes('index.php')) {
        document.getElementById('idAnakAksiPost').name = 'anak_id';
    } else {
        document.getElementById('idAnakAksiPost').name = 'id_anak';
    }
    form.action = urlTujuan;
    document.getElementById('idAnakAksiPost').value = idAnak;
    form.submit();
}

function confirmDelete(event, urlTujuan) {
    event.preventDefault();
    Swal.fire({
        title: 'Yakin hapus data anak?',
        text: "Seluruh data riwayat rekam medis anak ini akan terhapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = urlTujuan;
        }
    });
}
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>