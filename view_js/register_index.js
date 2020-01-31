 $(function() {
        $( "#datepicker2" ).datepicker({		
		startDate: new Date(),
		multidate: false,
		format: "mm/dd/yyyy",
		language: 'en'});
 });
 
 $(document).ready(function () {
 		
	var eventNameFromDB = "Insert Event Name Here";   // Replace with DB name field
	
	$('#eventName').text(eventNameFromDB);
 
 });
 
 
// Source: https://jsfiddle.net/christianklemp_imt/b20paum2/
$(document).ready(function () {

	$('#datepicker2').datepicker().on('changeDate', function (e) {

		//var popup = document.getElementById("myModal");
		//popup.style.display = "block";
		$('#timeSelectionField h2').text(e.format());
		createFields();
		selectASlot();
	});
});


function createFields() {

	$('.removeOnClear').remove(); //Clear all cells

	var selectedDuration = "15 Minutes";
	var startTime = 0;
	var temp = startTime;
	var endTime = 300;

	switch (selectedDuration) {

		case "15 Minutes":
			var minutesIncrement = 15;
			break;

		case "30 Minutes":
			var minutesIncrement = 30;
			break;
		
		case "1 Hour":
			var minutesIncrement = 60;
			break;
	}

	
	while (temp < endTime)
	{
		var newRow = $('<tr><th> '+ minutesToFormat(temp) +' </th></tr>');
		newRow.addClass("removeOnClear");
		newRow.css('vertical-align', "top");
		
		var minutesVal = $('<span>'+temp+'</span>');
		minutesVal.addClass('doNotDisplay');
		newRow.append(minutesVal);
		
		temp = temp + minutesIncrement
		newRow.attr('height', 45);
		$('#timeSelector tbody').append(newRow);

	}
	

	var newDateHeader = $('<th></th>');
	newDateHeader.addClass("removeOnClear");
	newDateHeader.attr('width', 45);
	$("#timeSelector tr:first").append(newDateHeader);

	var newDateColumn = $('<td></td>');

	newDateColumn.addClass("removeOnClear");
	$("#timeSelector tr").not(':first').not(':last').append(newDateColumn);



}

function selectASlot()
{
	$("#timeSelector td").click(function () {

		
			$(this).toggleClass("selected");

	});
}


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