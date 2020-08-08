$(function () {
  $(".table-btn").on("click", function () {
    const meetingName = $(this).attr("data-name");
    const url = $(this).attr("data-url");
    const onid = $(this).attr("data-attendee-onid");
    const slotHash = $(this).attr("data-timeslot-hash");
    const meetingHash = $(this).attr("data-meeting-hash");

    $("#delete-attendee-modal").modal("show");

    $("#delete-attendee-button").click(function () {
      removeAttendee(slotHash, onid, meetingName, meetingHash, url);
    });
  });
});

function removeAttendee(slotHash, onid, meetingName, meetingHash, url) {
  $.ajax({
    url: url,
    type: "POST",
    data: {
      slotHash: slotHash,
      attendeeOnid: onid,
      meetingName: meetingName,
      meetingHash: meetingHash,
    },
  }).done(function (response) {
    $("#delete-attendee-modal").modal("hide");
    location.reload();
  });
}
