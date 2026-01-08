-- 1. Veritabanı oluşturma 
CREATE DATABASE IF NOT EXISTS kuafor_randevu CHARACTER SET utf8 COLLATE utf8_general_ci;
USE kuafor_randevu;

-- 2. Kullanıcılar Tablosu
CREATE TABLE kullanicilar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad VARCHAR(50) NOT NULL,
    soyad VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    sifre VARCHAR(255) NOT NULL, -- Şifre hash'i için 255 karakter
    rol ENUM('admin', 'kullanici') NOT NULL DEFAULT 'kullanici',
    kayit_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Kategoriler Tablosu
CREATE TABLE kategoriler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kategori_adi VARCHAR(100) NOT NULL UNIQUE
);

-- 4. Hizmetler Tablosu
CREATE TABLE hizmetler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hizmet_adi VARCHAR(100) NOT NULL,
    aciklama TEXT,
    fiyat DECIMAL(10, 2) NOT NULL,
    sure INT NOT NULL COMMENT 'Süre dakika cinsinden',
    kategori_id INT,
    FOREIGN KEY (kategori_id) REFERENCES kategoriler(id)
);

-- 5. Personel Tablosu
CREATE TABLE personel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad VARCHAR(50) NOT NULL,
    soyad VARCHAR(50) NOT NULL,
    uzmanlik_hizmetleri VARCHAR(255) COMMENT 'Virgülle ayrılmış hizmet IDleri'
);

-- 6. Randevular Tablosu
CREATE TABLE randevular (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kullanici_id INT NOT NULL,
    personel_id INT NOT NULL,
    hizmet_id INT NOT NULL,
    randevu_tarihi DATE NOT NULL,
    randevu_saati TIME NOT NULL,
    durum ENUM('onay bekliyor', 'onaylandı', 'tamamlandı', 'iptal edildi') NOT NULL DEFAULT 'onay bekliyor',
    toplam_fiyat DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (kullanici_id) REFERENCES kullanicilar(id),
    FOREIGN KEY (personel_id) REFERENCES personel(id),
    FOREIGN KEY (hizmet_id) REFERENCES hizmetler(id),
    UNIQUE KEY unique_randevu (personel_id, randevu_tarihi, randevu_saati) -- Çakışmayı engeller
);


INSERT INTO kullanicilar (ad, soyad, email, sifre, rol) VALUES 
('Yönetici', 'Sistem', 'admin@kuafor.com', '$2y$10$wTf7qPZ5XbQ8nFqE5oN/4O/iLzY.L2vJt0K8.0eA1a.l4fT2H.o5K', 'admin');

