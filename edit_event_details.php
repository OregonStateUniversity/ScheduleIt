<?php

    // set up session

    require_once 'php/session.php';

    // set up connection to database via MySQLi

    require_once 'php/database.php';

    $database -> connectAsAdministrator();

    // check if user is creator

    $eventKey = $_POST['eventHash'];

    $eventData = $database -> getEvent($eventKey);

    if ($eventData == null) {
        echo "Something went wrong...";
        exit;
    }
 
    if ($eventData["creator"] != $_SESSION["user"]) {
        echo "You are not authorized to change event details.";
        exit;
    }

    // check if event name or event location is blank

    if (trim($_POST['eventName']) == "" || trim($_POST['eventLocation']) == "") {
        echo "Event name and event location must be specified." . "\n";
        echo "No changes to the event details were made.";
        exit;
    } 


    // get event details from POST requestt

    $eventData = array();

    $eventData["name"] = $_POST['eventName'];
    $eventData["location"] = $_POST['eventLocation'];
    $eventData["description"] = $_POST['eventDescription'];

    // events sign-up is anonymous by default
    // unless it is explicitly set to not be anonymous
    // assume it should be anonymous

    $isAnonymous = 1;
    if ($_POST['isAnonymous'] == "false") $isAnonymous = 0;

    $eventData["anonymous"] = $isAnonymous;

    // file upload is disabled by default
    // unless it is explicitly set to be enabled
    // assume it should be disabled

    $enableUpload = 0;
    if ($_POST['enableUpload'] == "true") $enableUpload = 1;

    $eventData["upload"] = $enableUpload;

    // update database entry using given data

    $result = $database -> changeEventDetails($eventKey, $eventData);


    // send response

    if ($result > 0) {
        echo "The event details changes were successfully saved!";
    }
    else {
        echo "No changes to the event details were made.";
    }

?>
