<?php

require_once dirname(__FILE__) . '/scheduleit.config.php';

// set up session

require_once ABSPATH . 'config/session.php';

// set up connection to database via MySQLi

require_once ABSPATH . 'config/database.php';

// set up twig

require_once ABSPATH . 'config/twig.php';

// get event data from database

$eventData = $database->getUserEvents($_SESSION["user"]);

if ($eventData) {
    $columnNames = array_keys($eventData[0]);
} else {
    $eventData = [];
    $columnNames = [];
}

// render page using twig

echo $twig->render('views/events.twig', [
  'table_headers' => $columnNames,
  'table_rows' => $eventData,
]);
