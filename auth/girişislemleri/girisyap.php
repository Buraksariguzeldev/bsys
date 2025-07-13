<?php
ob_start();
// navigasyon 
include_once($_SERVER['DOCUMENT_ROOT']  . '/assets/src/include/navigasyon.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');


?>

<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php'); //MySQL bağlantısını sağlayan dosya

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kullanici_adi = $_POST['username'];
    $password = $_POST['password'];

    // Kullanıcı bilgilerini kontrol et
    $stmt = $vt->prepare("SELECT id, password, status FROM bsys_kullanici WHERE username = ?");
    $stmt->bindParam(1, $kullanici_adi);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($user['status'] === 'pending') {
            $error_message = "Hesabınız henüz onaylanmadı.";
        } elseif ($user['status'] === 'rejected') {
            $error_message = "Hesabınız reddedildi.";
        } elseif (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['kullanici_adi'] = $kullanici_adi;
            header("Location: ../../index.php");
            exit();
        } else {
            $error_message = "Şifre yanlış.";
        }
    } else {
        $error_message = "Kullanıcı bulunamadı.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>

    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
 <div class="container mt-5">
 <div class="row justify-content-center">
 <div class="col-md-6">
 <div class="card shadow-sm">
 <div class="card-header bg-primary text-white text-center">
     <h3><i class="bi-person-circle me-2"></i>Giriş Yap</h3>
 </div>
 <div class="card-body">
 <form method="POST">
 <div class="mb-3">
     <label for="username" class="form-label">
         <i class="bi-person-fill me-1"></i>Kullanıcı Adı:
     </label>
     <input type="text" class="form-control" id="username" name="username" required>
 </div>
 <div class="mb-3">
     <label for="password" class="form-label">
         <i class="bi-lock-fill me-1"></i>Şifre:
     </label>
     <input type="password" class="form-control" id="password" name="password" required>
 </div>
 <button type="submit" class="btn btn-primary w-100">
     <i class="bi-box-arrow-in-right me-2"></i>Giriş Yap
 </button>
 </form>
 </div>
 <div class="card-footer text-center">
     <i class="bi-question-circle me-1"></i>
     <a href="../kayitislemleri/kayitol.php">Henüz kayıt olmadınız mı?</a>
 </div>
 </div>
 </div>
 </div>
 </div>

 <?php
 if (isset($error_message)) {
     echo "<p class='text-danger mt-3 text-center'><i class='bi-exclamation-circle me-1'></i>$error_message</p>";
 }
 ?>
</body>

</html>