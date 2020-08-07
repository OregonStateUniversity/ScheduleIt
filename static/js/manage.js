$(function () {
  $(".copy-btn").on("click", function () {
    const link = $(this).attr("data-link");

    const temp = document.createElement("textarea");
    document.body.appendChild(temp);
    temp.value = link;
    temp.select();
    temp.setSelectionRange(0, 99999);

    document.execCommand("copy");
    document.body.removeChild(temp);
    alert("Copied the Invite Link to the clipboard.");
  });
});
