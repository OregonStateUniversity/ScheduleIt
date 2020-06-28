<?php

// set up session

require_once 'config/session.php';

// set up connection to database via MySQLi

require_once 'config/database.php';

// set up twig

require_once 'config/twig.php';

// include code for rendering view for errors

require_once 'config/render_error.php';

// get key for event from URL

$eventKey = $_GET["key"];

// get event and time slot data

$resultObjects = $database->getEditingData($eventKey);

// check results
// if there are no results, show error 404
// if there are results or if current user is not event creator, show error 403

$errorCode = 0;

if ($resultObjects == null) {
  $errorCode = 404;
} elseif ($resultObjects[0]->creator != $_SESSION['user']) {
  $errorCode = 403;
}

if ($errorCode != 0) {
  render_error($twig, $errorCode, $errorMessages[$errorCode]);
  exit();
}

// encode array of PHP objects as JSON

$eventData = [];

foreach ($resultObjects as $resultObject) {
  $eventData[] = json_encode($resultObject);
}

// render page using twig

echo $twig->render('views/edit.twig', ['event_data' => $eventData]);

?>
