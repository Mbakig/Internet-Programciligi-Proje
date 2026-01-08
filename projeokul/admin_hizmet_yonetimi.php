

<?php
// admin_hizmet_yonetimi.php
include 'db_baglanti.php';

// Oturum ve Admin kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: giris.php");
    exit();
}

// Tüm hizmetleri ve kategori adlarını JOIN ile çekme
$sql = "SELECT h.id, h.hizmet_adi, h.fiyat, h.sure, h.aciklama, k.kategori_adi 
        FROM hizmetler h
        JOIN kategoriler k ON h.kategori_id = k.id
        ORDER BY k.kategori_adi, h.hizmet_adi";

$result = $conn->query($sql);
$hizmetler = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Hizmet Yönetimi</title>
    <link rel="stylesheet" href="stil.css">
</head>
<body>
    <h2>HİZMET YÖNETİMİ</h2>
    
    <div class="admin-konteyner">
        <a href="admin_hizmet_ekle.php" class="admin-kart">
            <span style="font-size: 40px; margin-bottom: 10px;">➕</span>
            <span>Yeni Hizmet Ekle</span>
        </a>

        <a href="admin_hizmet_listele.php" class="admin-kart">
            <span style="font-size: 40px; margin-bottom: 10px;">📝</span>
            <span>Hizmetleri Listele / Düzenle</span>
        </a>

        <a href="admin_kategori_yonetimi.php" class="admin-kart">
            <span style="font-size: 40px; margin-bottom: 10px;">📁</span>
            <span>Kategori Yönetimi</span>
        </a>

        <a href="admin_panel.php" class="admin-kart cikis-kart">
            <span style="font-size: 40px; margin-bottom: 10px;">⬅️</span>
            <span>Ana Panele Dön</span>
        </a>
    </div>
</body>
</html>
   
    
    <br><a href="admin_panel.php">Admin Paneline Dön</a>
</body>
</html>