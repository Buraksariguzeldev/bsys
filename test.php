<?php
session_start();
require 'myproject/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

ini_set('memory_limit', '1024M');
ini_set('max_execution_time', 600);

include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti1.php');

try {
    $stmt = $vt->query("SELECT barcode, product_name, unit, sale_price, purchase_price FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Başlık
    $sheet->setCellValue('A1', 'Ürünler Dışa Aktar - BenimPOS');
    $sheet->mergeCells('A1:P1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

    // Sütun başlıkları
    $headers = [
        "Ürün barkodu", "Ürün adı", "Stok", "Birim", "Fiyat 1", "KDV", "Alış Fiyatı", 
        "Üst Ürün grubu", "Ürün grubu", "Fiyat 2", "Stok kodu", "Ürün detayı", 
        "Hızlı Liste Grubu", "Hızlı Liste Sırası", "Kritik Stok", "Menşei"
    ];
    $sheet->fromArray($headers, NULL, 'A2');

    // Ürün verileri
    $row = 3;
    foreach ($products as $product) {
        $salePrice = ($product['sale_price'] > 0) ? number_format($product['sale_price'], 2, ',', '') : ""; 
        $purchasePrice = ($product['purchase_price'] > 0) ? number_format($product['purchase_price'], 2, ',', '') : ""; 

        $sheet->setCellValueExplicit('A' . $row, $product['barcode'], DataType::TYPE_STRING);
        $sheet->setCellValue('B' . $row, $product['product_name']);
        $sheet->setCellValue('C' . $row, 0);
        $sheet->setCellValue('D' . $row, $product['unit']);
        $sheet->setCellValue('E' . $row, $salePrice);
        $sheet->setCellValue('F' . $row, 0);
        $sheet->setCellValue('G' . $row, $purchasePrice);
        $sheet->setCellValue('H' . $row, "");
        $sheet->setCellValue('I' . $row, "");
        $sheet->setCellValue('J' . $row, 0);
        $sheet->setCellValue('K' . $row, "");
        $sheet->setCellValue('L' . $row, "");
        $sheet->setCellValue('M' . $row, "");
        $sheet->setCellValue('N' . $row, 0);
        $sheet->setCellValue('O' . $row, 0);
        $sheet->setCellValue('P' . $row, "");

        $row++;
    }

    // Dosya çıktısı
    $fileName = "urunler_disa_aktar.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
} catch (Exception $e) {
    die("Hata oluştu: " . $e->getMessage());
}
?>