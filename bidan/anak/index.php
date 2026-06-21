<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_bidan.php';

$title = 'Data Anak';
include __DIR__ . '/../../templates/sidebar.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$jk_filter = isset($_GET['jk']) ? $_GET['jk'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'upload_langsung') {
    $id_anak = (int)$_POST['id_anak'];
    $nik_ibu = mysqli_real_escape_string($conn, $_POST['nik_ibu']); 
    
    $cek_berkas = mysqli_query($conn, "SELECT berkas FROM anak WHERE id_anak = $id_anak");
    $data_berkas = mysqli_fetch_assoc($cek_berkas);
    
    if(!empty($data_berkas['berkas'])) {
        $_SESSION['error'] = "Akses ditolak! Berkas sudah divalidasi dan tidak dapat diubah oleh Bidan.";
        echo "<script>window.location.href='index.php?page=$page&search=" . urlencode($search) . "&jk=$jk_filter';</script>";
        exit();
    }
    
    if (isset($_FILES['berkas_validasi']) && $_FILES['berkas_validasi']['error'] == 0) {
        $file_tmp = $_FILES['berkas_validasi']['tmp_name'];
        $file_name = $_FILES['berkas_validasi']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $ekstensi_boleh = ['pdf', 'doc', 'docx'];
        
        if (in_array($file_ext, $ekstensi_boleh)) {
            $timestamp = date('Ymd_His');
            $nama_file_baru = $nik_ibu . "-" . $id_anak . "-" . $timestamp . "." . $file_ext;
            $folder_tujuan = "../../uploads/berkas_anak/" . $nama_file_baru;
            
            if (!file_exists("../../uploads/berkas_anak/")) {
                mkdir("../../uploads/berkas_anak/", 0777, true);
            }
            
            if (move_uploaded_file($file_tmp, $folder_tujuan)) {
                $update_query = "UPDATE anak SET berkas = '$nama_file_baru' WHERE id_anak = $id_anak";
                if (mysqli_query($conn, $update_query)) {
                    $_SESSION['success'] = "Berkas pendaftaran anak berhasil diverifikasi!";
                } else {
                    $_SESSION['error'] = "Gagal memperbarui data nama berkas di database.";
                }
            } else {
                $_SESSION['error'] = "Gagal memindahkan file ke folder storage server.";
            }
        } else {
            $_SESSION['error'] = "Format file tidak valid! Wajib berformat PDF atau Word (DOC/DOCX).";
        }
    } else {
        $_SESSION['error'] = "Gagal membaca file berkas pendaftaran.";
    }
    
    echo "<script>window.location.href='index.php?page=$page&search=" . urlencode($search) . "&jk=$jk_filter';</script>";
    exit();
}

$where = "WHERE 1=1";
if (!empty($search)) {
    $where .= " AND (a.nama_anak LIKE '%$search%' OR u.nama_lengkap LIKE '%$search%')";
}
if (!empty($jk_filter)) {
    $where .= " AND a.jenis_kelamin = '$jk_filter'";
}

$total_query = "SELECT COUNT(*) as total FROM anak a LEFT JOIN users u ON a.nik_ibu = u.nik $where";
$total = mysqli_fetch_assoc(mysqli_query($conn, $total_query))['total'];
$total_pages = ceil($total / $limit);

$query_anak = "SELECT a.*, u.nama_lengkap as nama_ibu 
               FROM anak a 
               LEFT JOIN users u ON a.nik_ibu = u.nik 
               $where 
               ORDER BY a.tanggal_lahir DESC 
               LIMIT $offset, $limit";
$result = mysqli_query($conn, $query_anak);
?>

<?php if(isset($_SESSION['success'])): ?>
<script>Swal.fire({ icon: 'success', title: 'Berhasil!', text: '<?php echo $_SESSION['success']; unset($_SESSION['success']); ?>', confirmButtonColor: '#10b981' });</script>
<?php endif; ?>
<?php if(isset($_SESSION['error'])): ?>
<script>Swal.fire({ icon: 'error', title: 'Peringatan!', text: '<?php echo $_SESSION['error']; unset($_SESSION['error']); ?>', confirmButtonColor: '#ef4444' });</script>
<?php endif; ?>

