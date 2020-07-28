<?php

require_once dirname(__DIR__) . '/scheduleit.config.php';
require_once ABSPATH . 'config/session.php';

$database->connectAsAdministrator();

// get data from js
$attendeeOnid = $_POST['attendeeOnid'];
$regLink = $_POST['link'];
$host = $_POST['hostOnid'];

// get the event hash from the link
$position = strpos($regLink, "=");
$hash = substr($regLink, $position + 1);

// get event id
$eventData = $database->getEvent($hash);

// turn onid string into array
$onidArray = explode(" ", $attendeeOnid);

// create email list
// send email in forloop so that other recipients emails are not
// exposed
$emailSentCount = 0;
$failedList = "";
foreach ($onidArray as $onid) {
    if (strlen($onid) > 2) {
        $email = $onid . '@oregonstate.edu';


      // create message

        $msgFormat =
        "Hi, %s, \n\nThis is an automated message, do not reply.\n\nYou are invited to %s's event! Please follow this address to reserve a seat:\n\n%s";

        $headers = "From: MyEventBoard" . "\r\n";

        $msg = sprintf($msgFormat, $onid, $host, $regLink);


        $result = mail($email, "You're Invited", $msg, $headers);

        if ($result == 1) {
            $emailSentCount += 1;
          // add onid to the events invite list
            $database->insertInviteList($onid, $eventData["id"]);
        } else {
            $failedList = $failedList . $email . ', ';
        }
    }
};

echo "sent " . $emailSentCount . " invitations.";
if (strlen($failedList) > 0) {
    echo "Failed to sent invitation to " . $failedList;
}
