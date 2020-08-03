<?php

require_once ABSPATH . 'config/session.php';

$meeting = $database->getMeetingById($meeting_id, $_SESSION['user_onid']);

if ($meeting) {
    $dates = [];
    $date_objects =  $database->getDatesByMeetingId($meeting_id);
    $timeslots = $database->getTimeslotsByMeetingId($meeting_id);
    $meeting['duration'] = $timeslots[0]['duration'];
    $meeting['slot_capacity'] = $timeslots[0]['slot_capacity'];
    $timeslot_times = [];

    foreach ($timeslots as $key => $timeslot) {
        array_push($timeslot_times, $timeslot['start_time']);
    }

    foreach ($date_objects as $key => $date) {
        array_push($dates, $date['date']);
    }

    // Create time labels
    $time_labels = [];

    $start_time = strtotime(MEETINGS_START_TIME);
    $end_time = strtotime(MEETINGS_END_TIME);

    $current = time();
    $add_time = strtotime('+' . $meeting['duration'] . ' mins', $current);
    $diff = $add_time - $current;

    while ($start_time < $end_time) {
        array_push($time_labels, date('G:i:s', $start_time));
        $start_time += $diff;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // TODO: Create timeslots
    }

    echo $twig->render('meetings/edit_dates.twig', [
        'dates' => $dates,
        'dates_json' => json_encode($dates),
        'edit_dates' => true,
        'meeting' => $meeting,
        'meetings_end_time' => MEETINGS_END_TIME,
        'meetings_start_time' => MEETINGS_START_TIME,
        'time_labels' => $time_labels,
        'timeslot_times' => $timeslot_times,
        'title' => 'Edit Meeting Dates - ' . $meeting['name'],
    ]);
} else {
    http_response_code(404);
    echo $twig->render('errors/error_logged_in.twig', [
        'message' => 'Sorry, we couldn\'t find that meeting.',
        'title' => 'Meeting Not Found',
    ]);
}
