<?php
// randevularim.php
include 'db_baglanti.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: giris.php");
    exit();
}

$kullanici_id = $_SESSION['user_id'];
$mesaj = "";

// --- PUAN VE YORUM KAYDETME İŞLEMİ ---
if (isset($_POST['puan_kaydet'])) {
    $randevu_id = (int)$_POST['randevu_id'];
    $puan = (int)$_POST['puan'];
    $yorum = htmlspecialchars($_POST['yorum']);

    $stmt = $conn->prepare("UPDATE randevular SET puan = ?, yorum = ? WHERE id = ? AND kullanici_id = ? AND durum = 'tamamlandı'");
    $stmt->bind_param("isii", $puan, $yorum, $randevu_id, $kullanici_id);
    if ($stmt->execute()) {
        $mesaj = "<div class='alert success'>✅ Değerlendirmeniz başarıyla kaydedildi!</div>";
    }
}

// --- RANDEVU İPTAL ETME İŞLEMİ ---
if (isset($_GET['action']) && $_GET['action'] == 'iptal' && isset($_GET['id'])) {
    $randevu_id = (int)$_GET['id'];
    $sql_check = "SELECT CONCAT(randevu_tarihi, ' ', randevu_saati) as dt, durum FROM randevular WHERE id = ? AND kullanici_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $randevu_id, $kullanici_id);
    $stmt_check->execute();
    $res = $stmt_check->get_result()->fetch_assoc();
    
    // Sadece 'onay bekliyor' veya 'onaylandı' durumundaki ve zamanı geçmemiş randevular iptal edilebilir
    if ($res && $res['durum'] != 'iptal edildi' && strtotime($res['dt']) > (time() + 3600)) {
        $stmt_iptal = $conn->prepare("UPDATE randevular SET durum = 'iptal edildi' WHERE id = ? AND kullanici_id = ?");
        $stmt_iptal->bind_param("ii", $randevu_id, $kullanici_id);
        $stmt_iptal->execute();
        $mesaj = "<div class='alert success'>✅ Randevu başarıyla iptal edildi.</div>";
    } else {
        $mesaj = "<div class='alert error'>❌ İptal işlemi yapılamaz (Süre dolmuş veya randevu zaten iptal edilmiş).</div>";
    }
}

// --- RANDEVULARI ÇEK ---
$sql = "SELECT r.*, h.hizmet_adi, p.ad as personel_ad, p.soyad as personel_soyad
        FROM randevular r
        JOIN hizmetler h ON r.hizmet_id = h.id
        JOIN personel p ON r.personel_id = p.id
        WHERE r.kullanici_id = ?
        ORDER BY r.randevu_tarihi DESC, r.randevu_saati DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $kullanici_id);
