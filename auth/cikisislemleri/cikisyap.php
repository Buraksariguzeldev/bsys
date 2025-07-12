<?php
session_start();
session_destroy(); // Oturumu sonlandır
header("Location: /index.php"); // Anasayfaya yönlendir
exit;
?>