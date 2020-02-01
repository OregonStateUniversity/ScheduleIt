<?php

    // set up session

    require_once 'php/session.php';

    // set up connection to database via MySQLi

	require_once 'php/database.php';
	
	$database -> connectAsAdministrator();

	// get slot key from POST request

	$slotKey = $_POST["key"];

	// get slot ID using hash associated with that slot

	$slotData = $database -> getTimeSlot($slotKey); 
	$slotID = $slotData["id"];
	
    // get user ID using ONID from database

	$userID = $database -> getUserKey($_SESSION["user"]);


    // reserve time slot for event using stored procedure

	$errorCode = $database -> reserveTimeSlot($slotID, $userID);

	// respond with error code

	echo $errorCode;


	// send confirmation e-mail if successful

	if ($errorCode != -1) {

		// get details about time slot

		$date = $_POST["date"];
		$slotTime = $_POST["start_time"];
		$duration = $_POST["duration"];

		// get event location

		$eventData = $database -> getEventBySlotKey($slotKey);
		$location = $eventData["location"];

		// create then send e-mail

		$formatA = "Hi %s,\n\nYou have successfully reserved a time slot.";
		$formatB = "\n\nDate: %s\nTime: %s\nDuration: %s\nLocation: %s\n";
		$format = $formatA . $formatB;
		
		$headers = "From: MyEventBoard" . "\r\n";

		$msg = sprintf(
			$format, $_SESSION["firstName"], 
			$date, $slotTime, $duration, $location
		);

		mail($_SESSION['email'], "Confirmation", $msg, $headers);

	}

?>
