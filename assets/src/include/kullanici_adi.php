  <?php
session_start();
// Kullanıcı girişi kontrolü
if (isset($_SESSION["kullanici_adi"])) {
    $kullanici_adi = htmlspecialchars($_SESSION["kullanici_adi"]);
    $kullanici_rol = isset($_SESSION["rol"]) ? htmlspecialchars($_SESSION["rol"]) : "";
} else {
    $kullanici_adi = "";
    $kullanici_rol = "";
}
?>
