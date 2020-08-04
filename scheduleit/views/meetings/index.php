<?php

require_once ABSPATH . 'config/session.php';

$search_term = !empty($_GET['q']) ? $_GET['q'] : null;

$upcoming_meetings = $database->getAllUpcomingMeetings($_SESSION['user_id']);
$created_meetings = $database->getUpcomingMeetingsByCreator($_SESSION['user_id']);
$past_meetings = $database->getPastMeetings($_SESSION['user_id']);
$search_meetings = $database->getMeetingsBySearchTerm($_SESSION['user_id'], $search_term);
$invites_with_dates = [];
$upcoming_meetings_with_attendees = [];
$created_meetings_with_attendees = [];
$past_meetings_with_attendees = [];
$search_meetings_with_attendees = [];

foreach ($upcoming_meetings as $key => $meeting) {
    if (isset($upcoming_meetings_with_attendees[$meeting['id']])) {
        $current_attendees = $upcoming_meetings_with_attendees[$meeting['id']]['attendees'];
        array_push($current_attendees, [
            'attendee_email' => $meeting['attendee_email'],
            'attendee_name' => $meeting['attendee_name'],
            'attendee_file' => $meeting['attendee_file']
        ]);
        usort($current_attendees, function ($a, $b) {
            return strcmp($a['attendee_email'], $b['attendee_email']);
        });
        $upcoming_meetings_with_attendees[$meeting['id']]['attendees'] = $current_attendees;
        $upcoming_meetings_with_attendees[$meeting['id']]['attendees_count'] = count($current_attendees);
    } else {
        $upcoming_meetings_with_attendees[$meeting['id']] = [
            'id' => $meeting['id'],
            'meeting_hash' => $meeting['meeting_hash'],
            'name' => $meeting['name'],
            'location' => $meeting['location'],
            'description' => $meeting['description'],
            'start_time' => $meeting['start_time'],
            'end_time' => $meeting['end_time'],
            'is_anon' => $meeting['is_anon'],
            'creator_id' => $meeting['creator_id'],
            'creator_file' => $meeting['creator_file'],
            'creator_email' => $meeting['creator_email'],
            'creator_name' => $meeting['creator_name'],
            'creator_onid' => $meeting['creator_onid'],
            'attendees' => [
                [
                    'attendee_email' => $meeting['attendee_email'],
                    'attendee_name' => $meeting['attendee_name']
                ]
            ],
            'attendees_count' => 1,
            'attendees_files' => [],
            'attendees_files_count' => 0
        ];
    }


    if ($meeting['attendee_file']) {
        $current_files = $upcoming_meetings_with_attendees[$meeting['id']]['attendees_files'];
        if ($meeting['attendee_onid'] == $_SESSION['user_onid']) {
            $upcoming_meetings_with_attendees[$meeting['id']]['current_attendee_file'] = $meeting['attendee_file'];
        }
        array_push($current_files, $meeting['attendee_file']);
        sort($current_files);
        $upcoming_meetings_with_attendees[$meeting['id']]['attendees_files'] = $current_files;
        $upcoming_meetings_with_attendees[$meeting['id']]['attendees_files_count'] = count($current_files);
    }
}

foreach ($created_meetings as $key => $meeting) {
    if (isset($created_meetings_with_attendees[$meeting['id']])) {
        $current_attendees = $created_meetings_with_attendees[$meeting['id']]['attendees'];
        array_push($current_attendees, [
            'attendee_email' => $meeting['attendee_email'],
            'attendee_name' => $meeting['attendee_name'],
            'attendee_file' => $meeting['attendee_file']
        ]);
        usort($current_attendees, function ($a, $b) {
            return strcmp($a['attendee_email'], $b['attendee_email']);
        });
        $created_meetings_with_attendees[$meeting['id']]['attendees'] = $current_attendees;
        $created_meetings_with_attendees[$meeting['id']]['attendees_count'] = count($current_attendees);
    } else {
        $created_meetings_with_attendees[$meeting['id']] = [
            'id' => $meeting['id'],
            'meeting_hash' => $meeting['meeting_hash'],
            'name' => $meeting['name'],
            'location' => $meeting['location'],
            'description' => $meeting['description'],
            'start_time' => $meeting['start_time'],
            'end_time' => $meeting['end_time'],
            'is_anon' => $meeting['is_anon'],
            'creator_id' => $meeting['creator_id'],
            'creator_file' => $meeting['creator_file'],
            'creator_email' => $meeting['creator_email'],
            'creator_name' => $meeting['creator_name'],
            'creator_onid' => $meeting['creator_onid'],
            'attendees' => [
                [
                    'attendee_email' => $meeting['attendee_email'],
                    'attendee_name' => $meeting['attendee_name'],
                    'attendee_file' => $meeting['attendee_file']
                ]
            ],
            'attendees_count' => 1,
            'attendees_files' => [],
            'attendees_files_count' => 0
        ];
    }

    if ($meeting['attendee_file']) {
        $current_files = $created_meetings_with_attendees[$meeting['id']]['attendees_files'];
        if ($meeting['attendee_onid'] == $_SESSION['user_onid']) {
            $upcoming_meetings_with_attendees[$meeting['id']]['current_attendee_file'] = $meeting['attendee_file'];
        }
        array_push($current_files, $meeting['attendee_file']);
        sort($current_files);
        $created_meetings_with_attendees[$meeting['id']]['attendees_files'] = $current_files;
        $created_meetings_with_attendees[$meeting['id']]['attendees_files_count'] = count($current_files);
    }
}

