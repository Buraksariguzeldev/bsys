<?php 
session_start();
include("styles.php");
include("header.php");
include 'kullanici_adi.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>

<!DOCTYPE html>
<html lang="tr">

<body>

<div id="bsd-main">
  <span class="bsd-openbtn" onclick="toggleNav()">&#9776;</span>
  
  <div class="bsd-middle-section">
    <a href="<?php echo site_url(); ?>">
      <img src="<?php echo site_url('img/bsys.png'); ?>" alt="Logo" class="bsd-logo">
    </a>
  </div>

  <div class="bsd-right-section">
    <?php if (!$kullanici_adi): ?>
      <a href="<?php echo site_url('../../../auth/girişislemleri/girisyap.php'); ?>" class="bsd-login-icon"><i class="fas fa-user"></i></a>
    <?php else: ?>
      <a href="<?php echo site_url('auth/cikisislemleri/cikisyap.php'); ?>" class="bsd-logout-icon"><i class="fas fa-sign-out-alt"></i></a>
    <?php endif; ?>
  </div>
</div>

<div id="bsd-mySidebar" class="bsd-sidebar">
  <span class="bsd-closebtn" onclick="closeNav()">&times;</span>

  <img src="<?php echo site_url('img/bsys.png'); ?>" alt="Sidebar Logo" class="bsd-sidebar-logo">

  <div class="bsd-datetime" id="bsd-datetime"></div>

  <?php if (isset($kullanici_adi) && $kullanici_adi): ?>
    <div class="bsd-welcome">Hoş geldiniz <span class="bsd-hys-kullanici-adi"><?php echo htmlspecialchars($kullanici_adi); ?></span></div>
  <?php endif; ?>

  <?php
  // Güvenlik ve fallback
  if (!isset($menu_items) || !is_array($menu_items)) {
      $menu_items = [];
  }

  $current_page = isset($current_page) ? $current_page : 'ana_sayfa';

  function site_url($yol = '') {
      $server_name = $_SERVER['SERVER_NAME'];
      $base_url = ($server_name == 'localhost' || $server_name == 'bsys.wuaze.com') ? '/' : '';
      return $base_url . ltrim($yol, '/');
  }

  $show_menu = !in_array($current_page, ['giris', 'kayit', 'sifre_sifirlama']);

  // Admin menüsü
  if (isset($kullanici_adi) && $kullanici_adi === "buraksariguzeldev") {
      $menu_items['Admin'] = [
          'url' => 'auth/adminislemleri/adminpanel.php',
          'icon' => 'fas fa-user-shield',
          'text' => 'Admin panel'
      ];
  }

  // Genel menü
  if (isset($kullanici_adi) && $kullanici_adi) {
      $menu_items['mySQL'] = [
          'url' => '#', 'icon' => 'fas fa-database', 'text' => 'mySQL',
          'submenu' => [
              'hesap' => [
                  'url' => '../../../MySQL/products_excel/dosya_excel.php',
                  'icon' => 'fas fa-exchange-alt',
                  'text' => 'Ürün aktarım'
              ]
          ]
      ];

      $menu_items['Karşılaştırma'] = [
          'url' => '#', 'icon' => 'fas fa-balance-scale', 'text' => 'Karşılaştırma',
          'submenu' => [
              'bsdev' => ['url' => '../../../karsilastirma/bsdev.php', 'icon' => 'fas fa-file-contract', 'text' => 'bsdev'],
              'bsdsoft' => ['url' => '../../../karsilastirma/bsdsoft.php', 'icon' => 'fas fa-file-contract', 'text' => 'bsdsoft']
          ]
      ];

      $menu_items['Ftp'] = [
          'url' => '#', 'icon' => 'fas fa-server', 'text' => 'FTP',
          'submenu' => [
              'bsdev' => ['url' => '../../../ftp/bsdevftp.php', 'icon' => 'fas fa-file-contract', 'text' => 'bsdev'],
              'bsdsoft' => ['url' => '../../../ftp/bsdsoftftp.php', 'icon' => 'fas fa-file-contract', 'text' => 'bsdsoft'],
              'ftp bilgi' => ['url' => '../../../ftp/ftpbilgi.php', 'icon' => 'fas fa-file-contract', 'text' => 'ftp bilgi'],
              'senkronizasyon' => ['url' => '../../../ftp/bsdsoftsek.php', 'icon' => 'fas fa-file-contract', 'text' => 'senkronizasyon']
          ]
      ];
  }

  // Ek menüler varsa ekle
  if (isset($additional_menu_items) && is_array($additional_menu_items)) {
      $menu_items = array_merge($menu_items, $additional_menu_items);
  }
  ?>

  <?php if ($show_menu): ?>
    <?php foreach ($menu_items as $item): ?>
      <div class="bsd-menu-item">
        <a href="<?php echo site_url($item['url']); ?>" class="bsd-navlink1">
          <span class="bsd-menu-icon"><i class="<?php echo $item['icon']; ?>"></i></span>
          <?php echo $item['text']; ?>
          <?php if (isset($item['submenu'])): ?>
            <span class="bsd-submenu-toggle"><i class="fas fa-chevron-down"></i></span>
          <?php endif; ?>
        </a>

        <?php if (isset($item['submenu'])): ?>
          <div class="bsd-submenu">
            <?php foreach ($item['submenu'] as $subitem): ?>
              <a href="<?php echo site_url($subitem['url']); ?>" class="bsd-navlink1">
                <span class="bsd-menu-icon"><i class="<?php echo $subitem['icon']; ?>"></i></span>
                <?php echo $subitem['text']; ?>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<div class="bsd-content">
  <!-- Sayfa içeriği buraya gelecek -->
</div>

<?php include("script.php"); ?>
