<?php

require_once dirname(__DIR__) . '/config/env.php';

// include functions for generating hashes
require_once dirname(__DIR__) . '/config/hash.php';

// definition of class for custom interface of MySQLi database
class DatabaseInterface
{
    private $database;

    public function __construct($mode = null)
    {
        switch ($mode) {
            case 'a':
                $this->connectAsAdministrator();
            case 'ro':
                $this->connectAsReadOnlyUser();
            default:
                $this->connectToDatabase('TEST');
        }
    }

    public function getError()
    {
        return $this->database->connect_error;
    }

    private function connectToDatabase($env)
    {
        $host_env = "DATABASE_HOST_{$env}";
        $username_env = "DATABASE_USERNAME_{$env}";
        $password_env = "DATABASE_PASSWORD_{$env}";
        $name_env = "DATABASE_NAME_{$env}";
        $port_env = "DATABASE_PORT_{$env}";


        $host = $_ENV[$host_env];
        $userName = $_ENV[$username_env];
        $password = $_ENV[$password_env];
        $databaseName = $_ENV[$name_env];
        $port = $_ENV[$port_env];

        // create connection to database

        $this->database = new mysqli(
            $host,
            $userName,
            $password,
            $databaseName,
            $port
        );

        // check if that was successful
        // application is completely dependent on database so dying is acceptable
        // return if it was successful

        if ($this->getError()) {
            die('A connection to the database could not be established.');
        }
    }

    public function connectAsReadOnlyUser()
    {
        $this->connectToDatabase('READONLY');
    }

    public function connectAsAdministrator()
    {
        $this->connectToDatabase('ADMIN');
    }

    public function getUserByONID($userONID)
    {
        $query = "

            SELECT id, onid, email, last_name, first_name
            FROM meb_user
            WHERE onid = ?

        ";

        $statement = $this->database->prepare($query);

        $statement->bind_param("s", $userONID);
        $statement->execute();

        $result = $statement->get_result();

        if ($result->num_rows > 0) {
            $resultArray = $result->fetch_all(MYSQLI_ASSOC);
            $userData = $resultArray[0];
        } else {
            $userData = null;
        }

        $statement->close();
        $result->free();

        return $userData;
    }

    public function getUserKey($userONID)
    {
        $userData = $this->getUserByONID($userONID);

        if ($userData) {
            return $userData["id"];
        } else {
            return -1;
        }
    }

    public function addUser($userONID, $email, $firstName, $lastName)
    {
        // check if user is in database

        $userData = $this->getUserByONID($userONID);

        // if user is not in database, add user to database

        if ($userData == null) {
            $query = "

                INSERT INTO meb_user(onid, email, last_name, first_name)
                VALUES (?, ?, ?, ?)

            ";

            $statement = $this->database->prepare($query);

            $statement->bind_param("ssss", $userONID, $email, $lastName, $firstName);
            $statement->execute();

            $result = $statement->affected_rows;

            $statement->close();

            return $result;
        }
    }

    public function getTimeSlot($slotKey)
    {
        $query = "

            SELECT
            id, hash, start_time, end_time, duration,
            slot_capacity, spaces_available, is_full, fk_event_id

            FROM meb_timeslot
            WHERE hash = ?

        ";

        $statement = $this->database->prepare($query);
        $statement->bind_param("s", $slotKey);
        $statement->execute();

        $result = $statement->get_result();

        if ($result->num_rows > 0) {
            $resultArray = $result->fetch_all(MYSQLI_ASSOC);
            $slotData = $resultArray[0];
        } else {
            $slotData = null;
        }

        $statement->close();
        $result->free();

        return $slotData;
    }

