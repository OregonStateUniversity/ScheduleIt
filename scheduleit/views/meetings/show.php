<?php

require_once ABSPATH . 'config/session.php';

$meeting = $database->getMeetingById($meeting_id, $_SESSION['user']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $attendeeOnids = $_POST['attendeeOnid'];
    $link = $_POST['link'];
    $link = "http://web.engr.oregonstate.edu" . $link;
    $host = $_SESSION['user'];
    $hash = $meeting['hash'];
    // turn onid string into array
    $onidArray = explode(" ",$attendeeOnids);
    
    // create email list
    // send email in forloop so that other recipients emails are not
    // exposed
    foreach ($onidArray as $onid) {
      if (strlen($onid) > 2) {
        
        $email = $onid . '@oregonstate.edu';

        // create message
        $msgFormat = 
        "Hi %s, \n\nYour are invited to %s's event! Please follow this address to reserve a seat:\n\n%s";

        $headers = "From: Schedule It" . "\r\n";

        $msg = sprintf($msgFormat, $onid, $host, $link);

        $result = mail($email, "You're Invited", $msg, $headers);
      }
    }
}

if ($meeting) {
    $meeting['dates'] = $database->getDatesByMeetingId($meeting['id']);
    $meeting['dates_count'] = count($meeting['dates']);
    $attendee_meetings = $database->getMeetingAttendees($meeting_id);
    
    echo $twig->render('meetings/show.twig', [
        'attendee_meetings' => $attendee_meetings,
        'meeting' => $meeting,
        'title' => $meeting['name'],        
    ]);
} else {
    http_response_code(404);
    echo $twig->render('errors/error_logged_in.twig', [
        'message' => 'Sorry, we couldn\'t find that meeting.',
        'title' => 'Meeting Not Found',
    ]);
}
