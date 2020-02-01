<?php

	// set up session

	require_once 'php/session.php';

	// set up connection to database via MySQLi

	require_once 'php/database.php';

	$database -> connectAsAdministrator();

	// get data from POST request

	$eventKey = $_POST["key"];

	// check if user is creator of event
	// delete event if that is true

	$eventData = $database -> getEvent($eventKey);

	if ($eventData["creator"] == $_SESSION["user"]) {

		$result = $database -> deleteEvent($eventKey);

		if ($result > 0) {
			echo "The event was deleted successfully!";
			exit;
		}

	}
	
	echo "The event could not be deleted.";

?>
