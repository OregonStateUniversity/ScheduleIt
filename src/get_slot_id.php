<?php

// set up session

require_once dirname(__DIR__) . '/config/session.php';

// set up connection to database via MySQLi

require_once dirname(__DIR__) . '/config/database.php';

$database->connectAsAdministrator();

// get data from POST request

$eventHash = $_POST["hash"];
$startTime = $_POST["startTime"];

// get reservation slot hash

$slotId = $database->getReservedSlotId($startTime, $eventHash);

// echo the array result
echo json_encode($slotId);


?>
