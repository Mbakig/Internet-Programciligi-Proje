<?php
require_once 'db_baglanti.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: giris.php");
    exit();
}

// 1. Personel Bazlı Ortalama Puanları Çek
$analiz_sorgu = "SELECT p.ad, p.soyad, 
                 AVG(r.puan) as ortalama_puan, 
                 COUNT(r.yorum) as toplam_yorum 
                 FROM personel p 
                 LEFT JOIN randevular r ON p.id = r.personel_id 
                 WHERE r.puan IS NOT NULL 
                 GROUP BY p.id";
$analiz_sonuc = $conn->query($analiz_sorgu);

// 2. Tüm Yorumları Çek
$yorum_sorgu = "SELECT r.puan, r.yorum, r.randevu_tarihi, 
                p.ad as p_ad, u.ad as u_ad, h.hizmet_adi 
                FROM randevular r 
                JOIN personel p ON r.personel_id = p.id 
                JOIN kullanicilar u ON r.kullanici_id = u.id 
                JOIN hizmetler h ON r.hizmet_id = h.id 
                WHERE r.yorum IS NOT NULL 
                ORDER BY r.id DESC";
$yorumlar = $conn->query($yorum_sorgu);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Personel Analizi | Admin</title>
    <link rel="stylesheet" href="stil.css">
    <style>
        .analiz-kartlar { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; width: 100%; max-width: 1100px; margin-bottom: 40px; }
        .puan-yildiz { color: #f1c40f; font-size: 20px; }
        .yorum-kutusu { background: rgba(255,255,255,0.05); border-left: 4px solid #8b0000; padding: 15px; margin-bottom: 15px; border-radius: 0 10px 10px 0; width: 100%; }
        .yorum-meta { font-size: 12px; color: #888; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="ana-konteyner" style="max-width: 1100px;">
        <h2>🌟 PERSONEL PERFORMANS ANALİZİ</h2>

        <div class="analiz-kartlar">
            <?php while($row = $analiz_sonuc->fetch_assoc()): ?>
                <div class="admin-kart" style="text-align: center;">
                    <h3><?php echo $row['ad'] . " " . $row['soyad']; ?></h3>
                    <div class="puan-yildiz">
                        <?php echo str_repeat("⭐", round($row['ortalama_puan'])); ?>
                        <span style="font-size: 14px; color: #fff;">(<?php echo number_format($row['ortalama_puan'], 1); ?>)</span>
                    </div>
                    <p style="font-size: 12px; color: #58a6ff;"><?php echo $row['toplam_yorum']; ?> Değerlendirme</p>
                </div>
            <?php endwhile; ?>
        </div>

        <hr style="width: 100%; border: 0; border-top: 1px solid #333; margin-bottom: 30px;">

        <h2 style="margin-bottom: 20px;">💬 SON MÜŞTERİ YORUMLARI</h2>
        <div style="width: 100%;">
            <?php while($y = $yorumlar->fetch_assoc()): ?>
                <div class="yorum-kutusu">
                    <div style="display: flex; justify-content: space-between;">
                        <strong style="color: #58a6ff;"><?php echo $y['u_ad']; ?></strong>
                        <span class="puan-yildiz"><?php echo str_repeat("⭐", $y['puan']); ?></span>
                    </div>
                    <p style="margin: 10px 0; font-style: italic;">"<?php echo htmlspecialchars($y['yorum']); ?>"</p>
                    <div class="yorum-meta">
                        📍 <b><?php echo $y['hizmet_adi']; ?></b> hizmeti için 
                        👤 <b><?php echo $y['p_ad']; ?></b> personeline yapıldı. 
                        📅 <?php echo date("d.m.Y", strtotime($y['randevu_tarihi'])); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div style="margin-top: 30px;">
            <a href="admin_panel.php" class="admin-kart" style="padding: 10px 20px;">🏠 Admin Panele Dön</a>
        </div>
    </div>
</body>
</html>