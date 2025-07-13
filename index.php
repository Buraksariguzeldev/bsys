<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
?>

<!DOCTYPE html>
<html lang="tr">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>bsys | Ana Sayfa</title>
   <!-- Bootstrap CSS -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <!-- Bootstrap Icons -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
   <div class="container mt-5">
      <div class="row justify-content-center">
         <div class="col-md-8">
            <div class="card shadow">
               <div class="card-header bg-info text-white text-center">
                  <h3><i class="bi-house-door-fill me-2"></i>BSYS - Hoş Geldiniz</h3>
               </div>
               <div class="card-body text-center">
                  <?php if (isset($_SESSION['kullanici_adi'])): ?>
                     <p class="lead">Merhaba <strong><?= htmlspecialchars($_SESSION['kullanici_adi']) ?></strong>, sistemde oturumunuz açık 🎉</p>
                     <p>İşlemlerinize devam etmek için aşağıdaki bağlantıları kullanabilirsiniz:</p>
                     <div class="d-grid gap-2 col-6 mx-auto mt-4">
                        <a href="/profil.php" class="btn btn-outline-primary">
                           <i class="bi-person-fill me-1"></i> Profilim
                        </a>
                        <a href="<?php echo site_url('auth/cikisislemleri/cikisyap.php'); ?>" class="btn btn-outline-danger">
                           <i class="bi-box-arrow-right me-1"></i> Çıkış Yap
                        </a>
                     </div>
                  <?php else: ?>
                     <p class="lead">Sistemimize hoş geldiniz! 👋</p>
                     <p>Giriş yaparak daha fazla özelliğe erişebilirsiniz:</p>
                     <div class="d-grid gap-2 col-6 mx-auto mt-4">
                        <a href="/auth/girişislemleri/girisyap.php" class="btn btn-primary">
                           <i class="bi-box-arrow-in-right me-1"></i> Giriş Yap
                        </a>
                        <a href="/auth/kayitislemleri/kayitol.php" class="btn btn-success">
                           <i class="bi-person-plus-fill me-1"></i> Kayıt Ol
                        </a>
                     </div>
                  <?php endif; ?>
               </div>
               <div class="card-footer text-muted text-center">
                  <small><i class="bi-calendar2-day me-1"></i>
                  <?php
                      $formatter = new IntlDateFormatter('tr_TR', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
                      $formatter->setPattern('d MMMM yyyy EEEE');
                      echo $formatter->format(new DateTime());
                  ?>
                  </small>
               </div>
            </div>
         </div>
      </div>
   </div>
</body>
</html>
