<?php
require_once 'db_baglanti.php';

$mesaj = "";
$mesaj_turu = ""; // success veya error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad = htmlspecialchars(trim($_POST['ad']));
    $soyad = htmlspecialchars(trim($_POST['soyad']));
    $email = htmlspecialchars(trim($_POST['email']));
    $sifre = $_POST['sifre'];
    $sifre_tekrar = $_POST['sifre_tekrar'];

    if (empty($ad) || empty($soyad) || empty($email) || empty($sifre)) {
        $mesaj = "⚠️ Lütfen tüm alanları doldurun.";
        $mesaj_turu = "error";
    } elseif ($sifre !== $sifre_tekrar) {
        $mesaj = "❌ Şifreler birbiriyle eşleşmiyor.";
        $mesaj_turu = "error";
    } else {
        // E-posta kontrolü
        $kontrol = $conn->prepare("SELECT id FROM kullanicilar WHERE email = ?");
        $kontrol->bind_param("s", $email);
        $kontrol->execute();
        $sonuc = $kontrol->get_result();

        if ($sonuc->num_rows > 0) {
            $mesaj = "❌ Bu e-posta adresi zaten kullanımda.";
            $mesaj_turu = "error";
        } else {
            // Kaydı gerçekleştir (Varsayılan rol: kullanici)
            $rol = 'kullanici';
            $stmt = $conn->prepare("INSERT INTO kullanicilar (ad, soyad, email, sifre, rol) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $ad, $soyad, $email, $sifre, $rol);

            if ($stmt->execute()) {
                $mesaj = "✅ Kayıt başarılı! Giriş sayfasına yönlendiriliyorsunuz...";
                $mesaj_turu = "success";
                header("Refresh: 2; url=giris.php");
            } else {
                $mesaj = "❌ Kayıt sırasında bir hata oluştu: " . $conn->error;
                $mesaj_turu = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol | Salon Pro</title>
    <link rel="stylesheet" href="stil.css">
    <style>
        body { background: #0d1117; color: #c9d1d9; font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .ana-kutu { background: #161b22; padding: 40px; border-radius: 20px; border: 1px solid #30363d; width: 100%; max-width: 450px; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
        h2 { text-align: center; color: #f0f6fc; margin-bottom: 25px; letter-spacing: 1px; }
        
        /* Mesaj Kutuları */
        .bildirim { padding: 12px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; text-align: center; }
        .error { background: rgba(248, 81, 73, 0.1); color: #f85149; border: 1px solid rgba(248, 81, 73, 0.2); }
        .success { background: rgba(63, 185, 80, 0.1); color: #3fb950; border: 1px solid rgba(63, 185, 80, 0.2); }

        .input-ikili { display: flex; gap: 10px; }
        input { width: 100%; padding: 14px; margin-bottom: 15px; background: #0d1117; color: white; border: 1px solid #30363d; border-radius: 12px; box-sizing: border-box; transition: 0.3s; }
        input:focus { border-color: #58a6ff; outline: none; box-shadow: 0 0 0 3px rgba(88, 166, 255, 0.1); }
        
        .buton-grubu { display: flex; gap: 15px; margin-top: 20px; }
        .btn { flex: 1; padding: 15px; border-radius: 12px; font-weight: bold; cursor: pointer; text-align: center; text-decoration: none; font-size: 14px; transition: 0.3s; border: none; }
        
        .btn-kayit { background: #238636; color: white; }
        .btn-kayit:hover { background: #2ea043; transform: translateY(-2px); }
        
        .btn-geri { background: #21262d; color: #c9d1d9; border: 1px solid #30363d; display: flex; align-items: center; justify-content: center; }
        .btn-geri:hover { background: #30363d; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="ana-kutu">
        <h2>📝 Yeni Hesap</h2>

        <?php if ($mesaj): ?>
            <div class="bildirim <?php echo $mesaj_turu; ?>"><?php echo $mesaj; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="input-ikili">
                <input type="text" name="ad" placeholder="Ad" required>
                <input type="text" name="soyad" placeholder="Soyad" required>
            </div>
            <input type="email" name="email" placeholder="E-posta Adresi" required>
            <input type="password" name="sifre" placeholder="Şifre" required>
            <input type="password" name="sifre_tekrar" placeholder="Şifre Tekrar" required>
            
            <div class="buton-grubu">
                <button type="submit" class="btn btn-kayit">Kaydı Tamamla</button>
                <a href="giris.php" class="btn btn-geri">Giriş'e Dön</a>
            </div>
        </form>
    </div>
</body>
</html>