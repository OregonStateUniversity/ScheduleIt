<?php

require_once dirname(__DIR__) . '/scheduleit.config.php';
require_once ABSPATH . 'config/session.php';

$database->connectAsAdministrator();

// get data from POST request
$eventHash = $_POST["hash"];
$startTime = $_POST["startTime"];

// get reservation slot id
$slotId = $database->getReservedSlotId($startTime, $eventHash);

// echo the array result
echo json_encode($slotId);
