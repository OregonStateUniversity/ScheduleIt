<?php

// set up session

require_once dirname(__DIR__) . '/config/session.php';

// set up connection to database via MySQLi

require_once dirname(__DIR__) . '/config/database.php';

// get data for dashboard, including
// user data, event data, time slot data

$reservationData = $database->getDashboardData($_SESSION["user"]);

$data = [];

if (!empty($reservationData)) {
    $data = $reservationData;
}

echo json_encode($data);
