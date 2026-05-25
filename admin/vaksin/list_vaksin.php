<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';
$title = 'Data Vaksin';
include __DIR__ . '/../../templates/sidebar.php';

$result = mysqli_query($conn, "SELECT * FROM vaksin ORDER BY usia_rekomendasi ASC");

// Fungsi untuk mengkonversi bulan ke format tahun dan bulan
function formatUsia($bulan) {
    if($bulan == 0) {
        return '0 bulan (baru lahir)';
    }
    
    $tahun = floor($bulan / 12);
    $sisa_bulan = $bulan % 12;
    
    if($tahun > 0 && $sisa_bulan > 0) {
        return $tahun . ' tahun ' . $sisa_bulan . ' bulan';
    } elseif($tahun > 0) {
        return $tahun . ' tahun';
    } else {
        return $sisa_bulan . ' bulan';
    }
}
?>

<!-- Form Tersembunyi Global untuk Mengirim ID Vaksin via POST ke Halaman Edit -->
<form id="formEditVaksinPost" action="edit_vaksin.php" method="POST" style="display:none;">
    <input type="hidden" name="id_vaksin" id="idVaksinEditPost">
</form>

<div class="fade-in">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-green-800">Data Vaksin</h1>
        <a href="tambah_vaksin.php" class="bg-gradient-to-r from-green-600 to-emerald-500 text-white px-4 py-2 rounded-xl hover:shadow-lg transition">
            <i class="fas fa-plus mr-2"></i>Vaksin
        </a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php if(mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <div class="bg-white rounded-2xl shadow-lg p-4 hover:shadow-xl transition flex flex-col h-full justify-between">
            <div>
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-syringe text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 break-all"><?php echo htmlspecialchars($row['nama_vaksin']); ?></h3>
                            <p class="text-xs text-green-600 mt-0.5">
                                <i class="fas fa-child mr-1"></i> 
                                Usia: <?php echo formatUsia($row['usia_rekomendasi']); ?>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button type="button" onclick="kirimEditVaksinPost('<?php echo $row['id_vaksin']; ?>')" 
                                class="text-blue-500 hover:text-blue-700 p-1 transition" title="Edit">
                            <i class="fas fa-edit text-lg"></i>
                        </button>
                        <a href="hapus_vaksin.php?id=<?php echo $row['id_vaksin']; ?>" 
                           class="text-red-500 hover:text-red-700 p-1 transition" title="Hapus" 
                           onclick="confirmDelete(event, this.href)">
                            <i class="fas fa-trash text-lg"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Deskripsi Singkat (3 baris) -->
                <div class="mt-3">
                    <?php 
                    $deskripsi_singkat = strip_tags($row['deskripsi'] ?? '');
                    if(strlen($deskripsi_singkat) > 120) {
                        $deskripsi_singkat = substr($deskripsi_singkat, 0, 120) . '...';
                    }
                    ?>
                    <p class="text-gray-600 text-sm leading-relaxed"><?php echo nl2br(htmlspecialchars($deskripsi_singkat ?: 'Tidak ada deskripsi.')); ?></p>
                </div>
            </div>
            
            <!-- Tombol Detail (Modal) -->
            <div class="mt-4 pt-3 border-t border-gray-100">
                <!-- Taruh data formatTanggalIndonesia langsung ke parameter data attribute agar dibaca JS modal -->
                <button onclick='openDetailModal(<?php echo json_encode($row); ?>, "<?php echo formatTanggalIndonesia($row['created_at']); ?>")' 
                        class="text-green-600 hover:text-green-700 text-sm font-medium flex items-center gap-1 transition w-full text-left">
                    <i class="fas fa-info-circle"></i> Lihat Detail
                    <i class="fas fa-arrow-right text-xs ml-auto"></i>
                </button>
            </div>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
        <div class="col-span-full bg-white rounded-2xl shadow-lg p-12 text-center">
            <i class="fas fa-folder-open text-5xl mb-3 text-gray-300"></i>
            <p class="text-gray-500 font-medium">Belum ada data vaksin</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Detail Vaksin -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4" onclick="closeDetailModal(event)">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto animate-popup" onclick="event.stopPropagation()">
        <div class="sticky top-0 bg-gradient-to-r from-green-600 to-emerald-500 p-4 rounded-t-2xl flex justify-between items-center shadow-sm">
            <h3 class="text-xl font-bold text-white shadow-sm" id="modalTitle"></h3>
            <button onclick="closeDetailModal()" class="text-white hover:text-gray-200 transition p-1">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="mb-4">
                <label class="block text-gray-400 text-xs font-semibold tracking-wider uppercase mb-1">Nama Vaksin</label>
                <p class="font-bold text-gray-800 text-lg" id="modalNama"></p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-400 text-xs font-semibold tracking-wider uppercase mb-1">Usia Rekomendasi</label>
                <p class="text-gray-700 font-medium" id="modalUsia"></p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-400 text-xs font-semibold tracking-wider uppercase mb-1">Deskripsi Lengkap</label>
                <div class="text-gray-700 leading-relaxed whitespace-pre-line bg-gray-50 p-3 rounded-xl border border-gray-100" id="modalDeskripsi"></div>
            </div>
            <div class="mb-2">
                <label class="block text-gray-400 text-xs font-semibold tracking-wider uppercase mb-1">Tanggal Input Master</label>
                <p class="text-gray-700 font-medium" id="modalCreatedAt"></p>
            </div>
            <div class="flex justify-end mt-6">
                <button onclick="closeDetailModal()" class="px-5 py-2 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi pemicu kirim data edit via POST
function kirimEditVaksinPost(idVaksin) {
    document.getElementById('idVaksinEditPost').value = idVaksin;
    document.getElementById('formEditVaksinPost').submit();
}

function openDetailModal(vaksin, tanggalFormatted) {
    let usiaBulan = vaksin.usia_rekomendasi;
    let usiaText = '';
    
    if(usiaBulan == 0) {
        usiaText = '0 bulan (baru lahir)';
    } else {
        let tahun = Math.floor(usiaBulan / 12);
        let sisaBulan = usiaBulan % 12;
        
        if(tahun > 0 && sisaBulan > 0) {
            usiaText = tahun + ' tahun ' + sisaBulan + ' bulan';
        } else if(tahun > 0) {
            usiaText = tahun + ' tahun';
        } else {
            usiaText = sisaBulan + ' bulan';
        }
    }
    
    document.getElementById('modalTitle').innerText = 'Detail Vaksin ' + vaksin.nama_vaksin;
    document.getElementById('modalNama').innerText = vaksin.nama_vaksin;
    document.getElementById('modalUsia').innerHTML = '<i class="fas fa-child mr-1 text-green-500"></i> ' + usiaText;
    document.getElementById('modalDeskripsi').innerText = vaksin.deskripsi || 'Tidak ada deskripsi lengkap.';
    
    // PERUBAHAN: Menggunakan string formatTanggalIndonesia yang dilempar langsung dari PHP
    document.getElementById('modalCreatedAt').innerHTML = '<i class="far fa-calendar-alt mr-1 text-gray-400"></i> ' + tanggalFormatted;
    
    document.getElementById('detailModal').classList.remove('hidden');
    document.getElementById('detailModal').classList.add('flex');
}

function closeDetailModal(event) {
    if (event && event.target !== event.currentTarget && event.target.closest('.bg-white')) return;
    document.getElementById('detailModal').classList.add('hidden');
    document.getElementById('detailModal').classList.remove('flex');
}

// Konfirmasi hapus SweetAlert terintegrasi ke link GET
function confirmDelete(event, url) {
    event.preventDefault();
    Swal.fire({
        title: 'Yakin hapus data vaksin?',
        text: "Menghapus vaksin ini dapat memengaruhi rekam medis imunisasi balita yang bersangkutan!",
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