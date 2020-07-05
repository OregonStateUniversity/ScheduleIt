<?php

// set up session

require_once dirname(__DIR__) . '/config/session.php';

// set up connection to database via MySQLi

require_once dirname(__DIR__) . '/config/database.php';

$database->connectAsAdministrator();

// get data from POST request

$slotId = $_POST["id"];
$attendeeOnid = $_POST["onid"];
// delete reservation

$id = $database->creatorDeleteReservation($slotId, $attendeeOnid);
echo json_encode($id);
//if ($errorCode == 0) {
//    echo "The reservation was deleted successfully!";
//} else {
//    echo "The reservation could not be deleted!";
?>
