<?php
require '../../myproject/vendor/autoload.php';
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti1.php');
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/giriskontrol.php');
use PhpOffice\PhpSpreadsheet\IOFactory;

error_reporting(0);
ini_set('display_errors', 0);

if (!isset($_GET['dosya'])) die("Dosya belirtilmedi.");
$dosya = 'uploads/' . basename($_GET['dosya']);
if (!file_exists($dosya)) die("Dosya bulunamadı.");
if (!isset($vt)) die("Veritabanı bağlantısı yok.");

$urunler = [
    "WINSTON Dark Blue & Deep Blue" => [106,107],
    "WINSTON Slender Q Line" => [139],
    "WINSTON Slender" => [115, 43],
    "WINSTON" => [130],
    "WINSTON Slims" => [137],
    "WINSTON Xsence" => [0],
    "CAMEL Slender" => [103, 104],
    "CAMEL Deep Blue" => [97, 102],
    "CAMEL Black & White" => [96],
    "CAMEL Yellow & Brown" => [105, 127, 128, 129],
    "LD" => [94, 111],
    "MONTE CARLO" => [112, 113, 114],
    "CAMEL Yellow 100 (Gram)" => [0]
];

$spreadsheet = IOFactory::load($dosya);
$worksheet = $spreadsheet->getActiveSheet();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Winston Ürün Güncelleme</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        h2 { font-weight: 600; }
        .table td, .table th { vertical-align: middle; }
        .badge i { margin-right: 4px; }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="text-center mb-4">
        <h2 class="text-primary"><i class="bi bi-pencil-square me-2"></i> Winston Ürün Güncelleme</h2>
        <p class="text-muted">Excel dosyasındaki ürün isimleri temizlendi, fiyatlar kontrol edildi ve sistemle karşılaştırıldı.</p>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Ürün Adı</th>
                            <th>Önceki Fiyat</th>
                            <th>Yeni Fiyat</th>
                            <th>Durum</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
$sira = 1;
foreach ($worksheet->getRowIterator() as $satir) {
    $i = $satir->getRowIndex();
    $urun_adi_raw = trim($worksheet->getCell("A$i")->getValue());
    $urun_adi = preg_replace('/^\*+|\*+$/', '', $urun_adi_raw);

    $yeni_raw = trim($worksheet->getCell("B$i")->getValue());
    $yeni_clean = preg_replace('/[^0-9.,]/', '', $yeni_raw);
    $yeni_clean = str_replace(',', '.', $yeni_clean);
    $yeni_fiyat = (float) $yeni_clean;

    if (empty($urun_adi) || $yeni_fiyat <= 0) continue;

    $durum = "<span class='badge bg-secondary'><i class='bi bi-exclamation-circle'></i> Eşleşme yok</span>";
    $eski_fiyat = "-";

    if (isset($urunler[$urun_adi])) {
        $idler = (array)$urunler[$urun_adi];
        foreach ($idler as $id) {
            if ($id == 0) {
                $durum = "<span class='badge bg-secondary'><i class='bi bi-x-circle'></i> Veritabanında yok</span>";
                continue;
            }

            try {
                $stmt = $vt->prepare("SELECT sale_price FROM products WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $eski_fiyat_db = $stmt->fetchColumn();
                $eski_fiyat = $eski_fiyat_db;

                if ($eski_fiyat_db === false) {
                    $eski_fiyat = "-";
                    $durum = "<span class='badge bg-secondary'><i class='bi bi-x-circle'></i> Ürün bulunamadı</span>";
                    continue;
                }

                if ((float) $eski_fiyat_db == $yeni_fiyat) {
                    $durum = "<span class='badge bg-warning text-dark'><i class='bi bi-arrow-repeat'></i> Değişiklik yok</span>";
                    continue;
                }

                $stmt = $vt->prepare("UPDATE products SET sale_price = :price WHERE id = :id");
                $stmt->execute([':id' => $id, ':price' => $yeni_fiyat]);
                $durum = "<span class='badge bg-success'><i class='bi bi-check-circle'></i> Güncellendi</span>";
            } catch (PDOException $e) {
                $durum = "<span class='badge bg-danger'><i class='bi bi-exclamation-triangle'></i> Hata</span>";
            }
        }
    }

    echo "<tr>
            <td>$sira</td>
            <td><strong>$urun_adi</strong></td>
            <td class='text-danger'>₺$eski_fiyat</td>
            <td class='text-success fw-semibold'>₺$yeni_raw</td>
            <td>$durum</td>
          </tr>";
    $sira++;
}
?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="text-center mt-4">
        <a href="dosya_excel.php" class="btn btn-outline-primary d-inline-flex align-items-center">
            <i class="bi bi-arrow-left-circle-fill me-2 fs-5"></i> Başa Dön
        </a>
        <small class="text-muted d-block mt-2">Excel yükleme ekranına geri dön</small>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
