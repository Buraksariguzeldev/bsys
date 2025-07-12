<?php

function writeLog($message) {
    $logDir = __DIR__ . '/../logs'; // Log dosyası için dizin
    $logFile = $logDir . '/vt_durum1.log'; // Log dosyasının tam yolu

    // Dizin yoksa oluştur
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    $currentPage = $_SERVER['REQUEST_URI']; // Kullanıcının bulunduğu sayfa

    // Mesaj formatı: [Tarih - Saat] [Sayfa Bilgisi] Mesaj
        $logMessage = "[" . date("d-m-Y H:i:s") . "] [" . $currentPage . "] " .
        $message ;
        
    // Dosya mevcut değilse oluştur
    if (!file_exists($logFile)) {
        file_put_contents($logFile, '');
    }

    // Mevcut log içeriklerini oku
    $currentLog = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Aynı mesaj ve aynı sayfa logda varsa yazma
    foreach ($currentLog as $logEntry) {
        if (strpos($logEntry, $currentPage) !== false && strpos($logEntry, $message) !== false) {
            return; // Aynı mesaj ve sayfa zaten logda, yazma
        }
    }

    // Mesaj logda yoksa ekle
    file_put_contents($logFile, $logMessage . PHP_EOL, FILE_APPEND);
}

try {
    // İlk veritabanına bağlanma (bsdsoft)
    $vt = new PDO('mysql:host=localhost;dbname=bsdsoft;charset=utf8', 'root', '');
    $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Başarılı bağlantıyı loga yaz
    writeLog("İlk veritabanına bağlanıldı.");
} catch (PDOException $e) {
    // İlk veritabanı bağlantısı hatası durumunda loga yaz
    writeLog("İlk veritabanına bağlanırken hata oluştu: " . $e->getMessage());

    try {
        // İkinci veritabanına bağlanma (if0_37752023)
        $vt = new PDO(
            'mysql:host=sql210.infinityfree.com;dbname=if0_37752023_oturumbilgileri;charset=utf8',
            'if0_37752023',
            'MdFkFsh8z4qk'
        );
        $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Başarılı bağlantıyı loga yaz
        writeLog("İkinci veritabanına bağlanıldı.");
    } catch (PDOException $e) {
        // İkinci veritabanı bağlantısı hatası durumunda loga yaz
        writeLog("İkinci veritabanına bağlanırken hata oluştu: " . $e->getMessage());
        exit; // Daha fazla işlem yapmayarak scripti durdur
    }
}