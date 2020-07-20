<?php

require_once dirname(__DIR__) . '/scheduleit.config.php';

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
