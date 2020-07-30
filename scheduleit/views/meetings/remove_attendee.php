<?php

require_once ABSPATH . 'config/session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
     $removeOnid = $_POST['attendeeOnid'];
     $slotHash = $_POST['slotHash'];
     $removeOnid = trim($removeOnid);
     $slotHash = trim($slotHash);
     
     $result = $database->deleteBooking($removeOnid, $slotHash);
          
     echo json_encode($result);
    if ($result > 0) {
        // add email here
    }
}
