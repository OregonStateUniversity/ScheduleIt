<?php

    // set up session

    require_once 'php/session.php';

    // set up connection to database via MySQLi

    require_once 'php/database.php';

    // get data for dashboard, including
    // user data, event data, time slot data

    $reservationData = $database -> getDashboardData($_SESSION["user"]);

    echo json_encode($reservationData);

?>
