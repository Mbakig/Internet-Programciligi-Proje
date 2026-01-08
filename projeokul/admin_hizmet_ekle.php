
<?php
// admin_hizmet_ekle.php
include 'db_baglanti.php';

// Oturum ve Admin kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: giris.php");
    exit();
}

$mesaj = "";

// Kategorileri Form İçin Çekme
$kategoriler_query = $conn->query("SELECT id, kategori_adi FROM kategoriler ORDER BY kategori_adi");
$kategoriler = $kategoriler_query->fetch_all(MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hizmet_adi = $_POST['hizmet_adi'];
    $aciklama = $_POST['aciklama'];
    $fiyat = $_POST['fiyat'];
    $sure = $_POST['sure']; // Dakika
    $kategori_id = $_POST['kategori_id'];

    // Basit doğrulama
    if (empty($hizmet_adi) || empty($fiyat) || empty($sure) || empty($kategori_id)) {
        $mesaj = "<p style='color:red;'>Lütfen tüm zorunlu alanları (Ad, Fiyat, Süre, Kategori) doldurun.</p>";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO hizmetler (hizmet_adi, aciklama, fiyat, sure, kategori_id) VALUES (?, ?, ?, ?, ?)");
            // 'ssdsi' => string, string, double, string, integer
            $stmt->bind_param("ssisi", $hizmet_adi, $aciklama, $fiyat, $sure, $kategori_id);
            
            if ($stmt->execute()) {
                $mesaj = "<p style='color:green;'>'**" . htmlspecialchars($hizmet_adi) . "**' hizmeti başarıyla eklendi.</p>";
            } else {
                $mesaj = "<p style='color:red;'>Hata: Hizmet eklenirken bir sorun oluştu.</p>";
            }
            $stmt->close();
        } catch (Exception $e) {
            $mesaj = "<p style='color:red;'>Bir hata oluştu: " . $e->getMessage() . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="stylesheet" href="stil.css">
<meta charset="UTF-8">
    <title>Hizmet Ekle</title>
</head>
<body>
    <h2>Yeni Hizmet Ekle</h2>
    <?php echo $mesaj; ?>
    
    <form method="post" action="admin_hizmet_ekle.php">
        <label for="hizmet_adi">Hizmet Adı:</label><br>
        <input type="text" id="hizmet_adi" name="hizmet_adi" required><br><br>

        <label for="aciklama">Açıklama:</label><br>
        <textarea id="aciklama" name="aciklama" rows="3"></textarea><br><br>

        <label for="fiyat">Fiyat (TL):</label><br>
        <input type="number" id="fiyat" name="fiyat" step="0.01" min="0" required><br><br>

        <label for="sure">Süre (Dakika):</label><br>
        <input type="number" id="sure" name="sure" min="15" required><br><br>

        <label for="kategori_id">Kategori:</label><br>
        <select name="kategori_id" id="kategori_id" required>
            <option value="">-- Kategori Seçiniz --</option>
            <?php foreach ($kategoriler as $kategori): ?>
                <option value="<?php echo $kategori['id']; ?>">
                    <?php echo htmlspecialchars($kategori['kategori_adi']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Hizmet Ekle</button>
    </form>
    
    <br><a href="admin_hizmet_yonetimi.php">Hizmet Yönetimine Geri Dön</a> 
    
</body>
</html>