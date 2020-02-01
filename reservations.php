<?php

    // set up session

    require_once 'php/session.php';

    // set up connection to database via MySQLi

    require_once 'php/database.php';

    // set up twig

    require_once 'php/twig.php';


    // get data for time slots reserved by user from database

    $reservationData = $database -> getReservationData($_SESSION["user"]);

    if ($reservationData) {
        $columnNames = array_keys($reservationData[0]);
    }
    else {
        $reservationData = [];
        $columnNames = [];
    }
    

    // render page using twig

    echo $twig -> render(
        'views/reservations.twig',
        [
            'table_headers' => $columnNames,
            'table_rows' => $reservationData
        ]
    );

?>
