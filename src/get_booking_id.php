<?php

require_once dirname(__DIR__) . '/scheduleit.config.php';
require_once ABSPATH . 'config/session.php';

$database->connectAsAdministrator();

// get data from POST request
$slotId = $_POST["id"];
$attendeeOnid = $_POST["onid"];

// delete reservation
$id = $database->creatorDeleteReservation($slotId, $attendeeOnid);
echo json_encode($id);
