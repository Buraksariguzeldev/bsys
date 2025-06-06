<?php 

// navigasyon 
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
// giriş kontrol
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/giriskontrol.php');

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel Yükle</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body class="container mt-5">

<a href="../../excel/urunlistesiexcel.php" class="btn btn-outline-primary">
    <i class="bi bi-file-earmark-excel"></i> Ürün Listesi
</a>

<div class="card p-4 mt-3">
    <h3 class="mb-3">
        <i class="bi bi-file-earmark-arrow-up"></i> Excel Dosyası Yükle
    </h3>
    <form action="yukle.php" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <input type="file" class="form-control" name="excelDosya" accept=".xls,.xlsx" required>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-upload"></i> Yükle ve İşle
        </button>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>