<?php
// Mevcut URL, port ve çalışma dizinini al
$siteURL = $_SERVER['HTTP_HOST']; // Örneğin: example.com veya localhost
$sitePort = $_SERVER['SERVER_PORT']; // Örneğin: 8001 veya 8003
$sitePath = trim($_SERVER['REQUEST_URI'], '/'); // Örneğin: modules/test.php veya modules/admin

// CSS dosyaları
$fontAwesome = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css';
$bootstrapURL = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css';
$mainCSS = '../../assets/src/css/main.css';

// CSS dosyalarını belirleme
$cssDosyalari = [];

// Eğer `localhost:8001` veya `buraksariguzeldev.wuaze.com` ise `main.css` yüklenecek
if ($sitePort == 8001 || $siteURL == 'buraksariguzeldev.wuaze.com') {
    $cssDosyalari[] = $mainCSS;
}
// Eğer `localhost:8003` veya `bsdsoft.wuaze.com` ise `bootstrap` yüklenecek
elseif ($sitePort == 8003 || $siteURL == 'bsdsoft.wuaze.com') {
    $cssDosyalari[] = $bootstrapURL;
    
    // Eğer `modules` klasöründeyse `main.css` de yüklenecek
    if (strpos($sitePath, 'modules') === 0) {
        $cssDosyalari[] = $mainCSS;
    }
} else {
    // Varsayılan olarak Bootstrap yüklenecek
    $cssDosyalari[] = $bootstrapURL;
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Meta taglar her durumda yüklenecek -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="icon" href="<?php echo site_url('img/bsys.png'); ?>" type="image/x-icon">

    <!-- Font Awesome her zaman yüklenecek -->
    <link rel="stylesheet" href="<?php echo $fontAwesome; ?>">

    <!-- CSS Dosyalarını Yükle -->
    <?php foreach ($cssDosyalari as $cssDosya): ?>
        <link rel="stylesheet" href="<?php echo filter_var($cssDosya, FILTER_VALIDATE_URL) ? $cssDosya : $cssDosya; ?>">
    <?php endforeach; ?>
</head>