<?php
require_once __DIR__ . '/../../config/database.php';

$jadwal_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$type = isset($_GET['type']) ? $_GET['type'] : 'peserta';


if ($type == 'peserta') {
    $query = "SELECT pi.*, a.nama_anak, a.jenis_kelamin, u.nama_lengkap as nama_ibu, u.no_wa 
              FROM pendaftaran_imunisasi pi 
              JOIN anak a ON pi.id_anak = a.id_anak 
              JOIN users u ON a.nik_ibu = u.nik 
              WHERE pi.id_jadwal = $jadwal_id AND pi.STATUS = 'pending'
              ORDER BY pi.created_at DESC";
    $result = mysqli_query($conn, $query);
    ?>
    <div class="overflow-x-auto">
        <div class="mb-4 flex gap-2">
            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold"><i class="fas fa-clock mr-1"></i> Menunggu Pelayanan</span>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-700 text-xs uppercase tracking-wider">
                    <th class="p-3 font-semibold">No</th>
                    <th class="p-3 font-semibold">Nama Anak</th>
                    <th class="p-3 font-semibold">L/P</th>
                    <th class="p-3 font-semibold">Nama Ibu</th>
                    <th class="p-3 font-semibold">Status Layanan</th>
                    <th class="p-3 font-semibold">Waktu Daftar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                <?php if (mysqli_num_rows($result) > 0): $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                <tr class="hover:bg-gray-50/80 transition">
                    <td class="p-3 text-gray-500"><?php echo $no++; ?></td>
                    <td class="p-3 font-semibold text-gray-800"><?php echo htmlspecialchars($row['nama_anak']); ?></td>
                    <td class="p-3">
                        <span class="px-1.5 py-0.5 rounded text-xs font-bold <?php echo $row['jenis_kelamin'] == 'L' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600'; ?>">
                            <?php echo $row['jenis_kelamin']; ?>
                        </span>
                    </td>
                    <td class="p-3 text-gray-600"><?php echo htmlspecialchars($row['nama_ibu']); ?></td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded-xl text-xs font-medium bg-yellow-100 text-yellow-700">
                            Menunggu
                        </span>
                    </td>
                    <td class="p-3 text-gray-500 text-xs"><?php echo formatTanggalIndonesia($row['created_at']); ?></td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="6" class="p-8 text-center text-gray-400 text-sm">
                        <i class="fas fa-users-slash text-3xl mb-2 opacity-40 block"></i>
                        Belum ada peserta antrean menunggu pada sesi ini.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
} 


elseif ($type == 'selesai') {
    $query = "SELECT pi.*, a.nama_anak, a.jenis_kelamin, u.nama_lengkap as nama_ibu, 
                     hi.berat_badan, hi.tinggi_badan, hi.lingkar_kepala, hi.status_gizi, hi.tgl_imunisasi
              FROM pendaftaran_imunisasi pi 
              JOIN anak a ON pi.id_anak = a.id_anak 
              JOIN users u ON a.nik_ibu = u.nik 
              LEFT JOIN hasil_imunisasi hi ON pi.id_pendaftaran = hi.id_pendaftaran
              WHERE pi.id_jadwal = $jadwal_id AND pi.STATUS = 'selesai'
              ORDER BY hi.tgl_imunisasi DESC";
    $result = mysqli_query($conn, $query);
    ?>
    <div class="overflow-x-auto">
        <div class="mb-4 flex gap-2">
            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold"><i class="fas fa-check-double mr-1"></i> Selesai Tindakan</span>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-700 text-xs uppercase tracking-wider">
                    <th class="p-3 font-semibold">No</th>
                    <th class="p-3 font-semibold">Nama Anak</th>
                    <th class="p-3 font-semibold">Nama Ibu</th>
                    <th class="p-3 font-semibold">Timbangan (BB)</th>
                    <th class="p-3 font-semibold">Tinggi (TB)</th>
                    <th class="p-3 font-semibold">L. Kepala</th>
                    <th class="p-3 font-semibold">Status Gizi</th>
                    <th class="p-3 font-semibold">Waktu Suntik</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                <?php if (mysqli_num_rows($result) > 0): $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                <tr class="hover:bg-gray-50/80 transition">
                    <td class="p-3 text-gray-500"><?php echo $no++; ?></td>
                    <td class="p-3 font-semibold text-gray-800"><?php echo htmlspecialchars($row['nama_anak']); ?></td>
                    <td class="p-3 text-gray-600"><?php echo htmlspecialchars($row['nama_ibu']); ?></td>
                    <td class="p-3 font-medium text-gray-700"><?php echo number_format($row['berat_badan'], 2); ?> kg</td>
                    <td class="p-3 text-gray-600"><?php echo number_format($row['tinggi_badan'], 2); ?> cm</td>
                    <td class="p-3 text-gray-600"><?php echo number_format($row['lingkar_kepala'], 2); ?> cm</td>
                    <td class="p-3">
                        <span class="px-2 py-0.5 rounded-lg text-xs font-semibold bg-green-100 text-green-700 border border-green-200">
                            <?php echo htmlspecialchars($row['status_gizi'] ?: 'Normal'); ?>
                        </span>
                    </td>
                    <td class="p-3 text-gray-500 text-xs"><?php echo formatTanggalIndonesia($row['tgl_imunisasi']); ?></td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="8" class="p-8 text-center text-gray-400 text-sm">
                        <i class="fas fa-comment-slash text-3xl mb-2 opacity-40 block"></i>
                        Belum ada data rekam imunisasi selesai yang diinput petugas.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}


