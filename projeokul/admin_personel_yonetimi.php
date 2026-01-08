<?php
require_once 'db_baglanti.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: giris.php"); exit();
}

// Personel Silme
if (isset($_GET['sil_id'])) {
    $sil_id = (int)$_GET['sil_id'];
    $conn->query("DELETE FROM personel WHERE id = $sil_id");
    header("Location: admin_personel_yonetimi.php"); exit();
}

$personeller = $conn->query("SELECT * FROM personel ORDER BY ad ASC");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Personel Yönetimi</title>
    <link rel="stylesheet" href="stil.css">
    <style>
        .p-kart { background: rgba(255,255,255,0.05); padding: 20px; border-radius: 10px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; width: 100%; max-width: 800px; }
        .saat-etiket { background: #333; padding: 4px 8px; border-radius: 5px; font-size: 13px; color: #58a6ff; }
        .islem-linkler a { text-decoration: none; margin-left: 15px; font-size: 14px; }
        .duzenle { color: #f1c40f; }
        .sil { color: #e74c3c; }
    </style>
</head>
<body>
    <div class="ana-konteyner">
        <h2>👥 PERSONEL YÖNETİMİ</h2>
        
        <a href="admin_personel_ekle.php" class="admin-kart" style="padding:10px 20px; margin-bottom:20px; text-decoration:none;">➕ Yeni Personel Ekle</a>

        <?php while($p = $personeller->fetch_assoc()): ?>
            <div class="p-kart">
                <div>
                    <strong style="font-size:18px;"><?php echo $p['ad'] . " " . $p['soyad']; ?></strong><br>
                    <small style="color:#888;">Mesai: </small>
                    <span class="saat-etiket">
                        <?php echo substr($p['mesai_baslangic'], 0, 5); ?> - <?php echo substr($p['mesai_bitis'], 0, 5); ?>
                    </span>
                </div>
                <div class="islem-linkler">
                    <a href="admin_personel_duzenle.php?id=<?php echo $p['id']; ?>" class="duzenle">⚙️ Düzenle / Saatler</a>
                    <a href="?sil_id=<?php echo $p['id']; ?>" class="sil" onclick="return confirm('Silmek istediğine emin misin?')">🗑️ Sil</a>
                </div>
            </div>
        <?php endwhile; ?>

        <br><a href="admin_panel.php" style="color:#fff;">⬅️ Panele Dön</a>
    </div>
</body>
</html>