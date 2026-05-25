<?php
require_once __DIR__ . '/../../config/database.php';

$jadwal_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$type = isset($_GET['type']) ? $_GET['type'] : 'peserta';

// Fungsi hitung usia
function hitungUsia($tanggal_lahir) {
    $lahir = new DateTime($tanggal_lahir);
    $now = new DateTime();
    $diff = $lahir->diff($now);
    
    if($diff->y > 0) {
        return $diff->y . ' tahun';
    } elseif($diff->m > 0) {
        return $diff->m . ' bulan';
    } else {
        return $diff->d . ' hari';
    }
}

// ============================================
// TAB 1: MENUNGGU (PENDING)
// ============================================
if($type == 'peserta') {
    $query = "SELECT pi.*, a.nama_anak, a.tanggal_lahir, u.nama_lengkap as nama_ibu, u.no_wa, 
                     j.tanggal, j.lokasi, v.nama_vaksin,
                     p.nama_lengkap as nama_petugas
              FROM pendaftaran_imunisasi pi 
              JOIN anak a ON pi.id_anak = a.id_anak 
              JOIN users u ON a.nik_ibu = u.nik 
              JOIN jadwal_imunisasi j ON pi.id_jadwal = j.id_jadwal
              JOIN vaksin v ON j.id_vaksin = v.id_vaksin
              LEFT JOIN users p ON j.petugas_nik = p.nik
              WHERE pi.id_jadwal = $jadwal_id AND pi.STATUS = 'pending'
              ORDER BY pi.created_at DESC";
    $result = mysqli_query($conn, $query);
    ?>
    <div class="overflow-x-auto">
        <div class="mb-4 flex gap-2">
            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">
                <i class="fas fa-clock mr-1"></i> Menunggu Imunisasi
            </span>
            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                <i class="fas fa-users mr-1"></i> Total: <?php echo mysqli_num_rows($result); ?> peserta
            </span>
        </div>
        
        <?php if(mysqli_num_rows($result) > 0): ?>
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 sticky top-0 text-gray-700 uppercase text-xs tracking-wider border-b border-gray-100">
                <tr>
                    <th class="p-3 font-semibold">No</th>
                    <th class="p-3 font-semibold">Nama Anak</th>
                    <th class="p-3 font-semibold">Usia</th>
                    <th class="p-3 font-semibold">Ibu</th>
                    <th class="p-3 font-semibold">WA</th>
                    <th class="p-3 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                <?php 
                $no = 1;
                while($row = mysqli_fetch_assoc($result)):
                    $usia_anak = hitungUsia($row['tanggal_lahir']);
                    $wa_message = "Halo Ibu " . $row['nama_ibu'] . ",\n\n Kami mengingatkan jadwal imunisasi anak Anda:\n\n Nama Anak: " . $row['nama_anak'] . "\n Vaksin: " . $row['nama_vaksin'] . "\n Tanggal: " . date('d F Y', strtotime($row['tanggal'])) . "\n Lokasi: " . ($row['lokasi'] ?? 'Posyandu') . "\n\n Mohon segera datang ke Posyandu. Terima kasih.";
                    $wa_url = "https://wa.me/" . preg_replace('/[^0-9]/', '', $row['no_wa']) . "?text=" . urlencode($wa_message);
                ?>
                <tr class="hover:bg-gray-50/80 transition">
                    <td class="p-3 text-gray-500"><?php echo $no++; ?></td>
                    <td class="p-3 font-bold text-gray-800"><?php echo htmlspecialchars($row['nama_anak']); ?></td>
                    <td class="p-3 font-medium"><?php echo $usia_anak; ?></td>
                    <td class="p-3 text-gray-600"><?php echo htmlspecialchars($row['nama_ibu']); ?></td>
                    <td class="p-3 text-gray-500"><?php echo htmlspecialchars($row['no_wa']); ?></td>
                    <td class="p-3 text-center">
                        <div class="flex gap-1.5 justify-center">
                            <a href="<?php echo $wa_url; ?>" target="_blank" class="bg-green-500 text-white p-1.5 rounded-lg text-xs hover:bg-green-600 shadow-sm transition" title="WhatsApp">
                                <i class="fab fa-whatsapp text-sm"></i>
                            </a>
                            <a href="tambah_hasil.php?id=<?php echo $row['id_pendaftaran']; ?>&jadwal_id=<?php echo $jadwal_id; ?>" class="bg-blue-500 text-white p-1.5 rounded-lg text-xs hover:bg-blue-600 shadow-sm transition" title="Hadir">
                                <i class="fas fa-syringe text-sm"></i>
                            </a>
                            <button type="button" 
                                    onclick="konfirmasiBatalManual('<?php echo $row['id_pendaftaran']; ?>', '<?php echo $jadwal_id; ?>')" 
                                    class="bg-red-500 text-white p-1.5 rounded-lg text-xs hover:bg-red-600 shadow-sm transition" 
                                    title="Batal">
                                <i class="fas fa-times text-sm px-0.5"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="text-center py-12 text-gray-400 bg-gray-50/50 rounded-xl border border-dashed border-gray-200">
            <i class="fas fa-user-clock text-4xl mb-2 opacity-50 block"></i>
            <p>Belum ada peserta yang mendaftar</p>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

// ============================================
// TAB 2: SELESAI
// ============================================
elseif($type == 'selesai') {
    $query = "SELECT pi.*, a.nama_anak, u.nama_lengkap as nama_ibu,
                     hi.berat_badan, hi.tinggi_badan, hi.lingkar_kepala, hi.status_gizi, hi.tgl_imunisasi,
                     v.nama_vaksin, j.tanggal
              FROM pendaftaran_imunisasi pi 
              JOIN anak a ON pi.id_anak = a.id_anak 
              JOIN users u ON a.nik_ibu = u.nik 
              JOIN jadwal_imunisasi j ON pi.id_jadwal = j.id_jadwal
              JOIN vaksin v ON j.id_vaksin = v.id_vaksin
              LEFT JOIN hasil_imunisasi hi ON pi.id_pendaftaran = hi.id_pendaftaran
              WHERE pi.id_jadwal = $jadwal_id AND pi.STATUS = 'selesai'
              ORDER BY hi.tgl_imunisasi DESC";
    $result = mysqli_query($conn, $query);
    ?>
    <div class="overflow-x-auto">
        <div class="mb-4 flex gap-2">
            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                <i class="fas fa-check-circle mr-1"></i> Selesai Imunisasi
            </span>
            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                <i class="fas fa-users mr-1"></i> Total: <?php echo mysqli_num_rows($result); ?> peserta
            </span>
        </div>
        
        <?php if(mysqli_num_rows($result) > 0): ?>
        <table class="w-full text-sm text-left border-collapse">
            <thead class="bg-gray-50 text-gray-700 uppercase text-xs tracking-wider border-b border-gray-100">
                <tr>
                    <th class="p-3 font-semibold">No</th>
                    <th class="p-3 font-semibold">Nama Anak</th>
                    <th class="p-3 font-semibold">Ibu</th>
                    <th class="p-3 font-semibold">Berat</th>
                    <th class="p-3 font-semibold">Tinggi</th>
                    <th class="p-3 font-semibold">Status Gizi</th>
                    <th class="p-3 font-semibold">Tgl Imunisasi</th>
                    <th class="p-3 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
                <tr class="hover:bg-gray-50/80 transition">
                    <td class="p-3 text-gray-500"><?php echo $no++; ?></td>
                    <td class="p-3 font-bold text-gray-800"><?php echo htmlspecialchars($row['nama_anak']); ?></td>
                    <td class="p-3 text-gray-600"><?php echo htmlspecialchars($row['nama_ibu']); ?></td>
                    <td class="p-3 font-medium"><?php echo $row['berat_badan']; ?> kg</td>
                    <td class="p-3 font-medium"><?php echo $row['tinggi_badan']; ?> cm</td>
                    <td class="p-3"><span class="px-2 py-0.5 rounded-lg text-xs font-bold bg-green-50 text-green-700 border border-green-200"><?php echo $row['status_gizi']; ?></span></td>
                    <td class="p-3 text-xs text-gray-500"><?php echo date('d/m/Y', strtotime($row['tgl_imunisasi'])); ?></td>
                    <td class="p-3 text-center">
                        <a href="edit_hasil.php?id=<?php echo $row['id_pendaftaran']; ?>" class="text-blue-500 hover:text-blue-700 p-1 block transition" title="Edit">
                            <i class="fas fa-edit text-base"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="text-center py-12 text-gray-400 bg-gray-50/50 rounded-xl border border-dashed border-gray-200">
            <i class="fas fa-comment-slash text-4xl mb-2 opacity-50 block"></i>
            <p>Belum ada imunisasi yang selesai</p>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

// ============================================
// TAB 3: DIBATALKAN
// ============================================
elseif($type == 'batal') {
    $query = "SELECT pi.*, a.nama_anak, u.nama_lengkap as nama_ibu,
                     v.nama_vaksin, j.tanggal
              FROM pendaftaran_imunisasi pi 
              JOIN anak a ON pi.id_anak = a.id_anak 
              JOIN users u ON a.nik_ibu = u.nik 
              JOIN jadwal_imunisasi j ON pi.id_jadwal = j.id_jadwal
              JOIN vaksin v ON j.id_vaksin = v.id_vaksin
              WHERE pi.id_jadwal = $jadwal_id AND pi.STATUS = 'batal'
              ORDER BY pi.updated_at DESC";
    $result = mysqli_query($conn, $query);
    ?>
    <div class="overflow-x-auto">
        <div class="mb-4 flex gap-2">
            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                <i class="fas fa-times-circle mr-1"></i> Dibatalkan Sesi
            </span>
            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                <i class="fas fa-users mr-1"></i> Total: <?php echo mysqli_num_rows($result); ?> pendaftaran
            </span>
        </div>
        
        <?php if(mysqli_num_rows($result) > 0): ?>
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-700 uppercase text-xs tracking-wider border-b border-gray-100">
                <tr>
                    <th class="p-3 font-semibold">No</th>
                    <th class="p-3 font-semibold">Nama Anak</th>
                    <th class="p-3 font-semibold">Nama Ibu</th>
                    <th class="p-3 font-semibold">Vaksin</th>
                    <th class="p-3 font-semibold">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
                <tr class="hover:bg-gray-50/80 transition">
                    <td class="p-3 text-gray-500"><?php echo $no++; ?></td>
                    <td class="p-3 font-bold text-gray-800 line-through decoration-red-400 opacity-70"><?php echo htmlspecialchars($row['nama_anak']); ?></td>
                    <td class="p-3 text-gray-600"><?php echo htmlspecialchars($row['nama_ibu']); ?></td>
                    <td class="p-3 font-medium text-purple-600"><?php echo htmlspecialchars($row['nama_vaksin']); ?></td>
                    <td class="p-3 text-xs text-gray-500"><?php echo formatTanggalIndonesia($row['tanggal']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="text-center py-12 text-gray-400 bg-gray-50/50 rounded-xl border border-dashed border-gray-200">
            <i class="fas fa-folder-open text-4xl mb-2 opacity-50 block"></i>
            <p>Tidak ada pendaftaran yang dibatalkan</p>
        </div>
        <?php endif; ?>
    </div>
    <?php
}
?>