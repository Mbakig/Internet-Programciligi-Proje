<?php
require_once 'db_baglanti.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: giris.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$mesaj = "";

// GÜNCELLEME İŞLEMİ
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad = trim($_POST['ad']);
    $soyad = trim($_POST['soyad']);
    $email = trim($_POST['email']);
    $sifre = trim($_POST['sifre']);

    if (!empty($sifre)) {
        // Şifre değişecekse
        $yeni_sifre = password_hash($sifre, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE kullanicilar SET ad=?, soyad=?, email=?, sifre=? WHERE id=?");
        $stmt->bind_param("ssssi", $ad, $soyad, $email, $yeni_sifre, $user_id);
    } else {
        // Şifre değişmeyecekse
        $stmt = $conn->prepare("UPDATE kullanicilar SET ad=?, soyad=?, email=? WHERE id=?");
        $stmt->bind_param("sssi", $ad, $soyad, $email, $user_id);
    }

    if ($stmt->execute()) {
        $mesaj = "<p style='color:green; text-align:center;'>✅ Bilgileriniz başarıyla güncellendi.</p>";
        $_SESSION['user_name'] = $ad; // Oturumdaki ismi de güncelle
    } else {
        $mesaj = "<p style='color:red; text-align:center;'>❌ Güncelleme sırasında bir hata oluştu.</p>";
    }
}

// MEVCUT BİLGİLERİ ÇEK
$sorgu = $conn->prepare("SELECT ad, soyad, email FROM kullanicilar WHERE id = ?");
$sorgu->bind_param("i", $user_id);
$sorgu->execute();
$user = $sorgu->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profil Ayarları</title>
    <link rel="stylesheet" href="stil.css">
    <style>
        .profil-form { max-width: 500px; width: 100%; }
    </style>
</head>
<body>
    <div class="profil-form">
        <h2>👤 PROFİL AYARLARIM</h2>
        <?php echo $mesaj; ?>

        <form method="post">
            <label>Adınız:</label>
            <input type="text" name="ad" value="<?php echo htmlspecialchars($user['ad']); ?>" required>

            <label>Soyadınız:</label>
            <input type="text" name="soyad" value="<?php echo htmlspecialchars($user['soyad']); ?>" required>

            <label>E-posta Adresiniz:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label>Yeni Şifre (Değiştirmek istemiyorsanız boş bırakın):</label>
            <input type="password" name="sifre" placeholder="********">

            <button type="submit">💾 DEĞİŞİKLİKLERİ KAYDET</button>
        </form>

        <div style="margin-top: 20px; display: flex; justify-content: center;">
            <a href="kullanici_panel.php" class="admin-kart" style="padding: 15px 30px; max-width: 200px;">
                <span>⬅️ Geri Dön</span>
            </a>
        </div>
    </div>
</body>
</html>