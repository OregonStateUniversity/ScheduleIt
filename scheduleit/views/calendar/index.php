<?php

require_once ABSPATH . 'config/session.php';

$meetings = $database->getCalendarMeetings($_SESSION['user_id']);

echo $twig->render('calendar/index.twig', [
    'calendar_page' => true,
    'meetings_json' => json_encode($meetings),
    'title' => 'Calendar'
]);
