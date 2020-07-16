<?php

// set up session

require_once dirname(__FILE__) . '/config/session.php';

// set up connection to database via MySQLi

require_once dirname(__FILE__) . '/config/database.php';

// set up twig

require_once dirname(__FILE__) . '/config/twig.php';

// include code for rendering view for errors

require_once dirname(__FILE__) . '/config/render_error.php';

// get key for event from URL
$slotKey = $_GET["key"];

// get event information and time slot reservation information from database
// using time slot key and user ONID
// if there are no results, show error 404

$reservationData = $database->getReservationDetails(
    $slotKey,
    $_SESSION["user"]
);

if ($reservationData == null) {
    $errorCode = 404;
    render_error($twig, $errorCode, $errorMessages[$errorCode]);
    exit();
}

// render page using twig

echo $twig->render('views/reservation_details.twig', [
  'event_key' => $reservationData['hash'],
  'event_name' => $reservationData['name'],
  'event_location' => $reservationData['location'],
  'event_description' => $reservationData['description'],
  'event_creator' => $reservationData['creator'],
  'event_upload' => $reservationData['upload'],
  'slot_start' => $reservationData['start_time'],
  'slot_end' => $reservationData['end_time'],
  'user_file' => $reservationData['file'],
  'event_file' => $reservationData['event_file'],
  'user_onid' => $_SESSION["user"],
]);
