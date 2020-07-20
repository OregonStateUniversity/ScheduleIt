<?php

require_once dirname(__DIR__) . '/scheduleit.config.php';
require_once ABSPATH . 'config/session.php';

$database->connectAsAdministrator();

// get data from POST request
$onid = $_POST["attendee"];
$eventName = $_POST["eventname"];

// get attendee's email
$user = $database->getUserByONID($onid);

// create message
$msgFormat =
"Hi, %s, \n\nThis is an automated message, do not reply.\n\nThe host for the event \"%s\" has removed you from the slot that you reserved.\n\nPlease contact them if you have any questions.";

$headers = "From: MyEventBoard" . "\r\n";

$msg = sprintf($msgFormat, $user["first_name"], $eventName);

$result = mail($user["email"], "Removed from event notification", $msg, $headers);

if ($result > 0) {
    echo $user["first_name"];
} else {
    echo "failed to notify attendee";
}
