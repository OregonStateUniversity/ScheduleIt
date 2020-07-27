<?php

require_once ABSPATH . 'config/session.php';

$hash = $_GET['key'];

$meeting = $database->getMeetingByHash($hash);

if ($meeting) {
    echo $twig->render('invites/show.twig', [
    ]);
} else {
    http_response_code(404);
    echo $twig->render('errors/error_logged_in.twig', [
        'message' => 'Sorry, we couldn\'t find that invite.',
        'title' => 'Invite Not Found',
    ]);
}
