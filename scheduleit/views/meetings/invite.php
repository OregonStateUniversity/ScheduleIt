<?php

require_once ABSPATH . 'config/session.php';

$key = !empty($_GET['key']) ? $_GET['key'] : null;

$meeting = $database->getMeetingByHash($key);

if ($meeting) {
    echo $twig->render('meetings/invite.twig', [
        'meeting' => $meeting,
        'title' => 'Invite - ' . $meeting['name'],
    ]);
} else {
    http_response_code(404);
    echo $twig->render('errors/error_logged_in.twig', [
        'message' => 'Sorry, we couldn\'t find that meeting.',
        'title' => 'Meeting Not Found',
    ]);
}
