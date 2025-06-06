<?php

if (!isset($_SESSION['kullanici_adi']) || empty($_SESSION['kullanici_adi'])) {
    // Navigasyonu göster
    
    echo '<style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
        }
        .uyari {
            text-align: center;
            font-size: 18px;
            padding: 20px;
            background: #ffc107;
            border: 1px solid #ffa000;
            border-radius: 5px;
            padding: 20px;
        }
    </style>';
    
    echo '<div class="uyari">
        <i class="fas fa-exclamation-circle"></i> Sitemize giriş yapmalısınız.
    </div>';
    
    exit; // Sayfanın geri kalanını tamamen durdur
}
?>