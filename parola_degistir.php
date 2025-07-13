<?php
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/giriskontrol.php');
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Parola Değiştir</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f9f9fc; }
        .container { max-width: 500px; }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="text-center text-primary"><i class="bi bi-key-fill me-2"></i> Parola Değiştir</h3>
            <p class="text-muted text-center">Güvenliğinizi sağlamak için güçlü bir parola kullanın.</p>
            <form action="parola_kaydet.php" method="post">
                <div class="mb-3">
                    <label for="eski_parola" class="form-label">Mevcut Parola</label>
                    <input type="password" class="form-control" id="eski_parola" name="eski_parola" required>
                </div>
                <div class="mb-3">
                    <label for="yeni_parola" class="form-label">Yeni Parola</label>
                    <input type="password" class="form-control" id="yeni_parola" name="yeni_parola" required>
                </div>
                <div class="mb-3">
                    <label for="yeni_parola_tekrar" class="form-label">Yeni Parola (Tekrar)</label>
                    <input type="password" class="form-control" id="yeni_parola_tekrar" name="yeni_parola_tekrar" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-shield-lock-fill me-1"></i> Parolayı Güncelle
                </button>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
