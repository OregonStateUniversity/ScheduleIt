<?php

require_once ABSPATH . 'config/session.php';

$meetings = $database->getCalendarMeetings($_SESSION['user']);

echo $twig->render('calendar/index.twig', [
    'calendar_page' => true,
    'meetings' => json_encode($meetings),
    'title' => 'Calendar'
]);
