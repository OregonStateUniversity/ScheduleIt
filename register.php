<?php

require_once dirname(__FILE__) . '/scheduleit.config.php';
require_once ABSPATH . 'config/session.php';

// get key for event from URL
$eventKey = $_GET["key"];

// get event data from database
$eventData = $database->getEvent($eventKey);

// if event data could not be found, show error page
if ($eventData == null) {
    $errorCode = 404;
    render_error($twig, $errorCode, $errorMessages[$errorCode]);
    exit();
}

$eventName = $eventData['name'];
$eventAnoymous = $eventData['anonymous'];
$eventUpload = $eventData['upload'];

// get time slot data for event from database
$slotData = $database->getRegistrationData($eventKey, $_SESSION["user"]);
$columnNames = array_keys($slotData[0]);

// if ($slotData == NULL) {
//     $errorCode = 404;
//     render_error($twig, $errorCode, $errorMessages[$errorCode]);
//     exit;
// }

echo $twig->render('views/register.twig', [
  'event_name' => $eventName,
  'event_anonymous' => $eventAnoymous,
  'event_upload' => $eventUpload,
  'table_headers' => $columnNames,
  'table_rows' => $slotData,
]);
