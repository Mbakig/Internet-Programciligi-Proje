<?php
require_once 'db_baglanti.php';

$id = (int)$_GET['id'];
$mesaj = "";

// Günler ve Numaraları (Randevu sistemiyle tam uyum için)
$gun_map = [
    "Pazartesi" => 1, "Salı" => 2, "Çarşamba" => 3, 
    "Perşembe" => 4, "Cuma" => 5, "Cumartesi" => 6, "Pazar" => 0
];

if ($_POST) {
    $ad = $_POST['ad'];
    $soyad = $_POST['soyad'];
    $baslangic = $_POST['mesai_baslangic'];
    $bitis = $_POST['mesai_bitis'];
    $secilen_gunler = isset($_POST['gunler']) ? $_POST['gunler'] : [];
    
    // 1. Personel Tablosunu Güncelle
    $gunler_str = implode(",", $secilen_gunler);
    $stmt = $conn->prepare("UPDATE personel SET ad=?, soyad=?, mesai_baslangic=?, mesai_bitis=?, calisma_gunleri=? WHERE id=?");
    $stmt->bind_param("sssssi", $ad, $soyad, $baslangic, $bitis, $gunler_str, $id);
    
    if($stmt->execute()) {
        // 2. KRİTİK ADIM: calisma_saatleri tablosunu randevu sistemi için güncelle
        // Önce bu personelin bu tablodaki eski kayıtlarını temizle
        $conn->query("DELETE FROM calisma_saatleri WHERE personel_id = $id");

        if (!empty($secilen_gunler)) {
            $ins = $conn->prepare("INSERT INTO calisma_saatleri (personel_id, gun, gun_no, baslangic_saati, bitis_saati) VALUES (?, ?, ?, ?, ?)");
            foreach ($secilen_gunler as $gun_adi) {
                $g_no = $gun_map[$gun_adi];
                $ins->bind_param("isiss", $id, $gun_adi, $g_no, $baslangic, $bitis);
                $ins->execute();
            }
        }
        $mesaj = "<div class='basari-mesaj'>✅ Personel bilgileri ve randevu takvimi güncellendi!</div>";
    }
}

$personel = $conn->query("SELECT * FROM personel WHERE id = $id")->fetch_assoc();
$secili_gunler = explode(",", $personel['calisma_gunleri']);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Mesai Düzenle</title>
    <link rel="stylesheet" href="stil.css">
    <style>
        body { background: #0d1117; color: #c9d1d9; }
        .ana-konteyner { max-width: 500px; margin: 40px auto; padding: 20px; }
        .admin-kart { background: #161b22; border: 1px solid #30363d; border-radius: 12px; padding: 25px; }
        .gun-tablosu { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin: 10px 0; background: rgba(0,0,0,0.3); padding: 15px; border-radius: 8px; }
        .gun-oge { display: flex; align-items: center; gap: 8px; font-size: 14px; color: #c9d1d9; cursor: pointer; }
        .basari-mesaj { background: #238636; color: white; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 20px; }
        input[type="text"], input[type="time"] { background: #0d1117; color: white; border: 1px solid #30363d; padding: 10px; border-radius: 6px; }
        .form-grup { margin-bottom: 20px; display: flex; flex-direction: column; gap: 8px; }
        label { color: #8b949e; font-size: 13px; }
    </style>
</head>
<body>
    <div class="ana-konteyner">
        <h2>⚙️ PERSONEL VE MESAİ DÜZENLE</h2>
        <?php echo $mesaj; ?>
        
        <form method="POST" class="admin-kart">
            <div class="form-grup">
                <label>Personel Adı ve Soyadı</label>
                <input type="text" name="ad" value="<?php echo htmlspecialchars($personel['ad']); ?>" required>
                <input type="text" name="soyad" value="<?php echo htmlspecialchars($personel['soyad']); ?>" required>
            </div>

            <div class="form-grup">
                <label>Mesai Saatleri (Başlangıç - Bitiş)</label>
                <div style="display:flex; gap:10px;">
                    <input type="time" name="mesai_baslangic" value="<?php echo $personel['mesai_baslangic']; ?>" style="flex:1;">
                    <input type="time" name="mesai_bitis" value="<?php echo $personel['mesai_bitis']; ?>" style="flex:1;">
                </div>
            </div>

            <div class="form-grup">
                <label>Çalışma Günleri</label>
                <div class="gun-tablosu">
                    <?php foreach($gun_map as $gun => $no): ?>
                        <label class="gun-oge">
                            <input type="checkbox" name="gunler[]" value="<?php echo $gun; ?>" 
                            <?php echo in_array($gun, $secili_gunler) ? 'checked' : ''; ?>>
                            <?php echo $gun; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <button type="submit" class="islem-btn" style="background:#238636; color:white; padding:15px; border:none; border-radius:8px; cursor:pointer; width:100%; font-weight:bold;">DEĞİŞİKLİKLERİ KAYDET</button>
            
            <div style="display:flex; justify-content: space-between; margin-top:20px;">
                <a href="admin_personel_yonetimi.php" style="color:#58a6ff; text-decoration:none; font-size:14px;">🔙 Listeye Dön</a>
                <a href="admin_panel.php" style="color:#8b949e; text-decoration:none; font-size:14px;">🏠 Panel</a>
            </div>
        </form>
    </div>
</body>
</html>