$stmt->execute();
$randevular = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Randevularım | Salon Yönetim</title>
    <link rel="stylesheet" href="stil.css">
    <style>
        body { background: #0d1117; color: #c9d1d9; font-family: 'Segoe UI', sans-serif; }
        .ana-konteyner { max-width: 900px; margin: 40px auto; background: #161b22; padding: 30px; border-radius: 15px; border: 1px solid #30363d; }
        
        /* Durum Etiketleri */
        .badge { padding: 5px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .onay-bekliyor { background: rgba(241, 196, 15, 0.1); color: #f1c40f; border: 1px solid #f1c40f; }
        .onaylandi { background: rgba(46, 204, 113, 0.1); color: #2ecc71; border: 1px solid #2ecc71; }
        .tamamlandi { background: rgba(52, 152, 219, 0.1); color: #3498db; border: 1px solid #3498db; }
        .iptal-edildi { background: rgba(231, 76, 60, 0.1); color: #e74c3c; border: 1px solid #e74c3c; }
        .reddedildi { background: rgba(149, 165, 166, 0.1); color: #95a5a6; border: 1px solid #95a5a6; }

        /* Tablo Tasarımı */
        table { width: 100%; border-collapse: separate; border-spacing: 0 10px; margin-top: 20px; }
        th { padding: 15px; text-align: left; color: #8b949e; font-weight: 500; border-bottom: 1px solid #30363d; }
        td { padding: 20px 15px; background: #0d1117; border-top: 1px solid #30363d; border-bottom: 1px solid #30363d; }
        td:first-child { border-left: 1px solid #30363d; border-radius: 10px 0 0 10px; }
        td:last-child { border-right: 1px solid #30363d; border-radius: 0 10px 10px 0; }

        /* Puanlama Kartı */
        .puan-kart { background: #1c2128; padding: 15px; border-radius: 10px; border: 1px solid #30363d; }
        .yildiz-rating { color: #f1c40f; font-size: 1.2rem; letter-spacing: 2px; }
        .yorum-onizleme { color: #8b949e; font-style: italic; font-size: 13px; display: block; margin-top: 5px; }
        
        textarea { width: 100%; background: #0d1117; color: white; border: 1px solid #30363d; border-radius: 5px; padding: 8px; margin: 8px 0; resize: none; }
        .btn-puan { background: #238636; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; width: 100%; }
        
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .success { background: rgba(35, 134, 54, 0.15); color: #3fb950; border: 1px solid #238636; }
        .error { background: rgba(248, 81, 73, 0.15); color: #f85149; border: 1px solid #f85149; }
    </style>
</head>
<body>

<div class="ana-konteyner">
    <h2>📅 RANDEVULARIM</h2>
    <?php echo $mesaj; ?>

    <?php if (empty($randevular)): ?>
        <div style="text-align:center; padding: 40px;">
            <p>Henüz bir randevu kaydınız bulunmuyor.</p>
            <a href="randevu_al.php" style="color: #58a6ff;">Hemen randevu alın ✂️</a>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Tarih & Saat</th>
                    <th>Hizmet Bilgisi</th>
                    <th>Durum</th>
                    <th>İşlem / Değerlendirme</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($randevular as $r): 
                    $dt = strtotime($r['randevu_tarihi'] . ' ' . $r['randevu_saati']);
                    $gecmis = $dt < time();
                    $durum_class = str_replace(' ', '-', $r['durum']);
                ?>
                <tr>
                    <td>
                        <span style="color: #f0f6fc; font-weight: bold;"><?php echo date('d.m.Y', $dt); ?></span><br>
                        <span style="color: #58a6ff; font-size: 13px;"><?php echo substr($r['randevu_saati'], 0, 5); ?></span>
                    </td>
                    <td>
                        <span style="color: #f0f6fc;"><?php echo htmlspecialchars($r['hizmet_adi']); ?></span><br>
                        <small style="color: #8b949e;">Usta: <?php echo htmlspecialchars($r['personel_ad']." ".$r['personel_soyad']); ?></small>
                    </td>
                    <td>
                        <span class="badge <?php echo $durum_class; ?>">
                            <?php echo $r['durum']; ?>
                        </span>
                    </td>
                    <td style="width: 250px;">
                        <?php if ($r['durum'] == 'tamamlandı'): ?>
                            <?php if (empty($r['puan'])): ?>
                                <div class="puan-kart">
                                    <form method="post">
                                        <input type="hidden" name="randevu_id" value="<?php echo $r['id']; ?>">
                                        <select name="puan" required style="width:100%; background:#0d1117; color:white; border:1px solid #30363d; padding:5px; border-radius:5px;">
                                            <option value="5">⭐⭐⭐⭐⭐ 5 Puan</option>
                                            <option value="4">⭐⭐⭐⭐ 4 Puan</option>
                                            <option value="3">⭐⭐⭐ 3 Puan</option>
                                            <option value="2">⭐⭐ 2 Puan</option>
                                            <option value="1">⭐ 1 Puan</option>
                                        </select>
                                        <textarea name="yorum" placeholder="Deneyiminizi yazın..." required rows="2"></textarea>
                                        <button type="submit" name="puan_kaydet" class="btn-puan">Gönder</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="yildiz-rating">
                                    <?php echo str_repeat("⭐", (int)$r['puan']); ?>
                                </div>
                                <span class="yorum-onizleme">"<?php echo htmlspecialchars($r['yorum']); ?>"</span>
                            <?php endif; ?>

                        <?php elseif ($r['durum'] == 'onay bekliyor' || $r['durum'] == 'onaylandı'): ?>
                            <?php if ($dt > (time() + 3600)): ?>
                                <a href="?action=iptal&id=<?php echo $r['id']; ?>" 
                                   style="color:#f85149; text-decoration:none; font-size:13px; border:1px solid #f85149; padding:5px 10px; border-radius:5px;"
                                   onclick="return confirm('Randevuyu iptal etmek istediğinizden emin misiniz?');">
                                   🗑️ Randevuyu İptal Et
                                </a>
                            <?php else: ?>
                                <small style="color: #484f58;">İptal süresi doldu</small>
                            <?php endif; ?>

                        <?php else: ?>
                            <span style="color: #484f58; font-size: 13px;">İşlem yapılamaz</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div style="margin-top: 40px; text-align: center;">
        <a href="kullanici_panel.php" style="color: #8b949e; text-decoration: none; border: 1px solid #30363d; padding: 10px 25px; border-radius: 8px;">🏠 Panele Dön</a>
    </div>
</div>

</body>
</html>