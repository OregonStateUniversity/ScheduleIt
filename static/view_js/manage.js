/******************************************************************
 * Manage.js
 *
 * This JavaScript file refers to the manage page. This is the page of each individual event UPON THE SELECTION in the events page.
 *
 *
 * REMAINING TASKS:
 *
 * - This page should be renamed upon refactoring. This is confusing with the events page as the navbar says manage. This probably should be renamed to manageDetails
 * 	or something like that.
 *
 * - Invite/Share Feature: The share feature should allow a user to upload a CSV file or text file with a list of ONIDs. Upon submission, the share feature will email
 *	those on the list a link to the Registration page for the respective event. There should also be an option to allow the user to
 *   optionally write or paste in a list of ONIDs into a textbox instead of uploading a CSV or textfile to achieve the above.
 *
 * - @@implemented needs further testing@@ Delete User Slot Feature: Allows the event creator to delete a user off a slot by clicking on the red X for each user in the table for this page.
 *	If a user is deleted off the slot, they should be notified via email and the slot they are deleted off of should reflect the capacity after the delete.
 *
 *********************************************************************/

$(document).ready(function () {
  $(".visitEvent").click(function () {
    window.location.href = $(this).children().attr("href");
  });

  $(".editEventButton").click(function () {
    window.location.href = $(this).children().attr("href");
  });

  $(".returnEventButton").click(function () {
    window.location.href = $(this).children().attr("href");
  });

  initializeEmptyDownloadItems(); // Change empty download buttons into a none label

  var hashForEventFromURL = window.location.href;
  var hashKey = hashForEventFromURL.split("?key=");

  // Delete Event Feature. This requires the hash of the event to work.
  $("#deleteEventButton").on("click", function () {
    $("#deleteConfirm").modal("toggle");

    $("#deleteSubmitButton").off();
    $("#deleteSubmitButton").on("click", function () {
      $("#deleteConfirm").modal("toggle");

      deleteThisEvent(hashKey[1]);
      $(".entryField1").empty();

      var removedContainer = $("<div></div>");
      removedContainer.addClass("removedContainer");

      var returnButton = $("<button> Return to Managing Events </Button>");
      returnButton.addClass("btn btn-dark");

      returnButton.on("click", function () {
        window.location.href = "./events.php";
      });

      removedContainer.append("<h3> Event Has Been Deleted </h3>");
      removedContainer.append(returnButton);

      $(".entryField1").append("<br><br>");
      $(".entryField1").append(removedContainer);
    });
  });

  // Invite/Share feature should be implemented here
  $("#inviteEventButton").on("click", function () {
    $("#massInvite").modal("toggle");

    $("#inviteSubmitButton").off();
    $("#inviteSubmitButton").on("click", function () {
      alert("Feature Unavailable: Currently Under Development. (WIP)");
      //send invitation emails here
    });
  });

  // Delete user from slot feature should be implemented here
  $(".deleteUserSlot").on("click", function () {
    var userTimeSlot = $(this).parent().parent().children().eq(0).text();
    var userName = $(this).parent().parent().children().eq(1).text();
    $("#deleteUserHeader").text(
      "Deleting Slot:   [ " + userName + "   at   " + userTimeSlot + " ]"
    );
    $("#deleteUser").modal("toggle");

    $("#deleteUserSubmitButton").off();
    $("#deleteUserSubmitButton").on("click", function () {
      $("#deleteUser").modal("toggle");
      //Delete user from slot here
      var onid = ""
      var pos = userName.lastIndexOf(" ");
      var lgn = userName.lngth;
      onid = userName.slice(pos + 1, lgn);
      var newDate = userTimeSlot.replace("on", "");
      var date = new Date(newDate);

      //https://stackoverflow.com/questions/8362952/javascript-output-current-datetime-in-yyyy-mm-dd-hhmsec-format
      var startTime =
          date.getUTCFullYear() + "-" +
          ("0" + (date.getMonth()+1)).slice(-2) + "-" +
          ("0" + date.getDate()).slice(-2) + " " +
          ("0" + date.getHours()).slice(-2) + ":" +
          ("0" + date.getMinutes()).slice(-2) + ":" +
          ("0" + date.getSeconds()).slice(-2);


      var eventName = document.getElementById("eventName").textContent;
      eventName = eventName.trim();
      getSlotId(startTime, hashKey[1], onid);
      notifyAttendee(onid, eventName);
      deleteFile(onid, hashKey[1], "_upload");
    });
  });
});