<form id="formDetailAnakPost" action="detail_perkembangan.php" method="POST" style="display:none;">
    <input type="hidden" name="id_anak" id="idAnakDetailPost">
</form>

<div class="fade-in">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-green-800">Data Anak</h1>
    </div>
    
    <div class="bg-white rounded-2xl shadow-lg p-4 mb-6">
        <form method="GET" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari nama anak atau nama ibu..." 
                       class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200">
            </div>
            
            <select name="jk" class="px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 bg-white">
                <option value="">Semua Jenis Kelamin</option>
                <option value="L" <?php echo $jk_filter == 'L' ? 'selected' : ''; ?>>Laki-laki (L)</option>
                <option value="P" <?php echo $jk_filter == 'P' ? 'selected' : ''; ?>>Perempuan (P)</option>
            </select>
            
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-xl hover:bg-green-700 transition flex items-center justify-center gap-2">
                <i class="fas fa-filter"></i> Filter
            </button>
            
            <?php if($search || $jk_filter): ?>
            <a href="index.php" class="bg-gray-500 text-white px-6 py-2 rounded-xl hover:bg-gray-600 transition text-center flex items-center justify-center">
                <i class="fas fa-times mr-2"></i> Reset
            </a>
            <?php endif; ?>
        </form>
    </div>
    
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-green-600 to-emerald-500 text-white">
                        <th class="p-4 text-left text-sm font-semibold">No</th>
                        <th class="p-4 text-left text-sm font-semibold">Nama Anak</th>
                        <th class="p-4 text-left text-sm font-semibold">Jenis Kelamin</th>
                        <th class="p-4 text-left text-sm font-semibold">Tanggal Lahir</th>
                        <th class="p-4 text-left text-sm font-semibold">Nama Ibu</th>
                        <th class="p-4 text-center text-sm font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php $no = $offset + 1; while($row = mysqli_fetch_assoc($result)): ?>
                    <tr class="hover:bg-gray-50 transition duration-200">
                        <td class="p-4 text-sm text-gray-600"><?php echo $no++; ?></td>
                        <td class="p-4 font-semibold text-gray-800"><?php echo htmlspecialchars($row['nama_anak']); ?></td>
                        <td class="p-4 text-sm">
                            <?php if($row['jenis_kelamin'] == 'L'): ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                <i class="fas fa-mars mr-1"></i> Laki-laki
                            </span>
                            <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-pink-100 text-pink-700">
                                <i class="fas fa-venus mr-1"></i> Perempuan
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 text-sm text-gray-600"><?php echo formatTanggalIndonesia($row['tanggal_lahir']); ?></td>
                        <td class="p-4 text-sm font-medium text-gray-700"><?php echo htmlspecialchars($row['nama_ibu']); ?></td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button" onclick="kirimDetailAnakPost('<?php echo $row['id_anak']; ?>')" 
                                        class="bg-green-500 hover:bg-green-600 text-white px-3 rounded-xl text-xs font-medium transition inline-flex items-center justify-center gap-1 shadow-sm h-8" title="Lihat Rekam Medis">
                                    <i class="fas fa-chart-line text-[13px]"></i> Perkembangan
                                </button>

                                <?php if(!empty($row['berkas'])): 
                                    $file_ext = strtolower(pathinfo($row['berkas'], PATHINFO_EXTENSION));
                                    $preview_url = "../../uploads/berkas_anak/" . $row['berkas'];
                                    $is_word = in_array($file_ext, ['doc', 'docx']);
                                ?>
                                    <a href="<?php echo $preview_url; ?>" target="_blank" <?php echo $is_word ? 'download' : ''; ?>
                                    class="bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-xs font-medium transition shadow-sm inline-flex items-center justify-center h-8 w-8 shrink-0" 
                                    title="<?php echo $is_word ? 'Download Dokumen Word' : 'Lihat Berkas PDF'; ?>">
                                        <i class="fas <?php echo $is_word ? 'fa-file-word' : 'fa-file-invoice'; ?> text-[13px]"></i>
                                    </a>
                                <?php else: ?>
                                    <button type="button" onclick="bukaModalUpload('<?php echo $row['id_anak']; ?>', '<?php echo htmlspecialchars($row['nama_anak'], ENT_QUOTES); ?>', '<?php echo $row['nik_ibu']; ?>')"
                                            class="bg-gray-500 hover:bg-gray-600 text-white rounded-xl text-xs font-medium transition shadow-sm inline-flex items-center justify-center h-8 w-8 shrink-0" 
                                            title="Upload Berkas Pendaftaran">
                                        <i class="fas fa-upload text-[13px]"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" class="p-12 text-center text-gray-500">
                            <i class="fas fa-child text-5xl mb-3 text-gray-300"></i>
                            <p>Data anak tidak ditemukan atau belum diinput</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php if($total_pages > 1): ?>
    <div class="mt-6">
        <?php echo paginate($page, $total_pages, "index.php?search=" . urlencode($search) . "&jk=$jk_filter"); ?>
    </div>
    <?php endif; ?>
