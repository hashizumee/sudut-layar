-- Create Database
CREATE DATABASE IF NOT EXISTS sudut_layar;
USE sudut_layar;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'client', 'demo') DEFAULT 'client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Projects Table
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100),
    title VARCHAR(100),
    number VARCHAR(10),
    year VARCHAR(10),
    image TEXT,
    google_drive_id TEXT,
    is_drive_folder BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Comments & Ratings Table
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    rating INT DEFAULT 5,
    parent_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE
);

-- Seed Initial Projects
INSERT INTO projects (category, title, number, year, image, google_drive_id, is_drive_folder) VALUES
('Karya 12 TJKT B', 'Si Guntur', '01', '2024', 'https://drive.google.com/thumbnail?id=1xOoK7DvOsSA89t415je4wq9hDcqXSjTZ&sz=w800', '1x5VdCek3ITHY2po1hxGpDcf2w0U7Q6Zj', FALSE),
('Karya 12 TP C', 'Titik Balik', '02', '2024', 'https://drive.google.com/thumbnail?id=1xAn_8V3ysBt-wwAeBC43T3VyUPhF_4nE&sz=w800', '1tRojpaKe_i7thqmTP1ezIFQq8ec1EXSC', FALSE),
('Karya 12 TC B', 'Selisih', '03', '2024', 'https://drive.google.com/thumbnail?id=1xV2S8BJaR0D3qb5ra32wbKMmmOWS_xqg&sz=w800', '1sWKtOPDAnsEu4Kx-6KW3qG1VGK21wkPq', FALSE),
('Karya Anak Bangsa', 'Jemput Bahagia', '04', '2024', 'https://drive.google.com/thumbnail?id=1zSvPx_6L7cvu0eyY2RQIuOsng1rCGcNs', '1zXiuaRvCDL73ssTAeHL3bPqWMaEBrw_Z', FALSE),
('Karya Rapi Film', 'Dear Nathan', '05', '2024', 'https://drive.google.com/thumbnail?id=1sBcnq6I6WEsNlfeoX8t5gSuiqVB-9oZt&sz=w800', '1sBcnq6I6WEsNlfeoX8t5gSuiqVB-9oZt', TRUE),
('Karya Anak Bangsa', 'FRI3ND', '06', '2024', 'https://drive.google.com/thumbnail?id=1zA71fVk5IfhfIVhfilmTk_H8buedpgzb', '1z_2FisbJffRr2lMc3wSTl3fPtwd2JiyG', FALSE);

-- Services (Education) Table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    title VARCHAR(100),
    content TEXT,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

