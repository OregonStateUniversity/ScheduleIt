<?php

require_once ABSPATH . 'config/session.php';
require_once ABSPATH . 'scheduleit/lib/send_email.php';
require_once ABSPATH . 'scheduleit/lib/file_upload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
     $removeOnid = $_POST['attendeeOnid'];
     $slotHash = $_POST['slotHash'];
     $removeOnid = trim($removeOnid);
     $slotHash = trim($slotHash);
     $meetingHash = $_POST['meetingHash'];
     $meetingHash = trim($meetingHash);
     $meetingName = $_POST['meetingName'];
     $result = $database->deleteBooking($removeOnid, $slotHash);

     // delete any uploaded file
     $file_name = UPLOADS_ABSPATH . $meetingHash . '/' . $removeOnid . '_upload' . '.*';
     $file_upload->delete($file_name);     
     echo json_encode($result);
     // add email here
     $send_email->notifyRemovedAttendee($removeOnid, $_SESSION['user_firstname'] . ' ' . $_SESSION['user_lastname'], $_SESSION['user_onid'], $meetingName);
}