// this will delete any file that was uploaded by the attendee
function deleteFile(attendeeOnid, eventHashKey, fileType) {
  $.ajax({
    url: "src/delete_file.php",
    type: "POST",
    data: { onid: attendeeOnid, eventHash: eventHashKey, type: fileType  }
  }).done(function(response) {
    
  });
}

// sends an email message to the attendee that they have been
// removed from the timeslot
function notifyAttendee(onid, eventName) {
  $.ajax({
    url: "src/notify_attendee.php",
    type: "POST",
    data: { attendee: onid, eventname: eventName }
  }).done(function(response) {
    
  });
}

// getslot id gets the time slot id and calls get bookingid
function getSlotId(time, eventHash, onid) {
  $.ajax({
    url: "src/get_slot_id.php",
    type: "POST",
    data: { hash: eventHash, startTime: time }
  }).done(function (response) {
    var slotId = JSON.parse(response);
    var id = slotId.slotId;
    getBookingId(id, onid, eventHash);
  });
}

// getBookingId gets the booking or time slot reservation for
// the attendee and calls deleteBookingSlot
function getBookingId(slotId, attendeeOnid, eventHash) {
  $.ajax({
    url: "src/get_booking_id.php",
    type: "POST",
    data: { id: slotId, onid: attendeeOnid }
  }).done(function (response) {
    var getJsonId = JSON.parse(response);
    var bookingId = getJsonId.bookingID;
    deleteBookingSlot(bookingId, eventHash, slotId);
  });
}

// deleteBookingSlot deletes the reservation and calls
// updateAvailableSlots
function deleteBookingSlot(id, eventHash, slotId) {
  $.ajax({
    url: "src/delete_booking.php",
    type: "POST",
    data: { bookingID: id }
  }).done(function (response) {
    updateAvailableSlots(eventHash, slotId);
  });
}

// updateAvailableSlots increases the avialable slots for
// the event and the time slot by 1.  It also sets the
// timeslot is_full variable to 0.
function updateAvailableSlots(eventHash, slotId) {
   $.ajax({
     url: "src/update_available_slots.php",
     type: "POST",
     data: { eventhash: eventHash, slotID: slotId }
   }).done(function(response) {
     alert(response);
     location.reload();
   });
}

function deleteThisEvent(hashKey) {
  $.ajax({
    url: "src/delete_event.php",
    type: "POST",
    data: { key: hashKey }
  }).done(function (response) {
    
  });
}

// Replace empty download buttons with none label
function initializeEmptyDownloadItems() {
  $(".fileDownloadFile").each(function () {
    //console.log($(this).attr("href"));
    if ($(this).attr("href") == "./") $(this).replaceWith("<text>None</text>");
  });
}

// Upload a file feature
 $("#submitFile").on("click", function () {
   var inputFile = $("#inputFile");
   
  if (!inputFile.val()) {
    alert("Please upload a file first!");
  } else {
    var fileData = inputFile.prop("files")[0];
    var slotKey = window.location.search.split("?key=")[1];
    
    var getOnid = document.getElementById("get-creator-onid");
    var creatorOnid = getOnid.getAttribute("data-id");
    
    deleteFile(creatorOnid, slotKey, "_event_file")
    creatorUploadFile(fileData, slotKey);
  }
});

$(this).find(".myLink").attr("href");
