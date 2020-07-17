<?php

require_once ABSPATH . 'config/database.php';
require_once ABSPATH . 'config/session.php';
require_once ABSPATH . 'scheduleit/config/twig.php';

echo $twig->render('errors/error.twig', [
    'message' => 'Sorry, we couldn\'t find that page.',
    'title' => 'Page Not Found'
]);
