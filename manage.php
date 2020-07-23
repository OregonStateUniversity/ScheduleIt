<?php

require_once dirname(__FILE__) . '/scheduleit.config.php';
require_once ABSPATH . 'config/session.php';

// get key for event from URL
$eventKey = $_GET["key"];

// get event data using event key
// if there are no results, show error 404
// if event creator ONID does not match user ONID, show error 403

$eventData = $database->getEvent($eventKey);

$errorCode = 0;

if ($eventData == null) {
    $errorCode = 404;
} elseif ($eventData['creator'] != $_SESSION['user']) {
    $errorCode = 403;
}

if ($errorCode != 0) {
    render_error($twig, $errorCode, $errorMessages[$errorCode]);
    exit();
}

// get duration and capacity of slots for event
$slotDetails = $database->getEventSlotDetails($eventKey);

// get list of attendees, their time slots, and their files from database
$attendeeData = $database->getAttendeeData($eventKey);

// get list of invited onids that didn't register yet
$inviteList = $database->getNotRegistered($eventData["id"]);

if ($attendeeData) {
    $columnNames = array_keys($attendeeData[0]);
} else {
    $attendeeData = [];
    $columnNames = [];
}

echo $twig->render('views/manage.twig', [
  'event_data' => $eventData,
  'slot_duration' => $slotDetails['duration'],
  'table_headers' => $columnNames,
  'table_rows' => $attendeeData,
  'creator_onid' => $_SESSION["user"],
  'invite_list' => $inviteList
]);
