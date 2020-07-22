<?php

require_once ABSPATH . 'config/session.php';

$search_term = !empty($_GET['q']) ? $_GET['q'] : null;

$upcoming_meetings = $database->getAllUpcomingMeetings($_SESSION['user']);
$created_meetings = $database->getUpcomingMeetingsByCreator($_SESSION['user']);
$past_meetings = $database->getPastMeetings($_SESSION['user']);
$search_meetings = $database->getMeetingsBySearchTerm($_SESSION['user'], $search_term);

echo $twig->render('meetings/index.twig', [
    'meetings_page' => true,
    'search_result_count' => count($search_meetings),
    'search_term' => $search_term,
    'upcoming_meetings' => $upcoming_meetings,
    'created_meetings' => $created_meetings,
    'past_meetings' => $past_meetings,
    'rsvps' => [1, 2],
    'search_meetings' => $search_meetings,
    'title' => 'My Meetings',
]);
