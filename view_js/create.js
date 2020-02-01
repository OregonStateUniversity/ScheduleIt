/******************************************************************
* Create.js
*
* This JavaScript file refers to the create page. The create page allows a user to create an event under their ONID. Required fields are event name, event location,
* a duration, a date for the event, and at least one time slot.
* 
* Duration is preset at 15 minutes, 30 minutes, and one hour as of now.
* Capacity of a slot defaults to 1.
* Time range is preset from 7:00 AM to 7:00 PM as of now.
*
* REMAINING TASKS: 
*
* - Infinite Duration Feature: The user should optionally be able to make the duration of the event infinite. My suggestion is to have a little check box that
* 	sets the duration to be infinite when checked.
*
* - Infinite Capacity Feature: Allow a slot to have infinite capacity.
*
* - 10 Minute Duration Option: Allow event to be set to a 10 minute duration per slot. 
*
* - Refactoring
*
* - Schedule Checking: Currently MyEventBoard doesn't check for time conflicts between events you own and events you are registered to. In the future, it would
*	be nice if the application can prevent events from over lapping with other events you own and events you are registered to.
*********************************************************************/

$(document).ready(function(){
	$('#createNav').addClass('activeNavItem'); // highlight the navbar for create
	initLocationInput();	// initialize location auto complete list
	initDatePicker();		// Initialize the date picker
	
});

// ? tool tip next to create page check boxes
$(function () {
	$('[data-toggle="tooltip"]').tooltip()
})


// Outlines name and location if empty with red. Must have values for these to proceed.
function checkInput(obj) {

	if (obj.val().length === 0)
	{

		if (obj.hasClass("requiredInput") === true)
		{
			return false;
		}
		else
		{
			obj.addClass("requiredInput");
			return false;
		}
	}
	else
	{
		obj.removeClass("requiredInput");
		return true;
	}
}

//source: https://blog.teamtreehouse.com/creating-autocomplete-dropdowns-datalist-element
function initLocationInput() {
	var dataList = document.getElementById('locationDatalist');
	var input = document.getElementById('locationInput');
	
	
	// Create a new XMLHttpRequest.
	var request = new XMLHttpRequest();

	// Handle state changes for the request.
	request.onreadystatechange = function(response) {
		if (request.readyState === 4) {
			if (request.status === 200) {
				// Parse the JSON
				var jsonOptions = JSON.parse(request.responseText);

				// Loop over the JSON array.
				jsonOptions.forEach(function(item) {
				
				// Create a new <option> element.
				var option = document.createElement('option');
				
				// Set the value using the item in the JSON array.
				option.value = item.name;
				
				// Add the <option> element to the <datalist>.
				dataList.appendChild(option);
				});

				// Update the placeholder text.
				input.placeholder = "Enter a Location";
			} else {
				// An error occured :(
				input.placeholder = "Couldn't load location options :(";
			}
		}
	};

	// Update the placeholder text.
	input.placeholder = "Loading options...";

	// Set up and make the request.
	request.open('GET', 'OSU_locations.json', true);
	request.send();
}

				
$(document).ready(function () {

	//The next button. Allows the user to proceed to the next page of the create page.
	document.getElementById("field1to2").addEventListener("click", function () {
		
		var eventNameObj = $('#eventNameInput');
		var eventLocationObj = $('#locationInput');
		var eventNameCheck = checkInput(eventNameObj);
		var eventLocationCheck = checkInput(eventLocationObj);
		var eventSlotsCheckEmpty = $('#timeslotCapInput');
		
		
		if ( eventNameCheck === true && eventLocationCheck === true)
		{
			if (eventSlotsCheckEmpty.val() === "")
					eventSlotsCheckEmpty.val("1");
				
			$('.entryField1').addClass('collapse');
			$('.entryField2').removeClass('collapse');
			
			
		}
	})

	// The second page of the create page submit button. Submits the event creation upon meeting the correct inputs.
	document.getElementById("field2toSubmit").addEventListener("click", function () {
		
		var slots = submitEvent();
		var eventDateInputObj = $('#Dates');

		var eventDateCheck = checkInput(eventDateInputObj);

		if (eventDateCheck === false)  // Must have a date selected
		{
			return;
		}
		else if (slots == false) {
			alert("Please select slots");
		}
		else {
			
			var totalCap = timeslotCapInput.value * slots.length;
			var slotCap = timeslotCapInput.value;
			var newArr = JSON.stringify(slots);

			var anonymous = 1;
			if ($('#anonymousCheck').prop("checked") == false) anonymous = 0;

			var upload = 0;
			if ($('#fileUpload').prop("checked") == true) upload = 1;

			$.ajax({
				type: "POST",
				url: "insert_event.php",
				data: {
					eventName: $('#eventNameInput').val(),
					eventLocation: $('#locationInput').val(),
					eventDescription: $('#eventDescriptTextArea').val(),
					eventDuration: parseInt($("#durationSelector").val(), 10),
					slotArray: newArr,
					eventCap: totalCap,
					sCap: slotCap,
					anonymous: anonymous,
					upload: upload
				}
			}).done(function(response) {
    			alert(response);
			});

			$('.entryField2').addClass('collapse');
			$('.submitField').removeClass('collapse');
		}

	})

	// Go back button
	document.getElementById("field2to1").addEventListener("click", function () {
		$('.entryField2').addClass('collapse');
		$('.entryField1').removeClass('collapse');
	})

});


