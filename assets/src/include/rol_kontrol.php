<?php
// rol_kontrol.php

function rol_kontrol($gerekli_rol_id) {
    // Geri dönüş URL'sini belirle
    $geri_donus = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../../index.php';

    if (!isset($_SESSION['kullanici_adi'])) {
        // Kullanıcı girişi yoksa
        echo '<!DOCTYPE html>
        <html lang="tr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Erişim Reddedildi</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                    background-color: #f5f5f5;
                }
                .hata-kutusu {
                    text-align: center;
                    padding: 30px;
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                .hata-kutusu h1 {
                    color: #e74c3c;
                }
                .buton {
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #3498db;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    margin-top: 20px;
                }
                .buton:hover {
                    background-color: #2980b9;
                }
                .butonlar {
                    display: flex;
                    gap: 10px;
                    justify-content: center;
                }
            </style>
        </head>
        <body>
            <div class="hata-kutusu">
                <h1>Erişim Reddedildi</h1>
                <p>Bu sayfaya erişmek için giriş yapmanız gerekmektedir.</p>
                <div class="butonlar">
                    <a href="../../login.php" class="buton">Giriş Yap</a>
                    <a href="' . htmlspecialchars($geri_donus) . '" class="buton">Geri Dön</a>
                </div>
            </div>
        </body>
        </html>';
        exit();
    }

    try {
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');

        $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $kullanici_adi = $_SESSION['kullanici_adi'];
        $stmt = $vt->prepare("SELECT rol_id FROM rollerplus WHERE kullanici_id = (SELECT id FROM kullanicilar WHERE kullanici_adi = :kullanici_adi)");
        $stmt->bindValue(':kullanici_adi', $kullanici_adi, PDO::PARAM_STR);
        $stmt->execute();
        $roller = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!in_array($gerekli_rol_id, $roller)) {
            // Yetersiz yetki durumunda
            echo '<!DOCTYPE html>
            <html lang="tr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Yetersiz Yetki</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        margin: 0;
                        background-color: #f5f5f5;
                    }
                    .hata-kutusu {
                        text-align: center;
                        padding: 30px;
                        background: white;
                        border-radius: 8px;
                        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    }
                    .hata-kutusu h1 {
                        color: #e74c3c;
                    }
                    .buton {
                        display: inline-block;
                        padding: 10px 20px;
                        background-color: #3498db;
                        color: white;
                        text-decoration: none;
                        border-radius: 5px;
                        margin-top: 20px;
                    }
                    .buton:hover {
                        background-color: #2980b9;
                    }
                </style>
            </head>
            <body>
                <div class="hata-kutusu">
                    <h1>Yetersiz Yetki</h1>
                    <p>Bu sayfaya erişmek için gerekli yetkiye sahip değilsiniz.</p>
                    <a href="' . htmlspecialchars($geri_donus) . '" class="buton">Geri Dön</a>
                </div>
            </body>
            </html>';
            exit();
        }

    } catch (PDOException $e) {
        error_log("Veritabanı işlemi sırasında hata: " . $e->getMessage());
        echo '<!DOCTYPE html>
        <html lang="tr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Sistem Hatası</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                    background-color: #f5f5f5;
                }
                .hata-kutusu {
                    text-align: center;
                    padding: 30px;
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                .hata-kutusu h1 {
                    color: #e74c3c;
                }
                .buton {
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #3498db;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    margin-top: 20px;
                }
                .buton:hover {
                    background-color: #2980b9;
                }
            </style>
        </head>
        <body>
            <div class="hata-kutusu">
                <h1>Sistem Hatası</h1>
                <p>Üzgünüz, bir hata oluştu. Lütfen daha sonra tekrar deneyin.</p>
                <a href="' . htmlspecialchars($geri_donus) . '" class="buton">Geri Dön</a>
            </div>
        </body>
        </html>';
        exit();
    }
}
?>
