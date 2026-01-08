<?php
require_once 'db_baglanti.php';

// Personelleri ve ortalama puanlarını çek
$sql = "SELECT p.*, 
        AVG(r.puan) as ortalama_puan, 
        COUNT(r.puan) as toplam_yorum 
        FROM personel p 
        LEFT JOIN randevular r ON p.id = r.personel_id 
        GROUP BY p.id 
        ORDER BY ortalama_puan DESC";

$personeller = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Personellerimiz | Salon Paneli</title>
    <link rel="stylesheet" href="stil.css">
    <style>
        .personel-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .personel-kart {
            background: rgba(22, 27, 34, 0.8);
            border: 1px solid #30363d;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }
        .yildizlar { color: #f1c40f; font-size: 1.2rem; margin: 10px 0; }
        .mesai-bilgi { font-size: 0.85rem; color: #8b949e; margin-bottom: 15px; }
        .yorumlar-ozet {
            border-top: 1px solid #30363d;
            padding-top: 15px;
            text-align: left;
            max-height: 150px;
            overflow-y: auto;
        }
        .tek-yorum {
            font-size: 0.85rem;
            font-style: italic;
            color: #ccc;
            margin-bottom: 8px;
            border-bottom: 1px solid #1f242c;
            padding-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="ana-konteyner" style="max-width: 1000px;">
        <h1 style="text-align: center;">✂️ Uzman Kadromuz</h1>
        
        <div class="personel-grid">
            <?php while($p = $personeller->fetch_assoc()): ?>
                <div class="personel-kart">
                    <div style="font-size: 40px;">👤</div>
                    <h3 style="margin: 10px 0;"><?php echo htmlspecialchars($p['ad'] . " " . $p['soyad']); ?></h3>
                    
                    <div class="yildizlar">
                        <?php 
                        $puan = round($p['ortalama_puan']);
                        echo $puan > 0 ? str_repeat("⭐", $puan) : "Henüz puanlanmamış";
                        ?>
                        <small style="color: #fff; font-size: 0.8rem;">(<?php echo number_format($p['ortalama_puan'], 1); ?>)</small>
                    </div>

                    <div class="mesai-bilgi">
                        🕒 Çalışma Saatleri: <?php echo substr($p['mesai_baslangic'], 0, 5); ?> - <?php echo substr($p['mesai_bitis'], 0, 5); ?>
                    </div>

                    <div class="yorumlar-ozet">
                        <h4 style="font-size: 0.9rem; margin-bottom: 10px; color: #58a6ff;">Müşteri Yorumları:</h4>
                        <?php 
                        // Bu personele ait son 3 yorumu çek
                        $p_id = $p['id'];
                        $y_sorgu = "SELECT yorum, puan FROM randevular WHERE personel_id = $p_id AND yorum IS NOT NULL AND yorum != '' ORDER BY id DESC LIMIT 3";
                        $yorumlar = $conn->query($y_sorgu);
                        
                        if($yorumlar->num_rows > 0):
                            while($y = $yorumlar->fetch_assoc()): ?>
                                <div class="tek-yorum">
                                    "<?php echo htmlspecialchars($y['yorum']); ?>"
                                </div>
                            <?php endwhile;
                        else:
                            echo "<small style='color:#666;'>Henüz yorum yapılmamış.</small>";
                        endif;
                        ?>
                    </div>
                    
                    <a href="randevu_al.php" class="admin-kart" style="display: block; margin-top: 15px; padding: 10px; font-size: 0.8rem; background: #8b0000; text-decoration: none;">Randevu Al</a>
                </div>
            <?php endwhile; ?>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="kullanici_panel.php" style="color: #58a6ff; text-decoration: none;">🏠 Panele Geri Dön</a>
        </div>
    </div>
</body>
</html>