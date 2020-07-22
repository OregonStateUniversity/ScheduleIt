<?php

require_once ABSPATH . 'config/session.php';

$results = $database->getMeeting($meeting_id, $_SESSION['user']);
$meetings = [];

// Add dates to meetings
foreach ($results as $key => $meeting) {
    if ($meeting['id']) {
        $meeting['dates'] = $database->getDatesByMeetingId($meeting['id']);
        $meeting['dates_count'] = count($meeting['dates']);
    }

    array_push($meetings, $meeting);
}

$attendee_meetings = $database->getMeetingAttendees($meeting_id, $_SESSION['user']);

if (count($meetings) > 0) {
    echo $twig->render('meetings/show.twig', [
        'attendee_meetings' => $attendee_meetings,
        'meeting' => $meetings[0],
        'title' => $meetings[0]['name'],
    ]);
} else {
    http_response_code(404);
    echo $twig->render('errors/error_logged_in.twig', [
        'message' => 'Sorry, we couldn\'t find that meeting.',
        'title' => 'Meeting Not Found',
    ]);
}
