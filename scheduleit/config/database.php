<?php

require_once ABSPATH . 'config/env.php';

// include functions for generating hashes
require_once ABSPATH . 'config/hash.php';

/**
 * DatabaseInterface
 * php version 7.2.28
 */
class DatabaseInterface
{
    /**
     * @var object
     */
    private $database;

    /**
     * Set up interface.
     *
     * @param string|null $mode
     * @return void
     */
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

    /**
     * Create connection with credentials from .env file.
     *
     * @param string $env
     * @return void
     */
    private function connectToDatabase($env)
    {

        $host = $_ENV["DATABASE_HOST_{$env}"];
        $userName = $_ENV["DATABASE_USERNAME_{$env}"];
        $password = $_ENV["DATABASE_PASSWORD_{$env}"];
        $databaseName = $_ENV["DATABASE_NAME_{$env}"];
        $port = $_ENV["DATABASE_PORT_{$env}"];

        // Create connection to database
        $this->database = new mysqli(
            $host,
            $userName,
            $password,
            $databaseName,
            $port
        );

        // Redirect to error page if there is a connection error
        if ($this->database->connect_error) {
            header('Location: ' . SITE_DIR . '/error?s=500');
        }
    }

    /**
     * Connect to database with read only privileges.
     *
     * @return void
     */
    public function connectAsReadOnlyUser()
    {
        $this->connectToDatabase('READONLY');
    }

    /**
     * Connect to database with admin privileges.
     *
     * @return void
     */
    public function connectAsAdministrator()
    {
        $this->connectToDatabase('ADMIN');
    }

    /**
     * Get user record by ONID.
     *
     * @param string $onid
     * @return mixed
     */
    public function getUserByONID($onid)
    {
        $query = "

            SELECT id, onid, email, last_name, first_name
            FROM meb_user
            WHERE onid = ?
            LIMIT 1

        ";

        $statement = $this->database->prepare($query);

        $statement->bind_param("s", $onid);
        $statement->execute();

        $result = $statement->get_result();

        if ($result->num_rows > 0) {
            $list = $result->fetch_all(MYSQLI_ASSOC);
            $user = $list[0];
        } else {
            $user = null;
        }

        $result->free();
        $statement->close();

        return $user;
    }

    /**
     * Create new user.
     *
     * @param string $onid
     * @param string $email
     * @param string $first_name
     * @param string $last_name
     * @return int;
     */
    public function addUser($onid, $email, $first_name, $last_name)
    {
        // Check if user is in database
        $user = $this->getUserByONID($onid);

        // If user is not in database, add user to database
        if ($user == null) {
            $query = "

                INSERT INTO meb_user(onid, email, last_name, first_name)
                VALUES (?, ?, ?, ?)

            ";

            $statement = $this->database->prepare($query);

            $statement->bind_param("ssss", $onid, $email, $last_name, $first_name);
            $statement->execute();

            $result = $statement->affected_rows;
            $statement->close();

            return $result;
        }

        return 0;
    }

    /**
     * Get meetings created by user for the manage page.
     *
     * @param string $onid
     * @param string $search_term
     * @return array
     */
    public function getManageMeetings($user_id, $search_term)
    {
        if ($search_term) {
            $events_query = "

            SELECT
            meb_event.id,
            meb_event.hash,
            meb_event.name,
            meb_event.location,
            meb_event.open_slots,
            meb_event.capacity
            FROM meb_event
            WHERE meb_event.fk_event_creator = ?
            AND meb_event.name LIKE ?
            ORDER BY meb_event.name ASC

            ;";
            $events = $this->database->prepare($events_query);
            $partial_match = '%' . $search_term . '%';
            $events->bind_param("is", $id, $partial_match);
        } else {
            $events_query = "

            SELECT
            meb_event.id,
            meb_event.hash,
            meb_event.name,
            meb_event.location,
            meb_event.open_slots,
            meb_event.capacity
            FROM meb_event
            WHERE meb_event.fk_event_creator = ?
            ORDER BY meb_event.name ASC

            ;";
            $events = $this->database->prepare($events_query);
            $events->bind_param("i", $user_id);
        }

        $events->execute();

        $result = $events->get_result();
        $list = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $events->close();

        return $list;
    }

