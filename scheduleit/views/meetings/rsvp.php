<?php

require_once ABSPATH . 'config/session.php';

$meeting = $database->getMeeting($meeting_id, $_SESSION['user']);

if (count($meeting) > 0) {
    echo $twig->render('meetings/rsvp.twig', [
        'meeting' => $meeting[0],
        'title' => 'RSVP - ' . $meeting[0]['name'],
    ]);
} else {
    http_response_code(404);
    echo $twig->render('errors/error_logged_in.twig', [
        'message' => 'Sorry, we couldn\'t find that meeting.',
        'title' => 'Meeting Not Found',
    ]);
}
