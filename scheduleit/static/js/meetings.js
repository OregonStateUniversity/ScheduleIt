$(function() {
  var hash = window.location.hash;
  hash && $('.nav-tabs a[href="' + hash + '"]').tab("show");

  setTimeout(function() {
    $("html, body").animate({ scrollTop: 0 }, "fast");
  }, 100);

  $(".nav-tabs a").click(function(e) {
    $(this).tab("show");
    window.location.hash = this.hash;
  });

  $(".navbar-invites-link").click(function(e) {
    $('.nav-tabs a[href="' + window.location.hash + '"]').tab("show");
  });
});
