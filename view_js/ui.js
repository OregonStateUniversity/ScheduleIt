/******************************************************************
* ui.js
*
* This JavaScript file is intended to be a general functions file. This should hold all functions that are used throughout the page.
* Likewise, this file should be included in every page of the application.
* 
* FUTURE TASKS:
*
* - Refactoring: There are a lot of reusable code that could be moved over to this file from the other JS files. Particularly the date functions 
*   throughout the JS files. Though it might be better to make a specific date formatter JS files and move it into that to keep this more organized.
* 
*********************************************************************/

// Sidebar feature (mobile)
$(document).ready(function () {
    $('#sidebarCollapse, #sidebarCollapseIcon').on('click', function () {
        $('#sidebar').toggleClass('hidden');
    });
});

// Search bar feature
$(document).ready(function(){
	$('#tableSearch').on('keyup', function() {
	  var value = $(this).val().toLowerCase();
	  $('.tableBody tr').filter(function() {
		$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
	  });
	});
  });


function minutesToFormat(totalMinutes) {
	totalMinutes = totalMinutes + "";
	
	if (totalMinutes.search("AM") != -1 || totalMinutes.search("PM") != -1) {
		return totalMinutes;
	}

	var timeString;
	var startHour = 7;
	var formatChange = 12 - startHour;

	var hours = startHour + Math.floor(totalMinutes / 60);
	var minutes = parseInt(totalMinutes) % 60;
	var format;

	if (Math.floor(totalMinutes / 60) >= formatChange) {
		if (hours != 12) hours = hours - 12;
		format = "PM";
	}
	else {
		format = "AM";
	}
	
	if (minutes === 0)
		timeString = hours + ":" + "00 "  + format;
	else
		timeString = hours + ":" + minutes + " " + format;

	return timeString;

}

function formatTime(time) {

	var temp = time.slice(11,13);
	var hour = parseInt(temp);

	if(hour > 12) {
		var newHour = hour - 12;
		var newTime = newHour.toString() + time.slice(13,16) + " PM";
		return newTime;
	}
	else if(hour === 12) {
		var newTime = hour.toString() + time.slice(13,16) + " PM";
		return newTime;
	}
	else {
		var newTime = hour.toString() + time.slice(13,16) + " AM";
		return newTime;
	}

}

function formatDate(date) {
	
	var dateYear = date.slice(0,4);
	
	var dateDay = date.slice(8, 10);
	dateDay = dateDay.replace(/^0+/, '') //truncate leading 0's
	
	var dateMonth = date.slice(5,7);
	dateMonth = dateMonth.replace(/^0+/, '')
	
	return dateMonth + "/" + dateDay + "/" + dateYear;

}

function formatDateTime(targetString) {

    var formattedDate = formatDate(targetString); 
    var formattedTime = formatTime(targetString); 
    return formattedTime + ' on ' +  formattedDate;

}

function formatTableDateTime(columnIndex) {

	var tableBody = document.getElementsByTagName('tbody')[0];
	
	if (tableBody == undefined)
		return;

    for (row of tableBody.children) {
        const timeSlotString = row.children[columnIndex].innerText;
        row.children[columnIndex].innerText = formatDateTime(timeSlotString);
	}
		
	var tableBody = document.getElementsByTagName('tbody')[1];

	if (tableBody != undefined) {
		for (row of tableBody.children) {
			var timeSlotString = row.children[columnIndex].innerText;
			row.children[columnIndex].innerText = formatDateTime(timeSlotString);
		}
	}	
	
}