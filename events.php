<?php

require_once dirname(__FILE__) . '/scheduleit.config.php';
require_once ABSPATH . 'config/session.php';

// get event data from database
$eventData = $database->getUserEvents($_SESSION["user"]);

if ($eventData) {
    $columnNames = array_keys($eventData[0]);
} else {
    $eventData = [];
    $columnNames = [];
}

echo $twig->render('views/events.twig', [
  'table_headers' => $columnNames,
  'table_rows' => $eventData,
]);
