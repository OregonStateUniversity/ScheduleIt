$(document).ready(function () {
  $('.table-btn').on("click", function () {
  var meetingName = $(this).attr('data-name');
  var url = $(this).attr('data-url');
  var onid = $(this).attr('data-attendee-onid');
  var slotHash = $(this).attr('data-timeslot-hash');
  var meetingHash = $(this).attr('data-meeting-hash');
  $("#delete-attendee-modal").modal("show");
  
    $("#delete-attendee-button").click( function () {
      $("#delete-attendee-modal").modal("hide");
      removeAttendee(slotHash, onid, meetingName, meetingHash, url);
    });
  });
});  


function removeAttendee(slotHash, onid, meetingName, meetingHash, url) {
  $.ajax({
    url: url,
    type: "POST",
    data: { slotHash: slotHash, attendeeOnid: onid, meetingName: meetingName, meetingHash: meetingHash }
  }).done(function (response) {
    $("#delete-attendee-modal").modal("hide");
    location.reload();
  });
}
