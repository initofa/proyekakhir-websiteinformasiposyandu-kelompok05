CREATE DATABASE db_posyandu;
USE db_posyandu;


CREATE TABLE users (
    nik VARCHAR(16) PRIMARY KEY,
    PASSWORD VARCHAR(255) NOT NULL,
    no_wa VARCHAR(20),
    nama_lengkap VARCHAR(100) NOT NULL,
    alamat TEXT,
    ROLE ENUM('admin','bidan','ibu') DEFAULT 'ibu',
    STATUS ENUM('active','pending','inactive') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by VARCHAR(16),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by VARCHAR(16),
    
    FOREIGN KEY (created_by) REFERENCES users(nik) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(nik) ON DELETE SET NULL
);


CREATE TABLE kategori_artikel (
    id_kategori INT PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE anak (
    id_anak INT PRIMARY KEY AUTO_INCREMENT,
    nik_ibu VARCHAR(16) NOT NULL,
    nama_anak VARCHAR(100) NOT NULL,
    tempat_lahir VARCHAR(100),
    tanggal_lahir DATE,
    jenis_kelamin ENUM('L','P'),
    berat_lahir DECIMAL(5,2),
    panjang_lahir DECIMAL(5,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (nik_ibu) REFERENCES users(nik) ON DELETE CASCADE ON UPDATE CASCADE
);


CREATE TABLE vaksin (
    id_vaksin INT PRIMARY KEY AUTO_INCREMENT,
    nama_vaksin VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    usia_rekomendasi INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE jadwal_imunisasi (
    id_jadwal INT PRIMARY KEY AUTO_INCREMENT,
    id_vaksin INT NOT NULL,
    tanggal DATE NOT NULL,
    lokasi VARCHAR(150) NOT NULL,
    petugas_nik VARCHAR(16),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by VARCHAR(16),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by VARCHAR(16),
    
    FOREIGN KEY (id_vaksin) REFERENCES vaksin(id_vaksin) ON DELETE CASCADE,
    FOREIGN KEY (petugas_nik) REFERENCES users(nik) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(nik) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(nik) ON DELETE SET NULL
);


CREATE TABLE pendaftaran_imunisasi (
    id_pendaftaran INT PRIMARY KEY AUTO_INCREMENT,
    id_jadwal INT NOT NULL,
    id_anak INT NOT NULL,
    STATUS ENUM('pending', 'selesai', 'batal') DEFAULT 'pending',
    tgl_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by VARCHAR(16),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by VARCHAR(16),

    FOREIGN KEY (id_jadwal) REFERENCES jadwal_imunisasi(id_jadwal) ON DELETE CASCADE,
    FOREIGN KEY (id_anak) REFERENCES anak(id_anak) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(nik) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(nik) ON DELETE SET NULL
);


CREATE TABLE hasil_imunisasi (
    id_hasil INT PRIMARY KEY AUTO_INCREMENT,
    id_pendaftaran INT NOT NULL,
    berat_badan DECIMAL(5,2),
    tinggi_badan DECIMAL(5,2),
    lingkar_kepala DECIMAL(5,2),
    status_gizi VARCHAR(50),
    nafsu_makan ENUM('baik', 'kurang', 'buruk'),
    catatan_kesehatan TEXT,
    tgl_imunisasi DATE,
    petugas_nik VARCHAR(16),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by VARCHAR(16),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by VARCHAR(16),

    FOREIGN KEY (id_pendaftaran) REFERENCES pendaftaran_imunisasi(id_pendaftaran) ON DELETE CASCADE,
    FOREIGN KEY (petugas_nik) REFERENCES users(nik) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(nik) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(nik) ON DELETE SET NULL
);


CREATE TABLE artikel (
    id_artikel INT PRIMARY KEY AUTO_INCREMENT,
    id_kategori INT,
    judul VARCHAR(200) NOT NULL,
    konten TEXT NOT NULL,
    thumbnail VARCHAR(255),
    penulis_nik VARCHAR(16),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_kategori) REFERENCES kategori_artikel(id_kategori) ON DELETE SET NULL,
    FOREIGN KEY (penulis_nik) REFERENCES users(nik) ON DELETE SET NULL
);


CREATE TABLE ibu_hamil (
    id_kehamilan INT PRIMARY KEY AUTO_INCREMENT,
    nik_ibu VARCHAR(16) NOT NULL,
    usia_kehamilan INT,
    hpht DATE,
    hpl DATE,
    berat_badan_ibu DECIMAL(5,2),
    tinggi_badan_ibu DECIMAL(5,2),
    tekanan_darah VARCHAR(20),
    status_kehamilan ENUM('aktif', 'melahirkan', 'keguguran', 'pindah') DEFAULT 'aktif',
    catatan_kesehatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by VARCHAR(16),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by VARCHAR(16),

    FOREIGN KEY (nik_ibu) REFERENCES users(nik) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(nik) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(nik) ON DELETE SET NULL
);


CREATE TABLE pemeriksaan_kehamilan (
    id_pemeriksaan INT PRIMARY KEY AUTO_INCREMENT,
    id_kehamilan INT NOT NULL,
    usia_kehamilan INT,
    tanggal_pemeriksaan DATE,
    berat_badan DECIMAL(5,2),
    tekanan_darah VARCHAR(20),
    lingkar_perut DECIMAL(5,2),
    tinggi_fundus DECIMAL(5,2),
    detak_jantung_janin INT,
    keluhan TEXT,
    tindakan TEXT,
    petugas_nik VARCHAR(16),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by VARCHAR(16),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by VARCHAR(16),

    FOREIGN KEY (id_kehamilan) REFERENCES ibu_hamil(id_kehamilan) ON DELETE CASCADE,
    FOREIGN KEY (petugas_nik) REFERENCES users(nik) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(nik) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(nik) ON DELETE SET NULL
);



-- 1 ADMIN
INSERT INTO users (nik, PASSWORD, no_wa, nama_lengkap, alamat, ROLE, STATUS, created_by) VALUES
('1234567890123456', MD5('admin123'), '081234567890', 'Administrator', 'Kantor Posyandu Ceria, Jl. Kesehatan No. 1, Jakarta', 'admin', 'active', NULL);

UPDATE users SET created_by = '1234567890123456' WHERE nik = '1234567890123456';

-- 3 BIDAN
INSERT INTO users (nik, PASSWORD, no_wa, nama_lengkap, alamat, ROLE, STATUS, created_by) VALUES
('1234567890123457', MD5('bidan123'), '081234567891', 'Bidan Dewi Sartika', 'Posyandu Ceria, Jl. Mawar No. 1, Jakarta', 'bidan', 'active', '1234567890123456'),
('1234567890123461', MD5('bidan123'), '081234567901', 'Bidan Ratna Sari', 'Puskesmas Kelurahan, Jl. Kenanga No. 5, Jakarta', 'bidan', 'active', '1234567890123456'),
('1234567890123462', MD5('bidan123'), '081234567902', 'Bidan Mardiana', 'Posyandu Flamboyan, Jl. Flamboyan No. 3, Jakarta', 'bidan', 'active', '1234567890123456');

-- 5 IBU
INSERT INTO users (nik, PASSWORD, no_wa, nama_lengkap, alamat, ROLE, STATUS, created_by) VALUES
('1234567890123458', MD5('ibu123'), '081234567892', 'Siti Aisyah', 'Jl. Mawar No. 1, RT 01 RW 02, Jakarta', 'ibu', 'active', '1234567890123456'),
('1234567890123459', MD5('ibu123'), '081234567893', 'Nurul Hidayah', 'Jl. Melati No. 5, RT 02 RW 03, Jakarta', 'ibu', 'active', '1234567890123456'),
('1234567890123463', MD5('ibu123'), '081234567903', 'Rina Febrianti', 'Jl. Anggrek No. 8, RT 03 RW 01, Jakarta', 'ibu', 'active', '1234567890123456'),
('1234567890123464', MD5('ibu123'), '081234567904', 'Lestari Handayani', 'Jl. Cempaka No. 12, RT 04 RW 02, Jakarta', 'ibu', 'active', '1234567890123456'),
('1234567890123465', MD5('ibu123'), '081234567905', 'Putri Wulandari', 'Jl. Dahlia No. 7, RT 05 RW 03, Jakarta', 'ibu', 'active', '1234567890123456');


INSERT INTO kategori_artikel (nama_kategori) VALUES
('Gizi Balita'),
('Imunisasi'),
('Perkembangan Anak'),
('Kesehatan Ibu'),
('MPASI'),
('Penyakit Anak'),
('Kesehatan Mental'),
('ASI Eksklusif'),
('Aktivitas Anak'),
('Kebersihan');


INSERT INTO vaksin (nama_vaksin, deskripsi, usia_rekomendasi) VALUES
('BCG', 'Vaksin untuk mencegah penyakit Tuberkulosis (TBC) pada anak. Diberikan satu kali sebelum anak berusia 2 bulan.', 0),
('Hepatitis B', 'Vaksin untuk mencegah infeksi virus Hepatitis B yang dapat menyebabkan kerusakan hati. Diberikan dalam 24 jam setelah lahir.', 0),
('Polio', 'Vaksin untuk mencegah penyakit Polio yang dapat menyebabkan kelumpuhan. Diberikan 4 kali pada usia 0-4 bulan.', 0),
('DPT-HB-Hib', 'Vaksin kombinasi untuk mencegah Difteri, Pertusis, Tetanus, Hepatitis B, dan Meningitis. Diberikan 3 kali pada usia 2-4 bulan.', 2),
('Rotavirus', 'Vaksin untuk mencegah diare berat akibat infeksi Rotavirus. Diberikan 2-3 kali pada usia 2-6 bulan.', 2),
('PCV', 'Vaksin untuk mencegah pneumonia, meningitis, dan infeksi telinga akibat bakteri Pneumokokus.', 2),
('Campak', 'Vaksin untuk mencegah penyakit Campak yang sangat menular. Diberikan pada usia 9 bulan dan 18 bulan.', 9),
('JE', 'Vaksin untuk mencegah Japanese Encephalitis (radang otak). Diberikan pada usia 9 bulan dan 24 bulan.', 9),
('HPV', 'Vaksin untuk mencegah kanker serviks pada perempuan. Diberikan pada usia 10-14 tahun sebanyak 2 kali.', 120),
('Td', 'Vaksin untuk mencegah Tetanus dan Difteri. Diberikan pada usia 7-18 tahun sebagai booster.', 84);

=

-- Ibu Siti Aisyah (2 anak)
INSERT INTO anak (nik_ibu, nama_anak, tempat_lahir, tanggal_lahir, jenis_kelamin, berat_lahir, panjang_lahir) VALUES
('1234567890123458', 'Ahmad Fathir', 'Jakarta', '2023-01-15', 'L', 3.20, 48.00),
('1234567890123458', 'Aisha Putri', 'Jakarta', '2024-06-20', 'P', 2.90, 47.00);

-- Ibu Nurul Hidayah (2 anak)
INSERT INTO anak (nik_ibu, nama_anak, tempat_lahir, tanggal_lahir, jenis_kelamin, berat_lahir, panjang_lahir) VALUES
('1234567890123459', 'Muhammad Rizki', 'Jakarta', '2022-03-10', 'L', 3.40, 49.00),
('1234567890123459', 'Zahra Nabila', 'Jakarta', '2023-08-05', 'P', 3.00, 48.00);

-- Ibu Rina Febrianti (2 anak)
INSERT INTO anak (nik_ibu, nama_anak, tempat_lahir, tanggal_lahir, jenis_kelamin, berat_lahir, panjang_lahir) VALUES
('1234567890123463', 'Budi Santoso', 'Jakarta', '2023-11-20', 'L', 3.10, 47.50),
('1234567890123463', 'Cahya Purnama', 'Jakarta', '2024-10-15', 'P', 2.85, 46.50);

-- Ibu Lestari Handayani (2 anak)
INSERT INTO anak (nik_ibu, nama_anak, tempat_lahir, tanggal_lahir, jenis_kelamin, berat_lahir, panjang_lahir) VALUES
('1234567890123464', 'Siti Aminah', 'Jakarta', '2022-02-14', 'P', 3.00, 48.00),
('1234567890123464', 'Abdul Rahman', 'Jakarta', '2024-01-30', 'L', 3.30, 49.00);

-- Ibu Putri Wulandari (2 anak)
INSERT INTO anak (nik_ibu, nama_anak, tempat_lahir, tanggal_lahir, jenis_kelamin, berat_lahir, panjang_lahir) VALUES
('1234567890123465', 'Nadia Putri', 'Jakarta', '2023-05-18', 'P', 2.95, 47.00),
('1234567890123465', 'Rafi Alamsyah', 'Jakarta', '2024-09-12', 'L', 3.25, 48.50);


INSERT INTO jadwal_imunisasi (id_vaksin, tanggal, lokasi, petugas_nik, created_by) VALUES
(1, DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'Posyandu Mawar - Dusun Krajan RT 01 RW 01', '1234567890123457', '1234567890123456'),
(2, DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'Posyandu Mawar - Dusun Krajan RT 01 RW 01', '1234567890123457', '1234567890123456'),
(3, DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'Posyandu Melati - Dusun Kopen RT 02 RW 01', '1234567890123461', '1234567890123456'),
(4, DATE_ADD(CURDATE(), INTERVAL 21 DAY), 'Posyandu Melati - Dusun Kopen RT 02 RW 01', '1234567890123461', '1234567890123456'),
(5, DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'Posyandu Anggrek - Dusun Ngemplak RT 03 RW 02', '1234567890123462', '1234567890123456'),
(6, DATE_ADD(CURDATE(), INTERVAL 45 DAY), 'Posyandu Anggrek - Dusun Ngemplak RT 03 RW 02', '1234567890123462', '1234567890123456'),
(7, DATE_ADD(CURDATE(), INTERVAL 60 DAY), 'Posyandu Flamboyan - Dusun Soka RT 04 RW 02', '1234567890123457', '1234567890123456'),
(8, DATE_ADD(CURDATE(), INTERVAL 90 DAY), 'Posyandu Flamboyan - Dusun Soka RT 04 RW 02', '1234567890123457', '1234567890123456');


INSERT INTO pendaftaran_imunisasi (id_jadwal, id_anak, STATUS, tgl_daftar, created_by) VALUES
(1, 1, 'pending', NOW(), '1234567890123458'),
(1, 2, 'pending', NOW(), '1234567890123458'),
(2, 3, 'pending', NOW(), '1234567890123459'),
(3, 1, 'selesai', DATE_SUB(NOW(), INTERVAL 30 DAY), '1234567890123458'),
(4, 2, 'selesai', DATE_SUB(NOW(), INTERVAL 25 DAY), '1234567890123458'),
(5, 3, 'pending', DATE_SUB(NOW(), INTERVAL 20 DAY), '1234567890123459'),
(6, 1, 'pending', DATE_SUB(NOW(), INTERVAL 15 DAY), '1234567890123458'),
(7, 4, 'batal', DATE_SUB(NOW(), INTERVAL 10 DAY), '1234567890123459'),
(8, 2, 'pending', DATE_SUB(NOW(), INTERVAL 5 DAY), '1234567890123458');


INSERT INTO hasil_imunisasi (id_pendaftaran, berat_badan, tinggi_badan, lingkar_kepala, status_gizi, nafsu_makan, catatan_kesehatan, tgl_imunisasi, petugas_nik) VALUES
(3, 7.50, 68.00, 42.00, 'Normal', 'baik', 'Anak sehat, perkembangan motorik baik', DATE_SUB(NOW(), INTERVAL 29 DAY), '1234567890123457'),
(4, 6.80, 65.00, 41.00, 'Normal', 'baik', 'Anak aktif dan ceria', DATE_SUB(NOW(), INTERVAL 24 DAY), '1234567890123457'),
(5, 8.20, 72.00, 44.00, 'Normal', 'baik', 'Perkembangan sesuai usia', DATE_SUB(NOW(), INTERVAL 19 DAY), '1234567890123461');


INSERT INTO artikel (id_kategori, judul, konten, thumbnail, penulis_nik) VALUES
(1, 'Pentingnya Gizi Seimbang untuk Tumbuh Kembang Anak', 
'Gizi seimbang sangat penting untuk tumbuh kembang anak. Anak membutuhkan asupan karbohidrat, protein, lemak, vitamin, dan mineral yang cukup setiap harinya.

Berikut panduan gizi seimbang untuk balita:
- Karbohidrat: Nasi, kentang, roti, atau mie sebagai sumber energi
- Protein: Ikan, telur, ayam, daging, tahu, tempe untuk pertumbuhan
- Sayuran: Brokoli, wortel, bayam, kangkung untuk vitamin
- Buah-buahan: Pisang, apel, jeruk, pepaya untuk serat dan vitamin
- Susu: Untuk kalsium dan protein tambahan', 
NULL, '1234567890123456'),

(2, 'Jadwal Imunisasi Lengkap untuk Anak 0-24 Bulan', 
'Imunisasi adalah langkah penting untuk melindungi anak. Berikut jadwal imunisasi lengkap:
- Usia 0-1 bulan: Hepatitis B, BCG, Polio 1
- Usia 2 bulan: DPT-HB-Hib 1, Polio 2, Rotavirus 1, PCV 1
- Usia 3 bulan: DPT-HB-Hib 2, Polio 3, Rotavirus 2
- Usia 4 bulan: DPT-HB-Hib 3, Polio 4, PCV 2
- Usia 9 bulan: Campak-Rubella
- Usia 18 bulan: DPT-HB-Hib (booster), Campak-Rubella (booster)', 
NULL, '1234567890123456'),

(3, 'Tahapan Perkembangan Anak Usia 0-12 Bulan', 
'Perkembangan bayi 0-12 bulan:
- Usia 0-3 bulan: Mulai tersenyum, mengikuti objek dengan mata
- Usia 4-6 bulan: Tengkurap, meraih mainan, duduk dengan bantuan
- Usia 7-9 bulan: Duduk tanpa bantuan, merangkak, bermain cilukba
- Usia 10-12 bulan: Berdiri dengan berpegangan, berjalan dengan dituntun', 
NULL, '1234567890123456'),

(4, 'Panduan Kesehatan Ibu Hamil Trimester 1, 2, dan 3', 
'Panduan kehamilan per trimester:
- Trimester 1: Konsumsi asam folat, hindari rokok dan alkohol
- Trimester 2: Rasakan gerakan janin, konsumsi kalsium dan zat besi
- Trimester 3: Pantau pergerakan janin, persiapkan persalinan', 
NULL, '1234567890123457'),

(5, 'Resep MPASI Sehat untuk Bayi 6 Bulan', 
'MPASI pertama harus mengandung 4 bintang: karbohidrat + protein hewani + protein nabati + sayuran.
Bahan-bahan: Beras putih, ayam kampung, tempe, wortel.
Cara membuat: Rebus ayam, parut wortel, masa bubur, campur semua bahan, blender sesuai tekstur.', 
NULL, '1234567890123457');


INSERT INTO ibu_hamil (nik_ibu, usia_kehamilan, hpht, hpl, berat_badan_ibu, tinggi_badan_ibu, tekanan_darah, status_kehamilan, catatan_kesehatan, created_by) VALUES
('1234567890123458', 28, '2024-10-15', '2025-07-22', 62.5, 158, '110/70', 'aktif', 'Kehamilan sehat, janin aktif bergerak', '1234567890123456'),
('1234567890123459', 16, '2025-01-20', '2025-10-27', 55.0, 160, '100/70', 'aktif', 'Kehamilan kedua, kondisi baik', '1234567890123456'),
('1234567890123464', 12, '2025-02-28', '2025-12-05', 58.0, 162, '110/70', 'aktif', 'Kehamilan pertama, kondisi prima', '1234567890123456'),
('1234567890123463', 0, '2024-01-10', '2024-10-17', 60.0, 155, '120/80', 'melahirkan', 'Lahir normal, bayi sehat', '1234567890123456'),
('1234567890123465', 0, '2024-02-15', '2024-11-21', 57.0, 158, '110/70', 'melahirkan', 'Persalinan lancar', '1234567890123456');


INSERT INTO pemeriksaan_kehamilan (id_kehamilan, usia_kehamilan, tanggal_pemeriksaan, berat_badan, tekanan_darah, lingkar_perut, tinggi_fundus, detak_jantung_janin, keluhan, tindakan, petugas_nik, created_by) VALUES
(1, 8, '2024-12-10', 58.0, '110/70', 72, NULL, NULL, 'Mual di pagi hari', 'Konsumsi jahe, makan sedikit tapi sering', '1234567890123457', '1234567890123457'),
(1, 12, '2025-01-15', 59.0, '110/70', 76, 10, 150, 'Mual berkurang', 'Suplemen zat besi', '1234567890123457', '1234567890123457'),
(1, 16, '2025-02-20', 60.5, '110/70', 80, 14, 155, 'Tidak ada keluhan', 'Edukasi gizi ibu hamil', '1234567890123457', '1234567890123457'),
(1, 20, '2025-03-25', 61.5, '110/70', 84, 18, 158, 'Sering BAK', 'Normal kehamilan', '1234567890123457', '1234567890123457'),
(1, 24, '2025-04-30', 62.0, '110/70', 88, 22, 160, 'Janin aktif bergerak', 'Edukasi persiapan persalinan', '1234567890123457', '1234567890123457'),
(2, 6, '2025-02-28', 54.0, '100/70', 70, NULL, NULL, 'Mual dan muntah', 'Vitamin B6, istirahat cukup', '1234567890123461', '1234567890123461'),
(2, 10, '2025-03-30', 54.5, '100/70', 74, 8, 148, 'Mual berkurang', 'Suplemen asam folat', '1234567890123461', '1234567890123461'),
(2, 14, '2025-04-27', 55.0, '100/70', 78, 12, 152, 'Tidak ada keluhan', 'Edukasi senam hamil', '1234567890123461', '1234567890123461'),
(3, 6, '2025-04-10', 57.0, '110/70', 72, NULL, NULL, 'Mual ringan', 'Konsumsi makanan bergizi', '1234567890123462', '1234567890123462'),
(3, 10, '2025-05-15', 58.0, '110/70', 76, 8, 150, 'Tidak ada keluhan', 'Pemeriksaan rutin', '1234567890123462', '1234567890123462');