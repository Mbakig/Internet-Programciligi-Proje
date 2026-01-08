<?php
// kullanici_panel.php
include 'db_baglanti.php';

// Oturum kontrolü - Veritabanındaki rol ismine göre 'kullanici' olarak kontrol eder
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'kullanici') {
    header("Location: giris.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kullanıcı Paneli | Hoş Geldiniz</title>
    <link rel="stylesheet" href="stil.css">
    <style>
        /* İsme özel bordo vurgu */
        .kullanici-adi {
            color: #8b0000;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <h2>HOŞ GELDİNİZ, <span class="kullanici-adi"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>!</h2>
    <p style="text-align: center; color: #888;">Yapmak istediğiniz işlemi aşağıdan seçebilirsiniz.</p>

    <div class="admin-konteyner">
        <a href="randevu_al.php" class="admin-kart">
            <span style="font-size: 45px; margin-bottom: 15px;">✂️</span>
            <span>Yeni Randevu Al</span>
        </a>

        <a href="randevularim.php" class="admin-kart">
            <span style="font-size: 45px; margin-bottom: 15px;">📅</span>
            <span>Randevularım</span>
        </a>

        <a href="hizmetler.php" class="admin-kart">
            <span style="font-size: 45px; margin-bottom: 15px;">💎</span>
            <span>Hizmetlerimizi İncele</span>
        </a>

        <a href="profil_ayarlari.php" class="admin-kart">
            <span style="font-size: 45px; margin-bottom: 15px;">👤</span>
            <span>Profil Ayarlarım</span>
        </a>

        <a href="personeller.php" class="admin-kart">
    <div style="font-size: 30px; margin-bottom: 10px;">👥</div>
    <span>Personellerimiz</span>
    <small style="display:block; color:#888; font-size:11px; margin-top:5px;">Uzman Kadro ve Yorumlar</small>
</a>

        <a href="cikis.php" class="admin-kart cikis-kart">
            <span style="font-size: 45px; margin-bottom: 15px;">❌</span>
            <span>Güvenli Çıkış</span>
        </a>
    </div>

</body>
</html>