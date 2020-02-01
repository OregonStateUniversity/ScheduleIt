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
		$('.modal-body h2').text(e.format());
		createFields();
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
		var newRow = $('<tr><th><div>' + minutesToFormat(temp) + '</div></th><td></td></tr>');
		newRow.addClass("removeOnClear");
		newRow.attr("scope", "row");
		
		var minutesVal = $('<span>'+temp+'</span>');
		minutesVal.addClass('doNotDisplay');
		newRow.append(minutesVal);
		
		temp = temp + minutesIncrement
		$('#slotPicker tbody').append(newRow);

	}
	
	selectASlot();
	


}

function selectASlot()
{
	$("#slotPicker td").click(function () {	
		var check = false;
		
		$("#slotPicker tr td:nth-child(2)").each(function () {
			if(($(this).hasClass("slotSelected")) === true)
				$(this).toggleClass("slotSelected");
		});
		
		$(this).toggleClass("slotSelected");

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