<?php

// set up session

require_once dirname(__DIR__) . '/config/session.php';

// set up connection to database via MySQLi

require_once dirname(__DIR__) . '/config/database.php';

$database->connectAsAdministrator();

// get data from POST request

$slotKey = $_POST["key"];

// delete reservation

$errorCode = $database->deleteReservation($slotKey, $_SESSION["user"]);

if ($errorCode == 0) {
    echo "The reservation was deleted successfully!";
} else {
    echo "The reservation could not be deleted!";
}
