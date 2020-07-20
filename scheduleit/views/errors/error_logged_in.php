<?php

require_once ABSPATH . 'config/session.php';

echo $twig->render('errors/error_logged_in.twig', [
    'message' => 'Sorry, we couldn\'t find that page.',
    'title' => 'Page Not Found'
]);
