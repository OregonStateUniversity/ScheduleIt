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
 * - Delete User Slot Feature: Allows the event creator to delete a user off a slot by clicking on the red X for each user in the table for this page.
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
      //var slotKey = createTimeSlotHash(
      //console.log(userName);
      var onid = ""
      var pos = userName.lastIndexOf(" ");
      var lgn = userName.lngth;
      onid = userName.slice(pos + 1, lgn);
      //console.log("pos: " + pos);
      //console.log("onid: " + onid);
      //console.log(userTimeSlot);
      //console.log(hashKey[1]);
      var newDate = userTimeSlot.replace("on", "");
      //console.log(newDate);
      var date = new Date(newDate);
      //console.log(date);
      //https://stackoverflow.com/questions/8362952/javascript-output-current-datetime-in-yyyy-mm-dd-hhmsec-format
      var startTime =
          date.getUTCFullYear() + "-" +
          ("0" + (date.getMonth()+1)).slice(-2) + "-" +
          ("0" + date.getDate()).slice(-2) + " " +
          ("0" + date.getHours()).slice(-2) + ":" +
          ("0" + date.getMinutes()).slice(-2) + ":" +
          ("0" + date.getSeconds()).slice(-2);

      //console.log(startTime);
      //var test = testSlotHash(startTime,hashKey[1]);
      //console.log(test);
      //console.log("before calls hashKey[1]: " + hashKey[1]);
      getSlotId(startTime, hashKey[1], onid, hashKey[1]);      
    });
  });
});

function getSlotId(time, eventHash, onid, hash2) {
  //console.log("onid testslothash: " + onid);
  //console.log("eventHash testslothash: " + eventHash);
  $.ajax({
    url: "src/get_slot_id.php",
    type: "POST",
    data: { hash: eventHash, startTime: time }
  }).done(function (response) {
    console.log(response);
    var slotId = JSON.parse(response);
    //console.log(slotId.slotId);
    var id = slotId.slotId;
    //console.log("testslothash id: " + id);
    //console.log("onidcallback: " + onid);
    //console.log("testSlot callback hash: " + eventHash);
    getBookingId(id, onid, eventHash);
  });  
}

function getBookingId(slotId, attendeeOnid, eventHash) {
  //console.log("creatorDelete hash: " + eventHash);
  //console.log("creatorDelete slotId: " + slotId);
  $.ajax({
    url: "src/get_booking_id.php",
    type: "POST",
    data: { id: slotId, onid: attendeeOnid }
  }).done(function (response) {
    console.log(response);
    var getJsonId = JSON.parse(response);
    //console.log("getJsonId.bookingID: " + getJsonId.bookingID);
    var bookingId = getJsonId.bookingID;
    //console.log("bookingId: " + bookingId);
    //console.log("creator callback eventHash: " + eventHash);
    //console.log("creator callack slotId: " + slotId);
    deleteBookingSlot(bookingId, eventHash, slotId);
  });
}

function deleteBookingSlot(id, eventHash, slotId) {
  //console.log(id);
  //console.log("deletebookingSlot eventHash: " + eventHash);
  //console.log("deletebookingSlot slotID: " + slotId);
  $.ajax({
    url: "src/delete_booking.php",
    type: "POST",
    data: { bookingID: id }
  }).done(function (response) {
    console.log(response);
    updateAvailableSlots(eventHash, slotId); 
  });
}

function updateAvailableSlots(eventHash, slotId) {
   //console.log("update event: " + eventHash);
   //console.log("update slot: " + slotId);
   $.ajax({
     url: "src/update_available_slots.php",
     type: "POST",
     data: { eventhash: eventHash, slotID: slotId }
   }).done(function(response) {
     console.log(response);
   });
}

function deleteThisEvent(hashKey) {
  $.ajax({
    url: "src/delete_event.php",
    type: "POST",
    data: { key: hashKey }
  }).done(function (response) {
    console.log(response);
  });
}

// Replace empty download buttons with none label
function initializeEmptyDownloadItems() {
  $(".fileDownloadFile").each(function () {
    //console.log($(this).attr("href"));
    if ($(this).attr("href") == "./") $(this).replaceWith("<text>None</text>");
  });
}

$(this).find(".myLink").attr("href");
