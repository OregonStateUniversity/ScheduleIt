/******************************************************************
* reservations.js
*
* This JavaScript file is for the reservations page.
* This page displays the user's current reservations that they have reserved in table format.
*
* The table lists as follows:
*
*	Event Name
* 	Start Time
*	Location
* 
*	Clicking on a event name will redirect to a page with more details for that reservation.
* 
* FUTURE TASKS:
*
* - Refactoring: There's probably a better way to implement the table iteration without the use of the index constants. That part should be reimplemented
*	such that the dependency for knowing a specific index of a column on the table is removed.
*
*********************************************************************/


const eventNameIndex = 0; // The index of the name on the reservations page table
const eventDateIndex = 2; // The index of the date on the reservations page table

$( document ).ready(function() {

	displayNoEventsHeader(); // If no reservations. Display No Reservations.
	
	$('#reserveNav').addClass('activeNavItem');

	var columnNames = [];

	$('#invitesTable thead tr th').each(function(index) {
		columnNames.push($(this).html());
	});


	var dateRow = [];
	var curDate = new Date();

	// Check the dates of all reservations. Any past reservations are moved to the PAST reservations table.
	$("#invitesTable tr td:nth-last-child( "+ eventDateIndex +" )").each(function () {

		var newDate = $(this).text().replace(/-/g, "/");

		var dateStrs = newDate.split(" ");


		var dt = new Date(dateStrs[0]);


		var timeStrs = dateStrs[1].split(":");

		dt.setHours(timeStrs[0]);
		dt.setMinutes(timeStrs[1]);
		dt.setSeconds(timeStrs[2]);


		if (dt < curDate) {
			var linkToPastEvent = $(this).parent().children().eq(eventNameIndex).children();
		//	linkToPastEvent.removeAttr("href");
			dateRow.push($(this).parent());
			$(this).parent().remove();
		}

	});

	// Create the past reservations table
	createPastEventsTable(dateRow, columnNames);

});


// Display No Reservations if reservation for user is empty
function displayNoEventsHeader() {

	if ($('.tableBody').children().length == 0) {

		var noEventsLabel = $('<h3> You are not Reserved for any Events <img src="./NoEventsImg.png" height="100" width="100"></h3>');
		noEventsLabel.addClass('noEvents');

		if (!$('#invitesTable').hasClass('doNotDisplay')) {
			$('#invitesTable').addClass('doNotDisplay');
			$('.yourInvites').append(noEventsLabel);
		}

	}

}

// If no past reservations exist. Dont display past events table
function checkHidePastEventsTable() {
	if($('.pastEventsTable tbody').children().length == 0) {
		$('.pastEventsField').addClass('doNotDisplay');
	}
}

// Create the past event table
function createPastEventsTable(dateRow, columnNames) {

	if (dateRow.length == 0) return;

	var rowItemCount = columnNames.length;

	var container = $('.entryField1');

	var pastEvents = $('<div></div>');

	var pastEventsField = $('<div></div>');

	pastEventsField.addClass('pastEventsField');
	pastEvents.addClass('pastEventsContainer table-responsive');
	pastEventsField.append('<h2> Past Reservations </h2>');
	pastEventsField.append(pastEvents);

	var table = $('<Table></Table>');
	table.addClass('table pastEventsTable table-striped');

	var rowHeader = $('<tr></tr>');
	rowHeader.attr("scope", "row");


	var i = 0;

	while(i < rowItemCount) {
		var header = $('<th>'+columnNames[i]+'</th>');
		header.attr("scope", "col");
		rowHeader.append(header);
		i++;
	}


	var thead = $('<Thead></Thead>');
	thead.append(rowHeader);


	var tbody = $('<Tbody></Tbody>');

	for (let i = 0; i < dateRow.length; i++) {
		tbody.append(dateRow[i]);
	}

	table.append(thead);
	table.append(tbody);

	pastEvents.append(table);
	container.append(pastEventsField);
	displayNoEventsHeader();

}

