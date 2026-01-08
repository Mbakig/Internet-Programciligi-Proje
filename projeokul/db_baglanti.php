<?php
// db_baglanti.php


if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

$servername = "localhost";
$username = "root"; // Kendi kullanıcı adınız
$password = "";     // Kendi şifreniz
$dbname = "kuafor_randevu";


$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8"); 

// Bağlantı kontrolü
if ($conn->connect_error) {
    // MySQL çalışmıyorsa veya bilgiler yanlışsa hata verecektir.
    die("Veritabanı bağlantı hatası: " . $conn->connect_error);
}

// Bu dosya burada biter. Başka hiçbir kod içermemeli.
?>