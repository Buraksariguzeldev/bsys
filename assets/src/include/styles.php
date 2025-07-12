  <style>
    .bsd-sidebar { 
      height: 100%; 
      width: 0; 
      position: fixed; 
      z-index: 1000; 
      top: 0; 
      left: -250px; /* Menü başlangıçta tamamen gizli */
      background-color: #111; 
      overflow-x: hidden; 
      transition: 0.5s; 
      padding-top: 60px; 
      box-shadow: 2px 0 5px rgba(0,0,0,0.5);
    }
    .bsd-sidebar.open {
      left: 0; /* Menü açıldığında görünür */
      width: 250px;
    }
    .bsd-sidebar a { 
      padding: 8px 8px 8px 24px;
      text-decoration: none; 
      font-size: 14px; 
      color: #818181; 
      display: block; 
      transition: 0.3s; 
      border-bottom: 1px solid red; 
    }
    .bsd-sidebar a:hover { color: #f1f1f1; }
    .bsd-openbtn { 
      font-size: 18px; 
      cursor: pointer; 
      background-color: transparent; 
      color: #333; 
      padding: 8px 12px; 
      border: none; 
      position: fixed;
      left: 10px;
      top: 15px;
      z-index: 1001;
    }
    .bsd-submenu { display: none; padding-left: 15px; }
    .bsd-submenu a { font-size: 12px; }
    .bsd-menu-item { cursor: pointer; }
    #bsd-main { 
      transition: margin-left .5s;
      padding: 10px; 
      display: flex; 
      align-items: center; 
      justify-content: space-between; 
      background-color: #f0f0f0; 
      position: fixed; 
      top: 0; 
      left: 0; 
      right: 0; 
      z-index: 999; 
      height: 50px; 
      border-bottom: 1px solid #ddd; 
    }
    .bsd-welcome { 
      font-size: 14px; 
      color: #f1f1f1; 
      padding: 12px; 
      border-bottom: 1px solid #444; 
      margin-bottom: 12px; 
    }
    .bsd-datetime { 
      font-size: 12px; 
      color: #ffff00; 
      padding: 8px 12px; 
      border: 1px solid #ffff00; 
      border-radius: 4px; 
      text-align: center; 
      background-color: #333; 
      margin-bottom: 12px; 
    }
    .bsd-menu-icon { 
      margin-right: 8px;
      width: 18px; 
      text-align: center; 
    }
    .bsd-submenu-toggle { 
      float: right;
      transition: transform 0.3s; 
    }
    .bsd-submenu-toggle.active { transform: rotate(180deg); }
    .bsd-logo { height: 35px; }
    .bsd-sidebar-logo { height: 50px; margin: 10px auto; display: block; }
    .bsd-content { 
      margin-top: 60px; 
      padding: 20px; 
      transition: margin-left .5s;
    }
    .bsd-middle-section { 
      display: flex;
      align-items: center;
      margin-left: 50px; /* Logo için boşluk */
    }
    .bsd-right-section {
      position: fixed;
      right: 20px;
      top: 15px;
    }
    .bsd-closebtn { 
      position: absolute; 
      top: 10px; 
      right: 10px;
      font-size: 24px; 
      color: #818181; 
      cursor: pointer; 
    }
    .bsd-closebtn:hover { color: #f1f1f1; }
    .bsd-login-icon, .bsd-logout-icon { 
      font-size: 18px; 
      color: #333; 
      cursor: pointer; 
    }
    
    @media (min-width: 1024px) {
      .bsd-content { 
        margin-left: 0;
        transition: margin-left .5s;
      }
      .bsd-content.sidebar-open {
        margin-left: 250px;
      }
      #bsd-main.sidebar-open {
        margin-left: 250px;
      }
    }
  </style>