$(document).ready(function () {
  $('.copy-btn').on("click", function () {
  var link = $(this).attr("data-link");
  
  var temp = document.createElement("textarea");
  document.body.appendChild(temp);
  temp.value = link;
  temp.select();
  temp.setSelectionRange(0,99999);

  document.execCommand("copy")
  document.body.removeChild(temp);
  alert("Copied the Invitation Link to the clipboard.");
  });
});