// Duration time drop down. Allows currently a fixed time slot of 15 minutes, 30 minutes, and 1 hours.
$('#durationSelector').on('change', function () {

	// Infinite duration and 10 minute duration values should be implemented here.
	
	$('.removeOnClear').remove(); //Clear all cells
	$('#datepicker').datepicker('update', ''); // clear all dates

	var selectedDuration = parseInt($("#durationSelector").val(), 10);

	var offset = 0;
	var minutes = 0;
	var hourInMinutes = 60;
	var totalHours = 12;

	//console.log(selectedDuration);
	switch (selectedDuration) {

		case 10: 
			//implement 10 minutes here.
			break;
		case 15:

			var durationOffset = 4;
			var durationSlots = 3;
			var minutesIncrement = 15;
			break;

		case 30:

			var durationOffset = 2;
			var durationSlots = 1;
			var minutesIncrement = 30;
			break;

		case 60:

			return;
			break;
		default:
			console.log("Something went wrong");
			break;
	}

	for (i = 0; i < totalHours; i++) {

		for (j = 0; j < durationSlots; j++) {
			minutes = minutes + minutesIncrement;
			//var min = minutesToFormat(minutes);
			var newRow = $('<tr><th><div>' + minutes + '</div></th></tr>');
			newRow.addClass("removeOnClear");
			newRow.children().children().addClass("doNotDisplay");
			$('#timeSelector tr').eq(offset + j + 1).after(newRow);

		}

		minutes = (hourInMinutes * (i + 1));
		offset = offset + durationOffset;

	}

});


// Menu Toggle Script

$("#menu-toggle").click(function (e) {
	e.preventDefault();
	$("#wrapper").toggleClass("toggled");
});

// Source: https://jsfiddle.net/christianklemp_imt/b20paum2/
function initDatePicker() {
	
	// initialize the datepicker
	
	$('#datepicker').datepicker({

		startDate: new Date(),
		multidate: true,
		format: "mm/dd/yyyy",
		language: 'en'

	}).on('changeDate', function (e) {

		dragTable(); // Add event handler

		// `e` here contains the extra attributes

		//Check if date exists in table
		var hasColumn = $('#timeSelector thead th').filter(function () {
			return this.textContent === e.format();
		}).length > 0;


		//-- check if column date exists.
		if (hasColumn === false && e.format() != "") {
			addNewCol(e); // Add if it is. Otherwise remove it.
			dragTable(); // Add event handler
		}
		else if (hasColumn === false && e.format() == "") // First column remove edge case
		{
			removeColumn();
			dragTable(); // Add event handler
		}
		else if (hasColumn === true && e.format() != "") // Remove columns
		{
			removeColumn();
			dragTable(); // Add event handler
		}

	});

}

