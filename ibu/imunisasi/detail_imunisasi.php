<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_ibu.php';

$id = $_GET['id'];
$nik = $_SESSION['nik'];

// Ambil data detail imunisasi
$query = "SELECT pi.*, a.nama_anak, a.tanggal_lahir, a.jenis_kelamin, a.berat_lahir, a.panjang_lahir, a.id_anak,
          v.nama_vaksin, j.tanggal,
          hi.berat_badan, hi.tinggi_badan, hi.lingkar_kepala, hi.status_gizi, hi.nafsu_makan, hi.catatan_kesehatan, hi.tgl_imunisasi
          FROM pendaftaran_imunisasi pi 
          JOIN anak a ON pi.id_anak = a.id_anak 
          JOIN jadwal_imunisasi j ON pi.id_jadwal = j.id_jadwal 
          JOIN vaksin v ON j.id_vaksin = v.id_vaksin 
          LEFT JOIN hasil_imunisasi hi ON pi.id_pendaftaran = hi.id_pendaftaran 
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

// Cek apakah ada masalah
$ada_masalah = false;

if($data['status_gizi'] != 'Normal'){
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
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <!-- Header -->
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
            <!-- Status Imunisasi -->
            <div class="mb-6">
                <?php if($data['status'] == 'pending'): ?>
                <div class="bg-yellow-100 border-l-4 border-yellow-500 p-4 rounded-r-lg">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        <div>
                            <p class="font-semibold text-yellow-800">Menunggu Konfirmasi</p>
                            <p class="text-sm text-yellow-700">Pendaftaran Anda sedang menunggu konfirmasi dari petugas.</p>
                        </div>
                    </div>
                </div>
                <?php elseif($data['status'] == 'selesai'): ?>
                <div class="bg-green-100 border-l-4 border-green-500 p-4 rounded-r-lg">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        <div>
                            <p class="font-semibold text-green-800">Imunisasi Selesai</p>
                            <p class="text-sm text-green-700">Imunisasi telah dilaksanakan pada <?php echo date('d F Y', strtotime($data['tgl_imunisasi'])); ?></p>
                        </div>
                    </div>
                </div>
                <?php elseif($data['status'] == 'batal'): ?>
                <div class="bg-red-100 border-l-4 border-red-500 p-4 rounded-r-lg">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                        <div>
                            <p class="font-semibold text-red-800">Pendaftaran Dibatalkan</p>
                            <p class="text-sm text-red-700">Pendaftaran imunisasi ini telah dibatalkan.</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Informasi Anak -->
            <div class="bg-blue-50 rounded-xl p-4 mb-6">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-child text-blue-600"></i> Data Anak
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div>
                        <p class="text-xs text-gray-500">Nama Anak</p>
                        <p class="font-semibold text-gray-800"><?php echo $data['nama_anak']; ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Tanggal Lahir</p>
                        <p class="font-semibold text-gray-800"><?php echo date('d/m/Y', strtotime($data['tanggal_lahir'])); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Usia</p>
                        <p class="font-semibold text-gray-800"><?php echo $usia->y; ?> tahun <?php echo $usia->m; ?> bulan</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Jenis Kelamin</p>
                        <p class="font-semibold text-gray-800"><?php echo $data['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Informasi Imunisasi -->
            <div class="bg-purple-50 rounded-xl p-4 mb-6">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-syringe text-purple-600"></i> Informasi Imunisasi
                </h3>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs text-gray-500">Vaksin</p>
                        <p class="font-semibold text-gray-800"><?php echo $data['nama_vaksin']; ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Tanggal Jadwal</p>
                        <p class="font-semibold text-gray-800"><?php echo date('d/m/Y', strtotime($data['tanggal'])); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Tanggal Daftar</p>
                        <p class="font-semibold text-gray-800"><?php echo date('d/m/Y H:i', strtotime($data['created_at'])); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Hasil Pemeriksaan (Jika sudah selesai) -->
            <?php if($data['status'] == 'selesai' && $data['berat_badan']): ?>
            <div class="bg-green-50 rounded-xl p-4 mb-6">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-chart-line text-green-600"></i> Hasil Pemeriksaan
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div>
                        <p class="text-xs text-gray-500">Berat Badan</p>
                        <p class="font-semibold text-gray-800"><?php echo $data['berat_badan']; ?> kg</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Tinggi Badan</p>
                        <p class="font-semibold text-gray-800"><?php echo $data['tinggi_badan']; ?> cm</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Lingkar Kepala</p>
                        <p class="font-semibold text-gray-800"><?php echo $data['lingkar_kepala']; ?> cm</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Status Gizi</p>
                        <p class="font-semibold <?php echo $data['status_gizi'] == 'Normal' ? 'text-green-600' : 'text-yellow-600'; ?>">
                            <?php echo $data['status_gizi']; ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Nafsu Makan</p>
                        <p class="font-semibold text-gray-800"><?php echo ucfirst($data['nafsu_makan']); ?></p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500">Catatan Kesehatan</p>
                        <p class="text-gray-700"><?php echo $data['catatan_kesehatan'] ?: '-'; ?></p>
                    </div>
                </div>
                
                <!-- Rekomendasi Artikel -->
                <?php if(!empty($rekomendasi_artikel)): ?>
                <div class="mt-4 p-3 <?php echo $ada_masalah ? 'bg-yellow-100' : 'bg-blue-100'; ?> rounded-lg">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fas fa-lightbulb <?php echo $ada_masalah ? 'text-yellow-600' : 'text-blue-600'; ?>"></i>
                        <span class="text-sm font-semibold <?php echo $ada_masalah ? 'text-yellow-700' : 'text-blue-700'; ?>">
                            <?php echo $ada_masalah ? 'Rekomendasi Artikel untuk Anda:' : 'Artikel Bermanfaat untuk Anda:'; ?>
                        </span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <?php foreach($rekomendasi_artikel as $artikel): ?>
                        <a href="../artikel/detail_artikel.php?id=<?php echo $artikel['id_artikel']; ?>" 
                           class="block bg-white rounded-lg p-3 hover:shadow-md transition group">
                            <div class="flex items-start gap-2">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-book text-green-600 text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 text-sm group-hover:text-green-600 transition">
                                        <?php echo $artikel['judul']; ?>
                                    </h4>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="far fa-calendar-alt mr-1"></i> <?php echo date('d M Y', strtotime($artikel['created_at'])); ?>
                                    </p>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="../artikel/list_artikel.php" class="text-sm text-green-600 hover:text-green-700">
                            Lihat semua artikel <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Tombol Aksi -->
            <div class="flex gap-3">
                <a href="riwayat_imunisasi.php" class="flex-1 bg-gray-200 text-gray-700 text-center py-2 rounded-xl font-semibold hover:bg-gray-300 transition">
                Kembali
                </a>
                <?php if($data['status'] == 'selesai'): ?>
                <a href="../perkembangan/detail_perkembangan.php?anak_id=<?php echo $data['id_anak']; ?>" class="flex-1 bg-green-600 text-white text-center py-2 rounded-xl font-semibold hover:bg-green-700 transition">
                    <i class="fas fa-eye mr-1"></i> Perkembangan
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>