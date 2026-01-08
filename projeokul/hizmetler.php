<?php
// hizmetler.php
require_once 'db_baglanti.php';

// Tüm hizmetleri kategorileriyle birlikte çekme sorgusu
$sql = "SELECT h.hizmet_adi, h.aciklama, h.fiyat, h.sure, k.kategori_adi 
        FROM hizmetler h
        JOIN kategoriler k ON h.kategori_id = k.id
        ORDER BY k.kategori_adi, h.hizmet_adi";

$result = $conn->query($sql);

if ($result === FALSE) {
    die("<div style='color:red; background:white; padding:20px;'>SQL Sorgu Hatası: " . $conn->error . "</div>");
}

$hizmetler_raw = $result->fetch_all(MYSQLI_ASSOC);

// Hizmetleri kategori bazında gruplama
$hizmetler_gruplanmis = [];
foreach ($hizmetler_raw as $hizmet) {
    $kategori = $hizmet['kategori_adi'];
    if (!isset($hizmetler_gruplanmis[$kategori])) {
        $hizmetler_gruplanmis[$kategori] = [];
    }
    $hizmetler_gruplanmis[$kategori][] = $hizmet;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hizmetlerimiz | Salon Paneli</title>
    <link rel="stylesheet" href="stil.css">
    <style>
        .kategori-section { margin-bottom: 40px; }
        
        .kategori-baslik { 
            color: #58a6ff; 
            border-left: 4px solid #8b0000; 
            padding-left: 15px; 
            margin-bottom: 20px; 
            font-size: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .hizmet-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .hizmet-kart {
            background: rgba(22, 27, 34, 0.8);
            border: 1px solid #30363d;
            border-radius: 12px;
            padding: 20px;
            transition: transform 0.3s, border-color 0.3s;
            position: relative;
            overflow: hidden;
        }

        .hizmet-kart:hover {
            transform: translateY(-5px);
            border-color: #8b0000;
            background: rgba(22, 27, 34, 1);
        }

        .hizmet-adi {
            color: #f0f6fc;
            font-size: 1.2rem;
            margin-bottom: 10px;
            display: block;
        }

        .hizmet-aciklama {
            color: #8b949e;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 15px;
            height: 45px; /* Sınırlı yükseklik */
            overflow: hidden;
        }

        .hizmet-alt {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #30363d;
            padding-top: 15px;
            margin-top: 10px;
        }

        .hizmet-sure {
            color: #8b949e;
            font-size: 0.85rem;
        }

        .hizmet-fiyat {
            color: #2ecc71;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .islem-linkleri {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="ana-konteyner" style="max-width: 1000px;">
        <h1 style="text-align: center; color: #f0f6fc; margin-bottom: 40px;">✂️ Hizmet Katalogumuz</h1>
        
        <?php if (empty($hizmetler_gruplanmis)): ?>
            <div class="admin-kart" style="text-align: center;">
                <p>Üzgünüz, şu anda listelenecek hizmet bulunmamaktadır.</p>
            </div>
        <?php else: ?>
            
            <?php foreach ($hizmetler_gruplanmis as $kategori_adi => $hizmetler): ?>
                <div class="kategori-section">
                    <h2 class="kategori-baslik"><?php echo htmlspecialchars($kategori_adi); ?></h2>
                    
                    <div class="hizmet-grid">
                        <?php foreach ($hizmetler as $hizmet): ?>
                            <div class="hizmet-kart">
                                <strong class="hizmet-adi"><?php echo htmlspecialchars($hizmet['hizmet_adi']); ?></strong>
                                <p class="hizmet-aciklama"><?php echo htmlspecialchars($hizmet['aciklama']); ?></p>
                                
                                <div class="hizmet-alt">
                                    <span class="hizmet-sure">⏱ <?php echo htmlspecialchars($hizmet['sure']); ?> dk</span>
                                    <span class="hizmet-fiyat"><?php echo number_format($hizmet['fiyat'], 2); ?> TL</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
        <?php endif; ?>

        <div class="islem-linkleri">
            <a href="kullanici_panel.php" class="admin-kart" style="padding: 12px 25px; text-decoration: none; font-size: 0.9rem;">🏠 Panele Geri Dön</a>
            <a href="randevu_al.php" class="admin-kart" style="padding: 12px 25px; text-decoration: none; font-size: 0.9rem; background: #8b0000; border-color: #8b0000;">📅 Hemen Randevu Al</a>
        </div>
    </div>
</body>
</html>