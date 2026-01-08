<?php
require_once 'db_baglanti.php';

$hata_mesaji = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars(trim($_POST['email']));
    $sifre = $_POST['sifre'];

    if (!empty($email) && !empty($sifre)) {
        // Kullanıcıyı veritabanında ara
        $sorgu = $conn->prepare("SELECT id, ad, sifre, rol FROM kullanicilar WHERE email = ?");
        $sorgu->bind_param("s", $email);
        $sorgu->execute();
        $sonuc = $sorgu->get_result();

        if ($sonuc->num_rows > 0) {
            $kullanici = $sonuc->fetch_assoc();
            
            if ($sifre === $kullanici['sifre']) {
                $_SESSION['user_id'] = $kullanici['id'];
                $_SESSION['user_name'] = $kullanici['ad'];
                $_SESSION['user_role'] = $kullanici['rol'];

               
                if ($kullanici['rol'] == 'admin') {
                    header("Location: admin_panel.php");
                } else {
                    header("Location: kullanici_panel.php");
                }
                exit();
            } else {
                $hata_mesaji = "❌ Hatalı şifre girdiniz.";
            }
        } else {
            $hata_mesaji = "❌ Bu e-posta adresi ile kayıtlı kullanıcı bulunamadı.";
        }
    } else {
        $hata_mesaji = "⚠️ Lütfen tüm alanları doldurun.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap | Salon Pro</title>
    <style>
        body { background: #0d1117; color: #c9d1d9; font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .ana-kutu { background: #161b22; padding: 40px; border-radius: 20px; border: 1px solid #30363d; width: 100%; max-width: 400px; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
        h2 { text-align: center; color: #f0f6fc; margin-bottom: 30px; letter-spacing: 1px; }
        
        .hata { background: rgba(248, 81, 73, 0.1); color: #f85149; border: 1px solid rgba(248, 81, 73, 0.2); padding: 12px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; text-align: center; }
        
        .input-grubu { margin-bottom: 20px; }
        input { width: 100%; padding: 14px; background: #0d1117; color: white; border: 1px solid #30363d; border-radius: 12px; box-sizing: border-box; transition: 0.3s; }
        input:focus { border-color: #58a6ff; outline: none; box-shadow: 0 0 0 3px rgba(88, 166, 255, 0.1); }
        
        .buton-grubu { display: flex; gap: 15px; margin-top: 25px; }
        .btn { flex: 1; padding: 15px; border-radius: 12px; font-weight: bold; cursor: pointer; text-align: center; text-decoration: none; font-size: 14px; transition: 0.3s; border: none; }
        
        .btn-giris { background: #238636; color: white; }
        .btn-giris:hover { background: #2ea043; transform: translateY(-2px); }
        
        .btn-kayit { background: #21262d; color: #c9d1d9; border: 1px solid #30363d; display: flex; align-items: center; justify-content: center; }
        .btn-kayit:hover { background: #30363d; border-color: #8b949e; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="ana-kutu">
        <h2>✂️ Salon Pro</h2>
        
        <?php if ($hata_mesaji): ?>
            <div class="hata"><?php echo $hata_mesaji; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="input-grubu">
                <input type="email" name="email" placeholder="E-posta Adresiniz" required>
            </div>
            <div class="input-grubu">
                <input type="password" name="sifre" placeholder="Şifreniz" required>
            </div>
            
            <div class="buton-grubu">
                <button type="submit" class="btn btn-giris">Giriş Yap</button>
                <a href="kayit.php" class="btn btn-kayit">Kayıt Ol</a>
            </div>
        </form>
    </div>
</body>

</html>
