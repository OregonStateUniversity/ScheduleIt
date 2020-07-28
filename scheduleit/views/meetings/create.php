<?php

require_once ABSPATH . 'config/session.php';

$meeting = [];

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
        $new_meeting = $database->addMeeting($_SESSION['user_id'], $meeting);

        if ($new_meeting == 1) {
            $msg->success('Meeting created.', SITE_DIR . '/manage');
        } else {
            $msg->error('Could not create meeting.');
        }
    }
}

echo $twig->render('meetings/create.twig', [
    'meeting' => $meeting,
    'title' => 'Create Meeting',
]);
