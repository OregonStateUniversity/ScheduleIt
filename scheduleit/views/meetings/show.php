<?php

require_once ABSPATH . 'config/session.php';

$all_meetings = [
    [
        "id" => 1,
        "dates" => ["July 17, 2020", "July 19, 2020"],
        "description" => "Lorem ipsum dolor amet.",
        "hostEmail" => "heer@eecs.oregonstate.edu",
        "hostName" => "Me",
        "location" => "Somewhere, USA",
        "per_slot" => 2,
        "slots" => 20,
        "slots_available" => 18,
        "title" => "Don's Meeting",
        "attendees" => [
            [
                "date" => "Sat, Jul 17",
                "email" => "czaparym@oregonstate.edu",
                "name" => "Michael Czapary",
                "time" => "12pm-1pm",
            ],
            [
                "date" => "Mon, Jul 19",
                "email" => "guerrero@oregonstate.edu",
                "name" => "Roman Guerrero",
                "time" => "11am-12pm",
            ]
        ],
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
        "hostEmail" => "sibolibb@oregonstate.edu",
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
        "dates" => ["July 20, 2020"],
        "description" => "Lorem ipsum dolor amet.",
        "hostEmail" => "heer@eecs.oregonstate.edu",
        "hostName" => "Me",
        "location" => "Somewhere, USA",
        "per_slot" => 2,
        "slots" => 20,
        "slots_available" => 20,
        "title" => "Don's Other Meeting",
    ]
];

if (!$all_meetings[$meeting_id - 1]['title']) {
    http_response_code(404);
    echo $twig->render('errors/error.twig', [
        'message' => 'Sorry, we couldn\'t find that meeting.',
        'title' => 'Meeting Not Found',
    ]);
} else {
    echo $twig->render('meetings/show.twig', [
        'meeting' => $all_meetings[$meeting_id - 1],
        'title' => $all_meetings[$meeting_id - 1]['title'],
    ]);
}
