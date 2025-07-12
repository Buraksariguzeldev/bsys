<?php

require $_SERVER['DOCUMENT_ROOT'] . '/myproject/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$filePath = 'fiyat_listesi.png';
$apiKey = 'K82779069988957';

$skipKeywords = ['fiyat listesi', 'tarihinden itibaren'];
$yasakliSatirlar = ['jti', '04', 'fiyat', 'ürün adı', 'a', 'b'];
$duzeltmeFiyatlari = ['100' => '85', '04' => '85'];

if (!file_exists($filePath)) die("❌ Dosya yok: $filePath\n");
$imageInfo = getimagesize($filePath);
if ($imageInfo === false) die("❌ Görsel bozuk: $filePath\n");

if ($imageInfo['mime'] !== 'image/png') {
    switch ($imageInfo['mime']) {
        case 'image/webp': $img = imagecreatefromwebp($filePath); break;
        case 'image/jpeg': case 'image/jpg': $img = imagecreatefromjpeg($filePath); break;
        default: die("❌ Desteklenmeyen format: {$imageInfo['mime']}\n");
    }
    $filePath = 'fiyat_listesi_converted.png';
    imagepng($img, $filePath);
    imagedestroy($img);
}

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.ocr.space/parse/image",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_POSTFIELDS => [
        'apikey' => $apiKey,
        'language' => 'tur',
        'isOverlayRequired' => 'false',
        'OCREngine' => 2,
        'file' => new CURLFile($filePath),
    ],
]);

$response = curl_exec($curl);
if ($response === false) die("❌ curl hatası: " . curl_error($curl));
curl_close($curl);

$result = json_decode($response, true);
if (!isset($result['ParsedResults'][0]['ParsedText'])) die("❌ OCR başarısız.");
if (!empty($result['IsErroredOnProcessing'])) die("❌ OCR API hatası: " . implode(" | ", $result['ErrorMessage'] ?? []));

$lines = preg_split('/\r\n|\r|\n/', $result['ParsedResults'][0]['ParsedText']);
$veriler = [];
$urunler = [];
$iUrun = 0;

foreach ($lines as $satir) {
    $satir = trim($satir);
    $satirLower = mb_strtolower($satir, 'UTF-8');

    if ($satir === '' ||
        preg_match('/\d{2}\.\d{2}\.\d{4}/', $satir) ||
        in_array($satirLower, $yasakliSatirlar) ||
        stripos($satirLower, $skipKeywords[0]) !== false ||
        stripos($satirLower, $skipKeywords[1]) !== false) {
        continue;
    }

    // Bozuk karakter düzeltme
    $satir = preg_replace('/(\d{2,3})\s*[b₺]?$/iu', '$1 ₺', $satir);

    // Ürün + fiyat aynı satırda
    if (preg_match('/^(.*?)(\s+)(\d{2,3})\s*₺?$/u', $satir, $match)) {
        $urun = trim($match[1]);
        $fiyat = $match[3];
        if (isset($duzeltmeFiyatlari[$fiyat])) $fiyat = $duzeltmeFiyatlari[$fiyat];
        $veriler[] = [$urun, $fiyat];
        continue;
    }

    $urunler[] = $satir;
}

// Fiyat ayrı satırda gelmişse eşleştir
foreach ($lines as $satir) {
    if (preg_match('/\b(\d{2,3})\b/', $satir, $match)) {
        $fiyat = $match[1];
        if (isset($duzeltmeFiyatlari[$fiyat])) $fiyat = $duzeltmeFiyatlari[$fiyat];
        if (isset($urunler[$iUrun])) {
            $veriler[] = [$urunler[$iUrun], $fiyat];
            $iUrun++;
        }
    }
}

// Excel dosyası oluştur
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue("A1", "Ürün Adı");
$sheet->setCellValue("B1", "Fiyat");

$row = 2;
foreach ($veriler as $veri) {
    $sheet->setCellValue("A{$row}", $veri[0]);
    $sheet->setCellValue("B{$row}", $veri[1]);
    $row++;
}

$excelFile = 'ocr_fiyat_listesi.xlsx';
$writer = new Xlsx($spreadsheet);
$writer->save($excelFile);

file_put_contents('products.txt', implode("\n", array_map(fn($v) => $v[0], $veriler)));
file_put_contents('prices.txt', implode("\n", array_map(fn($v) => $v[1], $veriler)));

echo "✅ Sahte veri filtrelendi, fiyatlar görsel sırasına göre düzeltildi.\n";
echo "📦 Excel: $excelFile\n";
echo "📋 TXT: products.txt | prices.txt\n";

?>
