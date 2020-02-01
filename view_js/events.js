/******************************************************************
* events.js
*
* This JavaScript file is for the events page (when you click manage on the navbar). This page allows you to view all the events you have
* created in a table. Values are shown in
* 	
*	Event Name
* 	Slots
* 	And a button that allows you to copy the link to the registration page for the respective event.
*
* Clicking on a event name in the table will redirect to a page with more details for that event.
*
* 
* FUTURE TASKS:
*
* - Refactoring: There are a lot of reusable code that could be moved over to this file from the other JS files. Particularly the date functions 
*   throughout the JS files. Though it might be better to make a specific date formatter JS files and move it into that to keep this more organized.
* 
*********************************************************************/


$( document ).ready(function() {

	$('#manageNav').addClass('activeNavItem');
	displayNoEventsHeader(); // check if there are existing events created. Otherwise display no events.
	
	$(".linkColumn").each(function () {

		var eventLink = $(this).parent().children().find('a:first').attr('href');
		
		eventLink = eventLink.replace('manage', 'register');
		pathArray = window.location.pathname.split('/');

		var newLink = window.location.protocol + "//" + window.location.host + "/" + pathArray[1] + "/" + pathArray[2] + eventLink.slice(1,eventLink.length);

		var newLinkItem = $('<a href='+newLink+'>'+newLink+'</a>');
		newLinkItem.addClass('linkToEvent');
		newLinkItem.attr('id', 'linkToEvent');
		$(this).append(newLinkItem);
	});

});

// Copy the link to event
$('.copy').on('click', function () {

	var temp_text = $('<input></input>');
	temp_text.attr("type", "text");
	temp_text.val($(this).next().text().toString());

    temp_text.attr('id', "copyToClipBoard");
	$(this).append(temp_text);

	var copyText = document.getElementById("copyToClipBoard");

	copyText.select();
	copyText.setSelectionRange(0, 99999);
    document.execCommand('copy');
	$('#copyToClipBoard').remove();
	alert("Copied to Clipboard!");

})

// Check if are events existing for this user. Otherwise display no events.
function displayNoEventsHeader() {
	if ($('.tableBody').children().length == 0) {
		$('#eventsTable').addClass('doNotDisplay');
		var noEventsLabel = $('<h3> No Events Created <img src="./NoEventsImg.png" height="100" width="100"></h3>');
		noEventsLabel.addClass('noEvents');
		$('.yourEvents').append(noEventsLabel);

		if ($('#deleteSelectedConfirmBox').hasClass('doNotDisplay') != true) {
			$('#deleteSelectedConfirmBox').toggleClass('doNotDisplay');
		};
	}
}

