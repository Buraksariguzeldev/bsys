<?php
ob_start();
session_start();
require '../../myproject/vendor/autoload.php'; // PhpSpreadsheet için
include_once($_SERVER['DOCUMENT_ROOT']  . '/assets/src/include/navigasyon.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/bsys/assets/src/include/giriskontrol.php');

use PhpOffice\PhpSpreadsheet\IOFactory;

// ...existing code...
include_once($_SERVER['DOCUMENT_ROOT']  . '/assets/src/config/vt_baglanti1.php');
// ...existing code...

?>

<?php
$islem = isset($_GET['islem']) ? $_GET['islem'] : (isset($_POST['islem']) ?
$_POST['islem'] : '');

if ($islem === 'ice_aktar') {
    $islemMetni = '<i class="bi bi-arrow-down-circle"></i> İçeri Aktarılıyor';
} elseif ($islem === 'disa_aktar') {
    $islemMetni = '<i class="bi bi-arrow-up-circle"></i> Dışa Aktarılıyor';
} else {
    $islemMetni = '<i class="bi bi-question-circle"></i> İşlem Belirtilmedi';
}
?>

<h3><?php echo $islemMetni; ?></h3>

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
            if (empty($barcode)) continue; // Barkodu olmayanları atla

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

                if ($old_price != $sale_price) {
                    $stmt = $vt->prepare($updateQuery);
                    $stmt->execute($updateParams);
                    $totalUpdated++;

                    $processedItems[] = [
                        'barcode' => $barcode,
                        'name' => $product_name,
                        'type' => 'Güncellendi',
                        'icon' => 'bi-arrow-repeat text-warning',
                        'old_price' => number_format($old_price, 2, ',', ''),
                        'new_price' => number_format($sale_price, 2, ',', '')
                    ];
                } else {
                    $totalNoChange++;

                    $processedItems[] = [
                        'barcode' => $barcode,
                        'name' => $product_name,
                        'type' => 'Değişiklik Yok',
                        'icon' => 'bi-dash-circle text-secondary',
                        'old_price' => number_format($old_price, 2, ',', ''),
                        'new_price' => number_format($sale_price, 2, ',', '')
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
                    'new_price' => number_format($sale_price, 2, ',', '')
                ];
                $new_id++;
            }
        }

        echo "<div class='alert alert-success mt-3'>
                <i class='bi-check-circle-fill'></i> İşlem tamamlandı! 
                <strong>$totalAdded</strong> yeni ürün eklendi, 
                <strong>$totalUpdated</strong> ürün güncellendi, 
                <strong>$totalNoChange</strong> üründe değişiklik yok.
              </div>";

        if (!empty($processedItems)) {
            echo "<table class='table table-bordered table-striped mt-3'>
                    <thead class='table-dark'>
                        <tr>
                            <th><i class='bi-upc-scan'></i> Barkod</th>
                            <th><i class='bi-box'></i> Ürün Adı</th>
                            <th><i class='bi-gear'></i> İşlem Türü</th>
                            <th><i class='bi-cash-coin'></i> Eski Fiyat</th>
                            <th><i class='bi-currency-dollar'></i> Yeni Fiyat</th>
                        </tr>
                    </thead>
                    <tbody>";

            foreach ($processedItems as $item) {
                echo "<tr>
                        <td>{$item['barcode']}</td>
                        <td>{$item['name']}</td>
                        <td><i class='{$item['icon']}'></i> {$item['type']}</td>
                        <td>{$item['old_price']} ₺</td>
                        <td>{$item['new_price']} ₺</td>
                      </tr>";
            }

            echo "</tbody></table>";
        }

    } catch (Exception $e) {
        echo "<div class='alert alert-danger'><i class='bi-exclamation-triangle-fill'></i> Hata oluştu: " . $e->getMessage() . "</div>";
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
            $barcode = trim($sheet->getCell("A$row")->getFormattedValue());
            $barcode = str_replace('"', '', $barcode);
            $barcode = preg_replace('/\s+/', '', $barcode);

            if (!empty($barcode)) {
                $stmt = $vt->prepare("SELECT sale_price, product_name FROM products WHERE barcode = ?");
                $stmt->execute([$barcode]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($product) {
                    $newPrice = floatval($product['sale_price']);
                    $formattedPrice = number_format($newPrice, 2, ',', '');
                    $oldExcelPrice = trim($sheet->getCell("E$row")->getValue());
                    $excelProductName = trim($sheet->getCell("B$row")->getValue());
                    $dbProductName = trim($product['product_name']);

                    $status = "Değişiklik Yok";
                    $icon = "bi-dash-circle text-secondary";
                    $rowColor = "table-light"; // Varsayılan renk

                    if ($excelProductName !== $dbProductName) {
                        $sheet->setCellValue("B$row", $dbProductName);
                        $status = "İsim Güncellendi";
                        $icon = "bi-pencil-square text-warning";
                        $rowColor = "table-warning";
                        $nameChanges++;
                    }

                    if ($oldExcelPrice !== $formattedPrice) {
                        $sheet->setCellValueExplicit("E$row", $formattedPrice, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                        $status = "Fiyat Güncellendi";
                        $icon = "bi-arrow-repeat text-orange";
                        $rowColor = "table-warning";
                        $priceChanges++;
                    }

                    if ($excelProductName !== $dbProductName && $oldExcelPrice !== $formattedPrice) {
                        $status = "Tam Güncellendi";
                        $icon = "bi-check-circle text-success";
                        $rowColor = "table-success";
                    }

                    if ($status === "Değişiklik Yok") {
                        $noChanges++;
                    }

                    $processedItems[] = [
                        'barcode' => $barcode,
                        'old_name' => $excelProductName,
                        'new_name' => $dbProductName,
                        'old_price' => $oldExcelPrice,
                        'new_price' => $formattedPrice,
                        'status' => $status,
                        'icon' => $icon,
                        'rowColor' => $rowColor
                    ];
                }
            }
        }

        $exportFileName = 'uploads/guncellenmis_fiyat_listesi.xlsx';
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($exportFileName);

        // **📢 Üst Mesajı Güncelle**
        echo "<div class='alert alert-info d-flex align-items-center' role='alert'>
                <i class='bi bi-info-circle-fill me-2'></i> 
                $nameChanges ürünün adı değişti, $priceChanges ürünün fiyatı değişti, $noChanges üründe değişiklik yok.
              </div>";

        // **📌 Tablolu Gösterim**
        if (!empty($processedItems)) {
            echo "<div class='table-responsive'>
                    <table class='table table-striped table-hover text-center mt-3'>
                        <thead class='table-dark'>
                            <tr>
                                <th><i class='bi bi-upc-scan'></i> Barkod</th>
                                <th><i class='bi bi-tag'></i> Eski Ürün Adı</th>
                                <th><i class='bi bi-pencil-square'></i> Yeni Ürün Adı</th>
                                <th><i class='bi bi-cash'></i> Eski Fiyat</th>
                                <th><i class='bi bi-currency-dollar'></i> Yeni Fiyat</th>
                                <th><i class='bi bi-info-circle'></i> Durum</th>
                            </tr>
                        </thead>
                        <tbody>";

            foreach ($processedItems as $item) {
                echo "<tr class='{$item['rowColor']}'>
                        <td>{$item['barcode']}</td>
                        <td>{$item['old_name']}</td>
                        <td class='fw-bold'>{$item['new_name']}</td>
                        <td class='text-danger'>{$item['old_price']} ₺</td>
                        <td class='text-success fw-bold'>{$item['new_price']} ₺</td>
                        <td><i class='bi {$item['icon']}'></i> {$item['status']}</td>
                      </tr>";
            }

            echo "</tbody>
                </table>
            </div>";
        }

        echo "<a href='$exportFileName' class='btn btn-success mt-3'>
                <i class='bi bi-file-earmark-excel'></i> Güncellenmiş Excel'i İndir
              </a>  <hr>";



    } catch (Exception $e) {
        echo "<div class='alert alert-danger'><i class='bi bi-exclamation-triangle-fill'></i> Dışa aktarma sırasında hata oluştu: {$e->getMessage()}</div>";
    }
}
?>

  <?php if (isset($dosyaAdi)): ?>
    <h3>
        <i class="bi bi-file-earmark-excel"></i> 
        Yüklenen Dosya: <?php echo htmlspecialchars($dosyaAdi, ENT_QUOTES, 'UTF-8'); ?>
    </h3>
<?php endif; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Excel İşlemleri</title>
</head>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Poppins', sans-serif;
    }
</style>
<body>
 

<hr>
<form method="post">
    <input type="hidden" name="islem" value="ice_aktar">
    <button type="submit" name="ice_aktar" class="btn btn-success">İçe Aktar</button>
</form>

<form method="post">
    <input type="hidden" name="islem" value="disa_aktar">
    <button type="submit" name="disa_aktar" class="btn btn-warning">Dışa Aktar (Fiyat Güncelle)</button>
</form>
    <a href="dosya_excel.php" class="btn btn-primary">
    <i class="bi bi-arrow-left-circle"></i> Başa Dön
</a>
</body>
</html>

<?php ob_end_flush(); ?>