-- Seed Services Data
-- Si Guntur
INSERT INTO services (project_id, title, content) VALUES
(1, 'Si Guntur', 'PEMBUATAN PROPERTI FILM :\n1. Bayi: boneka bayi realistis sebagai properti film\n2. Pistol: pistol mainan yang tidak berbahaya\n\nPENEMPATAN SOUND EFEK :\nMusik menegangkan pada adegan melahirkan dan penangkapan tersangka.\n\nPENYELESAIAN MASALAH :\nMenerima dan memaafkan semua kesalahan yang dilakukan oleh pemeran ayah.\n\nPESAN MORAL :\nJangan pernah menganggap seseorang lemah tanpa mengenalnya lebih jauh, serta percayalah pada diri sendiri.');
-- Selisih
INSERT INTO services (project_id, title, content) VALUES
(3, 'Selisih', 'PEMBUATAN PROPERTI FILM :\n1. Luka & memar: riasan wajah aman\n2. Darah: pewarna makanan / perma blood\n3. Pisau: pisau mainan\n4. Pistol: replika tidak berbahaya\n5. Borgol: replika borgol\n\nPENEMPATAN SOUND EFEK :\nSound aksi pada perkelahian & sound menegangkan saat introgasi.\n\nPENYELESAIAN MASALAH :\nKedua kelompok menyesali perbuatan dan memutuskan berdamai.\n\nPESAN MORAL :\nKekerasan hanya memberi solusi instan namun menimbulkan konsekuensi negatif panjang.');
-- Titik Balik
INSERT INTO services (project_id, title, content) VALUES
(2, 'Titik Balik', 'PENEMPATAN SOUND EFEK :\nSound menegangkan untuk membuat penonton penasaran.\n\nPENYELESAIAN MASALAH :\nMenyelesaikan masalah dengan kepala dingin tanpa merugikan orang lain.\n\nPESAN MORAL :\nAnak yang terbawa pergaulan bebas dapat berubah demi masa depan yang lebih baik.');
-- Jemput Bahagia
INSERT INTO services (project_id, title, content) VALUES
(4, 'Jemput Bahagia', 'PEMBUATAN PROPERTI FILM :\n1. Darah: saus tomat / pewarna aman\n2. Perban dan kain pembalut luka\n3. Botol obat berisi air/sirup aman\n4. Tisu mengelap darah mimisan\n5. Hadiah kecil dari ayah untuk anak\n\nPENEMPATAN SOUND EFEK :\nBel sekolah, suara kendaraan, alarm HP, langkah kaki, musik sedih & lembut.\n\nPENYELESAIAN MASALAH :\nDukungan keluarga, rasa syukur, dan perubahan pola pikir sebagai solusi.\n\nPESAN MORAL :\nKebahagiaan sejati dari menghargai cinta keluarga dan bersyukur atas hal kecil.');
-- Dear Nathan
INSERT INTO services (project_id, title, content) VALUES
(5, 'Dear Nathan', 'PEMBUATAN PROPERTI FILM :\n1. Surat catatan kecil dari Nathan untuk Salma\n2. Coklat, hadiah kecil, dan bunga sebagai simbol hubungan\n\nPENEMPATAN SOUND EFEK :\nSound romantis lembut, dramatis, dan ceria di berbagai adegan.\n\nPENYELESAIAN MASALAH :\n1. Komunikasi terbuka menyelesaikan konflik remaja\n2. Pengertian dan kompromi mempererat hubungan\n3. Dukungan orang terdekat membangun kembali kepercayaan\n\nPESAN MORAL :\nHubungan sehat dibangun lewat komunikasi, toleransi, dan rasa hormat terhadap perbedaan.');
-- Fri3nd
INSERT INTO services (project_id, title, content) VALUES
(6, 'Fri3nd', 'PEMBUATAN PROPERTI FILM :\n1. Kaleng makanan menunjukkan ketergantungan karakter\n2. Lampu temaram menciptakan suasana klaustrofobia\n3. Headphone besar sebagai simbol isolasi diri\n\nPENGGUNAAN SOUND EFEK :\nDiegetic Sound: dengungan lampu neon konstan. Non-Diegetic: musik latar minimalis mencekam.\n\nPENYELESAIAN MASALAH :\n1. AI sebagai teman melawan kesepian\n2. Kepercayaan melampaui kode pemrograman\n3. Penerimaan takdir sebagai penyelesaian rasa takut\n\nPESAN MORAL :\nKoneksi antarmahluk adalah kebutuhan dasar yang lebih penting dari sekadar bertahan fisik.');

-- Seed Users (password is 'demo' hashed)
INSERT INTO users (name, email, password) VALUES
('Portal Klien', 'klien@gmail.com', '$2y$12$b84cp1hS0LoPjFTBo8G4au5c3bqOrfQUIBRhfWNoXx6Q8Ix4xHNAm'),
('Admin Studio', 'admin@gmail.com', '$2y$12$qgSisrsK79nfACqrKMTkWeu88tKnE.3NHlSs0OA410d9GtD9V8BCi'),
('Demo User', 'demo@gmail.com', '$2y$12$.gArBqtDv/Lc6eAg3MS.Ce7mBouK/NwzpvlUU/CJQUXNgYtmhfbg.');
