<?php

require_once dirname(__FILE__) . '/scheduleit.config.php';
require_once ABSPATH . 'config/session.php';

// get data for time slots reserved by user from database
$reservationData = $database->getReservationData($_SESSION["user"]);

if ($reservationData) {
    $columnNames = array_keys($reservationData[0]);
} else {
    $reservationData = [];
    $columnNames = [];
}

echo $twig->render('views/reservations.twig', [
  'table_headers' => $columnNames,
  'table_rows' => $reservationData,
]);
