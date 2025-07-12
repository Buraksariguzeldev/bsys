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
    "KENT SWITCH - DARK BLUE" => [136],
    "KENT D RANGE - BLUE, GREY" => [116, 117],
    "KENT D RANGE - BLUE LONG, GREY LONG" => [118],
    "KENT - DARK BLUE, DARK BLUE LONG, SWITCH" => [132],
    "KENT - BLUE, WHITE" => [109 ],
    "KENT SLIMS LONG - BLUE, GREY" => [135],
    "KENT SLIMS - BLACK, GREY" => [119, 120],
    "ROTHMANS - TÜM ÜRÜNLER" => [121 , 112],
    "TEKEL 2000 - TÜM ÜRÜNLER" => [123 , 124 , 125 , 126],
    "TEKEL 2001 - TÜM ÜRÜNLER" => [131],
   "VICEROY - TÜM ÜRÜNLER" => [0] ,
   "PALL MALL - TÜM ÜRÜNLER" => [0],
    "MALTEPE & SAMSUN - TÜM ÜRÜNLER" => [110]
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
    <title>kent Ürün Güncelleme</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body class="container mt-4">

<h2 class="text-center text-primary"><i class="bi bi-pencil-square"></i> kent Ürün
Kent Güncelleme</h2>

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
foreach ($worksheet->getRowIterator() as $satir) {
    $urun_adi = trim($worksheet->getCell('A' . $satir->getRowIndex())->getValue());
    $yeni_fiyat = trim($worksheet->getCell('B' . $satir->getRowIndex())->getValue());

    if (empty($urun_adi) || empty($yeni_fiyat)) {
        continue;
    }

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
                $eski_fiyat = $stmt->fetchColumn();

                if ($eski_fiyat === false) {
                    $eski_fiyat = "Bulunamadı";
                    $durum = "<span class='badge bg-secondary'><i class='bi bi-x-circle'></i> Ürün bulunamadı</span>";
                    continue;
                }

                if ($eski_fiyat == $yeni_fiyat) {
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

    echo "<tr>
            <td>$sira</td>
            <td>$urun_adi</td>
            <td>₺$eski_fiyat</td>
            <td>₺$yeni_fiyat</td>
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