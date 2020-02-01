<?php

    // set up session

    require_once 'php/session.php';

    // set up connection to database via MySQLi

    require_once 'php/database.php';

    $database -> connectAsAdministrator();

    // include email function

    require_once 'php/notify_user.php';


    // get data from POST request

    $eventKey = $_POST['eventHash'];
    $addedSlots = json_decode($_POST['addedSlots'], true);
    $deletedSlots = json_decode($_POST['deletedSlots'], true);


    // get event data
    // if there are no results or event creator ONID does not match user ONID, abort now

    $eventData = $database -> getEvent($eventKey);

    if ($eventData == null) {
        echo "The specified event does not exist.";
        exit;
    }
    else if ($eventData['creator'] != $_SESSION['user']) {
        echo "You do not have permission to edit the specified event.";
        exit;
    }


    // initialize error codes

    $insertSuccess = TRUE;
    $deleteSuccess = TRUE;

    // delete slots if slots exist

    if (count($deletedSlots) > 0) {

        foreach ($deletedSlots as $slot) {

            // get list of users who will lose their time slots

            $removedUsers = $database -> getUsersOfSlot($slot["slotHash"]);

            // delete time slot
            
            $errorCode = $database -> editEvent_deleteSlot($eventKey, $slot["slotHash"]);

            if ($errorCode != 0) $deleteSuccess = FALSE;

            // build URL that leads to sign-up page for event

            // $developerONID = substr(getcwd(), strlen('/nfs/stak/users/'), -1 * strlen('/public_html/MyEventBoard'));
            // $siteURL = 'http://web.engr.oregonstate.edu/~' . $developerONID . '/MyEventBoard/';
            // $siteURL = $siteURL . 'register?key=' . $eventKey;

            $siteURL = 'https://eecs.oregonstate.edu/education/myeventboard/';
            $siteURL = $siteURL . 'register.php?key=' . $eventKey;

            // email users who were kicked off after successful delete

            if ($removedUsers == null || !$deleteSuccess) continue;

            foreach ($removedUsers as $user) {
                emailUserAboutDeletedSlot($user, $eventData["name"], $siteURL);
            }

        }

    }

    // add slots if slots exist

    if (count($addedSlots) > 0) {

        $slotData = array();

        $slotData["duration"] = intval($_POST['slot_duration']);
        $slotData["capacity"] = intval($_POST['slot_capacity']);

        foreach($addedSlots as $slot) {

            $slotData["startTime"] = $slot['startTime'] . ':00';
            $slotData["endTime"] = $slot['endTime'] . ':00';

            $errorCode = $database -> editEvent_addSlot($eventKey, $slotData);

            if ($errorCode != 0) $insertSuccess = FALSE;

        }

    }


    // response to front end

    if ($insertSuccess && $deleteSuccess) {
        echo "The event time slots changes were successfully saved!";
    }
    else {

        $errorCode = -1; // placeholder error code, means nothing

        if ($insertSuccess && ($deleteSuccess == FALSE)) {
            $errorCode = 2;
        }
        elseif (($insertSuccess == FALSE) && $deleteSuccess) {
            $errorCode = 1;
        }
        else {
            $errorCode = 3;
        }

        echo "The event could not be edited.\nError Code: " . $errorCode;

    }

?>
