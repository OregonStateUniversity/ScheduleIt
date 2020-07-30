$(document).ready(function () {
  
  
  $("#mybtn").click(function() {
    $("#delete-attendee-modal").modal("show"); 
      var info = document.getElementById("mybtn");
      var time = info.getAttribute("data-time");
      var onid = info.getAttribute("data-onid");
      var id = info.getAttribute("data-id");
      console.log(time);
      console.log(onid);
      console.log(id);
  });
  $("#delete-attendee-modal").on('shown.bs.modal', function() {
      console.log("whatever");
      var info = document.getElementById("mybtn");
      var time = info.getAttribute("data-time");
      var onid = info.getAttribute("data-onid");
      var id = info.getAttribute("data-id");
      var url = info.getAttribute("data-url");
      var meetingName = info.getAttribute("data-meeting-name");
      console.log(time);
      console.log(onid);
      console.log(id);
      console.log(url);
      console.log(meetingName);
      $("#delete-attendee-button").click( function () {
      console.log("hi hi");
      $("#delete-attendee-modal").modal("toggle");
      removeAttendee(time, onid, id, url, meetingName);
      });
  });
});

function removeAttendee(time, onid, id, url, name) {
  $.ajax({
    url: url,
    type: "POST",
    data: { startTime: time, attendeeOnid: onid, eventId: id, meetingName: name }
  }).done(function (response) {
    //console.log(response);
    location.reload();
});
}
