<?php
// admin_randevu_yonetimi.php
require_once 'db_baglanti.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: giris.php");
    exit();
}

$mesaj = "";

// --- 1. DURUM GÜNCELLEME (ONAYLA/REDDET/TAMAMLA) ---
if (isset($_GET['islem']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $islem = $_GET['islem'];
    
    // İşlem tipine göre durum belirle
    if ($islem == 'onayla') $yeni_durum = 'onaylandı';
    elseif ($islem == 'reddet') $yeni_durum = 'reddedildi';
    elseif ($islem == 'tamamla') $yeni_durum = 'tamamlandı';
    
    if (isset($yeni_durum)) {
        $stmt = $conn->prepare("UPDATE randevular SET durum = ? WHERE id = ?");
        $stmt->bind_param("si", $yeni_durum, $id);
        if ($stmt->execute()) {
            $mesaj = "<div class='alert success'>✅ Randevu durumu '$yeni_durum' olarak güncellendi.</div>";
        }
        $stmt->close();
    }
}

// --- 2. RANDEVU SİLME İŞLEMİ ---
if (isset($_GET['sil_id'])) {
    $sil_id = (int)$_GET['sil_id'];
    $stmt = $conn->prepare("DELETE FROM randevular WHERE id = ?");
    $stmt->bind_param("i", $sil_id);
    if ($stmt->execute()) {
        $mesaj = "<div class='alert error'>🗑️ Randevu sistemden kalıcı olarak silindi.</div>";
    }
    $stmt->close();
}

// --- 3. TÜM RANDEVULARI ÇEK ---
$sorgu = "
    SELECT 
        r.*,
        k.ad AS musterici_ad, k.soyad AS musterici_soyad,
        p.ad AS personel_ad, p.soyad AS personel_soyad,
        h.hizmet_adi
    FROM randevular r
    LEFT JOIN kullanicilar k ON r.kullanici_id = k.id
    LEFT JOIN personel p ON r.personel_id = p.id
    LEFT JOIN hizmetler h ON r.hizmet_id = h.id
    ORDER BY r.randevu_tarihi DESC, r.randevu_saati DESC";

$randevular = $conn->query($sorgu)->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Randevu Yönetimi | Admin Paneli</title>
    <link rel="stylesheet" href="stil.css">
    <style>
        body { background: #0d1117; color: #c9d1d9; font-family: sans-serif; padding: 40px 20px; }
        .ana-konteyner { max-width: 1100px; margin: auto; background: #161b22; padding: 30px; border-radius: 15px; border: 1px solid #30363d; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #30363d; }
        
        /* Durum Renkleri */
        .durum-onaybekliyor { color: #f1c40f; }
        .durum-onaylandi { color: #2ecc71; }
        .durum-reddedildi { color: #e74c3c; }
        .durum-tamamlandi { color: #3498db; font-weight: bold; }
        .durum-iptaledildi { color: #8b949e; }

        .btn-grup { display: flex; gap: 8px; flex-wrap: wrap; }
        .islem-btn { padding: 6px 10px; border-radius: 6px; text-decoration: none; font-size: 11px; color: white; font-weight: bold; transition: 0.2s; }
        .btn-onay { background: #238636; }
        .btn-red { background: #da3633; }
        .btn-tamam { background: #1f6feb; }
        .btn-sil { background: #30363d; border: 1px solid #8b949e; }
        .islem-btn:hover { opacity: 0.8; transform: translateY(-1px); }

        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .success { background: rgba(35, 134, 54, 0.1); color: #3fb950; border: 1px solid #238636; }
        .error { background: rgba(248, 81, 73, 0.1); color: #f85149; border: 1px solid #f85149; }
    </style>
</head>
<body>

    <div class="ana-konteyner">
        <h2>📅 RANDEVU YÖNETİMİ</h2>
        
        <?php echo $mesaj; ?>

        <?php if (empty($randevular)): ?>
            <p style="text-align:center;">Henüz randevu kaydı yok.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Tarih / Saat</th>
                        <th>Müşteri</th>
                        <th>Hizmet / Personel</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($randevular as $r): 
                        $durum_ham = mb_strtolower($r['durum']);
                        $durum_class = "durum-" . str_replace(' ', '', $durum_ham);
                    ?>
                        <tr>
                            <td>
                                <b><?php echo date("d.m.Y", strtotime($r['randevu_tarihi'])); ?></b><br>
                                <small style="color:#58a6ff;"><?php echo substr($r['randevu_saati'], 0, 5); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars(($r['musterici_ad'] ?? 'Bilinmiyor') . " " . ($r['musterici_soyad'] ?? '')); ?></td>
                            <td>
                                <b><?php echo htmlspecialchars($r['hizmet_adi'] ?? '---'); ?></b><br>
                                <small>Personel: <?php echo htmlspecialchars($r['personel_ad'] ?? '---'); ?></small>
                            </td>
                            <td>
                                <span class="<?php echo $durum_class; ?>">
                                    ● <?php echo strtoupper($r['durum']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-grup">
                                    <?php if ($durum_ham == 'onay bekliyor'): ?>
                                        <a href="?islem=onayla&id=<?php echo $r['id']; ?>" class="islem-btn btn-onay">ONAYLA</a>
                                        <a href="?islem=reddet&id=<?php echo $r['id']; ?>" class="islem-btn btn-red" onclick="return confirm('Reddetmek istediğine emin misin?')">REDDET</a>
                                    
                                    <?php elseif ($durum_ham == 'onaylandı'): ?>
                                        <a href="?islem=tamamla&id=<?php echo $r['id']; ?>" class="islem-btn btn-tamam">✅ TAMAMLANDI OLARAK İŞARETLE</a>
                                    
                                    <?php endif; ?>
                                    
                                    <a href="?sil_id=<?php echo $r['id']; ?>" class="islem-btn btn-sil" onclick="return confirm('Kalıcı olarak silmek istediğine emin misin?')">🗑️ SİL</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div style="text-align:center; margin-top:30px;">
            <a href="admin_panel.php" style="color:#58a6ff; text-decoration:none;">⬅️ Panele Dön</a>
        </div>
    </div>

</body>
</html>