// Removes a column from the time selection table (this applies when a date is removed)
function removeColumn() {

	var removedDate;
	var inputString = document.getElementById('Dates').value;

	$("#timeSelector thead tr th").each(function (index) {

		if (index != 0) {

			if (inputString.indexOf($(this).text()) >= 0) {
				// Date is still selected. Keep searching.
			}
			else {

				removedDate = $(this).text();

				index++;

				$('#timeSelector tr td:nth-child(' + index + ')').each(function () {
					//$(this).text("");
					//$(this).removeClass("selected");
					$(this).remove();
				});

				$(this).remove();

				return;

			}
		}

	});

}

// Adds a new column to the time selection table (2nd page). Applies when a date has been added
function addNewCol(e) {

	var dateName = e.format();
	var newDateHeader = $('<th></th>');
	newDateHeader.text(e.format());
	newDateHeader.addClass("removeOnClear");
	$("#timeSelector tr:first").append(newDateHeader);

	var newDateColumn = $('<td></td>');
	var timeDialogue = $('<div></div>');


	timeDialogue.addClass('doNotDisplay');

	newDateColumn.append(timeDialogue);
	newDateColumn.addClass("removeOnClear");
	$("#timeSelector tr").not(':first').not(':last').append(newDateColumn);

}

//format the date for the Database. database wants date in yyyy-mm-dd
function formatDate(currDate) {
	var year = currDate.substr(-4);
	var month = currDate.slice(0,2);
	var day = currDate.slice(3,5);
	var newDate = year + "-" + month + "-" + day;
	return newDate;
}


// Format the start time for DB. This is only instance where the time is in total minutes. 
// It applies for all time slider objects. The first column has the times in total minutes (this is hidden).
// This was an issue caused in the early stages of development.
function formatTime(temp) {
	var totalMinutes = parseInt(temp, 10);
	var startHour = 7;
	var hours = startHour + Math.floor(totalMinutes / 60);
	var minutes = totalMinutes % 60;
	if (minutes == 0) {
		return hours + ":" + minutes + "0";
	}
	return hours + ":" + minutes;
}

// Format the end time.
function formatEndTime(temp) {
	var duration = parseInt($("#durationSelector").val(), 10);
	var totalMinutes = parseInt(temp, 10);
	var newTime = totalMinutes + duration;
	
	return formatTime(newTime);
}


// Submit the event
function submitEvent() {

	var num = 0;

	var slotArray = [];
	var hasSelected;

	$("#timeSelector thead tr th").each(function (index) {

		if (index != 0) {

			var currDate = $(this).text();
			index++;

			hasSelected = false;

			$('#timeSelector tr td:nth-child(' + index + ')').each(function () {

				if ($(this).hasClass("selected")) {
					var date = formatDate(currDate);
					var currTime = $(this).closest('tr').find('th').children().text();
					var slot = {
						startDate: date + " " + formatTime(currTime),
						endDate: date + " " + formatEndTime(currTime)
					};
				//	console.log(slot.startDate);
				//	console.log(slot.endDate);
					slotArray.push(slot);
					hasSelected = true;
				}

			});

			if (hasSelected === false) return false; // if no time slot is selected. break out of statement

		}
	});

	if (hasSelected == false) {
		return false; // return false if not all column have a selected time
 	}
	else {
		return slotArray;
	}

}

// Drag slider feature. This must be recalled to that column if a new column is added dynamically.
function dragTable() {

	// Source: http://jsfiddle.net/few5E/

	var isMouseDown = false;
	var textHolder;

	$("#timeSelector td").mousedown(function () {

			isMouseDown = true;
			$(this).toggleClass("selected");

			return false; // prevent text selection

		}).mouseover(function () {

			if (isMouseDown) {
				$(this).toggleClass("selected");
			}

		});

	$(document).mouseup(function () {
		isMouseDown = false;
	});

}

$("#goToManageButton").on('click', function() {
	window.location = "./events.php";
})