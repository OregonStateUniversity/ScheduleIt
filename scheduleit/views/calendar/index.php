<?php

require_once ABSPATH . 'config/session.php';

echo $twig->render('calendar/index.twig', [
    'calendar_page' => true,
    'title' => 'Calendar'
]);