    /**
     * Get aggregated timeslot dates for meeting.
     *
     * @param int $id
     * @return array
     */
    public function getDatesByMeetingId($id)
    {
        $timeslots_query = "

        SELECT DISTINCT DATE_FORMAT(start_time, '%Y-%m-%d') as date FROM meb_timeslot
        WHERE fk_event_id = ?
        ORDER BY date ASC

        ;";

        $timeslots = $this->database->prepare($timeslots_query);
        $timeslots->bind_param("i", $id);
        $timeslots->execute();

        $result = $timeslots->get_result();
        $list = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $timeslots->close();

        return $list;
    }

    /**
     * Get meetings created by user or where they're an attendee for the calendar page.
     *
     * @param int $user_id
     * @return array
     */
    public function getCalendarMeetings($user_id)
    {
        $bookings_query = "

        SELECT
        meb_event.id,
        meb_event.name,
        meb_event.location,
        meb_event.fk_event_creator AS creator_id,
        meb_timeslot.start_time,
        meb_timeslot.end_time,
        meb_user.email AS attendee_email,
        CONCAT(meb_user.first_name, ' ', meb_user.last_name) AS attendee_name,
        meb_user.onid AS attendee_onid,
        meb_user2.email AS creator_email,
        CONCAT(meb_user2.first_name, ' ', meb_user2.last_name) AS creator_name,
        meb_user2.onid AS creator_onid
        FROM meb_booking
        INNER JOIN meb_timeslot ON meb_timeslot.id = meb_booking.fk_timeslot_id
        INNER JOIN meb_event ON meb_event.id = meb_timeslot.fk_event_id
        INNER JOIN meb_user ON meb_user.id = meb_booking.fk_user_id
        INNER JOIN meb_user AS meb_user2 ON meb_user2.id = meb_event.fk_event_creator
        WHERE (meb_user.id = ? OR meb_user2.id = ?)
        ORDER BY meb_timeslot.start_time DESC

        ;";

        $bookings = $this->database->prepare($bookings_query);
        $bookings->bind_param("ii", $user_id, $user_id);
        $bookings->execute();

        $result = $bookings->get_result();
        $list = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $bookings->close();

        return $list;
    }

    /**
     * Get upcoming meetings created by user or where they're an attendee for the meetings page.
     *
     * @param int $user_id
     * @return array
     */
    public function getAllUpcomingMeetings($user_id)
    {
        $bookings_query = "

        SELECT
        meb_event.id,
        meb_event.hash,
        meb_event.name,
        meb_event.location,
        meb_event.fk_event_creator AS creator_id,
        meb_timeslot.start_time,
        meb_timeslot.end_time,
        meb_user.email AS attendee_email,
        CONCAT(meb_user.first_name, ' ', meb_user.last_name) AS attendee_name,
        meb_user.onid AS attendee_onid,
        meb_user2.email AS creator_email,
        CONCAT(meb_user2.first_name, ' ', meb_user2.last_name) AS creator_name,
        meb_user2.onid AS creator_onid
        FROM meb_booking
        INNER JOIN meb_timeslot ON meb_timeslot.id = meb_booking.fk_timeslot_id
        INNER JOIN meb_event ON meb_event.id = meb_timeslot.fk_event_id
        INNER JOIN meb_user ON meb_user.id = meb_booking.fk_user_id
        INNER JOIN meb_user AS meb_user2 ON meb_user2.id = meb_event.fk_event_creator
        WHERE (meb_user.id = ? OR meb_user2.id = ?)
        AND meb_timeslot.start_time > now()
        ORDER BY meb_timeslot.start_time ASC

        ;";

        $bookings = $this->database->prepare($bookings_query);
        $bookings->bind_param("ii", $user_id, $user_id);
        $bookings->execute();

        $result = $bookings->get_result();
        $list = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $bookings->close();

        return $list;
    }