    private function addTimeSlots($slotData, $eventID)
    {
        $query = "

            INSERT INTO
            meb_timeslot(hash, start_time, end_time, duration, slot_capacity, spaces_available, is_full, fk_event_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)

        ";

        $statement = $this->database->prepare($query);

        $statement->bind_param(
            "sssiiiii",
            $hash,
            $startDate,
            $endDate,
            $duration,
            $capacity,
            $spaces,
            $full,
            $eventID
        );

        $result = 0;
        $full = 0;

        foreach ($slotData["dates"] as $item) {
            $startDate = $item["startDate"];
            $endDate = $item["endDate"];
            $duration = $slotData["duration"];
            $capacity = $slotData["capacity"];
            $spaces = $slotData["capacity"];

            $hash = createTimeSlotHash($startDate, $endDate, $eventID);

            $statement->execute();

            $result += $statement->affected_rows;
        }

        $statement->close();

        return $result;
    }

    public function getUserEvents($userONID)
    {
        $query = "

            SELECT
            meb_event.hash AS 'Event Key',
            meb_event.name AS 'Event Name',
            CONCAT(CAST(meb_event.open_slots AS CHAR), ' / ', CAST(meb_event.capacity AS CHAR)) AS 'Slots'

            FROM meb_event
            INNER JOIN meb_user
            ON meb_event.fk_event_creator = meb_user.id
            WHERE meb_user.onid = ?
            ORDER BY meb_event.name

        ";

        $statement = $this->database->prepare($query);
        $statement->bind_param("s", $userONID);
        $statement->execute();

        $result = $statement->get_result();

        if ($result->num_rows > 0) {
            $resultArray = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $resultArray = null;
        }

        $statement->close();
        $result->free();

        return $resultArray;
    }

    public function getEventSlotDetails($eventKey)
    {
        $query = "

            SELECT
            meb_timeslot.duration,
            meb_timeslot.slot_capacity AS 'capacity'

            FROM meb_timeslot
            INNER JOIN meb_event
            ON meb_event.id = meb_timeslot.fk_event_id

            WHERE meb_event.hash = ?
            LIMIT 1

        ";

        $statement = $this->database->prepare($query);

        $statement->bind_param("s", $eventKey);
        $statement->execute();

        $result = $statement->get_result();

        if ($result->num_rows > 0) {
            $resultArray = $result->fetch_all(MYSQLI_ASSOC);
            $slotDetails = $resultArray[0];
        } else {
            $slotDetails = null;
        }

        $statement->close();
        $result->free();

        return $slotDetails;
    }

    public function getEventBySlotKey($slotKey)
    {
        $query = "

            SELECT
            meb_event.id, meb_event.hash, name, description, location,
            onid AS 'creator', capacity, open_slots, meb_event.mod_date,
            is_anon AS 'anonymous', enable_upload AS 'upload'

            FROM meb_event
            INNER JOIN meb_user
                ON meb_event.fk_event_creator = meb_user.id
            INNER JOIN meb_timeslot
                ON meb_event.id = meb_timeslot.fk_event_id
            WHERE meb_timeslot.hash = ?

        ";

        $statement = $this->database->prepare($query);

        $statement->bind_param("s", $slotKey);
        $statement->execute();

        $result = $statement->get_result();

        if ($result->num_rows > 0) {
            $resultArray = $result->fetch_all(MYSQLI_ASSOC);
            $eventData = $resultArray[0];
        } else {
            $eventData = null;
        }

        $statement->close();
        $result->free();

        return $eventData;
    }

    public function getEvent($eventKey)
    {
        $query = "

            SELECT
            meb_event.id, hash, name, description, location, onid AS 'creator',
            capacity, open_slots, is_anon AS 'anonymous', enable_upload AS 'upload',
            mod_date, event_file

            FROM meb_event
            INNER JOIN meb_user ON meb_event.fk_event_creator = meb_user.id
            WHERE hash = ?

        ";

        $statement = $this->database->prepare($query);

        $statement->bind_param("s", $eventKey);
        $statement->execute();

        $result = $statement->get_result();

        if ($result->num_rows > 0) {
            $resultArray = $result->fetch_all(MYSQLI_ASSOC);
            $eventData = $resultArray[0];
        } else {
            $eventData = null;
        }

        $statement->close();
        $result->free();

        return $eventData;
    }

