<?php

require_once dirname(__DIR__) . '/scheduleit.config.php';
require_once ABSPATH . 'config/session.php';

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
