<?php

require_once dirname(__DIR__) . '/scheduleit.config.php';
require_once ABSPATH . 'config/session.php';

$database->connectAsAdministrator();

// get data from POST request
$onid = $_POST["onid"];
$eventHash = $_POST["eventHash"];
$type = $_POST["type"];

// delete file
$result = $database->deleteFile($onid, $eventHash, $type);

if ($result == true) {
    echo "The file was deleted successfully!";
} else {
    echo "The file could not be deleted!";
}
