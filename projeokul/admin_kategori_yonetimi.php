<?php
// admin_kategori_yonetimi.php
require_once 'db_baglanti.php';

// Oturum ve Admin kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: giris.php");
    exit();
}

$mesaj = "";

// --- 1. KATEGORİ EKLEME İŞLEMİ ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['kategori_ekle'])) {
    $kategori_adi = trim($_POST['kategori_adi']);
    if (!empty($kategori_adi)) {
        $stmt = $conn->prepare("INSERT INTO kategoriler (kategori_adi) VALUES (?)");
        $stmt->bind_param("s", $kategori_adi);
        if ($stmt->execute()) {
            $mesaj = "<p style='color:green;'>Kategori başarıyla eklendi.</p>";
        } else {
            $mesaj = "<p style='color:red;'>Hata: Bu kategori zaten mevcut olabilir.</p>";
        }
        $stmt->close();
    }
}

// --- 2. KATEGORİ SİLME İŞLEMİ ---
if (isset($_GET['sil_id'])) {
    $sil_id = (int)$_GET['sil_id'];
    
    // ÖNEMLİ: Bu kategoriye bağlı hizmetler varsa silme hatası almamak için kontrol
    try {
        $stmt = $conn->prepare("DELETE FROM kategoriler WHERE id = ?");
        $stmt->bind_param("i", $sil_id);
        if ($stmt->execute()) {
            $mesaj = "<p style='color:green;'>Kategori başarıyla silindi.</p>";
        }
    } catch (Exception $e) {
        $mesaj = "<p style='color:red;'>Hata: Bu kategoriye bağlı hizmetler olduğu için silemezsiniz! Önce hizmetleri silin.</p>";
    }
}

// --- 3. MEVCUT KATEGORİLERİ ÇEK ---
$kategoriler = $conn->query("SELECT * FROM kategoriler ORDER BY kategori_adi ASC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kategori Yönetimi</title>
    <link rel="stylesheet" href="stil.css">
</head>
<body>
    <h2>KATEGORİ YÖNETİMİ</h2>
    <?php echo $mesaj; ?>

    <div style="width: 100%; max-width: 600px;">
        <form method="post">
            <label for="kategori_adi">Yeni Kategori Adı:</label>
            <input type="text" id="kategori_adi" name="kategori_adi" placeholder="Örn: Cilt Bakımı" required>
            <button type="submit" name="kategori_ekle">➕ Kategori Ekle</button>
        </form>
    </div>

    <hr style="margin: 40px 0; border: 1px solid #30363d; width: 100%; max-width: 800px;">

    <h3>Mevcut Kategoriler</h3>
    <?php if (empty($kategoriler)): ?>
        <p>Henüz bir kategori eklenmemiş.</p>
    <?php else: ?>
        <table style="max-width: 600px;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kategori Adı</th>
                    <th style="text-align: center;">İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kategoriler as $kat): ?>
                    <tr>
                        <td><?php echo $kat['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($kat['kategori_adi']); ?></strong></td>
                        <td style="text-align: center;">
                            <a href="admin_kategori_yonetimi.php?sil_id=<?php echo $kat['id']; ?>" 
                               class="islem-linki sil-btn" 
                               onclick="return confirm('Bu kategoriyi silmek istediğine emin misin?')">🗑️ Sil</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div style="margin-top: 30px;">
        <a href="admin_panel.php" class="admin-kart" style="padding: 15px 30px;">
            <span>⬅️ Ana Panele Dön</span>
        </a>
    </div>
</body>
</html>