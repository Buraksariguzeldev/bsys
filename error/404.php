<?php
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php'); // stil korunsun
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sayfa Bulunamadı</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9fc;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            flex-direction: column;
            text-align: center;
        }
        h1 {
            font-size: 5rem;
            font-weight: bold;
            color: #0d6efd;
        }
        p {
            font-size: 1.2rem;
            color: #6c757d;
        }
        .bi {
            font-size: 3rem;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div>
        <i class="bi bi-exclamation-triangle-fill mb-3"></i>
        <h1>404</h1>
        <p>Aradığınız sayfa bulunamadı.<br>Yönlendirme hatası ya da bağlantı geçersiz olabilir.</p>
        <a href="/" class="btn btn-primary mt-3"><i class="bi bi-arrow-left-circle me-1"></i> Ana Sayfaya Dön</a>
    </div>
</body>
</html>