    /**
     * Get upcoming meetings created by user for the meetings page.
     *
     * @param int $user_id
     * @return array
     */
    public function getUpcomingMeetingsByCreator($user_id)
    {
        $bookings_query = "

        SELECT
        meb_event.id,
        meb_event.hash,
        meb_event.name,
        meb_event.location,
        meb_event.fk_event_creator AS creator_id,
        meb_timeslot.start_time,
        meb_timeslot.end_time,
        meb_user.email AS attendee_email,
        CONCAT(meb_user.first_name, ' ', meb_user.last_name) AS attendee_name,
        meb_user.onid AS attendee_onid,
        meb_user2.email AS creator_email,
        CONCAT(meb_user2.first_name, ' ', meb_user2.last_name) AS creator_name,
        meb_user2.onid AS creator_onid
        FROM meb_booking
        INNER JOIN meb_timeslot ON meb_timeslot.id = meb_booking.fk_timeslot_id
        INNER JOIN meb_event ON meb_event.id = meb_timeslot.fk_event_id
        INNER JOIN meb_user ON meb_user.id = meb_booking.fk_user_id
        INNER JOIN meb_user AS meb_user2 ON meb_user2.id = meb_event.fk_event_creator
        WHERE meb_user2.id = ?
        AND meb_timeslot.start_time > now()
        ORDER BY meb_timeslot.start_time ASC

        ;";

        $bookings = $this->database->prepare($bookings_query);
        $bookings->bind_param("i", $user_id);
        $bookings->execute();

        $result = $bookings->get_result();
        $list = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $bookings->close();

        return $list;
    }

    /**
     * Get past meetings created by user or where they're an attendee for the meetings page.
     *
     * @param int $user_id
     * @return array
     */
    public function getPastMeetings($user_id)
    {
        $bookings_query = "

        SELECT
        meb_event.id,
        meb_event.hash,
        meb_event.name,
        meb_event.location,
        meb_event.fk_event_creator AS creator_id,
        meb_timeslot.start_time,
        meb_timeslot.end_time,
        meb_user.email AS attendee_email,
        CONCAT(meb_user.first_name, ' ', meb_user.last_name) AS attendee_name,
        meb_user.onid AS attendee_onid,
        meb_user2.email AS creator_email,
        CONCAT(meb_user2.first_name, ' ', meb_user2.last_name) AS creator_name,
        meb_user2.onid AS creator_onid
        FROM meb_booking
        INNER JOIN meb_timeslot ON meb_timeslot.id = meb_booking.fk_timeslot_id
        INNER JOIN meb_event ON meb_event.id = meb_timeslot.fk_event_id
        INNER JOIN meb_user ON meb_user.id = meb_booking.fk_user_id
        INNER JOIN meb_user AS meb_user2 ON meb_user2.id = meb_event.fk_event_creator
        WHERE (meb_user.id = ? OR meb_user2.id = ?)
        AND meb_timeslot.start_time < now()
        ORDER BY meb_timeslot.start_time DESC

        ;";

        $bookings = $this->database->prepare($bookings_query);

        $bookings->bind_param("ii", $user_id, $user_id);
        $bookings->execute();

        $result = $bookings->get_result();
        $list = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $bookings->close();

        return $list;
    }

