<?php

// navigasyon 
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
// giriş kontrol
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/giriskontrol.php');

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <title>İmport Excel Hosting</title>

</head>
<body>

<div class="container mt-5">
    <h2>Excel Dosyası Yükle</h2>

    <form action="import_process.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <input type="file" id="excelFile" name="excelFile" class="form-control-file" accept=".xlsx,.xls" required>
        </div>
        <button type="submit" id="uploadBtn" class="btn btn-primary mt-2" disabled>Yükle</button>
    </form>
</div>

<?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>

<script>
    document.getElementById("excelFile").addEventListener("change", function() {
        var uploadBtn = document.getElementById("uploadBtn");
        uploadBtn.disabled = !this.files.length; // Dosya seçildiyse butonu aktif et
    });
</script>

</body>
</html>