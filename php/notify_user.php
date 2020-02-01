<?php

	function emailUserAboutDeletedSlot($user, $eventName, $siteURL) {

		$formatA = "Hi %s,\n\nThe host for %s has removed the slot that you reserved.\n\n";
		$formatB = "Please sign up for an available slot by clicking the link below:\n%s\n";
		$format = $formatA . $formatB;

		$headers = "From: MyEventBoard" . "\r\n";
		$msg = sprintf($format, $user["firstName"], $eventName, $siteURL);

		mail($user["email"], "Time Slot Removal Notification", $msg, $headers);

	}

?>