    public function changeEventDetails($eventKey, $eventData)
    {
        $name = $eventData["name"];
        $description = $eventData["description"];
        $location = $eventData["location"];
        $isAnonymous = $eventData["anonymous"];
        $enableUpload = $eventData["upload"];

        $query = "

            UPDATE meb_event
            SET name = ?, description = ?, location = ?, is_anon = ?, enable_upload = ?
            WHERE hash = ?

        ";

        $statement = $this->database->prepare($query);

        $statement->bind_param(
            "sssiis",
            $name,
            $description,
            $location,
            $isAnonymous,
            $enableUpload,
            $eventKey
        );

        $statement->execute();

        $result = $statement->affected_rows;

        $statement->close();

        return $result;
    }

    public function deleteEvent($eventKey)
    {
        $query = "DELETE FROM meb_event WHERE hash = ?";

        $statement = $this->database->prepare($query);

        $statement->bind_param("s", $eventKey);
        $statement->execute();

        $result = $statement->affected_rows;

        $statement->close();

        return $result;
    }

    // https://paulund.co.uk/php-delete-directory-and-files-in-directory
    // this will delete all dir/files inside the uploads/eventHash dir
    // and will then delete the eventHash dir.
    public function deleteEventFiles($eventKey)
    {
        $dirname = '../uploads/' . $eventKey . '/';
        
        if (is_dir($dirname)) {
          $dir_handle = opendir($dirname);
          if (!$dir_handle) {
            return false;
          }
          while($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
              if (!is_dir($dirname."/".$file)) {
                unlink($dirname."/".$file);
              } else {
                     delete_directory($dirname.'/'.$file);
              }           
            }  
          }
        }
        closedir($dir_handle);
        rmdir($dirname);
    }

    public function deleteFile($attendeeOnid, $eventKey, $type)
    {
       $fileName = $attendeeOnid . $type . '.*';
       $filePath = '../uploads/' . $eventKey . '/' . $fileName;
       $files = glob($filePath);
       foreach ($files as $file) {
         unlink($file);
       }       
    }

    public function addEvent($eventData, $slotData)
    {
        $name = $eventData["name"];
        $description = $eventData["description"];
        $creator = $eventData["creator"];
        $location = $eventData["location"];
        $capacity = $eventData["capacity"];
        $openSlots = $eventData["capacity"];
        $anonymous = $eventData["anonymous"];
        $upload = $eventData["upload"];

        $hash = createEventHash($name, $description, $creator, $location);

        $query = "

            INSERT INTO meb_event(hash, name, description, fk_event_creator, location, capacity, open_slots, is_anon, enable_upload)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)

        ";

        $statement = $this->database->prepare($query);

        $statement->bind_param(
            "sssisiiii",
            $hash,
            $name,
            $description,
            $creator,
            $location,
            $capacity,
            $openSlots,
            $anonymous,
            $upload
        );

        $statement->execute();

        $newEventID = $this->database->insert_id;
        $this->addTimeSlots($slotData, $newEventID);

        $result = $statement->affected_rows;

        $statement->close();

        return $result;
    }

    public function getRegistrationData($eventKey, $userONID)
    {
        $query = "

            SELECT
            T.hash, T.start_time, T.duration,
            T.slot_capacity, T.spaces_available, T.is_full,
            E.description, E.location,
            IF(U.onid = ?, TRUE, FALSE)

            FROM meb_timeslot T
            INNER JOIN meb_event E
                ON T.fk_event_id = E.id
            LEFT JOIN meb_booking B
                ON T.id = B.fk_timeslot_id
            LEFT JOIN meb_user U
                ON B.fk_user_id = U.id
            WHERE E.hash = ?
			ORDER BY T.start_time

        ";

        $statement = $this->database->prepare($query);

        $statement->bind_param("ss", $userONID, $eventKey);
        $statement->execute();

        $result = $statement->get_result();

        if ($result->num_rows > 0) {
            $resultArray = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $resultArray = null;
        }

        $statement->close();
        $result->free();

        return $resultArray;
    }

    public function getEditingData($eventKey)
    {
        $query = "

            SELECT
            t0.hash AS 'eventHash', t0.name, t0.description,
            t0.location, t1.onid AS 'creator', t2.hash AS 'slotHash',
            t2.start_time AS 'startTime', t2.end_time AS 'endTime',
            t2.duration, t2.slot_capacity AS 'capacity',
            t0.is_anon AS 'anonymous', t0.enable_upload as 'upload'

            FROM meb_event AS t0
            INNER JOIN meb_user AS t1
                ON t0.fk_event_creator = t1.id
            INNER JOIN meb_timeslot AS t2
                ON t0.id = t2.fk_event_id

            WHERE t0.hash = ?
            ORDER BY t2.start_time ASC

        ";

        $statement = $this->database->prepare($query);

        $statement->bind_param("s", $eventKey);
        $statement->execute();

        $result = $statement->get_result();

        $resultObjects = [];

        while (1) {
            $resultObject = $result->fetch_object();
            if ($resultObject == null) {
                break;
            }
            $resultObjects[] = $resultObject;
        }

        $statement->close();
        $result->free();

        return $resultObjects;
    }

    public function getDashboardData($userONID)
    {
        $query = "

            SELECT
            U.onid AS 'user',
            E.name AS 'event_name',
            E.location AS 'event_location',
            E.description AS 'event_description',
            T.start_time AS 'start_time',
            T.end_time as 'end_time',
            T.duration as 'slot_duration',
            T.spaces_available as 'slots_remaining',
            U1.first_name as 'ec_first_name',
            U1.last_name as 'ec_last_name'

            FROM meb_booking
            INNER JOIN meb_user U
                ON meb_booking.fk_user_id = U.id
            INNER JOIN meb_timeslot T
                ON meb_booking.fk_timeslot_id = T.id
            INNER JOIN meb_event E
                ON T.fk_event_id = E.id
            INNER JOIN meb_user U1
                ON E.fk_event_creator = U1.id

            WHERE U.onid = ?
            ORDER BY T.start_time

        ";

        $statement = $this->database->prepare($query);

        $statement->bind_param("s", $userONID);
        $statement->execute();

        $result = $statement->get_result();

        if ($result->num_rows > 0) {
            $resultArray = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $resultArray = null;
        }

        $statement->close();
        $result->free();

        return $resultArray;
    }

    public function getAttendeeData($eventKey)
    {
        $query = "

            SELECT
            t1.start_time AS 'Time Slot Start Time',
            CONCAT(t2.first_name, ' ', t2.last_name, ' — ', t2.onid) AS 'Attendee Name — ONID',
            f.path AS 'File'

            FROM meb_booking AS t0
            INNER JOIN meb_timeslot AS t1
                ON t0.fk_timeslot_id = t1.id
            LEFT JOIN meb_files as f
                ON t0.id = f.fk_booking_id
            INNER JOIN meb_user AS t2
                ON t0.fk_user_id = t2.id
            INNER JOIN meb_event AS t3
                ON t1.fk_event_id = t3.id

            WHERE t3.hash = ?
            ORDER BY t1.start_time AND t2.first_name

        ";

        $statement = $this->database->prepare($query);

        $statement->bind_param("s", $eventKey);
        $statement->execute();

        $result = $statement->get_result();

        if ($result->num_rows > 0) {
            $resultArray = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $resultArray = null;
        }

        $statement->close();
        $result->free();

        return $resultArray;
    }

    public function getReservationData($userONID)
    {
        $query = "

            SELECT
            t2.name AS 'Event',
            t1.start_time AS 'Start Time',
            t2.location AS 'Location',
            t2.hash AS 'Event Key',
            t1.hash AS 'Time Slot Key'

            FROM meb_booking AS t0
            INNER JOIN meb_timeslot AS t1
                ON t0.fk_timeslot_id = t1.id
            INNER JOIN meb_event AS t2
                ON t1.fk_event_id = t2.id
            INNER JOIN meb_user AS t3
                ON t0.fk_user_id = t3.id
            INNER JOIN meb_user AS t4
                ON t2.fk_event_creator = t4.id

            WHERE t3.onid = ?
            ORDER BY t1.start_time

        ";

        $statement = $this->database->prepare($query);

        $statement->bind_param("s", $userONID);
        $statement->execute();

        $result = $statement->get_result();

        if ($result->num_rows > 0) {
            $resultArray = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $resultArray = null;
        }

        $statement->close();
        $result->free();

        return $resultArray;
    }

    public function getReservationDetails($slotKey, $userONID)
    {
        $query = "

            SELECT
            t3.hash, t3.name, t3.location, t3.description, t3.enable_upload AS 'upload',
            t3.event_file,
            CONCAT(t4.first_name, ' ', t4.last_name) AS 'creator',
            t1.start_time, t1.end_time, t5.path AS 'file'

            FROM meb_booking AS t0
            INNER JOIN meb_timeslot AS t1
                ON t0.fk_timeslot_id = t1.id
            INNER JOIN meb_user AS t2
                ON t0.fk_user_id = t2.id
            INNER JOIN meb_event AS t3
                ON t1.fk_event_id = t3.id
            INNER JOIN meb_user AS t4
                ON t3.fk_event_creator = t4.id
            LEFT JOIN meb_files AS t5
                ON t0.id = t5.fk_booking_id

            WHERE t1.hash = ? AND t2.onid = ?

        ";

        $statement = $this->database->prepare($query);

        $statement->bind_param("ss", $slotKey, $userONID);
        $statement->execute();

        $result = $statement->get_result();

        if ($result->num_rows > 0) {
            $resultArray = $result->fetch_all(MYSQLI_ASSOC);
            $reservationDetails = $resultArray[0];
        } else {
            $reservationDetails = null;
        }

        $statement->close();
        $result->free();

        return $reservationDetails;
    }

    public function getUsersOfSlot($slotKey)
    {
        $query = "

            SELECT
            u.email, u.first_name AS 'firstName', u.last_name AS 'lastName'

            FROM meb_timeslot t
            INNER JOIN meb_booking b
                ON t.id = b.fk_timeslot_id
            INNER JOIN meb_user u
                ON b.fk_user_id = u.id
            INNER JOIN meb_event e
                ON t.fk_event_id = e.id

            WHERE t.hash = ?

        ";

        $statement = $this->database->prepare($query);

        $statement->bind_param("s", $slotKey);
        $statement->execute();

        $result = $statement->get_result();

        if ($result->num_rows > 0) {
            $resultArray = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $resultArray = null;
        }

        $statement->close();
        $result->free();

        return $resultArray;
    }

    public function getUserBooking($slotKey, $userONID)
    {
        $query = "

            SELECT
            b.id, fk_timeslot_id AS 'slotID', fk_user_id AS 'userID'

            FROM meb_booking AS b
            INNER JOIN meb_timeslot AS t
            ON t.id = b.fk_timeslot_id
            INNER JOIN meb_user AS u
            ON u.id = b.fk_user_id

            WHERE t.hash = ? AND u.onid = ?

        ";

        $statement = $this->database->prepare($query);

        $statement->bind_param("ss", $slotKey, $userONID);
        $statement->execute();

        $result = $statement->get_result();

        if ($result->num_rows > 0) {
            $resultArray = $result->fetch_all(MYSQLI_ASSOC);
            $bookingData = $resultArray[0];
        } else {
            $bookingData = null;
        }

        $statement->close();
        $result->free();

        return $bookingData;
    }

    public function reserveTimeSlot($slotID, $userID)
    {
        $query = "CALL meb_reserve_slot(?, ?, @res1)";

        $statement = $this->database->prepare($query);

        $statement->bind_param("ii", $slotID, $userID);
        $statement->execute();
        $statement->close();

        $query = "SELECT @res1";
        $result = $this->database->query($query);

        if ($result) {
            $resultArray = $result->fetch_all(MYSQLI_NUM);
            $errorCode = $resultArray[0][0];
        } else {
            $errorCode = -1;
        }

        $statement->close();
        $result->free();

        return $errorCode;
    }

    public function deleteReservation($slotKey, $userONID)
    {
        $query = "CALL meb_delete_reservation(?, ?, @res1)";

        $statement = $this->database->prepare($query);

        $statement->bind_param("ss", $slotKey, $userONID);
        $statement->execute();

        $query = "SELECT @res1";
        $result = $this->database->query($query);

        if ($result) {
            $resultArray = $result->fetch_all(MYSQLI_NUM);
            $errorCode = $resultArray[0];
        } else {
            $errorCode = -1;
        }

        $statement->close();
        $result->free();

        return $errorCode;
    }

    public function replaceFilePath($filePath, $fileID)
    {
        $query = "

            UPDATE meb_files
            SET path = ? WHERE id = ?

        ";

        $statement = $this->database->prepare($query);

        $statement->bind_param("si", $filePath, $fileID);
        $statement->execute();

        $result = $statement->affected_rows;

        $statement->close();

        return $result;
    }

    public function getFile($bookingID)
    {
        $query = "

            SELECT id, path, fk_booking_id AS 'bookingID'
            FROM meb_files
            WHERE fk_booking_id = ?

        ";

        $statement = $this->database->prepare($query);

        $statement->bind_param("i", $bookingID);
        $statement->execute();

        $result = $statement->get_result();

        if ($result->num_rows > 0) {
            $resultArray = $result->fetch_all(MYSQLI_ASSOC);
            $fileData = $resultArray[0];
        } else {
            $fileData = null;
        }

        $statement->close();
        $result->free();

        return $fileData;
    }

    public function addFile($filePath, $bookingID)
    {
        // if there exists file associated with booking
        // replace path for that file

        $fileData = $this->getFile($bookingID);

        if ($fileData) {
            $result = $this->replaceFilePath($filePath, $fileData["id"]);
            return 1;
        }

        // add file path associated with booking

        $query = "

            INSERT INTO meb_files(path, fk_booking_id)
            VALUES (?, ?)

        ";

        $statement = $this->database->prepare($query);

        $statement->bind_param("si", $filePath, $bookingID);
        $statement->execute();

        $result = $statement->affected_rows;

        $statement->close();

        return $result;
    }

    public function addEventFile($filePath, $eventKey)
    {
       // this will need to delete old files linked

       $queryClearFile = "
        
           UPDATE `meb_event`
           SET event_file = null
           WHERE hash = ?;
       ";

       $statementClearFile = $this->database->prepare($queryClearFile);

       $statementClearFile->bind_param("s", $eventKey);
       $statementClearFile->execute();

       $statementClearFile->close();
       
       $queryUpdateFile = "

           UPDATE `meb_event`
           SET event_file = ?
           WHERE hash = ?;
       ";

       $statementUpdateFile = $this->database->prepare($queryUpdateFile);
       
       $statementUpdateFile->bind_param("ss", $filePath, $eventKey);
       $statementUpdateFile->execute();

       $result = $statementUpdateFile->affected_rows;

       $statementUpdateFile->close();

       return $result;
    }

    public function editEvent_deleteSlot($eventKey, $slotKey)
    {
        // get event mod date and and event key

        $eventData = $this->getEvent($eventKey);
        $oldModDate = $eventData["mod_date"];

        // use stored procedure to delete slot

        $query = 'CALL meb_delete_slot(?, ?, ?, @res3)';

        $statement = $this->database->prepare($query);

        $statement->bind_param("sss", $oldModDate, $eventKey, $slotKey);
        $statement->execute();

        $query = "SELECT @res3";
        $result = $this->database->query($query);

        if ($result) {
            $resultArray = $result->fetch_all(MYSQLI_NUM);
            $errorCode = $resultArray[0][0];
        } else {
            $errorCode = -1;
        }

        $statement->close();
        $result->free();

        return $errorCode;
    }

    public function editEvent_addSlot($eventKey, $slotData)
    {
        // get event mod date and and event key

        $eventData = $this->getEvent($eventKey);
        $oldModDate = $eventData["mod_date"];

        // use stored procedure to add slot

        $query = 'CALL meb_add_slot(?, ?, ?, ?, ?, ?, ?, @res2)';

        $statement = $this->database->prepare($query);

        $startTime = $slotData["startTime"];
        $endTime = $slotData["endTime"];
        $duration = $slotData["duration"];
        $capacity = $slotData["capacity"];

        $slotKey = createTimeSlotHash($startTime, $endTime, $eventKey);

        $statement->bind_param(
            'sssssii',
            $oldModDate,
            $eventKey,
            $slotKey,
            $startTime,
            $endTime,
            $duration,
            $capacity
        );

        $statement->execute();

        $query = "SELECT @res2";
        $result = $this->database->query($query);

        if ($result) {
            $resultArray = $result->fetch_all(MYSQLI_NUM);
            $errorCode = $resultArray[0][0];
        } else {
            $errorCode = -1;
        }

        $statement->close();
        $result->free();

        return $errorCode;
    }


  //get a timeslot hash from a start time and event hash
    public function getReservedSlotId($startTime, $eventHash)
    {
        $query = "

	SELECT meb_timeslot.id as slotId FROM `meb_timeslot`
	INNER JOIN `meb_event` ON meb_timeslot.fk_event_id = meb_event.id
	AND meb_timeslot.start_time = ?
	AND meb_event.hash = ?

      	;";
        $statement = $this->database->prepare($query);

        $statement->bind_param("ss", $startTime, $eventHash);
        $statement->execute();
      
        $result = $statement->get_result();
        if ($result->num_rows > 0) {
            $resultArray = $result->fetch_all(MYSQLI_ASSOC);
            $hash = $resultArray[0];
        } else {
            $hash = null;
        }

        $statement->close();
        $result->free();
        return $hash;
    }

    // creator deletes an attendee's reservation
    public function creatorDeleteReservation($slotId, $attendeeOnid)
    {
        $query = "

       SELECT meb_booking.id as  bookingID  FROM `meb_booking`
       INNER JOIN `meb_user`
       ON meb_booking.fk_user_id = meb_user.id
       WHERE meb_user.onid = ?
       AND meb_booking.fk_timeslot_id = ?

       ;";

        $statement = $this->database->prepare($query);

        $statement->bind_param("si", $attendeeOnid, $slotId);
        $statement->execute();

        $result = $statement->get_result();
        if ($result->num_rows > 0) {
            $resultArray = $result->fetch_all(MYSQLI_ASSOC);
            $id = $resultArray[0];
        } else {
            $id = null;
        }

        $statement->close();
        $result->free();

        return $id;
    }

    public function deleteBooking($bookingId)
    {
        $query = "

        DELETE FROM `meb_booking` WHERE id = ?

        ;";

        $statement = $this->database->prepare($query);

        $statement->bind_param("i", $bookingId);
        $statement->execute();

        $result = $statement->affected_rows;

        $statement->close();

        return $result;
    }

    public function updateAvailableSlots($eventHash, $slotId)
    {
        $update_meb_timeslot_query = "

        UPDATE `meb_timeslot`
        SET spaces_available = spaces_available + 1, is_full = 0
        WHERE meb_timeslot.id = ?

        ;";

        $updateTimeSlotSpace = $this->database->prepare($update_meb_timeslot_query);

        $updateTimeSlotSpace->bind_param("i", $slotId);
        $updateTimeSlotSpace->execute();

        $result = $updateTimeSlotSpace->affected_rows;

        $updateTimeSlotSpace->close();

        //echo $result;

        $update_meb_event_query = "

        UPDATE `meb_event`
        SET open_slots = open_slots + 1
        WHERE meb_event.hash = ?

        ;";

        $updateEventSlot = $this->database->prepare($update_meb_event_query);

        $updateEventSlot->bind_param("s", $eventHash);
        $updateEventSlot->execute();

        $result2 = $updateEventSlot->affected_rows;

        $updateEventSlot->close();

        return $result2;
    }
}

$database = new DatabaseInterface();
$database->connectAsReadOnlyUser();
