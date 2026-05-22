<?php
require_once __DIR__ . '/../../config/database.php';

$jadwal_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$type = isset($_GET['type']) ? $_GET['type'] : 'peserta';

if($type == 'peserta') {
    // Query untuk peserta yang mendaftar (status pending dan selesai)
    $query = "SELECT pi.*, a.nama_anak, u.nama_lengkap as nama_ibu, u.no_wa 
              FROM pendaftaran_imunisasi pi 
              JOIN anak a ON pi.id_anak = a.id_anak 
              JOIN users u ON a.nik_ibu = u.nik 
              WHERE pi.id_jadwal = $jadwal_id AND pi.status != 'batal'
              ORDER BY 
                  CASE pi.status 
                      WHEN 'pending' THEN 1 
                      WHEN 'selesai' THEN 2 
                      ELSE 3 
                  END,
                  pi.created_at DESC";
    $result = mysqli_query($conn, $query);
    ?>
    <div class="overflow-x-auto">
        <div class="mb-4 flex gap-2">
            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs"><i class="fas fa-clock mr-1"></i> Menunggu</span>
            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs"><i class="fas fa-check-double mr-1"></i> Selesai</span>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">No</th>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Nama Anak</th>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Nama Ibu</th>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Status</th>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Tanggal Daftar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if(mysqli_num_rows($result) > 0): 
                    $no = 1;
                    while($row = mysqli_fetch_assoc($result)): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 text-sm"><?php echo $no++; ?></td>
                        <td class="p-3 text-sm font-semibold"><?php echo htmlspecialchars($row['nama_anak']); ?></td>
                        <td class="p-3 text-sm"><?php echo htmlspecialchars($row['nama_ibu']); ?></td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded-full text-xs <?php 
                                echo $row['status'] == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                    ($row['status'] == 'selesai' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'); 
                            ?>">
                                <?php 
                                    if($row['status'] == 'pending') echo 'Menunggu';
                                    elseif($row['status'] == 'selesai') echo 'Selesai';
                                    else echo ucfirst($row['status']);
                                ?>
                            </span>
                        </td>
                        <td class="p-3 text-sm"><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                    </tr>
                    <?php endwhile; 
                else: ?>
                    <tr><td colspan="5" class="p-8 text-center text-gray-500">Belum ada pendaftaran</td>
                    <tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
} 
elseif($type == 'selesai') {
    // Query untuk yang sudah selesai
    $query = "SELECT pi.*, a.nama_anak, u.nama_lengkap as nama_ibu, 
              hi.berat_badan, hi.tinggi_badan, hi.status_gizi, hi.tgl_imunisasi
              FROM pendaftaran_imunisasi pi 
              JOIN anak a ON pi.id_anak = a.id_anak 
              JOIN users u ON a.nik_ibu = u.nik 
              LEFT JOIN hasil_imunisasi hi ON pi.id_pendaftaran = hi.id_pendaftaran
              WHERE pi.id_jadwal = $jadwal_id AND pi.status = 'selesai'
              GROUP BY pi.id_anak
              ORDER BY hi.tgl_imunisasi DESC";
    $result = mysqli_query($conn, $query);
    ?>
    <div class="overflow-x-auto">
        <div class="mb-4 flex gap-2">
            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs"><i class="fas fa-check-double mr-1"></i> Selesai</span>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">No</th>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Nama Anak</th>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Nama Ibu</th>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Berat</th>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Tinggi</th>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Status Gizi</th>
                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Tanggal Imunisasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if(mysqli_num_rows($result) > 0): 
                    $no = 1;
                    while($row = mysqli_fetch_assoc($result)): ?>
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
                    </tr>
                    <?php endwhile; 
                else: ?>
                    <tr><td colspan="7" class="p-8 text-center text-gray-500">Belum ada imunisasi yang selesai</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>