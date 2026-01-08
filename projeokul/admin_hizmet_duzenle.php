<?php
// admin_hizmet_duzenle.php
require_once 'db_baglanti.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: giris.php");
    exit();
}

$id = (int)$_GET['id'];
$mesaj = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad = $_POST['hizmet_adi'];
    $fiyat = $_POST['fiyat'];
    $sure = $_POST['sure'];
    $kat_id = $_POST['kategori_id'];

    $stmt = $conn->prepare("UPDATE hizmetler SET hizmet_adi=?, fiyat=?, sure=?, kategori_id=? WHERE id=?");
    $stmt->bind_param("sdisi", $ad, $fiyat, $sure, $kat_id, $id);
    
    if ($stmt->execute()) {
        $mesaj = "<p style='color:green;'>Hizmet güncellendi!</p>";
    }
}

// MEVCUT VERİLERİ ÇEK
$hizmet = $conn->query("SELECT * FROM hizmetler WHERE id = $id")->fetch_assoc();
$kategoriler = $conn->query("SELECT * FROM kategoriler")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="stil.css">
    <title>Hizmet Düzenle</title>
</head>
<body>
    <h2>HİZMETİ DÜZENLE</h2>
    <?php echo $mesaj; ?>

    <form method="post">
        <label>Hizmet Adı:</label>
        <input type="text" name="hizmet_adi" value="<?php echo $hizmet['hizmet_adi']; ?>" required>

        <label>Fiyat (TL):</label>
        <input type="number" step="0.01" name="fiyat" value="<?php echo $hizmet['fiyat']; ?>" required>

        <label>Süre (Dakika):</label>
        <input type="number" name="sure" value="<?php echo $hizmet['sure']; ?>" required>

        <label>Kategori:</label>
        <select name="kategori_id">
            <?php foreach($kategoriler as $k): ?>
                <option value="<?php echo $k['id']; ?>" <?php echo ($k['id'] == $hizmet['kategori_id']) ? 'selected' : ''; ?>>
                    <?php echo $k['kategori_adi']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Değişiklikleri Kaydet</button>
    </form>
    
    <br><a href="admin_hizmet_listele.php">Listeye Geri Dön</a>
</body>
</html>