$(function() {
  updateHashOnTabChange.init();
});

const updateHashOnTabChange = {
  init: function() {
    $(".nav-tabs a").click(this.showTabOnClick);
    $(".navbar-invites-link").click(this.showInvitesTabOnClick);

    setTimeout(function() {
      $("html, body").animate({ scrollTop: 0 }, "fast");
    }, 100);

    this.showTabFromHash();
  },
  showTabFromHash: function() {
    const hash = window.location.hash;

    if (hash) {
      $(`.nav-link[href="${hash}"]`).tab("show");
    }
  },
  showTabOnClick: function(e) {
    e.preventDefault();
    $(this).tab("show");
    window.location.hash = $(this).attr('href');
  },
  showInvitesTabOnClick: function() {
    const hash = "#invites";
    $(`.nav-link[href="${hash}"]`).tab("show");
    window.location.hash = hash;
  }
};
