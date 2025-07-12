php
<?php

require 'vendor/autoload.php'; // Composer autoloader

use Aws\Textract\TextractClient;
use Aws\Exception\AwsException;

// AWS yapılandırması (kimlik bilgileri ve bölge)
$config = [
    'version' => 'latest', // Veya belirli bir versiyon
    'region' => 'your-aws-region', // Örneğin, 'us-east-1'
    'credentials' => [
        'key'    => 'AKIAQXPZDFIREJNGLJU7',
        'secret' => 'OI90spEsx6l0csiW/simginYNvtlTYCl7vlmhzJP',
    ],
];

try {
    // Textract istemcisini oluşturma
    $textractClient = new TextractClient($config);

    // Görsel dosyasının yolu
    $imagePath = 'uploads/fiyat_listesi.jpg'; // Kullanıcının yüklediği görsel

    // Görsel dosyasını okuma
    $image = file_get_contents($imagePath);

    // AnalyzeDocument API çağrısı (metin ve formları çıkarmak için)
    $result = $textractClient->analyzeDocument([
        'Document' => [
            'Bytes' => $image,
        ],
        'FeatureTypes' => ['FORMS', 'TABLES'], // Metin, form ve tablo çıkarma
    ]);

    // API yanıtını işleme
    $blocks = $result->get('Blocks');

    // Burada $blocks dizisindeki verileri işleyerek ürün adları ve fiyatları çıkaracaksınız.
    // Textract yanıtı biraz detaylıdır ve blokları dolaşarak istediğiniz bilgiyi bulmanız gerekir.
    // Genellikle 'LINE' tipi bloklar metin satırlarını temsil eder.
    // 'KEY_VALUE_SET' ve 'TABLE' blokları ise yapılandırılmış verileri içerir.

    // Örnek: Sadece 'LINE' tipi blokları yazdırma
    foreach ($blocks as $block) {
        if ($block['BlockType'] === 'LINE') {
            echo $block['Text'] . "\n";
        }
    }

} catch (AwsException $e) {
    // Hata yakalama
    echo 'AWS Textract hatası: ' . $e->getMessage();
    echo 'AWS İstek Kimliği: ' . $e->getAwsRequestId();
    echo 'AWS Hata Kodu: ' . $e->getAwsErrorCode();
}

?>
