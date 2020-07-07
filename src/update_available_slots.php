<?php

// set up session
require_once dirname(__DIR__) . '/config/session.php';

// set up connection to database via MySQLi
require_once dirname(__DIR__) . '/config/database.php';

$database->connectAsAdministrator();

// get data from POST request
$eventHash = $_POST["eventhash"];
$slotId = $_POST["slotID"];

// get update the event and time avaiable slots
$result = $database->updateAvailableSlots($eventHash, $slotId);

// echo the result
if ($result > 0) {
    echo "The available slots were updated!";
} else {
    echo " uh oh, there was a problem updating the slots.";
}
