<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once ABSPATH . 'config/session.php';
require_once ABSPATH . 'scheduleit/lib/send_email.php';

$meeting = $database->getMeetingById($meeting_id, $_SESSION['user_id']);
// list of onids that were invited to the event but have not registered
$inviteList = $database->getNotRegistered($meeting['id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['attendeeOnid'])) {
        $attendeeOnids = $_POST['attendeeOnid'];
        $link = $_POST['link'];
        $link = $_SERVER['HTTP_ORIGIN'] . $link;
        $host = $_SESSION['user_onid'];
        $hash = $meeting['hash'];
        // turn onid string into array
        $onidArray = explode(" ", $attendeeOnids);

        // create email list
        // send email in forloop so that other recipients emails are not
        // exposed
        $sentInvites = 0;
        foreach ($onidArray as $onid) {
            if (strlen($onid) > 2) {
                $send_email->invitation($_SESSION['user_onid'], $onid, $meeting['name'], $_SESSION['user_firstname'] . ' ' . $_SESSION['user_lastname'], $link);

              // add onid to the events inivte list
                $database->insertInviteList($onid, $meeting['id']);
                $sentInvites += 1;
            }
        }
        if ($sentInvites > 1) {
            $successMessage = 'Sent ' . $sentInvites . ' invites.';
            $msg->success($successMessage, SITE_DIR . '/meetings/' . $meeting['id']);
        } else {
            if ($sentInvites > 0) {
                $msg->success('Sent 1 invite.', SITE_DIR . '/meetings/' . $meeting['id']);
            }
        }
    } else {
    }
}

if ($meeting && $meeting['creator_id'] == $_SESSION['user_id']) {
    $meeting['dates'] = $database->getDatesByMeetingId($meeting['id']);
    $meeting['dates_count'] = count($meeting['dates']);
    $attendee_meetings = $database->getMeetingAttendees($meeting_id);

    echo $twig->render('meetings/show.twig', [
        'attendee_meetings' => $attendee_meetings,
        'meeting' => $meeting,
        'title' => $meeting['name'],
        'invite_list' => $inviteList,
    ]);
} else {
    http_response_code(404);
    echo $twig->render('errors/error_logged_in.twig', [
        'message' => 'Sorry, we couldn\'t find that meeting.',
        'title' => 'Meeting Not Found',
    ]);
}
