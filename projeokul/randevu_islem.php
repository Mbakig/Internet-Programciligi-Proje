<?php
require_once 'db_baglanti.php';

// --- 1. SAATLERİ LİSTELEME İŞLEMİ ---
if (isset($_POST['getir_saatler'])) {
    $tarih = $_POST['tarih'];
    $p_id = (int)$_POST['personel_id'];
    $gun_no = (int)date('w', strtotime($tarih)); // 0: Pazar, 1: Pazartesi...

   

    // MESAİ SORGUSU
    $stmt = $conn->prepare("SELECT baslangic_saati, bitis_saati FROM calisma_saatleri WHERE personel_id = ? AND gun_no = ?");
    $stmt->bind_param("ii", $p_id, $gun_no);
    $stmt->execute();
    $mesai = $stmt->get_result()->fetch_assoc();

    if (!$mesai) {
        echo "<div style='color:#f85149; background:rgba(248,81,73,0.1); padding:15px; border-radius:10px; text-align:center;'>❌ Personel bu gün çalışmamaktadır.</div>";
        exit;
    }

    // DOLU RANDEVULARI ÇEK (Çakışmaları önlemek için)
    $dolu_s = $conn->prepare("SELECT DATE_FORMAT(randevu_saati, '%H:%i') as saat FROM randevular WHERE personel_id = ? AND randevu_tarihi = ? AND durum != 'reddedildi'");
    $dolu_s->bind_param("is", $p_id, $tarih);
    $dolu_s->execute();
    $dolular = array_column($dolu_s->get_result()->fetch_all(MYSQLI_ASSOC), 'saat');

    // SAATLERİ ÜRET
    $basla = strtotime($mesai['baslangic_saati']);
    $bitis = strtotime($mesai['bitis_saati']);
    
    echo "<div style='display:grid; grid-template-columns: repeat(4, 1fr); gap:8px;'>";
    for ($i = $basla; $i <= $bitis; $i += 1800) { // 30 dakikalık aralık
        $saat_f = date("H:i", $i);
        $is_dolu = in_array($saat_f, $dolular);
        $gecmis = ($tarih == date('Y-m-d') && $saat_f < date('H:i'));

        if ($is_dolu || $gecmis) {
            echo "<button type='button' disabled style='background:#161b22; color:#444; border:1px solid #333; padding:10px; border-radius:8px; opacity:0.5; cursor:not-allowed;'>$saat_f</button>";
        } else {
            echo "<button type='button' class='saat-btn' onclick='saatSec(\"$saat_f\", this)' style='background:#21262d; color:#c9d1d9; border:1px solid #30363d; padding:10px; border-radius:8px; cursor:pointer;'>$saat_f</button>";
        }
    }
    echo "</div>";
    exit;
}

// --- 2. RANDEVU KAYDETME İŞLEMİ ---
if (isset($_POST['kaydet'])) {
    // Session kontrolü
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (!isset($_SESSION['user_id'])) { echo "Lütfen önce giriş yapın."; exit; }

    $u_id = $_SESSION['user_id'];
    $p_id = (int)$_POST['personel_id'];
    $h_id = (int)$_POST['hizmet_id'];
    $tarih = $_POST['tarih'];
    $saat = $_POST['secilen_saat'];
    $not = $_POST['aciklama'] ?? '';

    if (empty($saat)) { echo "Lütfen bir saat seçin."; exit; }

    $stmt = $conn->prepare("INSERT INTO randevular (kullanici_id, personel_id, hizmet_id, randevu_tarihi, randevu_saati, aciklama, durum) VALUES (?, ?, ?, ?, ?, ?, 'onay bekliyor')");
    $stmt->bind_param("iiisss", $u_id, $p_id, $h_id, $tarih, $saat, $not);
    
    if($stmt->execute()) {
        echo "başarılı"; // Randevu_al.php'deki JS bu kelimeyi bekliyor
    } else {
        echo "Hata: " . $conn->error;
    }
    exit;
}