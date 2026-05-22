<?php
require_once __DIR__ . '/../../config/database.php';

$jadwal_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$type = isset($_GET['type']) ? $_GET['type'] : 'peserta';

if($type == 'peserta') {
    // Query untuk peserta yang mendaftar (pending)
    $query = "SELECT pi.*, a.nama_anak, u.nama_lengkap as nama_ibu, u.no_wa, j.tanggal, v.nama_vaksin
              FROM pendaftaran_imunisasi pi 
              JOIN anak a ON pi.id_anak = a.id_anak 
              JOIN users u ON a.nik_ibu = u.nik 
              JOIN jadwal_imunisasi j ON pi.id_jadwal = j.id_jadwal
              JOIN vaksin v ON j.id_vaksin = v.id_vaksin
              WHERE pi.id_jadwal = $jadwal_id AND pi.status = 'pending'
              ORDER BY pi.created_at DESC";
    $result = mysqli_query($conn, $query);
    ?>
    <div class="overflow-x-auto">
        <div class="mb-4 flex gap-2">
            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs"><i class="fas fa-clock mr-1"></i> Menunggu Imunisasi</span>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">No</th>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Nama Anak</th>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Nama Ibu</th>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">No. WA</th>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Vaksin</th>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Tanggal</th>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if(mysqli_num_rows($result) > 0): 
                    $no = 1;
                    while($row = mysqli_fetch_assoc($result)):
                        $wa_message = "Halo Ibu " . $row['nama_ibu'] . ",\n\n";
                        $wa_message .= "Kami mengingatkan jadwal imunisasi anak Anda:\n\n";
                        $wa_message .= "*Nama Anak:* " . $row['nama_anak'] . "\n";
                        $wa_message .= "*Vaksin:* " . $row['nama_vaksin'] . "\n";
                        $wa_message .= "*Tanggal:* " . date('d F Y', strtotime($row['tanggal'])) . "\n";
                        $wa_message .= "*Lokasi:* Posyandu\n\n";
                        $wa_message .= "Mohon segera datang ke Posyandu untuk melaksanakan imunisasi.\n\n";
                        $wa_message .= "Jangan lupa membawa Buku KIA anak.\n\n";
                        $wa_message .= "Terima kasih.\n\n";
                        $wa_message .= "*- Petugas Posyandu -*";
                        
                        $wa_url = "https://wa.me/" . preg_replace('/[^0-9]/', '', $row['no_wa']) . "?text=" . urlencode($wa_message);
                        
                        $tambah_hasil_url = "tambah_hasil.php?id=" . $row['id_pendaftaran'];
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 text-sm"><?php echo $no++; ?></td>
                        <td class="p-3 text-sm font-semibold"><?php echo htmlspecialchars($row['nama_anak']); ?></td>
                        <td class="p-3 text-sm"><?php echo htmlspecialchars($row['nama_ibu']); ?></td>
                        <td class="p-3 text-sm"><?php echo $row['no_wa']; ?></td>
                        <td class="p-3 text-sm"><?php echo $row['nama_vaksin']; ?></td>
                        <td class="p-3 text-sm"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                        <td class="p-3">
                            <div class="flex gap-2">
                                <a href="<?php echo $wa_url; ?>" target="_blank" 
                                   class="inline-flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded-lg text-xs transition">
                                    <i class="fab fa-whatsapp"></i> WA
                                </a>
                                <a href="<?php echo $tambah_hasil_url; ?>" 
                                   class="inline-flex items-center gap-1 bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded-lg text-xs transition">
                                    <i class="fas fa-syringe"></i> Imunisasi
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; 
                else: ?>
                    <tr><td colspan="7" class="p-8 text-center text-gray-500">Belum ada pendaftaran</td></table>
                    <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
} 
elseif($type == 'selesai') {
    // Query untuk yang sudah selesai
    $query = "SELECT pi.*, a.nama_anak, u.nama_lengkap as nama_ibu,
              hi.berat_badan, hi.tinggi_badan, hi.lingkar_kepala, hi.status_gizi, hi.nafsu_makan, hi.catatan_kesehatan, hi.tgl_imunisasi,
              v.nama_vaksin, j.tanggal
              FROM pendaftaran_imunisasi pi 
              JOIN anak a ON pi.id_anak = a.id_anak 
              JOIN users u ON a.nik_ibu = u.nik 
              JOIN jadwal_imunisasi j ON pi.id_jadwal = j.id_jadwal
              JOIN vaksin v ON j.id_vaksin = v.id_vaksin
              LEFT JOIN hasil_imunisasi hi ON pi.id_pendaftaran = hi.id_pendaftaran
              WHERE pi.id_jadwal = $jadwal_id AND pi.status = 'selesai'
              ORDER BY hi.tgl_imunisasi DESC";
    $result = mysqli_query($conn, $query);
    ?>
    <div class="overflow-x-auto">
        <div class="mb-4 flex gap-2">
            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs"><i class="fas fa-check-circle mr-1"></i> Selesai</span>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">No</th>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Nama Anak</th>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Nama Ibu</th>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Berat (kg)</th>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Tinggi (cm)</th>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Status Gizi</th>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Tanggal</th>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if(mysqli_num_rows($result) > 0): 
                    $no = 1;
                    while($row = mysqli_fetch_assoc($result)):
                        $detail_url = "detail_hasil.php?id=" . $row['id_pendaftaran'];
                        $edit_url = "edit_hasil.php?id=" . $row['id_pendaftaran'];
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 text-sm"><?php echo $no++; ?></td>
                        <td class="p-3 text-sm font-semibold"><?php echo htmlspecialchars($row['nama_anak']); ?></td>
                        <td class="p-3 text-sm"><?php echo htmlspecialchars($row['nama_ibu']); ?></td>
                        <td class="p-3 text-sm"><?php echo $row['berat_badan']; ?> kg</td>
                        <td class="p-3 text-sm"><?php echo $row['tinggi_badan']; ?> cm</td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded-full text-xs <?php echo $row['status_gizi'] == 'Normal' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>">
                                <?php echo $row['status_gizi']; ?>
                            </span>
                        </td>
                        <td class="p-3 text-sm"><?php echo date('d/m/Y', strtotime($row['tgl_imunisasi'])); ?></td>
                        <td class="p-3">
                            <div class="flex gap-2">
                                <a href="<?php echo $detail_url; ?>" class="text-green-600 hover:text-green-800" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo $edit_url; ?>" class="text-blue-500 hover:text-blue-700" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; 
                else: ?>
                    <tr>
                        <td colspan="8" class="p-8 text-center text-gray-500">Belum ada imunisasi yang selesai</td>
                    </tr>
                    <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>