<?php
require_once 'db_baglanti.php';

// Karakter seti güvenliği
$conn->set_charset("utf8mb4");

// Linkten gelen ID'yi güvenli al
$p_id = $_GET['id'] ?? $_GET['personel_id'] ?? null;
$p_id = (int)$p_id;

if (!$p_id) {
    die("<div style='background:red; color:white; padding:20px;'>❌ HATA: Personel ID bulunamadı!</div>");
}

$mesaj = "";
$gun_map = ["Pazartesi"=>1, "Salı"=>2, "Çarşamba"=>3, "Perşembe"=>4, "Cuma"=>5, "Cumartesi"=>6, "Pazar"=>0];

// --- KAYDETME İŞLEMİ ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mesai_kaydet'])) {
    $gunler = $_POST['gunler'] ?? [];
    $baslangic = $_POST['baslangic']; 
    $bitis = $_POST['bitis'];

   
    $conn->query("DELETE FROM calisma_saatleri WHERE personel_id = $p_id");

    if (!empty($gunler)) {
        $stmt = $conn->prepare("INSERT INTO calisma_saatleri (personel_id, gun, gun_no, baslangic_saati, bitis_saati) VALUES (?, ?, ?, ?, ?)");
        
        $hata_olustu = false;
        foreach ($gunler as $g_no) {
            $g_adi = array_search($g_no, $gun_map);
            $stmt->bind_param("isiss", $p_id, $g_adi, $g_no, $baslangic, $bitis);
            
            if (!$stmt->execute()) {
                $mesaj = "<div style='background:red; color:white; padding:10px;'>❌ Kayıt Hatası: " . $stmt->error . "</div>";
                $hata_olustu = true;
                break;
            }
        }
        
        if (!$hata_olustu) {
            $mesaj = "<div style='background:#238636; color:white; padding:15px; border-radius:10px; text-align:center;'>✅ Çalışma saatleri başarıyla kaydedildi!</div>";
        }
    } else {
        $mesaj = "<div style='background:#f85149; color:white; padding:10px; border-radius:10px; text-align:center;'>⚠️ Lütfen en az bir gün seçin.</div>";
    }
}

// Personel bilgilerini ve mevcut kayıtları çek
$p_bilgi = $conn->query("SELECT ad, soyad FROM personel WHERE id = $p_id")->fetch_assoc();
$mevcutlar = [];
$res = $conn->query("SELECT gun_no FROM calisma_saatleri WHERE personel_id = $p_id");
if($res) {
    while($row = $res->fetch_assoc()) { $mevcutlar[] = $row['gun_no']; }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Mesai Ayarları | <?php echo htmlspecialchars($p_bilgi['ad'] ?? 'Personel'); ?></title>
    <style>
        body { background: #0d1117; color: #c9d1d9; font-family: 'Segoe UI', sans-serif; }
        .konteynir { max-width: 450px; margin: 40px auto; background: #161b22; padding: 30px; border: 1px solid #30363d; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        h2 { text-align: center; color: #f0f6fc; margin-bottom: 25px; }
        .gun-item { display: flex; align-items: center; gap: 12px; padding: 12px; border-bottom: 1px solid #21262d; cursor: pointer; transition: 0.2s; }
        .gun-item:hover { background: #21262d; }
        input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; }
        .saat-grup { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 25px; }
        label { font-size: 13px; color: #8b949e; display: block; margin-bottom: 5px; }
        input[type="time"] { width: 100%; padding: 10px; background: #0d1117; color: white; border: 1px solid #30363d; border-radius: 8px; box-sizing: border-box; }
        .btn-kaydet { width: 100%; padding: 15px; background: #238636; color: white; border: none; font-weight: bold; border-radius: 10px; cursor: pointer; font-size: 16px; margin-top: 25px; transition: 0.3s; }
        .btn-kaydet:hover { background: #2ea043; transform: translateY(-2px); }
        .geri-link { display: block; text-align: center; margin-top: 20px; color: #58a6ff; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
    <div class="konteynir">
        <h2><?php echo htmlspecialchars(($p_bilgi['ad'] ?? '') . " " . ($p_bilgi['soyad'] ?? '')); ?></h2>
        <?php echo $mesaj; ?>
        
        <form method="POST">
            <?php foreach($gun_map as $isim => $no): ?>
                <label class="gun-item">
                    <input type="checkbox" name="gunler[]" value="<?php echo $no; ?>" <?php echo in_array($no, $mevcutlar) ? 'checked' : ''; ?>>
                    <?php echo $isim; ?>
                </label>
            <?php endforeach; ?>
            
            <div class="saat-grup">
                <div>
                    <label>Mesai Başlangıç</label>
                    <input type="time" name="baslangic" value="09:00" required>
                </div>
                <div>
                    <label>Mesai Bitiş</label>
                    <input type="time" name="bitis" value="19:00" required>
                </div>
            </div>

            <button type="submit" name="mesai_kaydet" class="btn-kaydet">💾 AYARLARI KAYDET</button>
        </form>
        <a href="admin_personel_yonetimi.php" class="geri-link">⬅️ Personel Listesine Dön</a>
    </div>
</body>
</html>