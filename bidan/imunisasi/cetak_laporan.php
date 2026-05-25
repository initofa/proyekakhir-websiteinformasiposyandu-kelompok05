<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_bidan.php';

$id_jadwal = isset($_POST['id_jadwal']) ? (int)$_POST['id_jadwal'] : 0;

if ($id_jadwal === 0) {
    die("<div class='flex items-center justify-center min-h-screen bg-red-50'><div class='text-center p-8 bg-white rounded-2xl shadow-xl'><i class='fas fa-exclamation-triangle text-red-500 text-5xl mb-4'></i><h3 class='text-xl font-bold text-red-600'>Akses Tidak Sah!</h3><p class='text-gray-600 mt-2'>ID Jadwal tidak valid.</p></div></div>");
}

$query_jadwal = "SELECT j.*, v.nama_vaksin, u.nama_lengkap as nama_bidan 
                 FROM jadwal_imunisasi j
                 JOIN vaksin v ON j.id_vaksin = v.id_vaksin
                 LEFT JOIN users u ON j.petugas_nik = u.nik
                 WHERE j.id_jadwal = $id_jadwal";
$jadwal = mysqli_fetch_assoc(mysqli_query($conn, $query_jadwal));

if (!$jadwal) {
    die("<div class='flex items-center justify-center min-h-screen bg-red-50'><div class='text-center p-8 bg-white rounded-2xl shadow-xl'><i class='fas fa-search text-red-500 text-5xl mb-4'></i><h3 class='text-xl font-bold text-red-600'>Data Tidak Ditemukan!</h3><p class='text-gray-600 mt-2'>Jadwal imunisasi tidak tersedia.</p></div></div>");
}

$query_peserta = "SELECT pi.STATUS as status_reg, a.nama_anak, a.jenis_kelamin, u_ibu.nama_lengkap as nama_ibu,
                         hi.berat_badan, hi.tinggi_badan, hi.lingkar_kepala, hi.status_gizi, hi.catatan_kesehatan
                  FROM pendaftaran_imunisasi pi
                  JOIN anak a ON pi.id_anak = a.id_anak
                  JOIN users u_ibu ON a.nik_ibu = u_ibu.nik
                  LEFT JOIN hasil_imunisasi hi ON pi.id_pendaftaran = hi.id_pendaftaran
                  WHERE pi.id_jadwal = $id_jadwal AND pi.STATUS = 'selesai'
                  ORDER BY a.nama_anak ASC";
$result_peserta = mysqli_query($conn, $query_peserta);
$jumlah_peserta = mysqli_num_rows($result_peserta);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pelaksanaan Imunisasi #<?php echo $id_jadwal; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4;
                margin: 2cm 1.5cm !important;
            }
            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .bg-gray-50 {
                background-color: #f9fafb !important;
            }
            .border-b {
                border-bottom-width: 1px !important;
            }
            .print-container {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
        }
        
        @media print {
            body {
                padding: 0 !important;
                margin: 0 auto !important;
            }
            .max-w-5xl {
                max-width: 100% !important;
                margin: 0 !important;
            }
            .p-8 {
                padding: 0.5cm !important;
            }
        }
        
        @media print and (orientation: portrait) {
            @page {
                margin: 1.5cm 2cm;
            }
        }
        
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        .report-container {
            margin-left: auto;
            margin-right: auto;
        }
        
        @media print {
            .report-container {
                margin: 0;
                padding: 0;
            }
            table {
                width: 100%;
                table-layout: auto;
            }
            th, td {
                word-wrap: break-word;
            }
        }
    </style>
</head>
<body class="bg-gray-100 p-6 print:bg-white print:p-0">

