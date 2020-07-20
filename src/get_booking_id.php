<?php

require_once dirname(__DIR__) . '/scheduleit.config.php';

$database->connectAsAdministrator();

// get data from POST request
$slotId = $_POST["id"];
$attendeeOnid = $_POST["onid"];

// delete reservation
$id = $database->creatorDeleteReservation($slotId, $attendeeOnid);
echo json_encode($id);
