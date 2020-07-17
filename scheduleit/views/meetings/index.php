<?php

require_once ABSPATH . 'config/database.php';
require_once ABSPATH . 'config/session.php';
require_once ABSPATH . 'scheduleit/config/twig.php';

$search_term = isset($_GET['q']) ? $_GET['q'] : null;

if ($search_term == 'other') {
    $meetings = [
        [
            "id" => 9,
            "date" => "Mon, Jul 20",
            "attendeeEmail" => "sibolibb@oregonstate,edu",
            "attendeeName" => "Bangbay Siboliban",
            "hostEmail" => "heer@eecs.oregonstate.edu",
            "hostName" => "Me",
            "location" => "Somewhere, USA",
            "time" => "12pm-12:30pm",
            "title" => "Don's Other Meeting",
        ],
    ];
} else {
    $meetings = [];
}

echo $twig->render('meetings/index.twig', [
    'meeting' => true,
    'meetings' => $meetings,
    'search_result_count' => count($meetings),
    'search_term' => $search_term,
    'all_meetings' => [
        [
            "id" => 1,
            "attendeeEmail" => "sibolibb@oregonstate,edu",
            "attendeeName" => "Bangbay Siboliban",
            "date" => "Fri, Jul 17",
            "hostEmail" => "heer@eecs.oregonstate.edu",
            "hostName" => "Me",
            "location" => "Somewhere, USA",
            "time" => "12pm-12:30pm",
            "title" => "Don's Meeting",
        ],
        [
            "id" => 2,
            "date" => "Sat, Jul 18",
            "hostEmail" => "czaparym@oregonstate.edu",
            "hostName" => "Michael Czapary",
            "location" => "Somewhere, USA",
            "time" => "12pm-1pm",
            "title" => "Mike's Meeting",
        ],
        [
            "id" => 3,
            "date" => "Sun, Jul 19",
            "hostEmail" => "guerrero@oregonstate.edu",
            "hostName" => "Roman Guerrero",
            "location" => "Somewhere, USA",
            "time" => "11am-12pm",
            "title" => "Roman's Meeting",
        ],
        [
            "id" => 9,
            "attendeeEmail" => "sibolibb@oregonstate,edu",
            "attendeeName" => "Bangbay Siboliban",
            "date" => "Mon, Jul 20",
            "hostEmail" => "heer@eecs.oregonstate.edu",
            "hostName" => "Me",
            "location" => "Somewhere, USA",
            "time" => "12pm-12:30pm",
            "title" => "Don's Other Meeting",
        ]
    ],
    'my_meetings' => [
        [
            "id" => 1,
            "date" => "Fri, Jul 17",
            "attendeeEmail" => "sibolibb@oregonstate,edu",
            "attendeeName" => "Bangbay Siboliban",
            "hostEmail" => "heer@eecs.oregonstate.edu",
            "hostName" => "Me",
            "location" => "Somewhere, USA",
            "time" => "12pm-12:30pm",
            "title" => "Don's Meeting",
        ],
        [
            "id" => 9,
            "date" => "Mon, Jul 20",
            "attendeeEmail" => "sibolibb@oregonstate,edu",
            "attendeeName" => "Bangbay Siboliban",
            "hostEmail" => "heer@eecs.oregonstate.edu",
            "hostName" => "Me",
            "location" => "Somewhere, USA",
            "time" => "12pm-12:30pm",
            "title" => "Don's Other Meeting",
        ]
    ],
    'past_meetings' => [
        [
            "id" => 6,
            "date" => "Sun, Jun 21",
            "hostEmail" => "guerrero@oregonstate.edu",
            "hostName" => "Roman Guerrero",
            "location" => "Somewhere, USA",
            "time" => "11am-12pm",
            "title" => "Roman's Past Meeting",
        ],
        [
            "id" => 7,
            "date" => "Sat, Jun 20",
            "hostEmail" => "czaparym@oregonstate.edu",
            "hostName" => "Michael Czapary",
            "location" => "Somewhere, USA",
            "time" => "12pm-1pm",
            "title" => "Mike's Past Meeting",
        ],
        [
            "id" => 8,
            "date" => "Fri, Jun 19",
            "hostEmail" => "heer@eecs.oregonstate.edu",
            "hostName" => "Me",
            "location" => "Somewhere, USA",
            "time" => "12pm-12:30pm",
            "title" => "Don's Past Meeting",
        ]
    ],
    'title' => 'My Meetings',
]);
