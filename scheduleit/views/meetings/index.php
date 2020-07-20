<?php

require_once ABSPATH . 'config/session.php';

$search_term = isset($_GET['q']) ? $_GET['q'] : null;

$meetings = $database->getDashboardData($_SESSION['user']);

echo $twig->render('meetings/index.twig', [
    'meeting' => true,
    'search_result_count' => count($meetings),
    'search_term' => $search_term,
    'all_meetings' => $meetings,
    'my_meetings' => [],
    'past_meetings' => [],
    'title' => 'My Meetings',
]);
