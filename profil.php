<?php
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/giriskontrol.php');

// Örnek kullanıcı verisi (gerçek sistemde $_SESSION'dan veya veritabanından alınır)
$kullanici_adi = $_SESSION['kullanici_adi'] ?? 'buraksariguzeldev';
function tarih_turkce($datetime) {
    $gunler = ['Sunday'=>'Pazar','Monday'=>'Pazartesi','Tuesday'=>'Salı','Wednesday'=>'Çarşamba','Thursday'=>'Perşembe','Friday'=>'Cuma','Saturday'=>'Cumartesi'];
    $aylar = ['January'=>'Ocak','February'=>'Şubat','March'=>'Mart','April'=>'Nisan','May'=>'Mayıs','June'=>'Haziran','July'=>'Temmuz',
             'August'=>'Ağustos','September'=>'Eylül','October'=>'Ekim','November'=>'Kasım','December'=>'Aralık'];

    return strtr(date("d F Y l H:i:s"), array_merge($gunler, $aylar));
}

$giris_zamani = tarih_turkce(date("d F Y l H:i:s"));

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profilim</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        h2 { font-weight: 600; }
        .profile-card { max-width: 500px; margin: auto; }
    </style>
</head>
<body>
<div class="container mt-5 profile-card">
    <div class="card shadow-sm border-0">
        <div class="card-body text-center">
            <i class="bi bi-person-circle display-4 text-primary mb-3"></i>
            <h2 class="text-dark">Profilim</h2>
            <p class="text-muted">Kullanıcı bilgileri ve oturum detayları</p>
            <hr>
            <table class="table table-bordered text-start align-middle">
                <tr>
                    <th><i class="bi bi-person-fill"></i> Kullanıcı Adı</th>
                    <td><?= htmlspecialchars($kullanici_adi) ?></td>
                </tr>
                <tr>
                    <th><i class="bi bi-clock-fill"></i> Giriş Zamanı</th>
                    <td><?= $giris_zamani ?></td>
                </tr>
                <tr>
                    <th><i class="bi bi-person-badge-fill"></i> Rol</th>
                    <td>Yönetici</td> <!-- Dinamik hale getirilebilir -->
                </tr>
                <tr>
                    <th><i class="bi bi-shield-lock-fill"></i> Güvenlik</th>
                    <td>Oturum aktif ✅</td>
                </tr>
            </table>
            <div class="mt-3">
                <a href="/parola_degistir.php" class="btn btn-outline-primary me-2"><i class="bi bi-lock-fill me-1"></i> Parola Değiştir</a>
                <a href="<?php echo site_url('auth/cikisislemleri/cikisyap.php'); ?>" class="btn btn-outline-danger"><i class="bi bi-box-arrow-right me-1"></i> Çıkış Yap</a>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
