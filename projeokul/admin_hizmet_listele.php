<?php
require_once 'db_baglanti.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: giris.php");
    exit();
}
$sorgu = "SELECT h.*, k.kategori_adi FROM hizmetler h JOIN kategoriler k ON h.kategori_id = k.id ORDER BY k.kategori_adi";
$hizmetler = $conn->query($sorgu)->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="stil.css">
    <title>Hizmet Listesi</title>
</head>
<body>
    <h2>KAYITLI HİZMETLER</h2>
    
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Hizmet Adı</th>
                <th>Fiyat</th>
                <th>Süre</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($hizmetler as $h): ?>
            <tr>
                <td><?php echo $h['kategori_adi']; ?></td>
                <td><?php echo $h['hizmet_adi']; ?></td>
                <td><?php echo $h['fiyat']; ?> TL</td>
                <td><?php echo $h['sure']; ?> dk</td>
                <td>
                    <a href="admin_hizmet_duzenle.php?id=<?php echo $h['id']; ?>" class="islem-linki duzenle-btn">Düzenle</a>
                    <a href="admin_hizmet_sil.php?id=<?php echo $h['id']; ?>" class="islem-linki sil-btn" onclick="return confirm('Silmek istediğine emin misin?')">Sil</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <a href="admin_hizmet_yonetimi.php">Geri Dön</a>
</body>
</html>