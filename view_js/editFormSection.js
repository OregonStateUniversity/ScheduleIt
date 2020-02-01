/******************************************************************
* editFormSection.js 
*
* This JavaScript file refers to the form field section of the edit event page. A user can
* edit the name, location, file upload option, anonymous option, and description of the event here.
* Basically anything that isn't time or event slot editing related is managed in this file. 
* 
* All changes made are only applied to the database when the "SAVE BUTTON" is submitted.
* We implemented this such that there is only one transaction to the database per edit such that a user
* cannot make too many calls to the database unnecessarily.
*
* FUTURE TASKS:
*
* - Refactoring: Could be split from the edit time page.
*
* - Issue: The state object for this page is managed by a global
*	const object. Maybe this is a security issue. This should probably be replaced with something else.
*
*********************************************************************/

const formState = {	// This is the "state" object that manages the form page of the edit form section. Replace with a class maybe?
	eventName: "",
	eventLocation: "",
	eventDescription: "",
	eventAnonymousOption: true,
	eventFileOption: false
};

$(document).ready(function () {
	initLocationInput();
	initFormState();
});

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

// initialize the state object (formState)
function initFormState() {

	var databaseObj = [];

	var rawEntries = document.getElementsByClassName('eventDataForFormEntry');

	for (var entry of rawEntries) {
		var parsedEntry = JSON.parse(entry.innerText);
		databaseObj.push(parsedEntry);
	}

	while (rawEntries.length > 0) rawEntries[0].remove();
	//console.log(databaseObj);
	
	var dbEventName = databaseObj[0]['name'];
	var dbEventLocation = databaseObj[0]['location'];
	var dbFileOption = databaseObj[0]['upload'];
	var dbAnonymousOption = databaseObj[0]['anonymous'];
	var dbEventDescription = databaseObj[0]['description'];
	
	// store database form values in as a cached object

	formState.eventName = dbEventName;				
	formState.eventLocation = dbEventLocation;
	formState.eventDescription = dbEventDescription;
	
	formState.eventAnonymousOption = false;
	if (dbAnonymousOption == 1) formState.eventAnonymousOption = true;

	formState.eventFileOption = false ;
	if (dbFileOption == 1) formState.eventFileOption = true;

	
	// set fields in form
	$('#eventNameInput').val(dbEventName);
	$('#locationInput').val(dbEventLocation);
	$('#eventDescriptTextArea').val(dbEventDescription);
	$('#anonymousCheck').prop("checked", dbAnonymousOption);
	$('#fileUpload').prop("checked", dbFileOption);
	
	//console.log(formState);

}	

// reset values to the origin values in the DB upon load
$('#resetFormButton').on('click', function () {
	
	$('#eventNameInput').val(formState.eventName);
	$('#locationInput').val(formState.eventLocation);
	$('#eventDescriptTextArea').val(formState.eventDescription);
	$('#anonymousCheck').prop("checked", formState.eventAnonymousOption);
	$('#fileUpload').prop("checked", formState.eventFileOption);

});

// Upload file option. Has a warning message if changed.
$('#fileUpload').on('click', function (e) {

	e.preventDefault();
	
	buildModalForChangeConfirmation("Confirm Change", "Saving this field unchecked (File Upload Field) will cause files currently uploaded to this event to be deleted");
	$('#generalConfirm').modal('toggle');

	$('#generalAcceptButton').on('click', function () {
		$('#generalConfirm').modal('toggle');
		$('#fileUpload').prop("checked", !$('#fileUpload').prop("checked"));
	});

});

// Anonymous option. Has a warning message if changed.
$('#anonymousCheck').on('click', function (e) {

	e.preventDefault();
	
	buildModalForChangeConfirmation("Confirm Change", "Saving this field unchecked (Anonymous Field) will make registered users visible to other users upon event registration.");
	
	$('#generalConfirm').modal('toggle');

	$('#generalAcceptButton').on('click', function () {
		$('#generalConfirm').modal('toggle');
		$('#anonymousCheck').prop("checked", !$('#anonymousCheck').prop("checked"));
	});

});

