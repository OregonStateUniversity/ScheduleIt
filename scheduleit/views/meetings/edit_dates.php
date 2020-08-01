<?php

require_once ABSPATH . 'config/session.php';

$meeting = $database->getMeetingById($meeting_id, $_SESSION['user_onid']);

if ($meeting) {
    echo $twig->render('meetings/edit_dates.twig', [
        'meeting' => $meeting,
        'title' => 'Edit Meeting Dates - ' . $meeting['name'],
    ]);
} else {
    http_response_code(404);
    echo $twig->render('errors/error_logged_in.twig', [
        'message' => 'Sorry, we couldn\'t find that meeting.',
        'title' => 'Meeting Not Found',
    ]);
}
