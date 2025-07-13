<?php
require '../../myproject/vendor/autoload.php'; // PhpSpreadsheet kütüphanesi için

include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti1.php'); // Veritabanı bağlantısı

// navigasyon 
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
// giriş kontrol
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/giriskontrol.php');

use PhpOffice\PhpSpreadsheet\IOFactory;

error_reporting(0); // Hata mesajlarını kapat
ini_set('display_errors', 0);

if (!isset($_GET['dosya'])) {
    die("Dosya belirtilmedi.");
}

$dosya = 'uploads/' . basename($_GET['dosya']);
if (!file_exists($dosya)) {
    die("Dosya bulunamadı.");
}

// **Veritabanı bağlantısını kontrol et**
if (!isset($vt)) {
    die("Hata: Veritabanı bağlantısı yok.");
}

// **Ürün isimleri ile ID’leri eşleştiren dizi**
$urunler = [
    "PARLIAMENT Night Blue Pack / Long (Uzun)" => [88 ],
    "PARLIAMENT Aqua Blue Slims" => [0],
    "PARLIAMENT Night Blue / Aqua Blue / Reserve" => [90],
    "PARLIAMENT MIDNIGHT BLUE" => [0],
    "MARLBORO Red Long" => [86],
    "MARLBORO Red / Touch" => [87 , 89],
    "MARLBORO Touch Blue / Gray / Gray Rcb / White" => [99 , 100],
    "MARLBORO EDGE" => [133],
    "MARLBORO EDGE Blue / Sky" => [0],
    "MURATTI" => [91],
    "MURATTI BLU" => [101],
    "LARK" => [93 , 95 ],
    "CHESTERFIELD" => [108],
    "L&M" => [0],
    "MARLBORO ROLL 50 - Sarmalık Kıyılmış Tütün" => [0]
];

// **Excel dosyasını oku**
$spreadsheet = IOFactory::load($dosya);
$worksheet = $spreadsheet->getActiveSheet();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ürün Güncelleme</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body class="container mt-4">

<h2 class="text-center text-primary"><i class="bi bi-pencil-square"></i> Parliament Ürün Güncelleme</h2>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th><i class="bi bi-box"></i> Ürün Adı</th>
            <th><i class="bi bi-tag"></i> Önceki Fiyat</th>
            <th><i class="bi bi-cash-stack"></i> Yeni Fiyat</th>
            <th><i class="bi bi-check-circle"></i> Durum</th>
        </tr>
    </thead>
    <tbody>

<?php
$sira = 1;

function temizleFiyat($fiyat) {
    $fiyat = mb_strtolower(trim($fiyat));
    $fiyat = str_replace(['₺', 'tl', ' '], '', $fiyat);
    $fiyat = preg_replace('/[^\d.,]/u', '', $fiyat);
    $fiyat = str_replace(',', '.', $fiyat);
    return round((float)$fiyat, 2);
}

foreach ($worksheet->getRowIterator() as $satir) {
    $rowIndex = $satir->getRowIndex();
    $urun_adi = trim($worksheet->getCell('A' . $rowIndex)->getFormattedValue());
    $yeni_fiyat_raw = $worksheet->getCell('B' . $rowIndex)->getFormattedValue();

    if (empty($urun_adi) || empty($yeni_fiyat_raw)) {
        continue;
    }

    $yeni_fiyat = temizleFiyat($yeni_fiyat_raw);

    if (isset($urunler[$urun_adi])) {
        $idler = (array) $urunler[$urun_adi]; 
        $durum = "<span class='badge bg-success'><i class='bi bi-check-circle'></i> Güncellendi</span>";
        $eski_fiyat = "Bilinmiyor";

        foreach ($idler as $id) {
            if ($id == 0) {
                $durum = "<span class='badge bg-secondary'><i class='bi bi-exclamation-circle'></i> Ürün veritabanında bulunamadı</span>";
                continue;
            }

            try {
                $stmt = $vt->prepare("SELECT sale_price FROM products WHERE id = :id");
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $eski_fiyat_db = $stmt->fetchColumn();

                if ($eski_fiyat_db === false) {
                    $eski_fiyat = "Bulunamadı";
                    $durum = "<span class='badge bg-secondary'><i class='bi bi-x-circle'></i> Ürün bulunamadı</span>";
                    continue;
                }

                $eski_fiyat = temizleFiyat($eski_fiyat_db);

                if (abs($eski_fiyat - $yeni_fiyat) < 0.01) {
                    $durum = "<span class='badge bg-warning text-dark'><i class='bi bi-arrow-repeat'></i> Değişiklik yok</span>";
                    continue;
                }

                $stmt = $vt->prepare("UPDATE products SET sale_price = :price WHERE id = :id");
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $stmt->bindValue(':price', $yeni_fiyat, PDO::PARAM_STR);

                if (!$stmt->execute()) {
                    $durum = "<span class='badge bg-danger'><i class='bi bi-x-circle'></i> Güncellenemedi</span>";
                }

            } catch (PDOException $e) {
                $durum = "<span class='badge bg-danger'><i class='bi bi-exclamation-circle'></i> Hata</span>";
            }
        }
    } else {
        $durum = "<span class='badge bg-secondary'><i class='bi bi-exclamation-circle'></i> Eşleşme yok</span>";
        $eski_fiyat = "-";
    }

    $eski_goster = (is_numeric($eski_fiyat)) ? "₺" . number_format($eski_fiyat, 2, ',', '.') : $eski_fiyat;
    $yeni_goster = "₺" . number_format($yeni_fiyat, 2, ',', '.');

    echo "<tr>
            <td>$sira</td>
            <td>$urun_adi</td>
            <td>$eski_goster</td>
            <td>$yeni_goster</td>
            <td>$durum</td>
          </tr>";
    $sira++;
}
?>
    </tbody>
</table>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

