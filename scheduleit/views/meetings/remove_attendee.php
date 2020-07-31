<?php

require_once ABSPATH . 'config/session.php';
require_once ABSPATH . 'scheduleit/lib/send_email.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
     $removeOnid = $_POST['attendeeOnid'];
     $slotHash = $_POST['slotHash'];
     $removeOnid = trim($removeOnid);
     $slotHash = trim($slotHash); 
     $eventName = $_POST['eventName'];
     $result = $database->deleteBooking($removeOnid, $slotHash);
          
     echo json_encode($result); 
     // add email here
     $send_email->notifyRemovedAttendee($removeOnid, $_SESSION['user_firstname'] . ' ' . $_SESSION['user_lastname'], $_SESSION['user_onid'], $eventName);  
     
     
}

 

