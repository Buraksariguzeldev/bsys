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
    "KENT SWITCH - DARK BLUE" => [136],
    "KENT D RANGE - BLUE, GREY" => [116, 117],
    "KENT D RANGE - BLUE LONG, GREY LONG" => [118],
    "KENT - DARK BLUE, DARK BLUE LONG, SWITCH" => [132],
    "KENT - BLUE, WHITE" => [109],
    "KENT SLIMS LONG - BLUE, GREY" => [135],
    "KENT SLIMS - BLACK, GREY" => [119, 120],
    "ROTHMANS - TÜM ÜRÜNLER" => [121, 112],
    "TEKEL 2000 - TÜM ÜRÜNLER" => [123, 124, 125, 126],
    "TEKEL 2001 - TÜM ÜRÜNLER" => [131],
    "VICEROY - TÜM ÜRÜNLER" => [0],
    "PALL MALL - TÜM ÜRÜNLER" => [0],
    "MALTEPE & SAMSUN -" => [110]
];

$spreadsheet = IOFactory::load($dosya);
$worksheet = $spreadsheet->getActiveSheet();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>KENT Ürün Güncelleme</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f9f9fc; }
        h2 { font-weight: 600; }
        .badge i { margin-right: 4px; }
        .table td, .table th { vertical-align: middle; }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="text-center mb-4">
        <h2 class="text-primary"><i class="bi bi-pencil-square me-2"></i>KENT Ürün Güncelleme</h2>
        <p class="text-muted">Excel dosyasından alınan yeni fiyatlar sistemle karşılaştırıldı ve gerekirse güncelleme yapıldı.</p>
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
    $a = trim($worksheet->getCell("A$i")->getFormattedValue());
    $b_raw = trim($worksheet->getCell("B$i")->getFormattedValue());

    if (str_contains(strtoupper($b_raw), 'TÜM ÜRÜNLER')) {
        $b_clean = $b_raw;
    } else {
        // Çift virgülleri ve fazladan boşlukları temizle
        $b_temp = str_replace([',,', ', ,'], ',', $b_raw);
        $b_temp = preg_replace('/\s*,\s*/', ', ', $b_temp);
        $b_clean = $b_temp;
    }

    $birlesik_ad = trim($a . ' - ' . $b_clean);

    // Yeni fiyatı oku ve temizle
    $fiyat_raw = trim($worksheet->getCell("C$i")->getFormattedValue());
    $fiyat_raw = mb_strtolower($fiyat_raw);
    $fiyat_raw = str_replace(['₺', 'tl', ' '], '', $fiyat_raw);
    $fiyat_clean = preg_replace('/[^\d.,]/u', '', $fiyat_raw);
    $fiyat_clean = str_replace(',', '.', $fiyat_clean);
    $yeni_fiyat = round((float)$fiyat_clean, 2);

    // Eğer ürün eşleşmezse bile göster, durum farklı olsun
    if (!isset($urunler[$birlesik_ad])) {
        $durum = "<span class='badge bg-secondary'><i class='bi bi-exclamation-circle'></i> Eşleşme yok</span>";
        $eski_fiyat = "-";
    } else {
        $idler = (array)$urunler[$birlesik_ad];
        foreach ($idler as $id) {
            if ($id == 0) {
                $durum = "<span class='badge bg-secondary'><i class='bi bi-x-circle'></i> Veritabanında yok</span>";
                $eski_fiyat = "-";
            } else {
                try {
                    $stmt = $vt->prepare("SELECT sale_price FROM products WHERE id = :id");
                    $stmt->execute([':id' => $id]);
                    $eski_fiyat_db = $stmt->fetchColumn();

                    $eski_fiyat_raw = strval($eski_fiyat_db);
                    $eski_fiyat_clean = preg_replace('/[^\d.,]/', '', $eski_fiyat_raw);
                    $eski_fiyat_clean = str_replace(',', '.', $eski_fiyat_clean);
                    $eski_fiyat = round((float)$eski_fiyat_clean, 2);

                    if ($eski_fiyat_db === false) {
                        $durum = "<span class='badge bg-secondary'><i class='bi bi-x-circle'></i> Ürün bulunamadı</span>";
                        $eski_fiyat = "-";
                    } elseif (abs($eski_fiyat - $yeni_fiyat) < 0.01) {
                        $durum = "<span class='badge bg-warning text-dark'><i class='bi bi-arrow-repeat'></i> Değişiklik yok</span>";
                    } else {
                        $stmt = $vt->prepare("UPDATE products SET sale_price = :price WHERE id = :id");
                        $stmt->execute([':id' => $id, ':price' => $yeni_fiyat]);
                        $durum = "<span class='badge bg-success'><i class='bi bi-check-circle'></i> Güncellendi</span>";
                    }
                } catch (PDOException $e) {
                    $durum = "<span class='badge bg-danger'><i class='bi bi-exclamation-triangle'></i> Hata</span>";
                    $eski_fiyat = "-";
                }
            }
        }
    }

    $eski_goster = ($eski_fiyat === "-") ? "-" : "₺" . number_format($eski_fiyat, 2, ',', '.');
    $yeni_goster = "₺" . number_format($yeni_fiyat, 2, ',', '.');

    echo "<tr>
            <td>$sira</td>
            <td><strong>$birlesik_ad</strong></td>
            <td class='text-danger'>$eski_goster</td>
            <td class='text-success fw-semibold'>$yeni_goster</td>
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
