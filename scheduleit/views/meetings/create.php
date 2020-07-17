<?php

require_once ABSPATH . 'config/database.php';
require_once ABSPATH . 'config/session.php';
require_once ABSPATH . 'scheduleit/config/twig.php';

echo $twig->render('meetings/create.twig', [
    'title' => 'Create Meeting',
]);
