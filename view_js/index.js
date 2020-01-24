$(function () {
	$('[data-toggle="tooltip"]').tooltip()
})

window.onload = function () {

	document.getElementById("field1to2").addEventListener("click", function () {
		$('.entryField1').addClass('collapse');
		$('.entryField2').removeClass('collapse');
	})

	document.getElementById("field2toSubmit").addEventListener("click", function () {
		cellCount();
		$('.entryField2').addClass('collapse');
		$('.submitField').removeClass('collapse')
	})

	document.getElementById("field2to1").addEventListener("click", function () {
		$('.entryField2').addClass('collapse');
		$('.entryField1').removeClass('collapse');
	})

}


function minutesToFormat(totalMinutes) {

	if (totalMinutes.search("AM") != -1 || totalMinutes.search("PM") != -1) {
		console.log(totalMinutes);
		return totalMinutes;
	}

	var timeString;
	var startHour = 7;
	var formatChange = 12 - startHour;

	var hours = startHour + Math.floor(totalMinutes / 60);
	var minutes = totalMinutes % 60;
	var format;

	if (Math.floor(totalMinutes / 60) >= formatChange) {
		if (hours != 12) hours = hours - 12;
		format = "PM";
	}
	else {
		format = "AM";
	}

	timeString = hours + ":" + minutes + " " + format;
	console.log(timeString);
	return timeString;

}



$('#durationSelector').on('change', function () {

	$('.removeOnClear').remove(); //Clear all cells
	$('#datepicker').datepicker('update', ''); // clear all dates

	var selectedDuration = $("#durationSelector").val();

	var offset = 0;
	var minutes = 0;
	var hourInMinutes = 60;
	var totalHours = 12;


	switch (selectedDuration) {

		case "15 Minutes":

			var durationOffset = 4;
			var durationSlots = 3;
			var minutesIncrement = 15;
			break;

		case "30 Minutes":

			var durationOffset = 2;
			var durationSlots = 1;
			var minutesIncrement = 30;
			break;

		case "1 Hour":

			return;
			break;

	}

	for (i = 0; i < totalHours; i++) {

		for (j = 0; j < durationSlots; j++) {

			minutes = minutes + minutesIncrement;
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

$(document).ready(function () {

	$('#datepicker').datepicker({

		startDate: new Date(),
		multidate: true,
		format: "mm/dd/yyyy",
		daysOfWeekHighlighted: "5,6",
		datesDisabled: ['31/08/2017'],
		language: 'en'

	}).on('changeDate', function (e) {

		dragTable(); // Add event handler

		// `e` here contains the extra attributes

		//$(this).find('.input-group-addon .count').text(' ' + e.dates.length);

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

});


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


function cellCount() {

	var num = 0;
	var slot = { startDate: "", endDate: "" };
	var slotArray = [];

	$("#timeSelector thead tr th").each(function (index) {

		if (index != 0) {

			var currDate = $(this).text();
			index++;

			$('#timeSelector tr td:nth-child(' + index + ')').each(function () {

				if ($(this).hasClass("selected")) {
					var currTime = $(this).closest('tr').find('th').text();
					console.log(currTime);
					console.log(currDate);
				}

			});

		}
	});

}


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
