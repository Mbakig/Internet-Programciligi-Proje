<?php
// admin_personel_sil.php
require_once 'db_baglanti.php';

// Oturum ve Admin kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: giris.php");
    exit();
}

$mesaj = "";

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $personel_id = (int)$_GET['id'];

    // 1. Personel Randevularını Temizleme/İptal Etme (Zorunlu Adım)
    // Silinen personelin randevularını 'iptal edildi' olarak işaretliyoruz.
    try {
        $stmt_randevu = $conn->prepare("UPDATE randevular SET durum = 'iptal edildi' WHERE personel_id = ? AND durum != 'tamamlandı'");
        $stmt_randevu->bind_param("i", $personel_id);
        $stmt_randevu->execute();
        $stmt_randevu->close();
    } catch (Exception $e) {
        $mesaj .= "<p style='color:orange;'>Personel randevuları güncellenemedi: " . $e->getMessage() . "</p>";
    }
    
    // 2. Çalışma Saatlerini Silme
    try {
        $stmt_saat = $conn->prepare("DELETE FROM calisma_saatleri WHERE personel_id = ?");
        $stmt_saat->bind_param("i", $personel_id);
        $stmt_saat->execute();
        $stmt_saat->close();
    } catch (Exception $e) {
        $mesaj .= "<p style='color:orange;'>Personel çalışma saatleri silinemedi: " . $e->getMessage() . "</p>";
    }

    // 3. Personeli Silme
    try {
        $stmt = $conn->prepare("DELETE FROM personel WHERE id = ?");
        $stmt->bind_param("i", $personel_id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $mesaj = "<p style='color:green;'>Personel (ID: $personel_id) başarıyla silindi ve ilgili randevular iptal edildi.</p>";
            } else {
                $mesaj = "<p style='color:orange;'>Personel zaten silinmiş veya bulunamadı.</p>";
            }
        } else {
            $mesaj = "<p style='color:red;'>Personel silinirken bir sorun oluştu.</p>";
        }
        $stmt->close();
    } catch (Exception $e) {
        $mesaj = "<p style='color:red;'>Veritabanı hatası: " . $e->getMessage() . "</p>";
    }

} else {
    $mesaj = "<p style='color:red;'>Hata: Geçersiz veya eksik personel ID'si.</p>";
}


header("Location: admin_personel_yonetimi.php?msg=" . urlencode(strip_tags($mesaj)));
exit();
?>