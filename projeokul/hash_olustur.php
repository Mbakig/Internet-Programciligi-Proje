<?php
// hash_olustur.php

// GİRİŞ YAPMAK İSTEDİĞİNİZ ŞİFRE: admin123
$sifre = 'admin123'; 

// Yeni, güvenli hash oluştur
$hash = password_hash($sifre, PASSWORD_DEFAULT);

echo "<h2>Yeni Yönetici Hash'i:</h2>";
echo "<textarea rows='3' cols='60' readonly>" . $hash . "</textarea>";
echo "<p>Lütfen yukarıdaki tüm kodu kopyalayıp (seçili alanın içindeki tırnaklar olmadan) veritabanına yapıştırın.</p>";
?>