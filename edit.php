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

    // get event and time slot data

    $resultObjects = $database -> getEditingData($eventKey);

    // check results
    // if there are no results, show error 404
    // if there are results or if current user is not event creator, show error 403

    $errorCode = 0;

    if ($resultObjects == null) {
        $errorCode = 404;
    }
    else if ($resultObjects[0] -> creator != $_SESSION['user']) {
        $errorCode = 403;
    }

    if ($errorCode != 0) {
        render_error($twig, $errorCode, $errorMessages[$errorCode]);
        exit;
    }

    // encode array of PHP objects as JSON

    $eventData = [];

    foreach ($resultObjects as $resultObject) {
        $eventData[] = json_encode($resultObject);
    }


    // render page using twig

    echo $twig -> render(
        'views/edit.twig',
        [ 'event_data' => $eventData ]
    );

?>
