<?php

require_once ABSPATH . 'config/session.php';

$meeting = $database->getMeetingById($meeting_id, $_SESSION['user_id']);
// list of onids that were invited to the event but have not registered
$inviteList = $database->getNotRegistered($meeting['id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['attendeeOnid'])) {
    $attendeeOnids = $_POST['attendeeOnid'];
    $link = $_POST['link'];
    $link = $_SERVER['HTTP_ORIGIN'] . $link;
    $host = $_SESSION['user'];
    $hash = $meeting['hash'];
    // turn onid string into array
    $onidArray = explode(" ",$attendeeOnids);

    // create email list
    // send email in forloop so that other recipients emails are not
    // exposed
    $sentInvites = 0;
    foreach ($onidArray as $onid) {
      if (strlen($onid) > 2) {

        $email = $onid . '@oregonstate.edu';

        // create message
        $msgFormat =
        "Hi %s, \n\nYour are invited to %s's event! Please follow this address to reserve a seat:\n\n%s";

        $headers = "From: Schedule It" . "\r\n";

        $message = sprintf($msgFormat, $onid, $host, $link);

        $result = mail($email, "You're Invited", $message, $headers);

        if ($result == 1) {
          // add onid to the events inivte list
          $database->insertInviteList($onid, $meeting['id']);
          $sentInvites += 1;
        }
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
