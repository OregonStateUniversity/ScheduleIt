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


    // get event data from database

    $eventData = $database -> getEvent($eventKey);

    // if event data could not be found, show error page

    if ($eventData == NULL) {
        $errorCode = 404;
        render_error($twig, $errorCode, $errorMessages[$errorCode]);
        exit;
    }

    $eventName = $eventData['name'];
    $eventAnoymous = $eventData['anonymous'];
    $eventUpload = $eventData['upload'];


    // get time slot data for event from database

    $slotData = $database -> getRegistrationData($eventKey, $_SESSION["user"]);
    $columnNames = array_keys($slotData[0]);

    // if ($slotData == NULL) {
    //     $errorCode = 404;
    //     render_error($twig, $errorCode, $errorMessages[$errorCode]);
    //     exit;
    // }


    // render page using twig

    echo $twig -> render(
        'views/register.twig',
        [
            'event_name' => $eventName,
            'event_anonymous' => $eventAnoymous,
            'event_upload' => $eventUpload,
            'table_headers' => $columnNames,
            'table_rows' => $slotData
        ]
    );

?>