<?php

require_once ABSPATH . 'config/session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
     $eventId = $_POST['eventId'];
     $removeOnid = $_POST['attendeeOnid'];
     $startTime = $_POST['startTime'];
     $eventName = $_POST['meetingName'];

     $slotId = $database->getSlotId($startTime, $eventId);

     $result = $database->deleteBooking($startTime, $eventId, $removeOnid);

     if ($result > 0) {
         $user = $database->getUserByONID($removeOnid);
         //create message
         $msgFormat =
         "Hi, %s, \n\nThis is an automated message, do not reply.\n\nThe host for the event \"%s\" has removed you from the slot that you reserved.\n\nPlease contact them if you have any questions.";

         $headers = "From: Schedule It" . "\r\n";

         $message = sprintf($msgFormat, $user["first_name"], $eventName);

         $sendMail = mail($user["email"], "Removed from event notification", $message, $headers);
         
         // update slot and event space
         $slotUpdate = $database->updateAvailableSlots($eventId, $slotId['id']);
     echo ("ready"); 
     }
 }

 