<div class="max-w-5xl mx-auto bg-white shadow-2xl rounded-2xl overflow-hidden print:shadow-none print:rounded-none report-container">
    
    <div class="no-print bg-gray-800 text-white p-4 flex justify-between items-center print:hidden">
        <div class="flex items-center gap-4">
            <a href="list_pendaftaran.php" class="bg-gray-600 hover:bg-gray-500 px-4 py-2 rounded-lg transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <button onclick="window.print()" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg transition flex items-center gap-2">
            <i class="fas fa-print"></i> Cetak Laporan 
        </button>
    </div>

    <div class="p-8 print:p-6">
        
        <div class="text-center border-b-4 border-green-500 pb-6 mb-6">
            <div class="flex justify-center mb-4">
                <img src="/posyandu/img/sipanda.png" alt="Logo sipanda" class="h-24 w-auto">
            </div>
            <h1 class="text-2xl font-bold text-green-700 mt-2">Sistem Informasi Posyandu Anak dan Bunda</h1>
            <p class="text-xs text-gray-500 mt-1">Jl. Telang, Kamal - Layanan Pengaduan Terpadu | Telp. 081999925324</p>
        </div>

        <div class="text-center mb-8">
            <h3 class="text-xl font-bold text-gray-800 uppercase tracking-wide border-b-2 border-green-500 inline-block pb-2">
                Laporan Pelaksanaan Pelayanan Imunisasi Anak
            </h3>
        </div>

        <div class="bg-gray-50 rounded-xl p-5 mb-6 print:bg-white print:border print:border-gray-300">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Jenis Vaksinasi</p>
                    <p class="font-semibold text-gray-800 text-lg"><?php echo htmlspecialchars($jadwal['nama_vaksin']); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tanggal Pelaksanaan</p>
                    <p class="font-semibold text-gray-800"><?php echo formatTanggalIndonesia($jadwal['tanggal']); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Lokasi / Tempat Sesi</p>
                    <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($jadwal['lokasi']); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Bidan Penanggung Jawab</p>
                    <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($jadwal['nama_bidan'] ?? 'Belum Ditentukan'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Anak Dilayani</p>
                    <p class="font-semibold text-green-600 text-2xl"><?php echo $jumlah_peserta; ?> Anak</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto mb-8">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-green-600 text-white print:bg-gray-300 print:text-black">
                        <th class="border border-gray-300 p-2 text-center w-12">No</th>
                        <th class="border border-gray-300 p-2 text-center">Nama Lengkap Anak</th>
                        <th class="border border-gray-300 p-2 text-center w-16">L/P</th>
                        <th class="border border-gray-300 p-2 text-center">Nama Ibu Kandung</th>
                        <th class="border border-gray-300 p-2 text-center w-20">BB (Kg)</th>
                        <th class="border border-gray-300 p-2 text-center w-20">TB (Cm)</th>
                        <th class="border border-gray-300 p-2 text-center w-24">L. Kepala</th>
                        <th class="border border-gray-300 p-2 text-center w-28">Status Gizi</th>
                        <th class="border border-gray-300 p-2 text-center">Catatan Kesehatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($jumlah_peserta > 0): $no = 1; while($row = mysqli_fetch_assoc($result_peserta)): ?>
                    <tr class="hover:bg-gray-50 transition print:hover:bg-white">
                        <td class="border border-gray-300 p-2 text-center"><?php echo $no++; ?></td>
                        <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($row['nama_anak']); ?></td>
                        <td class="border border-gray-300 p-2 text-center"><?php echo $row['jenis_kelamin']; ?></td>
                        <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($row['nama_ibu']); ?></td>
                        <td class="border border-gray-300 p-2 text-center"><?php echo number_format($row['berat_badan'], 2); ?></td>
                        <td class="border border-gray-300 p-2 text-center"><?php echo number_format($row['tinggi_badan'], 2); ?></td>
                        <td class="border border-gray-300 p-2 text-center"><?php echo number_format($row['lingkar_kepala'], 2); ?></td>
                        <td class="border border-gray-300 p-2 text-center">
                            <?php 
                            $gizi = $row['status_gizi'] ?: 'Normal';
                            $giziClass = '';
                            if($gizi == 'Normal') $giziClass = 'text-green-600 font-semibold';
                            elseif($gizi == 'Kurang') $giziClass = 'text-yellow-600 font-semibold';
                            elseif($gizi == 'Buruk') $giziClass = 'text-red-600 font-semibold';
                            elseif($gizi == 'Lebih') $giziClass = 'text-orange-600 font-semibold';
                            ?>
                            <span class="<?php echo $giziClass; ?>"><?php echo htmlspecialchars($gizi); ?></span>
                        </td>
                        <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($row['catatan_kesehatan'] ?: '-'); ?></td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="9" class="border border-gray-300 p-8 text-center text-gray-500 italic">
                            <i class="fas fa-inbox text-4xl mb-2 block"></i>
                            Belum ada rekaman data anak yang berstatus selesai untuk sesi jadwal ini.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="flex justify-end mt-10">
            <div class="text-center w-64">
                <p class="text-sm text-gray-600 mb-2">Petugas Pelaksana,</p>
                <div class="h-16 mb-2"></div>
                <div class="border-t-2 border-gray-400 pt-2">
                    <p class="font-bold text-gray-800"><?php echo htmlspecialchars($jadwal['nama_bidan'] ?? '............................'); ?></p>
                    <p class="text-xs text-gray-500">Penanggung Jawab</p>
                </div>
            </div>
        </div>

        <div class="text-center text-xs text-gray-400 mt-8 pt-4 border-t">
            <p>Dokumen ini dicetak secara otomatis dari Sipanda</p>
            <p>Dicetak pada: <?php echo formatTanggalIndonesia(date('Y-m-d')) . ' ' . date('H:i:s'); ?></p>
        </div>
    </div>
</div>

<script>
    window.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            if(window.location.hash !== '#print') {
                window.print();
            }
        }, 300);
    });
</script>
</body>
</html>