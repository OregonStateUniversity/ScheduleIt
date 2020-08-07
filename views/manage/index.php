<?php

require_once ABSPATH . 'config/session.php';

$search_term = !empty($_GET['q']) ? $_GET['q'] : '';
$results = $database->getManageMeetings($_SESSION['user_id'], $search_term);
$meetings = [];

// Add dates to meetings
foreach ($results as $key => $meeting) {
    if ($meeting['id']) {
        $meeting['dates'] = $database->getDatesByMeetingId($meeting['id']);
        $meeting['dates_count'] = count($meeting['dates']);
    }

    array_push($meetings, $meeting);
}

echo $twig->render('manage/index.twig', [
    'manage_page' => true,
    'meetings' => $meetings,
    'search_result_count' => count($meetings),
    'search_term' => $search_term,
    'title' => 'Manage Created',
]);
