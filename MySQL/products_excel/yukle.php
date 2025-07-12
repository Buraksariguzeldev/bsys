<?php

require '../../myproject/vendor/autoload.php'; // PhpSpreadsheet kütüphanesi için

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_FILES['excelDosya']['error'] == UPLOAD_ERR_OK) {
    $geciciDosya = $_FILES['excelDosya']['tmp_name'];
    $yeniDosya = 'uploads/' . basename($_FILES['excelDosya']['name']);
    
    move_uploaded_file($geciciDosya, $yeniDosya); // Dosyayı uploads klasörüne taşı
    
    // Excel dosyasını oku
    $spreadsheet = IOFactory::load($yeniDosya);
    $worksheet = $spreadsheet->getActiveSheet();
    
    // A1 yerine A2 hücresindeki değeri al (ilk satırı atla)
    $ilkHucre = strtoupper(trim($worksheet->getCell('A2')->getValue()));
    
    // A2'ye göre ilgili dosyaya yönlendir
    if (stripos($ilkHucre, 'WINSTON') !== false) {
        header("Location: winston.php?dosya=" . urlencode($_FILES['excelDosya']['name']));
        exit;
    } elseif (stripos($ilkHucre, 'KENT') !== false) {
        header("Location: kent.php?dosya=" . urlencode($_FILES['excelDosya']['name']));
        exit;
    } elseif (stripos($ilkHucre, 'PARLIAMENT') !== false) {
        header("Location: parliament.php?dosya=" . urlencode($_FILES['excelDosya']['name']));
        exit;
    } elseif (stripos($ilkHucre, 'ÜRüNLER DışA AKTAR') !== false) {
        header("Location: import_process.php?dosya=" . urlencode($_FILES['excelDosya']['name']));
        exit;
    } else {
        echo "Geçerli bir işlem bulunamadı: $ilkHucre";
    }
} else {
    echo "Dosya yüklenirken hata oluştu.";
}
