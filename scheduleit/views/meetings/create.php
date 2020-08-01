<?php

require_once ABSPATH . 'config/session.php';
require_once ABSPATH . 'scheduleit/lib/file_upload.php';

$meeting = [
    'capacity' => 1
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $meeting['name'] = $_POST['name'];
    $meeting['location'] = $_POST['location'];
    $meeting['description'] = $_POST['description'];
    $meeting['is_anon'] = $_POST['is_anon'] == '1';
    $meeting['enable_upload'] = $_POST['enable_upload'] == '1';
    $meeting['capacity'] = $_POST['capacity'];

    if (
        empty($_POST['name']) ||
        empty($_POST['location']) ||
        empty($_POST['capacity'])
    ) {
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
    'meeting' => $meeting,
    'title' => 'Create Meeting',
]);