// Save modal. Build the necessary information to display here.
function buildModalForFormSave(modalHeaderName) {

	$('#generalHeaderLabel').text(modalHeaderName);

	var newEventName = $('#eventNameInput').val();
	var newEventLocation = $('#locationInput').val();
	var newEventDescription = $('#eventDescriptTextArea').val();
	var newEventAnonymousOption = $('#anonymousCheck').prop('checked');
	var newEventFileOption = $('#fileUpload').prop('checked');

	const EVENT_DESCRIPT_LIST_LABEL = "Event Description";

	var eventSaveVals = {
		"Event Name": newEventName,
		"Event Location": newEventLocation,
		"Event Description": newEventDescription,
		"Event Anonymous Option": newEventAnonymousOption,
		"Event File Option": newEventFileOption
	};

	const keys = Object.keys(eventSaveVals);
	
	var eventDbVals = {
		"Event Name": formState.eventName,
		"Event Location": formState.eventLocation,
		"Event Description": formState.eventDescription,
		"Event Anonymous Option": formState.eventAnonymousOption,
		"Event File Option": formState.eventFileOption
	}

	var list = $('<ul></ul>');
	list.addClass('list-group saveItemList');
	$('.confirmationDescriptionContainer').append(list);

	for (var key of keys) {

		if (typeof eventSaveVals[key] === "boolean") {

			var changeItem;
			
			if (eventSaveVals[key] === false) {
				var offLabel = $('<text>OFF</text>');
				offLabel.addClass('offLabel');
				changeItem = $('<li><label>' + key + ':</label> </li>');
				changeItem.append(offLabel);
			}
			else if (eventSaveVals[key] === true) {
				var onLabel = $('<text>ON</text>');
				onLabel.addClass('onLabel');
				changeItem = $('<li><label>' + key + ':</label> </li>');
				changeItem.append(onLabel);
			}
			
			// console.log(key)
			// console.log(eventSaveVals[key], eventDbVals[key], (eventSaveVals[key] == eventDbVals[key]))

			if (eventSaveVals[key] == eventDbVals[key])
				changeItem.append(" (Unchanged)");
				
		}
		else if (eventSaveVals[key] === "" || eventSaveVals[key] == eventDbVals[key]) {
			var changeItem = $('<li><label>' + key + ':</label> Unchanged</li>');
		}
		else {
			var itemValue = $('<text>' + eventSaveVals[key] + '</text>');
			itemValue.addClass('changedLabel');
			var changeItem = $('<li><label>' + key + ':</label> </li>');
			changeItem.append(itemValue);
		}

		changeItem.addClass('list-group-item');
		$('.confirmationDescriptionContainer ul').append(changeItem);

	}
}

// Save button upon click. Proceed the save transaction to Database.
$('#saveForm').on('click', function () {

	var hasChangedValue = false;
	
	var newEventName = $('#eventNameInput').val();
	var newEventLocation = $('#locationInput').val();
	var newEventDescription = $('#eventDescriptTextArea').val();
	var newEventAnonymousOption = $('#anonymousCheck').prop('checked');
	var newEventFileOption = $('#fileUpload').prop('checked');
	
	var eventSaveVals = {
		"Event Name": newEventName,
		"Event Location": newEventLocation,
		"Event Description": newEventDescription,
		"Event Anonymous Option": newEventAnonymousOption,
		"Event File Option": newEventFileOption
	};

	const keys = Object.keys(eventSaveVals);
	
	var eventDbVals = {
		"Event Name": formState.eventName,
		"Event Location": formState.eventLocation,
		"Event Description": formState.eventDescription,
		"Event Anonymous Option": formState.eventAnonymousOption,
		"Event File Option": formState.eventFileOption
	}
	
	for (var key of keys) {
		if (eventSaveVals[key] == eventDbVals[key])
			continue;
		else
		{
			hasChangedValue = true;
			break;
		}
	}
	
	if (hasChangedValue == false)
	{
		alert("No changes have been made");
		return;
	}

	buildModalForFormSave("Confirm Save");
	$('#generalConfirm').modal('toggle');

	$('#generalAcceptButton').on('click', function () {
		saveFormChanges();
		$('#generalConfirm').modal('toggle');
	});

	$('#generalCancelButton').on('click', function () {
		$('#generalConfirm').modal('toggle');
	});

});

// Save the new form values to DB
function saveFormChanges() {

	var newEventName = $('#eventNameInput').val();
	var newEventLocation = $('#locationInput').val();
	var newEventDescript = $('#eventDescriptTextArea').val();
	var newEventFileOption = $('#fileUpload').prop('checked');
	var newEventAnonymousCheck = $('#anonymousCheck').prop('checked');

	var eventSaveVals = [];

	eventSaveVals.push(newEventName);
	eventSaveVals.push(newEventLocation);
	eventSaveVals.push(newEventDescript);
	eventSaveVals.push(newEventAnonymousCheck);
	eventSaveVals.push(newEventFileOption);

	console.log(eventSaveVals);

	$.ajax({
		type: "POST",
		url: "edit_event_details.php",
		data: {
			eventHash: window.location.search.split('?key=')[1],
			eventName: eventSaveVals[0],
			eventLocation: eventSaveVals[1],
			eventDescription: eventSaveVals[2],
			isAnonymous: eventSaveVals[3],
			enableUpload: eventSaveVals[4]
		}
	}).done(function(response) {
		$('#refreshChangeHeader').text(response);	// Right here because we don't have data binding. We require a refresh for the data to reflect on the page.
		$('#refreshConfirm').modal('toggle');
		$('#saveForm').prop('disabled', true);
	});

	// Reinitialize cached data needs to be done after ajax call

}