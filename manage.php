<?php

    // set up session

    require_once 'php/session.php';

    // set up connection to database via MySQLi

    require_once 'php/database.php';

    // set up twig

	require_once 'php/twig.php';

    // include code for rendering view for errors

    require_once 'php/render_error.php';


    // get key for event from URL

    $eventKey = $_GET["key"];

    // get event data using event key
    // if there are no results, show error 404
    // if event creator ONID does not match user ONID, show error 403

    $eventData = $database -> getEvent($eventKey);

    $errorCode = 0;

    if ($eventData == null) {
        $errorCode = 404;
        
    }
    else if ($eventData['creator'] != $_SESSION['user']) {
        $errorCode = 403;
    }

    if ($errorCode != 0) {
        render_error($twig, $errorCode, $errorMessages[$errorCode]);
        exit;
    }

    // get duration and capacity of slots for event

    $slotDetails = $database -> getEventSlotDetails($eventKey);

    // get list of attendees, their time slots, and their files from database

    $attendeeData = $database -> getAttendeeData($eventKey);

    if ($attendeeData) {
        $columnNames = array_keys($attendeeData[0]);
    }
    else {
        $attendeeData = [];
        $columnNames = [];
    }


    // render page using twig

    echo $twig -> render(
        'views/manage.twig',
        [
            'event_data' => $eventData,
            'slot_duration' => $slotDetails['duration'],
            'table_headers' => $columnNames,
            'table_rows' => $attendeeData
        ]
    );

?>
