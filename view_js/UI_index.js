$(document).ready(function () {
    $('#sidebarCollapse, #sidebarCollapseIcon').on('click', function () {
        $('#sidebar').toggleClass('hidden');
    });
});

function minutesToFormat(totalMinutes) {
	totalMinutes = totalMinutes + "";
	
	if (totalMinutes.search("AM") != -1 || totalMinutes.search("PM") != -1) {
		//console.log(totalMinutes);
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
	//console.log(timeString);
	return timeString;

}