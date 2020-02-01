/******************************************************************
* editGeneralFunctions.js
*
* This JavaScript file refers to edit page. These functions are used throughout both the edit time and edit form page. 
* 
*
* FUTURE TASKS:
*
* - Refactoring
*
*
*********************************************************************/




$(document).ready(function () {

	disableEnterInputOnFields();
	
	document.getElementById("field1to2").addEventListener("click", function () {
		$('.entryField1').addClass('collapse');
		$('.entryField2').removeClass('collapse');
	})

	document.getElementById("field2to1").addEventListener("click", function () {
		$('.entryField2').addClass('collapse');
		$('.entryField1').removeClass('collapse');
	})

});

// Make it so hitting the enter key on an input field doesn't redirect the user
function disableEnterInputOnFields() {

	$('form input').keydown(function (e) {
		if (e.keyCode == 13) {
			e.preventDefault();
			return false;
		}
	});
}

// Modal used for confirmation changes (duration changes, capacity changes, anonymous event option toggle, file upload toggle)
function buildModalForChangeConfirmation(modalHeaderName, description) {

	$('#generalHeaderLabel').text(modalHeaderName);

	var warningLabel = $('<label>*Warning*</label>');
	warningLabel.attr("id", "warningTag");
	var warningMessage = $('<text> ' + description + ' </text>');
	warningMessage.attr("id", "confirmationDescription");

	$('.confirmationDescriptionContainer').append(warningLabel);
	$('.confirmationDescriptionContainer').append('<br>');
	$('.confirmationDescriptionContainer').append('<br>');
	$('.confirmationDescriptionContainer').append(warningMessage);

	$('#generalAcceptButton').off();
	$('#generalCancelButton').off();

}

// Clear the general modal upon hiding (finished using)
$('#generalConfirm').on('hidden.bs.modal', function () {

	resetCanceledInput();
	clearModal();
});

// Clear the general modal
function clearModal() {

	$('.confirmationDescriptionContainer').empty();
	$('#generalAcceptButton').off();
	$('#generalCancelButton').off();
	$('.close').off();
	$('#generalHeaderLabel').text("");
}

$(function () {
	$('[data-toggle="tooltip"]').tooltip()
})

// Menu Toggle Script

$("#menu-toggle").click(function (e) {
	e.preventDefault();
	$("#wrapper").toggleClass("toggled");
});


// Refresh button
$('#refreshButton').on('click', function() {
	location.reload(true);
});