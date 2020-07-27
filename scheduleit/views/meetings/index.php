<?php

require_once ABSPATH . 'config/session.php';

$search_term = !empty($_GET['q']) ? $_GET['q'] : null;

$upcoming_meetings = $database->getAllUpcomingMeetings($_SESSION['user_id']);
$created_meetings = $database->getUpcomingMeetingsByCreator($_SESSION['user_id']);
$past_meetings = $database->getPastMeetings($_SESSION['user_id']);
$search_meetings = $database->getMeetingsBySearchTerm($_SESSION['user_id'], $search_term);
$invites = $database->getInvites($_SESSION['user_onid']);
$invite_count = count($invites);

echo $twig->render('meetings/index.twig', [
    'meetings_page' => true,
    'search_result_count' => count($search_meetings),
    'search_term' => $search_term,
    'upcoming_meetings' => $upcoming_meetings,
    'created_meetings' => $created_meetings,
    'past_meetings' => $past_meetings,
    'invites' => $invites,
    'invite_count' => $invite_count,
    'search_meetings' => $search_meetings,
    'title' => 'My Meetings',
]);
