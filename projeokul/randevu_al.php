<?php
require_once 'db_baglanti.php';

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) { 
    header("Location: giris.php"); 
    exit(); 
}

// Aktif personelleri ve hizmetleri çek
$personeller = $conn->query("SELECT * FROM personel ORDER BY ad ASC");
$hizmetler = $conn->query("SELECT * FROM hizmetler ORDER BY hizmet_adi ASC");
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Randevu Oluştur | Salon Pro</title>
    <link rel="stylesheet" href="stil.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { background: #0d1117; color: #c9d1d9; font-family: 'Segoe UI', sans-serif; }
        .ana-konteyner { max-width: 600px; background: #161b22; border: 1px solid #30363d; padding: 30px; border-radius: 20px; margin: 40px auto; box-shadow: 0 25px 50px rgba(0,0,0,0.5); }
        h2 { text-align: center; color: #f0f6fc; margin-bottom: 30px; letter-spacing: 1px; }
        .secim-alani { margin-bottom: 22px; }
        label { display: block; margin-bottom: 8px; font-size: 14px; color: #8b949e; }
        input, select, textarea { width: 100%; padding: 12px; background: #0d1117; color: #f0f6fc; border: 1px solid #30363d; border-radius: 10px; box-sizing: border-box; }
        input:focus, select:focus { border-color: #58a6ff; outline: none; }
        
        /* Saat Alanı ve Butonlar */
        #saatAlani { margin: 25px 0; padding: 15px; background: rgba(0, 0, 0, 0.2); border-radius: 12px; border: 1px dashed #30363d; }
        .saat-izgarasi { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
        .saat-btn { background: #21262d; border: 1px solid #30363d; color: #c9d1d9; padding: 10px; border-radius: 8px; cursor: pointer; transition: 0.2s; }
        .saat-btn:hover:not(:disabled) { border-color: #58a6ff; background: #30363d; }
        .saat-btn.active { background: #238636 !important; color: white !important; border-color: #2ea043 !important; }
        .saat-btn:disabled { opacity: 0.3; cursor: not-allowed; }

        #onayBtn { display: none; width: 100%; background: #238636; color: white; padding: 16px; border: none; border-radius: 12px; cursor: pointer; font-weight: bold; font-size: 16px; margin-top: 15px; }
        
        /* Modal */
        #overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 999; }
        #basariMesaji { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #161b22; padding: 40px; border-radius: 20px; text-align: center; z-index: 1000; border: 1px solid #30363d; }
    </style>
</head>
<body>

    <div id="overlay"></div>
    <div id="basariMesaji">
        <div style="font-size: 50px;">✅</div>
        <h2 style="color:#3fb950;">Randevu Alındı!</h2>
        <p style="color:#8b949e;">Talebiniz başarıyla iletildi.</p>
        <button onclick="window.location.href='randevularim.php'" style="background:#21262d; color:white; border:1px solid #30363d; padding:10px 20px; cursor:pointer; border-radius:8px; margin-top:20px;">Randevularımı Gör</button>
    </div>

    <div class="ana-konteyner">
        <h2>✂️ Randevu Al</h2>
        
        <form id="randevuForm">
            <div class="secim-alani">
                <label>Hizmet</label>
                <select name="hizmet_id" id="hizmet_id" required>
                    <option value="" disabled selected>Hizmet Seçin</option>
                    <?php while($h = $hizmetler->fetch_assoc()): ?>
                        <option value="<?php echo $h['id']; ?>"><?php echo $h['hizmet_adi']; ?> - <?php echo $h['fiyat']; ?> TL</option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="secim-alani">
                <label>Personel</label>
                <select name="personel_id" id="personel_id" required>
                    <option value="" disabled selected>Uzman Seçin</option>
                    <?php while($p = $personeller->fetch_assoc()): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo $p['ad']." ".$p['soyad']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="secim-alani">
                <label>Tarih</label>
                <input type="date" name="tarih" id="tarih" min="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="secim-alani">
                <label>Notunuz</label>
                <textarea name="aciklama" id="aciklama" rows="2" placeholder="Özel bir isteğiniz var mı?"></textarea>
            </div>

            <div id="saatAlani">
                <p style="text-align:center; font-size:13px; color:#484f58;">Lütfen tarih ve personel seçin.</p>
            </div>

            <input type="hidden" name="secilen_saat" id="secilen_saat" required>
            <button type="submit" id="onayBtn">RANDEVUYU ONAYLA</button>
        </form>
    </div>

    <script>
    // Personel veya Tarih değiştiğinde saatleri yükle
    $('#tarih, #personel_id').on('change', function() {
        const tarih = $('#tarih').val();
        const p_id = $('#personel_id').val();
        
        if(tarih && p_id) {
            $('#saatAlani').html('<p style="text-align:center; color:#58a6ff;">Saatler getiriliyor...</p>');
            $.ajax({
                url: 'randevu_islem.php',
                type: 'POST',
                data: {getir_saatler: true, tarih: tarih, personel_id: p_id},
                success: function(response) {
                    $('#saatAlani').html(response);
                    $('#onayBtn').hide(); // Yeni saatler gelince onay butonunu gizle
                    $('#secilen_saat').val('');
                }
            });
        }
    });

    // Saat butonu seçimi
    function saatSec(saat, btn) {
        $('.saat-btn').removeClass('active');
        $(btn).addClass('active');
        $('#secilen_saat').val(saat);
        $('#onayBtn').fadeIn();
    }

    // Formu gönder
    $('#randevuForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize() + "&kaydet=true";

        $.ajax({
            url: 'randevu_islem.php',
            type: 'POST',
            data: formData,
            success: function(res) {
                if(res.includes("başarılı")) {
                    $('#overlay, #basariMesaji').fadeIn();
                } else {
                    alert("Hata: " + res);
                }
            }
        });
    });
    </script>
</body>
</html>
<a href="kullanici_panel.php" class="admin-kart" style="padding: 12px 25px; text-decoration: none; font-size: 0.9rem;">🏠 Panele Geri Dön</a>