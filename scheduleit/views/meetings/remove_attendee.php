<?php

require_once ABSPATH . 'config/session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
     $eventId = $_POST['eventId'];
     $removeOnid = $_POST['attendeeOnid'];
     $startTime = $_POST['startTime'];
     $eventName = $_POST['meetingName'];

     $slotHash = $database->getSlotHash($startTime, $eventId);

     $result = $database->deleteBooking($removeOnid, $slotHash);

     if ($result > 0) {
         // add email here
     echo ("ready"); 
     }
 }

 
