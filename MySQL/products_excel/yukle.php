<?php
require '../../myproject/vendor/autoload.php'; // PhpSpreadsheet kütüphanesi için
use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_FILES['excelDosya']['error'] == UPLOAD_ERR_OK) {
    $geciciDosya = $_FILES['excelDosya']['tmp_name'];
    $yeniDosya = 'uploads/' . basename($_FILES['excelDosya']['name']);

    move_uploaded_file($geciciDosya, $yeniDosya); // Dosyayı uploads klasörüne taşı

    $spreadsheet = IOFactory::load($yeniDosya);
    $worksheet = $spreadsheet->getActiveSheet();

    // Hem A1 hem A2'yi oku
    $hucreA1 = strtoupper(trim($worksheet->getCell('A1')->getValue()));
    $hucreA2 = strtoupper(trim($worksheet->getCell('A2')->getValue()));

    // POS geçiyorsa A1'e göre yönlendir
    if (stripos($hucreA1, 'POS') !== false) {
               header("Location: import_process.php?dosya=" . urlencode($_FILES['excelDosya']['name']));
        exit;
    }

    // Diğer yönlendirmeler A2’ye göre yapılır
    if (stripos($hucreA2, 'WINSTON') !== false) {
        header("Location: winston.php?dosya=" . urlencode($_FILES['excelDosya']['name']));
        exit;
    } elseif (stripos($hucreA2, 'KENT') !== false) {
        header("Location: kent.php?dosya=" . urlencode($_FILES['excelDosya']['name']));
        exit;
    } elseif (stripos($hucreA2, 'PARLIAMENT NIGHT BLUE PACK / LONG (UZUN)') !== false) {
        header("Location: parliament.php?dosya=" . urlencode($_FILES['excelDosya']['name']));
        exit;
   
    } else {
        echo "Geçerli bir işlem bulunamadı: $hucreA2";
    }
} else {
    echo "Dosya yüklenirken hata oluştu.";
}
