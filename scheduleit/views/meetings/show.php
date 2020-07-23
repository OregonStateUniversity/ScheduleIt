<?php

require_once ABSPATH . 'config/session.php';

$meeting = $database->getMeetingById($meeting_id, $_SESSION['user']);

if ($meeting) {
    $meeting['dates'] = $database->getDatesByMeetingId($meeting['id']);
    $meeting['dates_count'] = count($meeting['dates']);
    $attendee_meetings = $database->getMeetingAttendees($meeting_id);

    echo $twig->render('meetings/show.twig', [
        'attendee_meetings' => $attendee_meetings,
        'meeting' => $meeting,
        'title' => $meeting['name'],
    ]);
} else {
    http_response_code(404);
    echo $twig->render('errors/error_logged_in.twig', [
        'message' => 'Sorry, we couldn\'t find that meeting.',
        'title' => 'Meeting Not Found',
    ]);
}
