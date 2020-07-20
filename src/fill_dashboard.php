<?php

require_once dirname(__DIR__) . '/scheduleit.config.php';
require_once ABSPATH . 'config/session.php';

// get data for dashboard, including
// user data, event data, time slot data
$reservationData = $database->getDashboardData($_SESSION["user"]);

$data = [];

if (!empty($reservationData)) {
    $data = $reservationData;
}

echo json_encode($data);
