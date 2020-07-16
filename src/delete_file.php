<?php

// set up session

require_once dirname(__DIR__) . '/config/session.php';

// set up connection to database via MySQLi

require_once dirname(__DIR__) . '/config/database.php';

$database->connectAsAdministrator();

// get data from POST request

$onid = $_POST["onid"];
$eventHash = $_POST["eventHash"];
$type = $_POST["type"];
// delete reservation

$result = $database->deleteFile($onid, $eventHash, $type);

if ($result == true) {
    echo "The file was deleted successfully!";
} else {
    echo "The file could not be deleted!";
}