elseif ($type == 'batal') {
    $query = "SELECT pi.*, a.nama_anak, a.jenis_kelamin, u.nama_lengkap as nama_ibu, j.tanggal
              FROM pendaftaran_imunisasi pi 
              JOIN anak a ON pi.id_anak = a.id_anak 
              JOIN users u ON a.nik_ibu = u.nik 
              JOIN jadwal_imunisasi j ON pi.id_jadwal = j.id_jadwal
              WHERE pi.id_jadwal = $jadwal_id AND pi.STATUS = 'batal'
              ORDER BY pi.updated_at DESC";
    $result = mysqli_query($conn, $query);
    ?>
    <div class="overflow-x-auto">
        <div class="mb-4 flex gap-2">
            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold"><i class="fas fa-times-circle mr-1"></i> Dibatalkan Sesi</span>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-700 text-xs uppercase tracking-wider">
                    <th class="p-3 font-semibold">No</th>
                    <th class="p-3 font-semibold">Nama Anak</th>
                    <th class="p-3 font-semibold">L/P</th>
                    <th class="p-3 font-semibold">Nama Ibu</th>
                    <th class="p-3 font-semibold">Rencana Tanggal</th>
                    <th class="p-3 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                <?php if (mysqli_num_rows($result) > 0): $no = 1; while ($row = mysqli_fetch_assoc($result)): 
                    // Tombol aktif jika tanggal pelaksanaan adalah hari ini atau masa depan
                    $bisa_pulih = strtotime($row['tanggal']) >= strtotime(date('Y-m-d'));
                ?>
                <tr class="hover:bg-gray-50/80 transition">
                    <td class="p-3 text-gray-500"><?php echo $no++; ?></td>
                    <td class="p-3 font-bold text-gray-800 line-through decoration-red-400 opacity-60"><?php echo htmlspecialchars($row['nama_anak']); ?></td>
                    <td class="p-3">
                        <span class="px-1.5 py-0.5 rounded text-xs font-bold bg-gray-100 text-gray-500">
                            <?php echo $row['jenis_kelamin']; ?>
                        </span>
                    </td>
                    <td class="p-3 text-gray-600"><?php echo htmlspecialchars($row['nama_ibu']); ?></td>
                    <td class="p-3 text-gray-500 text-xs"><?php echo formatTanggalIndonesia($row['tanggal']); ?></td>
                    <td class="p-3 text-center">
                        <?php if ($bisa_pulih): ?>
                            <button type="button" 
                                    onclick="konfirmasiDaftarUlang('<?php echo $row['id_pendaftaran']; ?>', '<?php echo $jadwal_id; ?>')" 
                                    class="bg-green-500 text-white px-3 py-1 rounded-lg text-xs hover:bg-green-600 transition shadow-sm font-semibold flex items-center gap-1 mx-auto">
                                <i class="fas fa-undo text-[10px]"></i> Pulihkan
                            </button>
                        <?php else: ?>
                            <span class="text-xs text-gray-400 italic">Sesi Lewat</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="6" class="p-8 text-center text-gray-400 text-sm">
                        <i class="fas fa-folder-open text-3xl mb-2 opacity-40 block"></i>
                        Tidak ada riwayat pendaftaran yang dibatalkan pada sesi ini.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>