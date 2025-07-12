<?php

// navigasyon 
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');

?>

<?php
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php'); //MySQL bağlantısını sağlayan dosya

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kullanıcı adı kontrolü
    $stmt = $vt->prepare("SELECT COUNT(*) FROM bsys_kullanici WHERE username = ?");
    $stmt->bindParam(1, $username);
    $stmt->execute();
    $userExists = $stmt->fetchColumn();

    if ($userExists > 0) {
        $error_message = "Bu kullanıcı adı zaten kullanılıyor.";
    } else {
        // Şifreyi hash'le ve kaydı yap
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $vt->prepare("INSERT INTO bsys_kullanici (username, password, status) VALUES (?, ?, 'pending')");
        $stmt->bindParam(1, $username);
        $stmt->bindParam(2, $hashed_password);
        if ($stmt->execute()) {
            $success_message = "Kaydınız alınmıştır. Admin onayından sonra giriş yapabilirsiniz.";
        } else {
            $error_message = "Kayıt işlemi sırasında bir hata oluştu.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
</head>
<body>
    <h3>Kayıt Ol</h3>
    <form method="POST">
        <label for="username">Kullanıcı Adı:</label>
        <input type="text" name="username" required><br>

        <label for="password">Şifre:</label>
        <input type="password" name="password" required><br>

        <button type="submit">Kayıt Ol</button>
    </form>
<a href="../girişislemleri/girisyap.php">zaten giris yaptinizmi </a>
    <?php
    if (isset($error_message)) {
        echo "<p style='color:red;'>$error_message</p>";
    }
    if (isset($success_message)) {
        echo "<p style='color:green;'>$success_message</p>";
    }
    ?>
</body>
</html>