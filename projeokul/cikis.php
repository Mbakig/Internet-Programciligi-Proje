<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Tüm oturum değişkenlerini temizle
$_SESSION = array();

// 3. Oturum çerezini (cookie) sil
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Oturumu tamamen sonlandır
session_destroy();

// 5. Giriş sayfasına yönlendir
header("Location: giris.php");
exit; 

?>
