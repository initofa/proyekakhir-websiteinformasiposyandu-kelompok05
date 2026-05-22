<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_bidan.php';
$title = 'Hasil Imunisasi';
include __DIR__ . '/../../templates/sidebar.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM hasil_imunisasi hi JOIN pendaftaran_imunisasi pi ON hi.id_pendaftaran=pi.id_pendaftaran JOIN jadwal_imunisasi j ON pi.id_jadwal=j.id_jadwal WHERE j.created_by='{$_SESSION['nik']}'"))['total'];
$total_pages = ceil($total / $limit);
$result = mysqli_query($conn, "SELECT hi.*, a.nama_anak, u.nama_lengkap as nama_ibu, u.no_wa, v.nama_vaksin, j.tanggal 
                                FROM hasil_imunisasi hi 
                                JOIN pendaftaran_imunisasi pi ON hi.id_pendaftaran=pi.id_pendaftaran 
                                JOIN anak a ON pi.id_anak=a.id_anak 
                                JOIN users u ON a.nik_ibu=u.nik
                                JOIN jadwal_imunisasi j ON pi.id_jadwal=j.id_jadwal 
                                JOIN vaksin v ON j.id_vaksin=v.id_vaksin 
                                WHERE j.created_by='{$_SESSION['nik']}' 
                                ORDER BY hi.created_at DESC 
                                LIMIT $offset, $limit");
?>

<div class="fade-in">
    <h1 class="text-2xl font-bold text-green-800 mb-4">Hasil Imunisasi</h1>
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-green-600 to-emerald-500 text-white">
                    <tr>
                        <th class="p-3">No</th>
                        <th class="p-3">Anak</th>
                        <th class="p-3">Ibu</th>
                        <th class="p-3">Vaksin</th>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Berat</th>
                        <th class="p-3">Tinggi</th>
                        <th class="p-3">Status Gizi</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $no = $offset + 1; while($row = mysqli_fetch_assoc($result)): 
                        // Pesan WhatsApp hasil imunisasi
                        $wa_message = "Halo Ibu " . $row['nama_ibu'] . ",\n\n";
                        $wa_message .= "Hasil imunisasi anak Anda:\n\n";
                        $wa_message .= "*Nama Anak:* " . $row['nama_anak'] . "\n";
                        $wa_message .= "*Vaksin:* " . $row['nama_vaksin'] . "\n";
                        $wa_message .= "*Tanggal Imunisasi:* " . date('d F Y', strtotime($row['tanggal'])) . "\n";
                        $wa_message .= "*Berat Badan:* " . $row['berat_badan'] . " kg\n";
                        $wa_message .= "*Tinggi Badan:* " . $row['tinggi_badan'] . " cm\n";
                        $wa_message .= "*Status Gizi:* " . $row['status_gizi'] . "\n\n";
                        
                        if($row['status_gizi'] != 'Normal') {
                            $wa_message .= "⚠️ *Perhatian:* Status gizi anak perlu mendapat perhatian khusus.\n";
                            $wa_message .= "Silakan konsultasi lebih lanjut dengan bidan.\n\n";
                        }
                        
                        $wa_message .= "Terima kasih sudah mengikuti imunisasi di Posyandu Ceria.\n\n";
                        $wa_message .= "*- Petugas Posyandu Ceria -*";
                        
                        $wa_url = "https://wa.me/" . preg_replace('/[^0-9]/', '', $row['no_wa']) . "?text=" . urlencode($wa_message);
                    ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-3"><?php echo $no++; ?></td>
                        <td class="p-3 font-semibold text-gray-800"><?php echo $row['nama_anak']; ?></td>
                        <td class="p-3"><?php echo $row['nama_ibu']; ?></td>
                        <td class="p-3"><?php echo $row['nama_vaksin']; ?></td>
                        <td class="p-3"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                        <td class="p-3"><?php echo $row['berat_badan']; ?> kg</td>
                        <td class="p-3"><?php echo $row['tinggi_badan']; ?> cm</td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded-full text-xs <?php echo $row['status_gizi'] == 'Normal' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>">
                                <?php echo $row['status_gizi']; ?>
                            </span>
                        </td>
                        <td class="p-3">
                            <div class="flex gap-2">
                                <a href="detail_hasil_imunisasi.php?id=<?php echo $row['id_hasil']; ?>" class="text-green-600 hover:text-green-800" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit_hasil_imunisasi.php?id=<?php echo $row['id_hasil']; ?>" class="text-blue-500 hover:text-blue-700" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="hapus_hasil_imunisasi.php?id=<?php echo $row['id_hasil']; ?>" class="text-red-500 hover:text-red-700" title="Hapus" onclick="confirmDelete(event, this.href)">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <a href="<?php echo $wa_url; ?>" target="_blank" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded-lg text-xs transition flex items-center gap-1" title="Kirim WhatsApp ke Ibu">
                                    <i class="fab fa-whatsapp"></i> WA
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php if($total_pages > 1) echo paginate($page, $total_pages, "list_hasil_imunisasi.php"); ?>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>