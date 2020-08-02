$(function () {
  initLocationInput(); // initialize location auto complete list
  timesSelector();
});

//source: https://blog.teamtreehouse.com/creating-autocomplete-dropdowns-datalist-element

function initLocationInput() {
  var dataList = document.getElementById("location-data-list");
  var input = document.getElementById("location-input");

  if (input) {
    // create a new xmlhttprequest.
    var request = new XMLHttpRequest();

    // Handle state changes for the request.
    request.onreadystatechange = function (response) {
      if (request.readyState === 4) {
        if (request.status === 200) {
        // parse the JSON
        var jsonOptions = JSON.parse(request.responseText);

        // Loop over the JSON array.
        jsonOptions.forEach(function (item) {
          // Create a new <option> element.
          var option = document.createElement("option");

            // Set the value using the item in the JSON array.
            option.value = item.name;

            // Add the <option> element to the <datalist>
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
    request.open("GET", input.getAttribute('data-locations-json'), true);
    request.send();
  }
}

function timesSelector() {
  const selectedDates = [];
  const calendarElement = document.getElementById("calendar-times-selector");
  const calendarSettings = {
    initialView: "dayGridMonth",
    dateClick: function(info) {
      const { dateStr: date, dayEl: el } = info;

      if ($(el).hasClass("fc-day-active")) {
        const dateIndex = selectedDates.indexOf(date);

        $(el).removeClass("fc-day-active");
        removeDate(date);

        if (dateIndex > -1) {
          selectedDates.splice(dateIndex, 1);
        }
      } else {
        $(el).addClass("fc-day-active");
        addDate(date);
        selectedDates.push(date);
      }

      if (!selectedDates.length) {
        $(".times-selector-placeholder").removeClass("d-none");
      }
    },
    datesSet: function(info) {
      selectedDates.forEach(function(date) {
        $(`[data-date="${date}"]`).addClass("fc-day-active");
      });
    }
  }

  if ($("#calendar-times-selector").data("create") === true) {
    calendarSettings.validRange = function(nowDate) {
      return {
        start: nowDate
      };
    }
  }

  const calendar = new FullCalendar.Calendar(calendarElement, calendarSettings);
  calendar.render();

  $("#duration").on("change", function() {
    updateAvailableTimes();
  });

  function updateAvailableTimes() {
    let timeLabels = "";

    // Create times
    const times = [];

    let startTime = $("#calendar-times-selector").data("start-time");
    const endTime = $("#calendar-times-selector").data("end-time");
    const duration = $("#duration").val();

    while (startTime < endTime) {
      times.push(moment(startTime, "HH:mm:ss").format("HH:mm:ss"));
      startTime = moment(startTime, "HH:mm:ss").add(duration, "minutes").format("HH:mm:ss");
    }

    times.forEach((time) => {
      const timeLabel = moment(time, "HH:mm:ss").format("h:mm a");
      timeLabels += `<div class="times-label">${timeLabel}</div>`;
    });

    $("#times-selector-legend").html(timeLabels);
    $("#times-selector").html('<li class="times-selector-placeholder">Select dates first.</li>');

    selectedDates.forEach(function(date) {
      addDate(date);
    });
  }

  function addDate(date) {
    let timeCheckboxes = "";

    // Create times
    const times = [];

    let startTime = $("#calendar-times-selector").data("start-time");
    const endTime = $("#calendar-times-selector").data("end-time");
    const duration = $("#duration").val();

    while (startTime < endTime) {
      times.push(moment(startTime, "HH:mm:ss").format("HH:mm:ss"));
      startTime = moment(startTime, "HH:mm:ss").add(duration, "minutes").format("HH:mm:ss");
    }

    times.forEach((time) => {
      const timeLabel = moment(time, "HH:mm:ss").format("hh:mm A");
      timeCheckboxes += `<label class="times-label"><input name="datetime[]" data-meetings-datetime value="${date} ${time}" type="checkbox"> ${timeLabel}</label>`;
    });

    $("#times-selector").append(`<li class="time-selector-list-item" id="time-${date}"><h4 class="date-label mb-0 text-center">${moment(date, "YYYY-MM-DD").format("ddd, MMM D")}</h4>${timeCheckboxes}</li>`);

    const sorted = $("#times-selector li").sort((a, b) => {
      return a.id > b.id ? 1 : -1;
    })

    sorted.each(function() {
      const elem = $(this);
      elem.remove();
      $(elem).appendTo("#times-selector");
    });

    $(".times-selector-placeholder").addClass("d-none");
  }

  function removeDate(date) {
    $(`#time-${date}`).remove();
  }

  const savedDates = $("#calendar-times-selector").data("dates");

  savedDates.forEach(function(date) {
    $(`[data-date="${date}"]`).addClass("fc-day-active");
    selectedDates.push(date);
  });

  $("body").on("click", "[data-meetings-datetime]", function() {
    if ($(this).prop("checked")) {
      $(this).parent("label").addClass("times-label--checked");
    } else{
      $(this).parent("label").removeClass("times-label--checked")
    }
  });
}
