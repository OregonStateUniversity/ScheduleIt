<?php

require_once ABSPATH . 'config/database.php';
require_once ABSPATH . 'config/session.php';
require_once ABSPATH . 'scheduleit/config/twig.php';

echo $twig->render('calendar/index.twig', [
    'calendar_page' => true,
    'title' => 'Calendar'
]);
