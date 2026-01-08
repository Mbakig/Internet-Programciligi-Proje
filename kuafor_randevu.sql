-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 08 Oca 2026, 14:30:09
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `kuafor_randevu`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `calisma_saatleri`
--

CREATE TABLE `calisma_saatleri` (
  `id` int(11) NOT NULL,
  `personel_id` int(11) NOT NULL,
  `gun` varchar(20) DEFAULT NULL,
  `gun_no` int(11) NOT NULL,
  `baslangic_saati` time NOT NULL,
  `bitis_saati` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `calisma_saatleri`
--

INSERT INTO `calisma_saatleri` (`id`, `personel_id`, `gun`, `gun_no`, `baslangic_saati`, `bitis_saati`) VALUES
(5, 1, 'Pazartesi', 1, '09:00:00', '18:00:00'),
(6, 1, 'Salı', 2, '09:00:00', '18:00:00'),
(7, 1, 'Çarşamba', 3, '09:00:00', '18:00:00'),
(8, 1, 'Perşembe', 4, '09:00:00', '18:00:00'),
(9, 4, 'Pazartesi', 1, '09:00:00', '17:00:00'),
(10, 4, 'Salı', 2, '09:00:00', '17:00:00'),
(11, 4, 'Çarşamba', 3, '09:00:00', '17:00:00'),
(12, 4, 'Perşembe', 4, '09:00:00', '17:00:00'),
(13, 4, 'Cuma', 5, '09:00:00', '17:00:00'),
(14, 3, 'Pazartesi', 1, '09:00:00', '18:00:00'),
(15, 3, 'Salı', 2, '09:00:00', '18:00:00'),
(16, 3, 'Çarşamba', 3, '09:00:00', '18:00:00'),
(17, 3, 'Perşembe', 4, '09:00:00', '18:00:00'),
(18, 3, 'Cuma', 5, '09:00:00', '18:00:00');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `hizmetler`
--

CREATE TABLE `hizmetler` (
  `id` int(11) NOT NULL,
  `hizmet_adi` varchar(100) NOT NULL,
  `aciklama` text DEFAULT NULL,
  `fiyat` decimal(10,2) NOT NULL,
  `sure` int(11) NOT NULL COMMENT 'Süre dakika cinsinden',
  `kategori_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `hizmetler`
--

INSERT INTO `hizmetler` (`id`, `hizmet_adi`, `aciklama`, `fiyat`, `sure`, `kategori_id`) VALUES
(3, 'Sadece Saç Kesimi', '', 600.00, 30, 3),
(4, 'Saç Ve Sakal Kesimi', '', 1000.00, 45, 4),
(5, 'Cilt Bakımı', '', 800.00, 30, 7),
(6, 'Manikür ve Pedikür.', '', 600.00, 30, 7),
(7, 'Makyaj ve Güzellik Uygulamaları', '', 1000.00, 90, 7),
(8, 'Saç Boyama', 'Rengi Belirtiniz\r\n', 3000.00, 120, 2);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kategoriler`
--

CREATE TABLE `kategoriler` (
  `id` int(11) NOT NULL,
  `kategori_adi` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `kategoriler`
--

INSERT INTO `kategoriler` (`id`, `kategori_adi`) VALUES
(7, 'Cilt,TIrnak Ve İstenmeyen Tüylerden arınma'),
(2, 'Saç Boyama'),
(3, 'Saç Kesme'),
(4, 'Saç Ve Sakal Kesimi');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanicilar`
--

CREATE TABLE `kullanicilar` (
  `id` int(11) NOT NULL,
  `ad` varchar(50) NOT NULL,
  `soyad` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `rol` enum('admin','kullanici') NOT NULL DEFAULT 'kullanici',
  `kayit_tarihi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `kullanicilar`
--

INSERT INTO `kullanicilar` (`id`, `ad`, `soyad`, `email`, `sifre`, `rol`, `kayit_tarihi`) VALUES
(4, 'Yönetici', 'Sistem', 'admin@kuafor.com', '$2y$10$xZVKaBf7cZgss/Sr3z3HQu7fHygDTSoG2WeenBtkvNI3/x26d5pcG', 'admin', '2025-11-07 15:50:21'),
(5, 'Baki', 'Müşteri', 'deneme@musteri.com', '$2y$10$LaaF1L8rRF4PVOaW2lTMcOa7aM7tVnAY5ToAr2vGjo1U/aCEyV6.u', 'kullanici', '2025-11-07 16:11:08');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `personel`
--

CREATE TABLE `personel` (
  `id` int(11) NOT NULL,
  `ad` varchar(50) NOT NULL,
  `soyad` varchar(50) NOT NULL,
  `uzmanlik_hizmetleri` varchar(255) DEFAULT NULL COMMENT 'Virgülle ayrılmış hizmet IDleri',
  `puan` decimal(3,2) DEFAULT 5.00,
  `mesai_baslangic` time DEFAULT '09:00:00',
  `mesai_bitis` time DEFAULT '19:00:00',
  `calisma_gunleri` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `personel`
--

INSERT INTO `personel` (`id`, `ad`, `soyad`, `uzmanlik_hizmetleri`, `puan`, `mesai_baslangic`, `mesai_bitis`, `calisma_gunleri`) VALUES
(1, 'Ahmet', 'Aslan', '1', 4.00, '09:00:00', '18:00:00', 'Pazartesi,Salı,Çarşamba,Perşembe'),
(3, 'Osman', 'Yılmaz', NULL, 3.00, '09:00:00', '18:00:00', 'Pazartesi,Salı,Çarşamba,Perşembe,Cuma'),
(4, 'Berivan', 'Çiçek', NULL, 5.00, '09:00:00', '17:00:00', 'Pazartesi,Salı,Çarşamba,Perşembe,Cuma');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `randevular`
--

CREATE TABLE `randevular` (
  `id` int(11) NOT NULL,
  `kullanici_id` int(11) NOT NULL,
  `personel_id` int(11) NOT NULL,
  `hizmet_id` int(11) NOT NULL,
  `randevu_tarihi` date NOT NULL,
  `randevu_saati` time NOT NULL,
  `durum` varchar(50) DEFAULT 'beklemede',
  `toplam_fiyat` decimal(10,2) NOT NULL,
  `puan` int(11) DEFAULT NULL,
  `yorum` text DEFAULT NULL,
  `aciklama` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `randevular`
--

INSERT INTO `randevular` (`id`, `kullanici_id`, `personel_id`, `hizmet_id`, `randevu_tarihi`, `randevu_saati`, `durum`, `toplam_fiyat`, `puan`, `yorum`, `aciklama`) VALUES
(4, 5, 1, 4, '2025-12-31', '09:30:00', 'onaylandı', 0.00, 3, 'iyi gibi ', NULL),
(6, 5, 1, 3, '2026-02-02', '12:00:00', 'reddedildi', 0.00, NULL, NULL, NULL),
(7, 5, 1, 3, '2026-04-01', '11:30:00', 'beklemede', 0.00, NULL, NULL, NULL),
(8, 5, 4, 7, '2026-12-01', '13:30:00', 'tamamlandı', 0.00, 3, 'Güzel.', '');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `calisma_saatleri`
--
ALTER TABLE `calisma_saatleri`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `hizmetler`
--
ALTER TABLE `hizmetler`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_hizmet_kategori` (`kategori_id`);

--
-- Tablo için indeksler `kategoriler`
--
ALTER TABLE `kategoriler`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kategori_adi` (`kategori_adi`);

--
-- Tablo için indeksler `kullanicilar`
--
ALTER TABLE `kullanicilar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Tablo için indeksler `personel`
--
ALTER TABLE `personel`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `randevular`
--
ALTER TABLE `randevular`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_randevu` (`personel_id`,`randevu_tarihi`,`randevu_saati`),
  ADD KEY `kullanici_id` (`kullanici_id`),
  ADD KEY `hizmet_id` (`hizmet_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `calisma_saatleri`
--
ALTER TABLE `calisma_saatleri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Tablo için AUTO_INCREMENT değeri `hizmetler`
--
ALTER TABLE `hizmetler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Tablo için AUTO_INCREMENT değeri `kategoriler`
--
ALTER TABLE `kategoriler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tablo için AUTO_INCREMENT değeri `kullanicilar`
--
ALTER TABLE `kullanicilar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `personel`
--
ALTER TABLE `personel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `randevular`
--
ALTER TABLE `randevular`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `hizmetler`
--
ALTER TABLE `hizmetler`
  ADD CONSTRAINT `fk_hizmet_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategoriler` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `hizmetler_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategoriler` (`id`);

--
-- Tablo kısıtlamaları `randevular`
--
ALTER TABLE `randevular`
  ADD CONSTRAINT `randevular_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`),
  ADD CONSTRAINT `randevular_ibfk_2` FOREIGN KEY (`personel_id`) REFERENCES `personel` (`id`),
  ADD CONSTRAINT `randevular_ibfk_3` FOREIGN KEY (`hizmet_id`) REFERENCES `hizmetler` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