    /**
     * Search meetings by name, location, creator, or attendee for the meetings page.
     *
     * @param int $user_id
     * @param string $search_term
     * @return array
     */
    public function getMeetingsBySearchTerm($user_id, $search_term)
    {
        $bookings_query = "

        SELECT
        meb_event.id,
        meb_event.hash,
        meb_event.name,
        meb_event.location,
        meb_event.fk_event_creator AS creator_id,
        meb_timeslot.start_time,
        meb_timeslot.end_time,
        meb_user.email AS attendee_email,
        CONCAT(meb_user.first_name, ' ', meb_user.last_name) AS attendee_name,
        meb_user.onid AS attendee_onid,
        meb_user2.email AS creator_email,
        CONCAT(meb_user2.first_name, ' ', meb_user2.last_name) AS creator_name,
        meb_user2.onid AS creator_onid
        FROM meb_booking
        INNER JOIN meb_timeslot ON meb_timeslot.id = meb_booking.fk_timeslot_id
        INNER JOIN meb_event ON meb_event.id = meb_timeslot.fk_event_id
        INNER JOIN meb_user ON meb_user.id = meb_booking.fk_user_id
        INNER JOIN meb_user AS meb_user2 ON meb_user2.id = meb_event.fk_event_creator
        WHERE (meb_user.id = ? OR meb_user2.id = ?)
        AND (
            meb_event.name LIKE ?
            OR meb_event.location LIKE ?
            OR CONCAT(meb_user.first_name, ' ', meb_user.last_name) LIKE ?
            OR CONCAT(meb_user2.first_name, ' ', meb_user2.last_name) LIKE ?
        )
        ORDER BY meb_timeslot.start_time DESC

        ;";

        $bookings = $this->database->prepare($bookings_query);

        $partial_match = '%' . $search_term . '%';
        $bookings->bind_param("iissss", $user_id, $user_id, $partial_match, $partial_match, $partial_match, $partial_match);
        $bookings->execute();

        $result = $bookings->get_result();
        $list = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $bookings->close();

        return $list;
    }

    /**
     * Get meeting by id.
     *
     * @param id $id
     * @return mixed
     */
    public function getMeetingById($id)
    {
        $bookings_query = "

        SELECT
        meb_event.id,
        meb_event.name,
        meb_event.location,
        meb_event.description,
        meb_event.hash,
        meb_event.capacity,
        meb_event.open_slots,
        meb_event.is_anon,
        meb_event.enable_upload,
        meb_event.event_file AS creator_file,
        meb_event.fk_event_creator AS creator_id
        FROM meb_event
        WHERE meb_event.id = ?
        LIMIT 1

        ;";

        $bookings = $this->database->prepare($bookings_query);

        $bookings->bind_param("i", $id);
        $bookings->execute();

        $result = $bookings->get_result();

        if ($result->num_rows > 0) {
            $list = $result->fetch_all(MYSQLI_ASSOC);
            $meeting = $list[0];
        } else {
            $meeting = null;
        }

        $result->free();
        $bookings->close();

        return $meeting;
    }

    /**
     * Get meeting by hash.
     *
     * @param string $hash
     * @return mixed
     */
    public function getMeetingByHash($hash)
    {
        $bookings_query = "

        SELECT
        meb_event.id,
        meb_event.name,
        meb_event.location,
        meb_event.description,
        meb_event.hash,
        meb_event.capacity,
        meb_event.open_slots,
        meb_event.is_anon,
        meb_event.enable_upload,
        meb_event.event_file AS creator_file,
        meb_event.fk_event_creator AS creator_id,
        meb_user.email AS creator_email,
        CONCAT(meb_user.first_name, ' ', meb_user.last_name) AS creator_name
        FROM meb_event
        INNER JOIN meb_user ON meb_user.id = meb_event.fk_event_creator
        WHERE meb_event.hash = ?
        LIMIT 1

        ;";

        $bookings = $this->database->prepare($bookings_query);

        $bookings->bind_param("s", $hash);
        $bookings->execute();

        $result = $bookings->get_result();

        if ($result->num_rows > 0) {
            $list = $result->fetch_all(MYSQLI_ASSOC);
            $meeting = $list[0];
        } else {
            $meeting = null;
        }

        $result->free();
        $bookings->close();

        return $meeting;
    }

