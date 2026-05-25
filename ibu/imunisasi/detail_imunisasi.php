<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_ibu.php';

$nik = $_SESSION['nik'];

// Tangkap ID menggunakan POST sesuai kiriman dari riwayat_imunisasi.php
$id = isset($_POST['id_pendaftaran']) ? (int)$_POST['id_pendaftaran'] : 0;

// Jika tidak ada data POST (misal di-refresh paksa), kembalikan ke halaman riwayat
if($id === 0){
    $_SESSION['error'] = "Data detail imunisasi tidak ditemukan!";
    header("Location: riwayat_imunisasi.php");
    exit();
}

// PERUBAHAN QUERY: Menambahkan JOIN ke tabel users (u) untuk mengambil nama_lengkap bidan dari hasil_imunisasi
$query = "SELECT pi.*, pi.STATUS as status_pendaftaran, a.nama_anak, a.tanggal_lahir, a.jenis_kelamin, a.berat_lahir, a.panjang_lahir, a.id_anak,
          v.nama_vaksin, j.tanggal, j.lokasi,
          hi.berat_badan, hi.tinggi_badan, hi.lingkar_kepala, hi.status_gizi, hi.nafsu_makan, hi.catatan_kesehatan, hi.tgl_imunisasi,
          u.nama_lengkap as nama_petugas
          FROM pendaftaran_imunisasi pi 
          JOIN anak a ON pi.id_anak = a.id_anak 
          JOIN jadwal_imunisasi j ON pi.id_jadwal = j.id_jadwal 
          JOIN vaksin v ON j.id_vaksin = v.id_vaksin 
          LEFT JOIN hasil_imunisasi hi ON pi.id_pendaftaran = hi.id_pendaftaran 
          LEFT JOIN users u ON hi.petugas_nik = u.nik
          WHERE pi.id_pendaftaran = $id AND a.nik_ibu = '$nik'";

$data = mysqli_fetch_assoc(mysqli_query($conn, $query));

if(!$data){
    $_SESSION['error'] = "Data tidak ditemukan!";
    header("Location: riwayat_imunisasi.php");
    exit();
}

// Hitung usia anak
$usia = date_diff(date_create($data['tanggal_lahir']), date_create('today'));

// Ambil rekomendasi artikel berdasarkan kondisi
$rekomendasi_artikel = [];
$ada_masalah = false;

if($data['status_gizi'] != 'Normal' && $data['status_gizi'] != ''){
    $ada_masalah = true;
    // Artikel tentang gizi (id_kategori = 1)
    $gizi = mysqli_query($conn, "SELECT * FROM artikel WHERE id_kategori = 1 ORDER BY created_at DESC LIMIT 3");
    while($row = mysqli_fetch_assoc($gizi)){
        $rekomendasi_artikel[] = $row;
    }
}

if($data['nafsu_makan'] == 'kurang' || $data['nafsu_makan'] == 'buruk'){
    $ada_masalah = true;
    // Artikel tentang MPASI (id_kategori = 5)
    $mpasi = mysqli_query($conn, "SELECT * FROM artikel WHERE id_kategori = 5 ORDER BY created_at DESC LIMIT 3");
    while($row = mysqli_fetch_assoc($mpasi)){
        $found = false;
        foreach($rekomendasi_artikel as $art){
            if($art['id_artikel'] == $row['id_artikel']){
                $found = true;
                break;
            }
        }
        if(!$found){
            $rekomendasi_artikel[] = $row;
        }
    }
}

// Jika tidak ada masalah (normal), tampilkan artikel pilihan (imunisasi & perkembangan)
if(!$ada_masalah){
    // Artikel imunisasi (id_kategori = 2)
    $imunisasi = mysqli_query($conn, "SELECT * FROM artikel WHERE id_kategori = 2 ORDER BY created_at DESC LIMIT 2");
    while($row = mysqli_fetch_assoc($imunisasi)){
        $rekomendasi_artikel[] = $row;
    }
    // Artikel perkembangan anak (id_kategori = 3)
    $perkembangan = mysqli_query($conn, "SELECT * FROM artikel WHERE id_kategori = 3 ORDER BY created_at DESC LIMIT 2");
    while($row = mysqli_fetch_assoc($perkembangan)){
        $rekomendasi_artikel[] = $row;
    }
}

// Batasi maksimal 4 artikel
$rekomendasi_artikel = array_slice($rekomendasi_artikel, 0, 4);

