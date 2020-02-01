<?php

    // set up session
    
    require_once 'php/session.php';

    // set up connection to database via MySQLi
    
    require_once 'php/database.php';

    $database -> connectAsAdministrator();
    
    // get time slot key from POST request

    $slotKey = $_POST['slotKey'];

    // get ID of user's booking for time slot
    // if there is no booking, show user error message and abort now

    $bookingData = $database -> getUserBooking($slotKey, $_SESSION["user"]);
    
    if ($bookingData == null) {
        echo "You have not reserved a time slot." . '\n';
        echo "File upload is not possible." . '\n';
        exit;
    }

    $bookingID = $bookingData["id"];

    // get event key using time slot key

    $eventData = $database -> getEventBySlotKey($slotKey);
    $eventKey = $eventData["hash"];

    // determine path

    $uploadsDirectory = './uploads/';
    $newPath = $uploadsDirectory . $eventKey;
    $newFileName = $_SESSION["user"] . '_upload';

    $oldFileName = $_FILES['file']['name'];
    $ext = pathinfo($oldFileName, PATHINFO_EXTENSION);
    $path = $newPath . '/' . $newFileName . '.' . $ext;


    // if directory for event's files does not exist, create it

    if (!file_exists($newPath)) mkdir($newPath, 0700, true);

    // if there is error with file upload
    // do not try to add path to database

    $fileError = false;

    if (0 < $_FILES['file']['error']) {
        echo 'Error: ' . $_FILES['file']['error'] . '<br>';
        $fileError = true;
    }

    // if there is no error with file upload, add path to database
    // otherwise, report that file upload was not successful

    if ($fileError == false) {
        
        move_uploaded_file($_FILES['file']['tmp_name'], $path);
        chmod($path, 0755);

        $result = $database -> addFile($path, $bookingID);

        if ($result > 0) {
            echo "Your file has been uploaded.";
            shell_exec('chmod -R 755 ./uploads/');
            exit;
        }

    }
    
    echo "Your file could not be uploaded.";

?>
