<?php

$error_code = !empty($_GET['s']) ? $_GET['s'] : '';

switch ($error_code) {
    case '500':
        http_response_code(500);
        $message = 'There was a problem making a connection.';
        $title = 'Server error';
        break;
    default:
        http_response_code(404);
        $message = "Sorry, we couldn't find that page.";
        $title = 'Page not found';
}

echo $twig->render('errors/error_logged_out.twig', [
    'message' => $message,
    'title' => $title
]);
