<?php
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');

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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-success text-white text-center">
                        <h3><i class="bi-person-plus-fill me-2"></i>Kayıt Ol</h3>
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

                            <?php
                            if (isset($error_message)) {
                                echo "<div class='text-danger text-center mb-3'><i class='bi-exclamation-circle me-1'></i>$error_message</div>";
                            }
                            if (isset($success_message)) {
                                echo "<div class='text-success text-center mb-3'><i class='bi-check-circle me-1'></i>$success_message</div>";
                            }
                            ?>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi-pencil-square me-1"></i>Kayıt Ol
                                </button>
                            </div>
                        </form>
                        <p class="text-center mt-3">
                            <i class="bi-box-arrow-in-right me-1"></i>
                            <a href="../girişislemleri/girisyap.php">Zaten hesabınız var mı? Giriş yapın</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>