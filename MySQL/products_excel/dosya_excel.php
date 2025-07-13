<?php 
// Navigasyon ve giriş kontrolü
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/giriskontrol.php');
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel Dosyası Yükle</title>
    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <!-- Başlık ve Navigasyon -->
        <div class="row justify-content-center mb-4">
            <div class="col-md-8 text-center">
                <h2><i class="bi bi-file-earmark-excel-fill text-success me-2"></i>Excel Dosyası Yükleme</h2>
                <p class="text-muted">Lütfen .xls veya .xlsx biçiminde dosyanızı seçip yükleyin</p>
                <a href="../../excel/urunlistesiexcel.php" class="btn btn-outline-success mt-2">
                    <i class="bi bi-box-arrow-down me-1"></i> Ürün Listesini İndir
                </a>
            </div>
        </div>

        <!-- Form Kartı -->
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h4 class="card-title mb-4"><i class="bi bi-upload me-2"></i>Dosya Seç ve Gönder</h4>
                        <form action="yukle.php" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="excelDosya" class="form-label">
                                    <i class="bi bi-file-earmark-arrow-up-fill me-1"></i> Excel Dosyanız:
                                </label>
                                <input type="file" class="form-control" name="excelDosya" id="excelDosya" accept=".xls,.xlsx" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-arrow-right-circle me-1"></i> Yükle ve İşle
                            </button>
                        </form>
                    </div>
                    <div class="card-footer text-muted text-center">
                        <small><i class="bi bi-calendar2-day me-1"></i>
                        <?php
                            $aylar = [
                                'January' => 'Ocak', 'February' => 'Şubat', 'March' => 'Mart', 'April' => 'Nisan',
                                'May' => 'Mayıs', 'June' => 'Haziran', 'July' => 'Temmuz', 'August' => 'Ağustos',
                                'September' => 'Eylül', 'October' => 'Ekim', 'November' => 'Kasım', 'December' => 'Aralık'
                            ];
                            $gunler = [
                                'Monday' => 'Pazartesi', 'Tuesday' => 'Salı', 'Wednesday' => 'Çarşamba',
                                'Thursday' => 'Perşembe', 'Friday' => 'Cuma', 'Saturday' => 'Cumartesi', 'Sunday' => 'Pazar'
                            ];
                            $parcalar = explode(' ', date('d F Y l'));
                            echo $parcalar[0] . ' ' . $aylar[$parcalar[1]] . ' ' . $parcalar[2] . ' ' . $gunler[$parcalar[3]];
                        ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