$title = 'Detail Imunisasi';
include __DIR__ . '/../../templates/sidebar.php';
?>

<div class="max-w-3xl mx-auto fade-in">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-green-600 to-emerald-500 p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Detail Imunisasi</h1>
                    <p class="text-green-100 mt-1">Informasi lengkap imunisasi anak Anda</p>
                </div>
                <div class="text-5xl opacity-50">
                    <i class="fas fa-syringe"></i>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <div class="mb-6">
                <?php if($data['status_pendaftaran'] == 'pending'): ?>
                <div class="bg-yellow-100 border-l-4 border-yellow-500 p-4 rounded-r-lg">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        <div>
                            <p class="font-semibold text-yellow-800">Menunggu Konfirmasi</p>
                            <p class="text-sm text-yellow-700">Pendaftaran Anda sedang menunggu konfirmasi pelayanan dari petugas posyandu.</p>
                        </div>
                    </div>
                </div>
                <?php elseif($data['status_pendaftaran'] == 'selesai'): ?>
                <div class="bg-green-100 border-l-4 border-green-500 p-4 rounded-r-lg">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        <div>
                            <p class="font-semibold text-green-800">Imunisasi Selesai</p>
                            <p class="text-sm text-green-700">Imunisasi telah sukses dilaksanakan pada <?php echo formatTanggalIndonesia($data['tgl_imunisasi']); ?></p>
                        </div>
                    </div>
                </div>
                <?php elseif($data['status_pendaftaran'] == 'batal'): ?>
                <div class="bg-red-100 border-l-4 border-red-500 p-4 rounded-r-lg">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                        <div>
                            <p class="font-semibold text-red-800">Pendaftaran Dibatalkan</p>
                            <p class="text-sm text-red-700">Pendaftaran sesi imunisasi ini telah dibatalkan.</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="bg-blue-50 rounded-xl p-4 mb-6 border border-blue-100/50">
                <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2 text-sm">
                    <i class="fas fa-child text-blue-600"></i> Data Anak
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                    <div>
                        <p class="text-gray-400 font-medium">Nama Anak</p>
                        <p class="font-bold text-gray-800 mt-0.5"><?php echo htmlspecialchars($data['nama_anak']); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-400 font-medium">Tanggal Lahir</p>
                        <p class="font-bold text-gray-800 mt-0.5"><?php echo date('d/m/Y', strtotime($data['tanggal_lahir'])); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-400 font-medium">Usia Saat Tindakan</p>
                        <p class="font-bold text-gray-800 mt-0.5"><?php echo $usia->y; ?> tahun <?php echo $usia->m; ?> bulan</p>
                    </div>
                    <div>
                        <p class="text-gray-400 font-medium">Jenis Kelamin</p>
                        <p class="font-bold text-gray-800 mt-0.5"><?php echo $data['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-purple-50 rounded-xl p-4 mb-6 border border-purple-100/50">
                <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2 text-sm">
                    <i class="fas fa-syringe text-purple-600"></i> Informasi Sesi Imunisasi
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                    <div class="space-y-2">
                        <div>
                            <p class="text-gray-400 font-medium">Jenis Vaksin</p>
                            <p class="font-bold text-gray-800 mt-0.5"><?php echo htmlspecialchars($data['nama_vaksin']); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-400 font-medium">Tanggal Sesuai Jadwal</p>
                            <p class="font-bold text-gray-800 mt-0.5"><?php echo formatTanggalIndonesia($data['tanggal']); ?></p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div>
                            <p class="text-gray-400 font-medium">Tempat / Lokasi Pelaksanaan</p>
                            <p class="font-bold text-gray-800 mt-0.5 break-words"><i class="fas fa-map-marker-alt text-red-500 mr-1"></i><?php echo htmlspecialchars($data['lokasi']); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-400 font-medium">Tanggal Pengajuan Daftar</p>
                            <p class="font-bold text-gray-800 mt-0.5"><?php echo date('d/m/Y H:i', strtotime($data['created_at'])); ?> WIB</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if($data['status_pendaftaran'] == 'selesai' && $data['berat_badan']): ?>
            <div class="bg-green-50 rounded-xl p-4 mb-6 border border-green-100">
                <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2 text-sm">
                    <i class="fas fa-chart-line text-green-600"></i> Hasil Rekam Medis & Pemeriksaan Balita
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-xs mb-4">
                    <div>
                        <p class="text-gray-400 font-medium">Berat Badan</p>
                        <p class="font-bold text-gray-700 text-sm mt-0.5"><?php echo number_format($data['berat_badan'], 2); ?> kg</p>
                    </div>
                    <div>
                        <p class="text-gray-400 font-medium">Tinggi Badan</p>
                        <p class="font-bold text-gray-700 text-sm mt-0.5"><?php echo number_format($data['tinggi_badan'], 2); ?> cm</p>
                    </div>
                    <div>
                        <p class="text-gray-400 font-medium">Lingkar Kepala</p>
                        <p class="font-bold text-gray-700 text-sm mt-0.5"><?php echo number_format($data['lingkar_kepala'], 2); ?> cm</p>
                    </div>
                    <div>
                        <p class="text-gray-400 font-medium">Status Gizi Balita</p>
                        <p class="font-bold text-sm mt-0.5 <?php echo $data['status_gizi'] == 'Normal' ? 'text-green-600' : 'text-yellow-600'; ?>">
                            <?php echo htmlspecialchars($data['status_gizi']); ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400 font-medium">Kondisi Nafsu Makan</p>
                        <p class="font-bold text-gray-700 text-sm mt-0.5"><?php echo ucfirst($data['nafsu_makan']); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-400 font-medium">Bidan / Petugas Pelaksana</p>
                        <p class="font-bold text-gray-700 text-sm mt-0.5"><i class="fas fa-user-md text-blue-500 mr-1"></i><?php echo htmlspecialchars($data['nama_petugas'] ?: 'Bidan Posyandu'); ?></p>
                    </div>
                    <div class="col-span-full pt-2 border-t border-gray-200/60">
                        <p class="text-gray-400 font-medium">Catatan Kesehatan Tambahan</p>
                        <p class="text-gray-700 font-medium mt-0.5 leading-relaxed"><?php echo nl2br(htmlspecialchars($data['catatan_kesehatan'] ?: '-')); ?></p>
                    </div>
                </div>
                
                <?php if(!empty($rekomendasi_artikel)): ?>
                <div class="mt-4 p-3 <?php echo $ada_masalah ? 'bg-yellow-100/70' : 'bg-blue-100/70'; ?> rounded-xl border">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fas fa-lightbulb <?php echo $ada_masalah ? 'text-yellow-600' : 'text-blue-600'; ?>"></i>
                        <span class="text-xs font-bold <?php echo $ada_masalah ? 'text-yellow-800' : 'text-blue-800'; ?>">
                            <?php echo $ada_masalah ? 'Rekomendasi Artikel Edukasi Khusus Untuk Anda:' : 'Artikel Kesehatan Bermanfaat Untuk Anda:'; ?>
                        </span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <?php foreach($rekomendasi_artikel as $artikel): ?>
                        <a href="../artikel/detail_artikel.php?id=<?php echo $artikel['id_artikel']; ?>" 
                           class="block bg-white rounded-xl p-3 hover:shadow-md transition group border border-transparent hover:border-green-100">
                            <div class="flex items-start gap-2">
                                <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0 text-green-600">
                                    <i class="fas fa-book text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 text-xs leading-snug group-hover:text-green-600 transition line-clamp-2">
                                        <?php echo htmlspecialchars($artikel['judul']); ?>
                                    </h4>
                                    <p class="text-[10px] text-gray-400 mt-1 font-medium">
                                        <i class="far fa-calendar-alt mr-1"></i> <?php echo formatTanggalIndonesia($artikel['created_at']); ?>
                                    </p>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="../artikel/list_artikel.php" class="text-xs text-green-600 hover:text-green-700 font-bold flex items-center justify-center gap-0.5">
                            Lihat semua koleksi artikel edukasi <i class="fas fa-arrow-right text-[10px] ml-0.5"></i>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <div class="flex gap-3">
                <a href="riwayat_imunisasi.php" class="flex-1 bg-gray-100 text-gray-600 text-center py-2.5 rounded-xl font-semibold hover:bg-gray-200 transition text-sm flex items-center justify-center gap-1 shadow-sm">
                    Kembali
                </a>
                <?php if($data['status_pendaftaran'] == 'selesai'): ?>
                <form action="../perkembangan/detail_perkembangan.php" method="POST" class="flex-1">
                    <input type="hidden" name="anak_id" value="<?php echo $data['id_anak']; ?>">
                    <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-emerald-500 text-white text-center py-2.5 rounded-xl font-semibold hover:shadow-lg transition text-sm flex items-center justify-center gap-1">
                        <i class="fas fa-eye text-xs"></i>Perkembangan
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>