foreach ($past_meetings as $key => $meeting) {
    if (isset($past_meetings_with_attendees[$meeting['id']])) {
        $current_attendees = $past_meetings_with_attendees[$meeting['id']]['attendees'];
        array_push($current_attendees, [
            'attendee_email' => $meeting['attendee_email'],
            'attendee_name' => $meeting['attendee_name'],
            'attendee_file' => $meeting['attendee_file']
        ]);
        usort($current_attendees, function ($a, $b) {
            return strcmp($a['attendee_email'], $b['attendee_email']);
        });
        $past_meetings_with_attendees[$meeting['id']]['attendees'] = $current_attendees;
        $past_meetings_with_attendees[$meeting['id']]['attendees_count'] = count($current_attendees);
    } else {
        $past_meetings_with_attendees[$meeting['id']] = [
            'id' => $meeting['id'],
            'meeting_hash' => $meeting['meeting_hash'],
            'name' => $meeting['name'],
            'location' => $meeting['location'],
            'description' => $meeting['description'],
            'start_time' => $meeting['start_time'],
            'end_time' => $meeting['end_time'],
            'creator_id' => $meeting['creator_id'],
            'creator_file' => $meeting['creator_file'],
            'creator_email' => $meeting['creator_email'],
            'creator_name' => $meeting['creator_name'],
            'creator_onid' => $meeting['creator_onid'],
            'attendees' => [
                [
                    'attendee_email' => $meeting['attendee_email'],
                    'attendee_name' => $meeting['attendee_name'],
                    'attendee_file' => $meeting['attendee_file']
                ]
            ],
            'attendees_count' => 1,
            'attendees_files' => [],
            'attendees_files_count' => 0
        ];
    }

    if ($meeting['attendee_file']) {
        $current_files = $past_meetings_with_attendees[$meeting['id']]['attendees_files'];
        if ($meeting['attendee_onid'] == $_SESSION['user_onid']) {
            $past_meetings_with_attendees[$meeting['id']]['current_attendee_file'] = $meeting['attendee_file'];
        }
        array_push($current_files, $meeting['attendee_file']);
        sort($current_files);
        $past_meetings_with_attendees[$meeting['id']]['attendees_files'] = $current_files;
        $past_meetings_with_attendees[$meeting['id']]['attendees_files_count'] = count($current_files);
    }
}

foreach ($search_meetings as $key => $meeting) {
    if (isset($search_meetings_with_attendees[$meeting['id']])) {
        $current_attendees = $search_meetings_with_attendees[$meeting['id']]['attendees'];
        array_push($current_attendees, [
            'attendee_email' => $meeting['attendee_email'],
            'attendee_name' => $meeting['attendee_name'],
            'attendee_file' => $meeting['attendee_file']
        ]);
        $search_meetings_with_attendees[$meeting['id']]['attendees'] = $current_attendees;
        $search_meetings_with_attendees[$meeting['id']]['attendees_count'] = count($current_attendees);
    } else {
        $search_meetings_with_attendees[$meeting['id']] = [
            'id' => $meeting['id'],
            'meeting_hash' => $meeting['meeting_hash'],
            'name' => $meeting['name'],
            'location' => $meeting['location'],
            'description' => $meeting['description'],
            'start_time' => $meeting['start_time'],
            'end_time' => $meeting['end_time'],
            'is_anon' => $meeting['is_anon'],
            'creator_id' => $meeting['creator_id'],
            'creator_file' => $meeting['creator_file'],
            'creator_email' => $meeting['creator_email'],
            'creator_name' => $meeting['creator_name'],
            'creator_onid' => $meeting['creator_onid'],
            'attendees' => [
                [
                    'attendee_email' => $meeting['attendee_email'],
                    'attendee_name' => $meeting['attendee_name'],
                    'attendee_file' => $meeting['attendee_file']
                ]
            ],
            'attendees_count' => 1,
            'attendees_files' => [],
            'attendees_files_count' => 0
        ];
    }

    if ($meeting['attendee_file']) {
        $current_files = $search_meetings_with_attendees[$meeting['id']]['attendees_files'];
        if ($meeting['attendee_onid'] == $_SESSION['user_onid']) {
            $search_meetings_with_attendees[$meeting['id']]['current_attendee_file'] = $meeting['attendee_file'];
        }
        array_push($current_files, $meeting['attendee_file']);
        sort($current_files);
        $search_meetings_with_attendees[$meeting['id']]['attendees_files'] = $current_files;
        $search_meetings_with_attendees[$meeting['id']]['attendees_files_count'] = count($current_files);
    }
}

// Add dates to invites
foreach ($invites as $key => $meeting) {
    if ($meeting['id']) {
        $meeting['dates'] = $database->getDatesByMeetingId($meeting['id']);
        $meeting['dates_count'] = count($meeting['dates']);
    }

    array_push($invites_with_dates, $meeting);
}

echo $twig->render('meetings/index.twig', [
    'meetings_page' => true,
    'search_result_count' => count($search_meetings_with_attendees),
    'search_term' => $search_term,
    'upcoming_meetings' => $upcoming_meetings_with_attendees,
    'created_meetings' => $created_meetings_with_attendees,
    'past_meetings' => $past_meetings_with_attendees,
    'invites' => $invites_with_dates,
    'search_meetings' => $search_meetings_with_attendees,
    'title' => 'My Meetings',
]);
