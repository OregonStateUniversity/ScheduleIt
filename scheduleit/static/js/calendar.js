$(function() {
  calendarInit();
});

function calendarInit() {
  const calendarElement = document.getElementById("calendar");

  if (calendarElement) {
    const events = $("#calendar").data("calendar-events");
    const siteUrl = $("#calendar").data("site-url");
    const userId = $("#calendar").data("user-id");

    const mappedEvents = events.map(function(event) {
      return {
        classNames: event.creator_id === parseInt(userId, 10) ? ["calendar-meeting-mine"] : ["calendar-meeting-other"],
        title: event.name,
        url: event.creator_id === parseInt(userId, 10) ? `${siteUrl}/meetings/${event.id}` : `${siteUrl}/invite?key=${event.meeting_hash}`,
        start: event.start_time
      }
    });

    const calendar = new FullCalendar.Calendar(calendarElement, {
      events: mappedEvents,
      initialView: "dayGridMonth"
    });
    calendar.render();
  }
}
