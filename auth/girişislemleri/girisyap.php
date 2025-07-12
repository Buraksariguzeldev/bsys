<?php
ob_start();
// navigasyon 
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');

?>

<?php

include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php'); //MySQL bağlantısını sağlayan dosya

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
</head>
<body>
    <h3>Giriş Yap</h3>
    <form method="POST">
        <label for="username">Kullanıcı Adı:</label>
        <input type="text" name="username" required><br>

        <label for="password">Şifre:</label>
        <input type="password" name="password" required><br>

        <button type="submit">Giriş Yap</button>
    </form>
<a href="../kayitislemleri/kayitol.php">henüz kayit olmadinizmi</a>
    <?php
    if (isset($error_message)) {
        echo "<p style='color:red;'>$error_message</p>";
    }
    ?>
</body>
</html>