    /**
     * Get attendees of a meeting.
     *
     * @param id $id
     * @return array
     */
    public function getMeetingAttendees($id)
    {
        $bookings_query = "

        SELECT
        meb_event.id,
        meb_event.name,
        meb_event.location,
        meb_timeslot.start_time,
        meb_timeslot.end_time,
        meb_files.path AS attendee_file,
        meb_user.email AS attendee_email,
        CONCAT(meb_user.first_name, ' ', meb_user.last_name) AS attendee_name,
        meb_user.onid AS attendee_onid
        FROM meb_booking
        INNER JOIN meb_timeslot ON meb_timeslot.id = meb_booking.fk_timeslot_id
        INNER JOIN meb_event ON meb_event.id = meb_timeslot.fk_event_id
        INNER JOIN meb_user ON meb_user.id = meb_booking.fk_user_id
        LEFT OUTER JOIN meb_files ON meb_files.fk_booking_id = meb_booking.id
        WHERE meb_event.id = ?
        ORDER BY meb_timeslot.start_time ASC

        ;";

        $bookings = $this->database->prepare($bookings_query);
        $bookings->bind_param("i", $id);
        $bookings->execute();

        $result = $bookings->get_result();
        $list = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $bookings->close();

        return $list;
    }

    /**
    * Get invitations that the user does not
    * have a booking for
    *
    * @param string $userOnid
    * @return array of events
    */
    public function getInvites($userOnid)
    {
        $get_invite_query = "

        SELECT
        meb_event.id,
        meb_event.name,
        meb_event.description,
        meb_event.hash,
        meb_event.location,
        meb_user.id AS creator_id,
        meb_user.email AS creator_email,
        CONCAT(meb_user.first_name, ' ', meb_user.last_name) AS creator_name

        FROM meb_event
        INNER JOIN meb_user ON meb_user.id = meb_event.fk_event_creator
        INNER JOIN meb_invites ON meb_event.id = meb_invites.fk_event_id
        WHERE meb_invites.user_onid = ?
        AND fk_event_id NOT IN
          (SELECT fk_event_id FROM meb_timeslot
          INNER JOIN meb_booking ON meb_timeslot.id = meb_booking.fk_timeslot_id
          INNER JOIN meb_user ON meb_booking.fk_user_id = meb_user.id
          WHERE meb_user.onid = ?)
      ";

        $getList = $this->database->prepare($get_invite_query);
        $getList->bind_param("ss", $userOnid, $userOnid);
        $getList->execute();

        $result = $getList->get_result();
        $list = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $getList->close();

        return $list;
    }

    /**
     * Add new meeting.
     *
     * @param object $meeting
     * @return int;
     */
    public function addMeeting($user_id, $meeting)
    {
        $name = $meeting['name'];
        $location = $meeting['location'];
        $description = $meeting['description'];
        $event_file = $meeting['event_file'];
        $is_anon = $meeting['is_anon'];
        $enable_upload = $meeting['enable_upload'];
        $capacity = $meeting['capacity'];

        $hash = createEventHash($name, $description, $user_id, $location);

        $query = "

            INSERT INTO meb_event(
                hash,
                name,
                description,
                fk_event_creator,
                location,
                capacity,
                open_slots,
                is_anon,
                enable_upload,
                event_file
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)

        ";

        $statement = $this->database->prepare($query);

        $statement->bind_param(
            "sssisiiiis",
            $hash,
            $name,
            $description,
            $user_id,
            $location,
            $capacity,
            $capacity,
            $is_anon,
            $enable_upload,
            $event_file
        );

        $statement->execute();

        // $newEventID = $this->database->insert_id;
        // $this->addTimeSlots($slotData, $newEventID);

        $result = $statement->affected_rows;

        $statement->close();

        return $result;
    }

    /**
    * Adds time slots to database
    *
    * @param obj $slotData
    * @param id $eventID
    * @return int
    */
    private function addTimeSlots($slotData, $eventID)
    {
        $query = "
            INSERT INTO
            meb_timeslot(
              hash,
              start_time,
              end_time,
              duration,
              slot_capacity,
              spaces_available,
              is_full,
              fk_event_id
            )
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
}

$database = new DatabaseInterface();
$database->connectAsReadOnlyUser();
