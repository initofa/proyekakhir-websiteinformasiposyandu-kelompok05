<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_bidan.php';

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT hi.*, a.nama_anak, u.nama_lengkap as nama_ibu, v.nama_vaksin, j.tanggal FROM hasil_imunisasi hi JOIN pendaftaran_imunisasi pi ON hi.id_pendaftaran=pi.id_pendaftaran JOIN anak a ON pi.id_anak=a.id_anak JOIN users u ON a.nik_ibu=u.nik JOIN jadwal_imunisasi j ON pi.id_jadwal=j.id_jadwal JOIN vaksin v ON j.id_vaksin=v.id_vaksin WHERE hi.id_hasil=$id"));
$title = 'Detail Hasil Imunisasi';
include __DIR__ . '/../../templates/sidebar.php';
?>

<div class="max-w-2xl mx-auto fade-in">
    <div class="bg-white rounded-2xl shadow-lg p-6"><h1 class="text-2xl font-bold text-green-800 mb-6">Detail Hasil Imunisasi</h1>
    <div class="grid grid-cols-2 gap-4"><div><label class="text-gray-500">Nama Anak</label><p class="font-semibold"><?php echo $data['nama_anak']; ?></p></div><div><label class="text-gray-500">Nama Ibu</label><p><?php echo $data['nama_ibu']; ?></p></div>
    <div><label class="text-gray-500">Vaksin</label><p><?php echo $data['nama_vaksin']; ?></p></div><div><label class="text-gray-500">Tanggal Imunisasi</label><p><?php echo date('d/m/Y', strtotime($data['tanggal'])); ?></p></div>
    <div><label class="text-gray-500">Berat Badan</label><p><?php echo $data['berat_badan']; ?> kg</p></div><div><label class="text-gray-500">Tinggi Badan</label><p><?php echo $data['tinggi_badan']; ?> cm</p></div>
    <div><label class="text-gray-500">Lingkar Kepala</label><p><?php echo $data['lingkar_kepala']; ?> cm</p></div><div><label class="text-gray-500">Status Gizi</label><p><?php echo $data['status_gizi']; ?></p></div>
    <div><label class="text-gray-500">Nafsu Makan</label><p><?php echo ucfirst($data['nafsu_makan']); ?></p></div><div class="col-span-2"><label class="text-gray-500">Catatan</label><p><?php echo $data['catatan_kesehatan']; ?></p></div>
    <div><label class="text-gray-500">Petugas</label><p><?php echo getUserName($data['petugas_nik']); ?></p></div><div><label class="text-gray-500">Tanggal Input</label><p><?php echo date('d/m/Y H:i', strtotime($data['created_at'])); ?></p></div></div>
    <div class="flex gap-3 mt-6"><a href="list_hasil_imunisasi.php" class="flex-1 bg-gray-200 text-gray-700 text-center py-2 rounded-xl hover:bg-gray-300 transition">Kembali</a><a href="edit_hasil_imunisasi.php?id=<?php echo $id; ?>" class="flex-1 bg-blue-500 text-white text-center py-2 rounded-xl hover:bg-blue-600 transition">Edit</a></div></div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>