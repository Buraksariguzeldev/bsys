<?php
ob_start();
session_start();

include ($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
require '../../myproject/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Excel dosyası oluştur
$spreadsheet = new Spreadsheet(); // veya IOFactory::load() ile oku
$sheet = $spreadsheet->getActiveSheet();

// Satır numarasını belirt
$row = 1; // örnek

// Stil array'i
$styleArray = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '28A745']]
];

// Stili uygula
$sheet->getStyle("A{$row}:F{$row}")->applyFromArray($styleArray);


// ...existing code...
include_once($_SERVER['DOCUMENT_ROOT']  . '/assets/src/config/vt_baglanti1.php');
// ...existing code...

?>

<?php
$islem = isset($_GET['islem']) ? $_GET['islem'] : (isset($_POST['islem']) ?
$_POST['islem'] : '');

$alertClass = 'alert-secondary'; // Varsayılan

if ($islem === 'ice_aktar') {
    $islemMetni = '<i class="bi bi-arrow-down-circle"></i> İçeri Aktarılıyor';
    $alertClass = 'alert-info';
} elseif ($islem === 'disa_aktar') {
    $islemMetni = '<i class="bi bi-arrow-up-circle"></i> Dışa Aktarılıyor';
    $alertClass = 'alert-warning';
} else {
    $islemMetni = '<i class="bi bi-question-circle"></i> İşlem Belirtilmedi';
    $alertClass = 'alert-secondary';
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel İşlemleri</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <!-- Bootstrap Icons CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="alert <?php echo $alertClass; ?> mb-4" role="alert"><?php echo $islemMetni; ?></div>

<?php
try {
    $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Bağlantı hatası: " . $e->getMessage());
}

$excelFilePath = '';

if (isset($_GET['dosya']) && !empty($_GET['dosya'])) {
    $dosyaAdi = basename($_GET['dosya']);
    $excelFilePath = 'uploads/' . $dosyaAdi;
    if (!file_exists($excelFilePath)) {
        die("Belirtilen dosya bulunamadı!");
    }
} elseif (isset($_FILES['excelFile']) && $_FILES['excelFile']['error'] == 0) {
    $dosyaAdi = $_FILES['excelFile']['name']; // Kullanıcının yüklediği dosyanın gerçek adı
    $excelFilePath = $_FILES['excelFile']['tmp_name'];
} else {
    die("Dosya yükleme hatası!");
}

if (isset($_POST['ice_aktar'])) {
    try {
        $spreadsheet = IOFactory::load($excelFilePath);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        $stmt = $vt->query("SELECT MAX(id) FROM products");
        $max_id = $stmt->fetchColumn();
        $new_id = $max_id ? $max_id + 1 : 1;

        $totalUpdated = 0;
        $totalAdded = 0;
        $totalNoChange = 0;
        $processedItems = [];

        foreach ($sheetData as $index => $row) {
            if ($index < 2) continue; // Başlıkları atla

            $barcode = isset($row[0]) ? trim($row[0], '"') : '';
            if (empty($barcode)) continue;

            $product_name = !empty($row[1]) ? $row[1] : 'Bilinmeyen Ürün';
            $unit = !empty($row[3]) ? $row[3] : 'Birim';
            $sale_price = !empty($row[4]) ? floatval(str_replace(',', '.', $row[4])) : 0;
            $purchase_price = !empty($row[6]) ? floatval(str_replace(',', '.', $row[6])) : 0;
            $profit_margin = !empty($row[7]) ? floatval(str_replace(',', '.', $row[7])) : 0;

            $stmt = $vt->prepare("SELECT * FROM products WHERE barcode = ?");
            $stmt->execute([$barcode]);
            $existing_product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing_product) {
                $old_price = floatval($existing_product['sale_price']);
                $updateQuery = "UPDATE products SET sale_price = ?";
                $updateParams = [$sale_price];

                if ($existing_product['product_name'] != $product_name) {
                    $updateQuery .= ", product_name = ?";
                    $updateParams[] = $product_name;
                }

                $updateQuery .= " WHERE barcode = ?";
                $updateParams[] = $barcode;

                if ($old_price != $sale_price || $existing_product['product_name'] != $product_name) {
                    $stmt = $vt->prepare($updateQuery);
                    $stmt->execute($updateParams);
                    $totalUpdated++;

                    $processedItems[] = [
                        'barcode' => $barcode,
                        'name' => $product_name,
                        'type' => 'Güncellendi',
                        'icon' => 'bi-arrow-repeat text-warning',
                        'old_price' => number_format($old_price, 2, ',', ''),
                        'new_price' => number_format($sale_price, 2, ',', ''),
                        'rowColor' => 'table-warning'
                    ];
                } else {
                    $totalNoChange++;

                    $processedItems[] = [
                        'barcode' => $barcode,
                        'name' => $product_name,
                        'type' => 'Değişiklik Yok',
                        'icon' => 'bi-dash-circle text-secondary',
                        'old_price' => number_format($old_price, 2, ',', ''),
                        'new_price' => number_format($sale_price, 2, ',', ''),
                        'rowColor' => 'table-light'
                    ];
                }
            } else {
                $stmt = $vt->prepare("INSERT INTO products (id, barcode, product_name, unit, purchase_price, sale_price, profit_margin) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$new_id, $barcode, $product_name, $unit, $purchase_price, $sale_price, $profit_margin]);
                $totalAdded++;

                $processedItems[] = [
                    'barcode' => $barcode,
                    'name' => $product_name,
                    'type' => 'Eklendi',
                    'icon' => 'bi-plus-circle text-success',
                    'old_price' => '0,00',
                    'new_price' => number_format($sale_price, 2, ',', ''),
                    'rowColor' => 'table-success'
                ];
                $new_id++;
            }
        }

        echo "<div class='alert alert-success mt-3 d-flex align-items-center'>
                <i class='bi bi-check-circle-fill me-2'></i> 
                <strong>İçeri aktarma tamamlandı:</strong> 
                $totalAdded yeni ürün eklendi, 
                $totalUpdated ürün güncellendi, 
                $totalNoChange üründe değişiklik yok.
              </div>";

        if (!empty($processedItems)) {
            echo "<div class='table-responsive mt-3'>
                    <table class='table table-bordered table-hover text-center'>
                        <thead class='table-dark'>
                            <tr>
                                <th><i class='bi bi-upc-scan'></i> Barkod</th>
                                <th><i class='bi bi-box'></i> Ürün Adı</th>
                                <th><i class='bi bi-tools'></i> İşlem Türü</th>
                                <th><i class='bi bi-cash'></i> Eski Fiyat</th>
                                <th><i class='bi bi-currency-exchange'></i> Yeni Fiyat</th>
                            </tr>
                        </thead>
                        <tbody>";
            foreach ($processedItems as $item) {
                echo "<tr class='{$item['rowColor']}'>
                        <td>{$item['barcode']}</td>
                        <td><strong>{$item['name']}</strong></td>
                        <td><i class='{$item['icon']}'></i> {$item['type']}</td>
                        <td class='text-danger'>{$item['old_price']} ₺</td>
                        <td class='text-success fw-semibold'>{$item['new_price']} ₺</td>
                      </tr>";
            }
            echo "</tbody></table></div>";
        }

    } catch (Exception $e) {
        echo "<div class='alert alert-danger mt-3'><i class='bi bi-exclamation-triangle-fill'></i> Hata oluştu: " . $e->getMessage() . "</div>";
    }
}


if (isset($_POST['disa_aktar'])) {
    try {
        $spreadsheet = IOFactory::load($excelFilePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rowCount = $sheet->getHighestRow();

        $processedItems = [];
        $nameChanges = 0;
        $priceChanges = 0;
        $noChanges = 0;

        for ($row = 3; $row <= $rowCount; $row++) {
            $barcode = preg_replace('/\s+/', '', str_replace('"', '', trim($sheet->getCell("A$row")->getFormattedValue())));

            if (!empty($barcode)) {
                $stmt = $vt->prepare("SELECT sale_price, product_name FROM products WHERE barcode = ?");
                $stmt->execute([$barcode]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($product) {
                    $newPrice = number_format(floatval($product['sale_price']), 2, ',', '');
                    $oldExcelPrice = trim($sheet->getCell("E$row")->getValue());
                    $excelProductName = trim($sheet->getCell("B$row")->getValue());
                    $dbProductName = trim($product['product_name']);

                    $status = "Değişiklik Yok";
                    $icon = "bi-dash-circle text-secondary";
                    $rowColor = "table-light";

                    $isNameDifferent = $excelProductName !== $dbProductName;
                    $isPriceDifferent = $oldExcelPrice !== $newPrice;

                    if ($isNameDifferent) {
                        $sheet->setCellValue("B$row", $dbProductName);
                        $status = "İsim Güncellendi";
                        $icon = "bi-pencil-square text-warning";
                        $rowColor = "table-warning";
                        $nameChanges++;
                    }

                    if ($isPriceDifferent) {
                        $sheet->setCellValueExplicit("E$row", $newPrice, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                        $status = "Fiyat Güncellendi";
                        $icon = "bi-arrow-repeat text-orange";
                        $rowColor = "table-warning";
                        $priceChanges++;
                    }

                    if ($isNameDifferent && $isPriceDifferent) {
                        $status = "Tam Güncellendi";
                        $icon = "bi-check-circle-fill text-success";
                        $rowColor = "table-success";
                    }

                    if (!$isNameDifferent && !$isPriceDifferent) {
                        $noChanges++;
                    }

                    $processedItems[] = compact('barcode', 'excelProductName', 'dbProductName', 'oldExcelPrice', 'newPrice', 'status', 'icon', 'rowColor');
                }
            }
        }

        $exportFileName = 'uploads/guncellenmis_fiyat_listesi.xlsx';
        IOFactory::createWriter($spreadsheet, 'Xlsx')->save($exportFileName);

        echo "<div class='alert alert-info d-flex align-items-center mt-4'>
                <i class='bi bi-info-circle-fill me-2'></i> 
                <strong>Özet:</strong> $nameChanges isim, $priceChanges fiyat değişti, $noChanges üründe değişiklik yok.
              </div>";

        // Tablo çıktısı
        if (!empty($processedItems)) {
            echo "<div class='table-responsive mt-3'>
                    <table class='table table-bordered table-hover text-center'>
                        <thead class='table-dark'>
                            <tr>
                                <th><i class='bi bi-upc-scan'></i> Barkod</th>
                                <th><i class='bi bi-tag'></i> Eski Ad</th>
                                <th><i class='bi bi-pencil'></i> Yeni Ad</th>
                                <th><i class='bi bi-cash'></i> Eski Fiyat</th>
                                <th><i class='bi bi-currency-exchange'></i> Yeni Fiyat</th>
                                <th><i class='bi bi-info-circle'></i> Durum</th>
                            </tr>
                        </thead>
                        <tbody>";

            foreach ($processedItems as $item) {
                echo "<tr class='{$item['rowColor']}'>
                        <td>{$item['barcode']}</td>
                        <td>{$item['excelProductName']}</td>
                        <td><strong>{$item['dbProductName']}</strong></td>
                        <td class='text-danger'>{$item['oldExcelPrice']} ₺</td>
                        <td class='text-success fw-semibold'>{$item['newPrice']} ₺</td>
                        <td><i class='bi {$item['icon']}'></i> {$item['status']}</td>
                      </tr>";
            }

            echo "</tbody>
                </table>
            </div>";
        }

        echo "<a href='$exportFileName' class='btn btn-outline-success mt-3'>
                <i class='bi bi-file-earmark-excel'></i> Güncellenmiş Dosyayı İndir
              </a><hr>";

    } catch (Exception $e) {
        echo "<div class='alert alert-danger mt-3'><i class='bi bi-exclamation-triangle-fill'></i> Hata: {$e->getMessage()}</div>";
    }
}

?>

<?php if (isset($dosyaAdi)): ?>
    <div class="alert alert-secondary d-flex align-items-center mb-4">
        <i class="bi bi-file-earmark-excel-fill me-2 fs-5"></i>
        <strong>Yüklenen Dosya:</strong> <?= htmlspecialchars($dosyaAdi, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<!-- İŞLEM BUTONLARI -->
<div class="card p-4 shadow-sm mb-4">
    <h4 class="mb-3">
        <i class="bi bi-arrow-repeat me-2 text-primary"></i> Excel İşlemleri
    </h4>
    <div class="d-flex flex-wrap gap-2">
        <form method="post">
            <input type="hidden" name="islem" value="ice_aktar">
            <button type="submit" name="ice_aktar" class="btn btn-success">
                <i class="bi bi-upload me-1"></i> İçe Aktar
            </button>
        </form>

     <form method="post">
    <input type="hidden" name="islem" value="disa_aktar">
    <button type="submit" name="disa_aktar" class="btn btn-warning">
        <i class="bi bi-arrow-bar-down me-1"></i> Dışa Aktar (Fiyat Güncelle)
    </button>
</form>


     <a href="dosya_excel.php" class="btn btn-outline-primary d-inline-flex align-items-center">
    <i class="bi bi-arrow-left-circle-fill me-2 fs-5"></i> 
    <span style="font-weight: 500;">Başa Dön</span>
</a>

    </div>
</div>

</html>

<?php ob_end_flush(); ?>