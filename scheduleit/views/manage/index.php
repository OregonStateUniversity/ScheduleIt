<?php

require_once ABSPATH . 'config/session.php';

$search_term = isset($_GET['q']) ? $_GET['q'] : null;

if ($search_term == 'other') {
    $meetings = [
        [
            "id" => 9,
            "dates" => "July 20, 2020",
            "location" => "Somewhere, USA",
            "slots" => 20,
            "slots_available" => 18,
            "title" => "Don's Other Meeting",
        ],
    ];
} elseif ($search_term) {
    $meetings = [];
} else {
    $meetings = [
        [
            "id" => 1,
            "dates" => ["July 17, 2020", "July 19, 2020"],
            "location" => "Somewhere, USA",
            "slots" => 20,
            "slots_available" => 18,
            "title" => "Don's Meeting",
        ],
        [
            "id" => 9,
            "dates" => ["July 20, 2020"],
            "location" => "Somewhere, USA",
            "slots" => 20,
            "slots_available" => 20,
            "title" => "Don's Other Meeting",
        ],
    ];
}

echo $twig->render('manage/index.twig', [
    'manage_page' => true,
    'meetings' => $meetings,
    'search_result_count' => count($meetings),
    'search_term' => $search_term,
    'title' => 'Manage Created',
]);
