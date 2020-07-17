<?php

require_once ABSPATH . 'config/database.php';
require_once ABSPATH . 'config/session.php';
require_once ABSPATH . 'scheduleit/config/twig.php';

$all_meetings = [
    [
        "id" => 1,
        "date" => "Fri, Jul 17",
        "description" => "Lorem ipsum dolor amet.",
        "hostEmail" => "heer@eecs.oregonstate.edu",
        "hostName" => "Me",
        "location" => "Somewhere, USA",
        "time" => "12pm-12:30pm",
        "title" => "Don's Meeting",
    ],
    [
        "id" => 2,
        "date" => "Sat, Jul 18",
        "description" => "Lorem ipsum dolor amet.",
        "hostEmail" => "czaparym@oregonstate.edu",
        "hostName" => "Michael Czapary",
        "location" => "Somewhere, USA",
        "time" => "12pm-1pm",
        "title" => "Mike's Meeting",
    ],
    [
        "id" => 3,
        "date" => "Sun, Jul 19",
        "description" => "Lorem ipsum dolor amet.",
        "hostEmail" => "guerrero@oregonstate.edu",
        "hostName" => "Roman Guerrero",
        "location" => "Somewhere, USA",
        "time" => "11am-12pm",
        "title" => "Roman's Meeting",
    ],
    [
        "id" => 4,
        "date" => "Sun, Jul 26",
        "description" => "Lorem ipsum dolor amet.",
        "hostEmail" => "sibolibb@oregonstate.edu>",
        "hostName" => "Bangbay Siboliban",
        "location" => "Somewhere, USA",
        "time" => "11am-12pm",
        "title" => "Bangbay's Meeting",
    ],
    [
        "id" => 5,
        "date" => "Sat, Jul 25",
        "description" => "Lorem ipsum dolor amet.",
        "hostEmail" => "william.pfeil@oregonstate.ed",
        "hostName" => "Bill Pfeil",
        "location" => "Somewhere, USA",
        "time" => "11am-12pm",
        "title" => "Bill's Meeting",
    ],
    [
        "id" => 6,
        "date" => "Sun, Jun 21",
        "description" => "Lorem ipsum dolor amet.",
        "hostEmail" => "guerrero@oregonstate.edu",
        "hostName" => "Roman Guerrero",
        "location" => "Somewhere, USA",
        "time" => "11am-12pm",
        "title" => "Roman's Past Meeting",
    ],
    [
        "id" => 7,
        "date" => "Sat, Jun 20",
        "description" => "Lorem ipsum dolor amet.",
        "hostEmail" => "czaparym@oregonstate.edu",
        "hostName" => "Michael Czapary",
        "location" => "Somewhere, USA",
        "time" => "12pm-1pm",
        "title" => "Mike's Past Meeting",
    ],
    [
        "id" => 8,
        "date" => "Fri, Jun 19",
        "description" => "Lorem ipsum dolor amet.",
        "hostEmail" => "heer@eecs.oregonstate.edu",
        "hostName" => "Me",
        "location" => "Somewhere, USA",
        "time" => "12pm-12:30pm",
        "title" => "Don's Past Meeting",
    ],
    [
        "id" => 9,
        "date" => "Mon, Jul 20",
        "description" => "Lorem ipsum dolor amet.",
        "hostEmail" => "heer@eecs.oregonstate.edu",
        "hostName" => "Me",
        "location" => "Somewhere, USA",
        "time" => "12pm-12:30pm",
        "title" => "Don's Other Meeting",
    ]
];

if (!$all_meetings[MEETING_ID - 1]['title']) {
    http_response_code(404);
    echo $twig->render('errors/error.twig', [
        'message' => 'Sorry, we couldn\'t find that meeting.',
        'title' => 'Meeting Not Found',
    ]);
} else {
    echo $twig->render('meetings/rsvp.twig', [
        'meeting' => $all_meetings[MEETING_ID - 1],
        'title' => 'RSVP - ' . $all_meetings[MEETING_ID - 1]['title'],
    ]);
}