</div>

<div id="modalUpload" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl mx-4 transform scale-95 transition-transform duration-300" id="modalBox">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-file-invoice text-green-600"></i> Validasi Berkas
            </h3>
            <button type="button" onclick="tutupModalUpload()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        
        <div class="mb-4">
            <p class="text-xs text-gray-600 mb-2">
                Upload berkas kelengkapan fisik untuk anak: <strong id="namaAnakModal" class="text-gray-800"></strong>.
            </p>
            <p class="text-[11px] text-gray-500 bg-gray-50 border border-gray-100 p-2.5 rounded-xl leading-relaxed">
                <span class="font-bold text-gray-700 block mb-1"><i class="fas fa-info-circle text-blue-500 mr-1"></i> Ketentuan Dokumen Wajib (Bundling 1 File PDF/Word):</span>
                1. Buku KIA<br>
                2. Kartu Keluarga (KK) Asli/Fotokopi<br>
                3. Kartu Tanda Penduduk (KTP) Orang Tua
            </p>
            <p class="text-[11px] text-red-500 font-semibold mt-2 flex items-start gap-1">
                <i class="fas fa-exclamation-triangle shrink-0 mt-0.5"></i>
                <span>Catatan: Berkas dikunci setelah diupload. Jika ada kesalahan, hubungi Admin SIPANDA untuk update data.</span>
            </p>
        </div>
        
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_anak" id="idAnakModal">
            <input type="hidden" name="nik_ibu" id="nikIbuModal">
            <input type="hidden" name="action" value="upload_langsung">
            
            <div class="mb-5">
                <input type="file" name="berkas_validasi" accept=".pdf,.doc,.docx" required
                       class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 border border-gray-200 rounded-xl p-1.5 bg-gray-50">
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-xl font-semibold text-xs transition shadow-sm">
                    Simpan Berkas
                </button>
                <button type="button" onclick="tutupModalUpload()" class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-xl font-semibold text-xs hover:bg-gray-300 transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function kirimDetailAnakPost(idAnak) {
    document.getElementById('idAnakDetailPost').value = idAnak;
    document.getElementById('formDetailAnakPost').submit();
}
function bukaModalUpload(idAnak, namaAnak, nikIbu) {
    document.getElementById('idAnakModal').value = idAnak;
    document.getElementById('namaAnakModal').innerText = namaAnak;
    document.getElementById('nikIbuModal').value = nikIbu;
    const modal = document.getElementById('modalUpload');
    const box = document.getElementById('modalBox');
    modal.classList.remove('hidden');
    setTimeout(() => { modal.classList.remove('opacity-0'); box.classList.remove('scale-95'); }, 20);
}
function tutupModalUpload() {
    const modal = document.getElementById('modalUpload');
    const box = document.getElementById('modalBox');
    modal.classList.add('opacity-0'); box.classList.add('scale-95');
    setTimeout(() => { modal.classList.add('hidden'); }, 300);
}
</script>
<?php include __DIR__ . '/../../templates/footer.php'; ?>