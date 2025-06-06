  
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"> </script>

  

<script>
$(document).ready(function() {
  var isNavOpen = false;

  function openNav() {
    $("#bsd-mySidebar").addClass("open");
    $("#bsd-main").addClass("sidebar-open");
    $(".bsd-content").addClass("sidebar-open");
    $(".bsd-openbtn").hide();
    isNavOpen = true;
  }

  window.closeNav = function() {
    $("#bsd-mySidebar").removeClass("open");
    $("#bsd-main").removeClass("sidebar-open");
    $(".bsd-content").removeClass("sidebar-open");
    $(".bsd-openbtn").show();
    isNavOpen = false;
    localStorage.setItem('navState', 'closed');
  }

  window.toggleNav = function() {
    isNavOpen ? closeNav() : openNav();
  }

  $('.bsd-menu-item > a').click(function(e) {
    var submenu = $(this).siblings('.bsd-submenu');
    if (submenu.length) {
      e.preventDefault();
      submenu.slideToggle();
      $(this).find('.bsd-submenu-toggle').toggleClass('active');
    }
  });

  function updateDateTime() {
    var now = new Date();
    var options = { 
      timeZone: 'Europe/Istanbul',
      weekday: 'long',
      year: 'numeric', 
      month: 'long', 
      day: 'numeric',
      hour: '2-digit', 
      minute: '2-digit', 
      second: '2-digit',
      hour12: false
    };
    $('#bsd-datetime').text(now.toLocaleString('tr-TR', options));
  }

  setInterval(updateDateTime, 1000);
  updateDateTime();

  $(document).click(function(event) {
    var sidebar = $("#bsd-mySidebar");
    var openbtn = $(".bsd-openbtn");
    if (!sidebar.is(event.target) && sidebar.has(event.target).length === 0 && 
        !openbtn.is(event.target) && openbtn.has(event.target).length === 0) {
      closeNav();
    }
  });

  $(window).resize(function() {
    if (window.innerWidth < 1024) {
      closeNav();
    }
  });

  closeNav(); // Sayfa yüklendiğinde menüyü kapalı başlat
});
</script>