<?php

// set up session

require_once dirname(__DIR__) . '/config/session.php';

// set up connection to database via MySQLi

require_once dirname(__DIR__) . '/config/database.php';

$database->connectAsAdministrator();

// get time slot key from POST request

$eventKey = $_POST['eventKey'];

// check file size ** final size TBD **

if ($_FILES['file']['size'] > 5000000) {
   echo "This file is too large.";
   exit();
}


// determine path

$uploadsDirectory = '../uploads/';
$newPath = $uploadsDirectory . $eventKey;
$newFileName = $_SESSION["user"] . '_event_file';

$oldFileName = $_FILES['file']['name'];
$ext = pathinfo($oldFileName, PATHINFO_EXTENSION);
$path = $newPath . '/' . $newFileName . '.' . $ext;

// file extention whitelist
// to enable more file types just add the extentions with no . to the array.
$allowedExtensions = array('txt', 'zip', 'pdf', 'docx', 'xlsx' ,'pptx');
$isAllowed = in_array($ext, $allowedExtensions);

if (!$isAllowed) {
   $fileErrorMsg = "This file type is not allowed.  Accepted file types: .txt, .zip, .pdf, .docx, .xlsx, .pptx";
   echo $fileErrorMsg;
   exit();
}

// if directory for event's files does not exist, create it
if (!file_exists($newPath)) {
    mkdir($newPath, 0755, true);
}

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
    chmod($path, 0644);

    $result = $database->addEventFile($path, $eventKey);

    if ($result > 0) {
        echo "Your file has been uploaded.";
        shell_exec('chmod -R 755 ../uploads/');
        exit();
    }
}

echo "Your file could not be uploaded.";
