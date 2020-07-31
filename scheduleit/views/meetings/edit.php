<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once ABSPATH . 'config/session.php';

$meeting = $database->getMeetingById($meeting_id, $_SESSION['user_onid']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['deleteHash'])) {
     $meetingHash = $_POST['deleteHash'];
     $meetingHash = trim($meetingHash);
     
     $result = $database->deleteMeeting($meetingHash);
     if ($result > 0) {
        // delete event files here
        $msg->success('Meeting successfully deleted.', SITE_DIR . '/meetings');
     } else {
         $msg->error('Could not delete the meeting.');
     }
  }
}
     
if ($meeting) {
    echo $twig->render('meetings/edit.twig', [
        'meeting' => $meeting,
        'title' => 'Edit Meeting - ' . $meeting['name'],
    ]);
} else {
    http_response_code(404);
    echo $twig->render('errors/error_logged_in.twig', [
        'message' => 'Sorry, we couldn\'t find that meeting.',
        'title' => 'Meeting Not Found',
    ]);
}
