<?php

// set up sessio
require_once dirname(__DIR__) . '/config/session.php';

// set up connection to database via MySQLi
require_once dirname(__DIR__) . '/config/database.php';

$database->connectAsAdministrator();

// get data from POST request
$bookingID = $_POST["bookingID"];

// get booking Id
$result = $database->deleteBooking($bookingID);

if ($result > 0) {
    echo "The event was deleted successfully!";
} else {
    echo "The event could not be deleted.";
}
