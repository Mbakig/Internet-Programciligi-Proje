<?php
// admin_hizmet_sil.php
require_once 'db_baglanti.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: giris.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    

    $stmt = $conn->prepare("DELETE FROM hizmetler WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: admin_hizmet_listele.php?mesaj=silindi");
    } else {
        echo "Hata oluştu: " . $conn->error;
    }
}
?>