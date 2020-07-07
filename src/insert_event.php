<?php

// set up session

require_once dirname(__DIR__) . '/config/session.php';

// set up connection to database via MySQLi

require_once dirname(__DIR__) . '/config/database.php';

$database->connectAsAdministrator();

// insert event data and time slot data into database
// if something was submitted via HTTP POST

if (!empty($_POST)) {
    $eventData = [];

    $eventData["name"] = $_POST['eventName'];
    $eventData["description"] = $_POST['eventDescription'];
    $eventData["creator"] = $database->getUserKey($_SESSION['user']);
    $eventData["location"] = $_POST['eventLocation'];
    $eventData["capacity"] = $_POST['eventCap'];
    $eventData["anonymous"] = $_POST["anonymous"];
    $eventData["upload"] = $_POST["upload"];

    $slotData = [];

    $slotData["dates"] = json_decode($_POST['slotArray'], true);
    $slotData["duration"] = $_POST['eventDuration'];
    $slotData["capacity"] = $_POST['sCap'];

    $result = $database->addEvent($eventData, $slotData);

    if ($result == 1) {
        echo "Your event was successfully created.";
    } else {
        echo "Your event could not be created.";
    }
}