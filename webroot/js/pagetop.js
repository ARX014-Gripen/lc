$(window).scroll(function () {
    var now = $(window).scrollTop();
    if (now > 50) {
      $('.pagetop').fadeIn("slow");
    } else {
      $('.pagetop').fadeOut('slow');
    }
});