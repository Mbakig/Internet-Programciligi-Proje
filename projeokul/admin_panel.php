<?php
// admin_panel.php
require_once 'db_baglanti.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: giris.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Yönetim Paneli | Salon Pro</title>
    <link rel="stylesheet" href="stil.css">
    <style>
        body {
            background: #0d1117; /* GitHub tarzı koyu arka plan */
            color: #c9d1d9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .admin-konteyner {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            width: 90%;
            max-width: 900px;
            padding: 20px;
        }

        .panel-baslik {
            grid-column: 1 / -1;
            text-align: center;
            color: #f0f6fc;
            margin-bottom: 30px;
            letter-spacing: 2px;
        }

        .admin-kart {
            background: rgba(22, 27, 34, 0.8);
            border: 1px solid #30363d;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            text-decoration: none;
            color: #c9d1d9;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(5px);
        }

        .admin-kart:hover {
            transform: translateY(-8px);
            border-color: #8b0000; /* Senin bordo teman */
            background: rgba(33, 38, 45, 1);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
        }

        .admin-kart .icon {
            font-size: 40px;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }

        .admin-kart:hover .icon {
            transform: scale(1.2);
        }

        .admin-kart span {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .admin-kart small {
            color: #8b949e;
            font-size: 12px;
        }

        .cikis-kart {
            border-color: rgba(217, 83, 79, 0.2);
        }

        .cikis-kart:hover {
            border-color: #d9534f;
            background: rgba(217, 83, 79, 0.1);
        }

        /* Alt Bilgi */
        .footer-text {
            grid-column: 1 / -1;
            text-align: center;
            margin-top: 30px;
            font-size: 13px;
            color: #484f58;
        }
    </style>
</head>
<body>

    <div class="admin-konteyner">
        <div class="panel-baslik">
            <h1 style="margin:0;">⚙️ YÖNETİM PANELİ</h1>
            <p style="color: #8b949e; font-size: 14px;">Hoş geldiniz, her şey kontrol altında.</p>
        </div>

        <a href="admin_randevu_yonetimi.php" class="admin-kart">
            <div class="icon">📅</div>
            <span>Randevular</span>
            <small>Onay bekleyenleri yönet</small>
        </a>

        <a href="admin_personel_yonetimi.php" class="admin-kart">
            <div class="icon">👥</div>
            <span>Personel</span>
            <small>Mesai ve kadro yönetimi</small>
        </a>

        <a href="admin_hizmet_yonetimi.php" class="admin-kart">
            <div class="icon">✂️</div>
            <span>Hizmetler</span>
            <small>Fiyat ve kategori düzenle</small>
        </a>

        <a href="admin_personel_analiz.php" class="admin-kart">
            <div class="icon">📊</div>
            <span>Analiz</span>
            <small>Puanlar ve müşteri yorumları</small>
        </a>

        <a href="cikis.php" class="admin-kart cikis-kart">
            <div class="icon">❌</div>
            <span>Çıkış Yap</span>
            <small>Oturumu güvenli kapat</small>
        </a>

        <div class="footer-text">
            Salon Yönetim Sistemi v2.0 | 2026
        </div>
    </div>

</body>
</html>