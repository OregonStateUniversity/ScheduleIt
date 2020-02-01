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
	
	$('.visitEvent').click(function() {
		window.location.href = $(this).children().attr('href');
	});
	
	$('.editEventButton').click(function() {
		window.location.href = $(this).children().attr('href');
	});
	
	$('.returnEventButton').click(function() {
		window.location.href = $(this).children().attr('href');
	});
	
	initializeEmptyDownloadItems();	// Change empty download buttons into a none label
	
	
	var hashForEventFromURL = window.location.href;
	var hashKey = hashForEventFromURL.split("?key=");
	
	
	// Delete Event Feature. This requires the hash of the event to work.
	$('#deleteEventButton').on("click", function() {
		$('#deleteConfirm').modal('toggle');
		
		$('#deleteSubmitButton').off();
		$('#deleteSubmitButton').on("click", function() {
			$('#deleteConfirm').modal('toggle');
			
			deleteThisEvent(hashKey[1]);
			$('.entryField1').empty();
			
			var removedContainer = $('<div></div>');
			removedContainer.addClass("removedContainer");
			
			var returnButton = $('<button> Return to Managing Events </Button>');
			returnButton.addClass('btn btn-dark');
			
			returnButton.on('click', function() {
				window.location.href = "./events.php";
			});
			
			removedContainer.append('<h3> Event Has Been Deleted </h3>');
			removedContainer.append(returnButton);
			
			$('.entryField1').append('<br><br>');
			$('.entryField1').append(removedContainer);
		});
		
	});
	
	
	// Invite/Share feature should be implemented here
	$('#inviteEventButton').on("click", function() {
		$('#massInvite').modal('toggle');
		
		$('#inviteSubmitButton').off();
		$('#inviteSubmitButton').on("click", function() {
			alert("Feature Unavailable: Currently Under Development. (WIP)");
			//send invitation emails here
		});
		
	});
	
	
	// Delete user from slot feature should be implemented here
	$('.deleteUserSlot').on("click", function() {
		
		var userTimeSlot = $(this).parent().parent().children().eq(0).text();
		var userName = $(this).parent().parent().children().eq(1).text();
		$('#deleteUserHeader').text('Deleting Slot:   [ '+userName+'   at   '+userTimeSlot+' ]');
		$('#deleteUser').modal('toggle');
		
		$('#deleteUserSubmitButton').off();
		$('#deleteUserSubmitButton').on("click", function() {
			$('#deleteUser').modal('toggle');
			//Delete user from slot here
		});
		
	});
});

function deleteThisEvent(hashKey) {
	
	$.ajax({
		url: "delete_event.php",
		type: "POST",
		data: { key: hashKey },
	}).done(function (response) {
		console.log(response);
	});
}

// Replace empty download buttons with none label
function initializeEmptyDownloadItems() {
	$(".fileDownloadFile").each(function() {
		//console.log($(this).attr("href"));
		if ($(this).attr("href") == "./")
			$(this).replaceWith( "<text>None</text>");
	});
}


($(this).find('.myLink').attr('href'));