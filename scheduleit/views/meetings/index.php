<?php

require_once ABSPATH . 'config/session.php';

$search_term = !empty($_GET['q']) ? $_GET['q'] : null;

$upcoming_meetings = $database->getAllUpcomingMeetings($_SESSION['user_id']);
$created_meetings = $database->getUpcomingMeetingsByCreator($_SESSION['user_id']);
$past_meetings = $database->getPastMeetings($_SESSION['user_id']);
$search_meetings = $database->getMeetingsBySearchTerm($_SESSION['user_id'], $search_term);
$invite_meetings = [];

// Add dates to invites
foreach ($invites as $key => $meeting) {
    if ($meeting['id']) {
        $meeting['dates'] = $database->getDatesByMeetingId($meeting['id']);
        $meeting['dates_count'] = count($meeting['dates']);
    }

    array_push($invite_meetings, $meeting);
}

echo $twig->render('meetings/index.twig', [
    'meetings_page' => true,
    'search_result_count' => count($search_meetings),
    'search_term' => $search_term,
    'upcoming_meetings' => $upcoming_meetings,
    'created_meetings' => $created_meetings,
    'past_meetings' => $past_meetings,
    'invites' => $invite_meetings,
    'search_meetings' => $search_meetings,
    'title' => 'My Meetings',
]);
