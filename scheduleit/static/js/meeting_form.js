$(function() {
  initLocationInput(); // initialize location auto complete list
  timesSelector.init();
});

//source: https://blog.teamtreehouse.com/creating-autocomplete-dropdowns-datalist-element

function initLocationInput() {
  const dataList = document.getElementById("location-data-list");
  const input = document.getElementById("location-input");

  if (input) {
    // create a new xmlhttprequest.
    const request = new XMLHttpRequest();

    // Handle state changes for the request.
    request.onreadystatechange = function (response) {
      if (request.readyState === 4) {
        if (request.status === 200) {
        // parse the JSON
        const jsonOptions = JSON.parse(request.responseText);

        // Loop over the JSON array.
        jsonOptions.forEach(function (item) {
          // Create a new <option> element.
          const option = document.createElement("option");

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

const timesSelector = {
  init: function() {
    $("body").on("click", "[data-meetings-datetime-confirm]", this.addToRemovedTimes.bind(this));
    $("body").on("click", "[data-meetings-datetime]", function(event) {
      event.preventDefault();
    });
    $("body").on("mousedown mouseover", "[data-meetings-datetime-label]:not('.times-label--checked-confirm')", this.selectMultipleTimes.bind(this));
    $("#duration").on("change", this.updateAvailableTimesCheck.bind(this));
    $("#btn-save-meeting-dates").on("click", this.confirmTimesRemoval.bind(this));
    this.initSavedDates();
    this.initSelectedDates();
    this.initCalendar();
    this.initDuration();
  },
  initSavedDates: function() {
    const savedDates = $("#calendar-times-selector").data("dates-saved") || [];

    savedDates.forEach((date) => {
      this.savedDates.push(date);
    });
  },
  initSelectedDates: function() {
    const selectedDates = $("#calendar-times-selector").data("dates") || [];

    selectedDates.forEach((date) => {
      this.selectedDates.push(date);
    });
  },
  initCalendar: function() {
    const calendarElement = document.getElementById("calendar-times-selector");

    if (calendarElement) {
      const calendarSettings = {
        initialView: "dayGridMonth",
        dateClick: (info) => {
          const { dateStr: date, dayEl: el } = info;

          if ($(el).hasClass("fc-day-confirm")) {

            $("#remove-date-modal").modal("show");

            const _this = this;

            $("#btn-remove-date").on("click", function() {
              const dateIndex = _this.selectedDates.indexOf(date);

              $(el).removeClass("fc-day-active").removeClass("fc-day-confirm");
              _this.removeDate(date);

              if (dateIndex > -1) {
                _this.selectedDates.splice(dateIndex, 1);
              }

              $("#remove-date-modal").modal("hide");
            });
          } else {
            if ($(el).hasClass("fc-day-active")) {
              const dateIndex = this.selectedDates.indexOf(date);

              $(el).removeClass("fc-day-active");
              this.removeDate(date);

              if (dateIndex > -1) {
                this.selectedDates.splice(dateIndex, 1);
              }
            } else {
              $(el).addClass("fc-day-active");
              this.addDate(date);
              this.selectedDates.push(date);
            }
          }

          if (!this.selectedDates.length) {
            $(".times-selector-placeholder").removeClass("d-none");
          }
        },
        datesSet: (info) => {
          this.savedDates.forEach(function(date) {
            $(`[data-date="${date}"]`).addClass("fc-day-confirm");
          });

          this.selectedDates.forEach(function(date) {
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
    }
  },
  initDuration: function() {
    // Use the saved duration to set value in case of back button issues
    const duration = $("#duration").data("saved-duration");
    $("#duration").val(duration);
  },
  addDate(date) {
    const times = this.createTimes();
    let timeCheckboxes = "";

    times.forEach((time) => {
      const timeLabel = moment(time, "HH:mm:ss").format("hh:mm A");
      timeCheckboxes += `<label class="times-label" data-meetings-datetime-label="${date} ${time}">` +
        `<input name="timeslots[]" data-meetings-datetime="${date} ${time}" value="${date} ${time}" type="checkbox"> ${timeLabel}` +
        '</label>';
    });

    $("#times-selector").append(
      `<li class="time-selector-list-item" id="time-${date}">` +
      `<h4 class="date-label mb-0 text-center">${moment(date, "YYYY-MM-DD").format("ddd, MMM D")}</h4>` +
      `${timeCheckboxes}` +
      '</li>'
    );

    const sorted = $("#times-selector li").sort((a, b) => {
      return a.id > b.id ? 1 : -1;
    })

    sorted.each(function() {
      const elem = $(this);
      elem.remove();
      $(elem).appendTo("#times-selector");
    });

    $(".times-selector-placeholder").addClass("d-none");
  },
  addToRemovedTimes: function(e) {
    e.preventDefault();
    let datetime = $(e.currentTarget).val();
    $(`input[data-meetings-datetime-confirm="${datetime}"]`)
      .prop("checked", false)
      .removeAttr("data-meetings-datetime-confirm")
      .attr("data-meetings-datetime", "");

    $(`label[data-meetings-datetime-label="${datetime}"]`)
      .removeClass("times-label--checked")
      .removeClass("times-label--checked-confirm");

    $("#removed-times").append(`
      <li data-meetings-datetime-removal="${datetime}">${moment(datetime).format("ddd, MMM D h:mm a")}</li>
    `);

    const sorted = $("#removed-times li").sort((a, b) => {
      return a.id > b.id ? 1 : -1;
    })

    sorted.each(function() {
      const elem = $(this);
      elem.remove();
      $(elem).appendTo("#removed-times");
    });
  },
  confirmTimesRemoval: function(e) {
    e.preventDefault();

    if ($("#removed-times li").length > 0) {
      $("#remove-time-modal").modal("show");

      $("#btn-remove-time").on("click", function() {
        $("#form-meeting-dates").submit();
      });
    } else {
      $("#form-meeting-dates").submit();
    }
  },
  createTimes: function() {
    const times = [];

    let startTime = $("#calendar-times-selector").data("start-time");
    const endTime = $("#calendar-times-selector").data("end-time");
    const duration = $("#duration").val();

    while (startTime < endTime) {
      times.push(moment(startTime, "HH:mm:ss").format("HH:mm:ss"));
      startTime = moment(startTime, "HH:mm:ss").add(duration, "minutes").format("HH:mm:ss");
    }

    return times;
  },
  removeDate: function(date) {
    $(`#time-${date}`).remove();
  },
  savedDates: [],
  selectMultipleTimes: function(event) {
    event.preventDefault();

    if (event.type === "mousedown") {
      this.toggleTimeSelection(event.currentTarget, true);
      $("#times-selector").data("drag-active", true);
    }

    if (event.type === "mouseover" && $("#times-selector").data("drag-active") === true) {
      this.toggleTimeSelection(event.currentTarget);
    }

    $(document).mouseup(function() {
      $("#times-selector").data("drag-active", false);
    });
  },
  selectedDates: [],
  toggleTimeSelection: function(target, startingCell) {
    const datetime = $(target).data("meetings-datetime-label");

    if (startingCell) {
      const startingValue = $(target).hasClass("times-label--checked") ? "false" : "true";
      $("#times-selector").data("drag-start-cell", startingValue);
    }

    if ($("#times-selector").data("drag-start-cell") === "true") {
      $(target).addClass("times-label--checked");
      $(`[data-meetings-datetime="${datetime}"]`).prop("checked", true);
      $(`[data-meetings-datetime-removal="${datetime}"]`).remove();
    } else {
      $(target).removeClass("times-label--checked");
      $(`[data-meetings-datetime="${datetime}"]`).prop("checked", false);
    }
  },
  updateAvailableTimes: function() {
     const times = this.createTimes();
    let timeLabels = "";

    times.forEach((time) => {
      const timeLabel = moment(time, "HH:mm:ss").format("h:mm a");
      timeLabels += `<div class="times-label">${timeLabel}</div>`;
    });

    $("#times-selector-legend").html(timeLabels);
    $("#times-selector").html('<li class="times-selector-placeholder">Select dates first.</li>');

    this.selectedDates.forEach((date) => {
      this.addDate(date);
    });
  },
  updateAvailableTimesCheck: function(event) {
    const _this = this;
    event.preventDefault();

    if ($("#change-duration-modal").length) {
      $("#change-duration-modal").modal("show");

      $("#btn-change-duration").on("click", function() {
        // Update the saved duration so closing modal won't reset the value
        $("#duration").data("saved-duration", $(event.currentTarget).val());
        $("#change-duration-modal").modal("hide");
        _this.updateAvailableTimes();
      });

      $("#change-duration-modal").on("hide.bs.modal", function() {
        _this.initDuration();
      });
    } else {
      _this.updateAvailableTimes();
    }
  }
};
