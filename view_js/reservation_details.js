/******************************************************************
* reservations_details.js (rename this to reservationsDetails.js to keep consistency with JS files in camel case)
*
* This JavaScript file is for the reservations details page. This page is the page that when clicking on anchor
* individual reservation under the reservations page. This page will allow the user to view all the details of a specific
* reservation and upload a file to that reservation if the event has file uploading enabled.
* 
* 
* FUTURE TASKS:
*
* - Refactoring
*
*********************************************************************/


$(document).ready(function () {
	
	($(this).find('.myLink').attr('href'));
	
	$('.visitEventButton').click(function() {
		window.location.href = $(this).children().attr('href');
	});
	
	$('.returnEventButton').click(function() {
		window.location.href = $(this).children().attr('href');
	});
	
	var hashForEventFromURL = window.location.href;
	var hashKey = hashForEventFromURL.split("?key=");
	
	
	// Delete the reservation. Requires the slot hash to work.
	$('#deleteEventButton').on("click", function() {
		$('#deleteConfirm').modal('toggle');
		
		$('#deleteSubmitButton').off();
		$('#deleteSubmitButton').on("click", function() {

			$('#deleteConfirm').modal('toggle');
			
			deleteThisEvent(hashKey[1]);
			
			$('.entryField1').empty();
			
			var removedContainer = $('<div></div>');
			removedContainer.addClass("removedContainer");
			
			var returnButton = $('<button> Return to Viewing Reservations </Button>');
			returnButton.addClass('btn btn-dark');
			
			returnButton.on('click', function() {
				window.location.href = "./reservations.php";
			});
			
			removedContainer.append('<h3> Registered Slot Has Been Deleted </h3>');
			removedContainer.append(returnButton);
			
			$('.entryField1').append('<br><br>');
			$('.entryField1').append(removedContainer);

		});
		
	});

	// Format the start and end time of the date pulled from the Database to be viewable.
	const startTime = document.getElementById('eventStartTimeLabel').children[0];
	startTime.innerText = formatDateTime(startTime.innerText);

	const endTime = document.getElementById('eventEndTimeLabel').children[0];
	endTime.innerText = formatDateTime(endTime.innerText);
	
});

// Upload a file feature
$('#submitFile').on('click', function () {

    var inputFile = $('#inputFile');

    if (!inputFile.val()) {
        alert('Please upload a file first!');
    }
    else {

		var fileData = inputFile.prop('files')[0];
		var slotKey = window.location.search.split('?key=')[1];

		uploadFile(fileData, slotKey);

    }

})

// Delete this reservation. Requires event slot hash.
function deleteThisEvent(hashKey) {
	
	$.ajax({
		url: "delete_reservation.php",
		type: "POST",
		data: { key: hashKey }
	}).done(function (response) {
		console.log(response);
	});

}


