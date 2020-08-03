<?php

require_once ABSPATH . 'config/session.php';
require_once ABSPATH . 'scheduleit/lib/file_upload.php';

$dates = [];
$meeting = [
    'slot_capacity' => 1,
    'duration' => 60
];
$timeslot_times = [];

// Create time labels
$time_labels = [];

$start_time = strtotime(MEETINGS_START_TIME);
$end_time = strtotime(MEETINGS_END_TIME);

$current = time();
$add_time = strtotime('+' . $meeting['duration'] . ' mins', $current);
$diff = $add_time - $current;

while ($start_time < $end_time) {
    array_push($time_labels, date('G:i:s', $start_time));
    $start_time += $diff;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $slot_capacity = !empty($_POST['slot_capacity']) ? $_POST['slot_capacity'] : 1;
    $timeslot_times = !empty($_POST['timeslots']) ? $_POST['timeslots'] : [];

    $meeting['name'] = $_POST['name'];
    $meeting['location'] = $_POST['location'];
    $meeting['description'] = $_POST['description'];
    $meeting['is_anon'] = !empty($_POST['is_anon']) ? 1 : 0;
    $meeting['enable_upload'] = !empty($_POST['enable_upload']) ? 1 : 0;
    $meeting['slot_capacity'] = $_POST['slot_capacity'];
    $meeting['duration'] = $_POST['duration'];
    $meeting['timeslots'] = $timeslot_times;

    foreach ($timeslot_times as $key => $timeslot) {
        $date = explode(' ', $timeslot);

        if (!in_array($date[0], $dates)) {
            array_push($dates, $date[0]);
        }
    }

    if (empty($_POST['name']) || empty($_POST['location'])) {
        $msg->error('Please fill out all required fields.');
    } else {
        $new_meeting_id = $database->addMeeting($_SESSION['user_id'], $meeting);

        // Check for file to upload
        if ($new_meeting_id > 0 && !empty($_FILES['file']['name'])) {
            $created_meeting = $database->getMeetingById($new_meeting_id);
            // Upload file
            $new_file_upload = $file_upload->upload($_SESSION['user_onid'], $created_meeting['hash']);

            if ($new_file_upload['error']) {
                $msg->error($new_file_upload['message']);
            } else {
                $msg->success('"' . $meeting['name'] . '" has been created.', SITE_DIR . '/meetings/' . $new_meeting_id);
            }
        // No file uploaded, just meeting creation
        } elseif ($new_meeting_id > 0) {
            $msg->success('"' . $meeting['name'] . '" has been created.', SITE_DIR . '/meetings/' . $new_meeting_id);
        } else {
            $msg->error('Could not create meeting.');
        }
    }
}

echo $twig->render('meetings/create.twig', [
    'dates' => $dates,
    'dates_json' => json_encode($dates),
    'meeting' => $meeting,
    'time_labels' => $time_labels,
    'timeslot_times' => $timeslot_times,
    'meetings_end_time' => MEETINGS_END_TIME,
    'meetings_start_time' => MEETINGS_START_TIME,
    'title' => 'Create Meeting',
]);
