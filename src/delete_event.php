<?php

require_once dirname(__DIR__) . '/scheduleit.config.php';
require_once ABSPATH . 'config/session.php';

$database->connectAsAdministrator();

// get data from POST request

$eventKey = $_POST["key"];

// check if user is creator of event
// delete event if that is true

$eventData = $database->getEvent($eventKey);

if ($eventData["creator"] == $_SESSION["user"]) {
    $result = $database->deleteEvent($eventKey);
    if ($result > 0) {
        $database->deleteEventFiles($eventKey);
        echo "The event was deleted successfully!";
        exit();
    }
}

echo "The event could not be deleted.";
