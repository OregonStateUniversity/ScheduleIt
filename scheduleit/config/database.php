<?php

require_once ABSPATH . 'config/env.php';

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

            SELECT onid, email, last_name, first_name
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
     * Get meetings created by user for the maange page.
     *
     * @param string $onid
     * @param string $search_term
     * @return array
     */
    public function getManageMeetings($onid, $search_term)
    {
        if ($search_term) {
            $events_query = "

            SELECT
            meb_event.id,
            meb_event.name,
            meb_event.location,
            meb_event.open_slots,
            meb_event.capacity
            FROM meb_event
            INNER JOIN meb_user ON meb_user.id = meb_event.fk_event_creator
            WHERE meb_user.onid = ?
            AND meb_event.name LIKE ?
            ORDER BY meb_event.name ASC

            ;";
            $events = $this->database->prepare($events_query);
            $partial_match = '%' . $search_term . '%';
            $events->bind_param("ss", $onid, $partial_match);
        } else {
            $events_query = "

            SELECT
            meb_event.id,
            meb_event.name,
            meb_event.location,
            meb_event.open_slots,
            meb_event.capacity
            FROM meb_event
            INNER JOIN meb_user ON meb_user.id = meb_event.fk_event_creator
            WHERE meb_user.onid = ?
            ORDER BY meb_event.name ASC

            ;";
            $events = $this->database->prepare($events_query);
            $events->bind_param("s", $onid);
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
     * @param string $onid
     * @return array
     */
    public function getCalendarMeetings($onid)
    {
        $bookings_query = "

        SELECT
        meb_event.id,
        meb_event.name,
        meb_event.location,
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
        WHERE (meb_user.onid = ? OR meb_user2.onid = ?)
        ORDER BY meb_timeslot.start_time DESC

        ;";

        $bookings = $this->database->prepare($bookings_query);
        $bookings->bind_param("ss", $onid, $onid);
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
     * @param string $onid
     * @return array
     */
    public function getAllUpcomingMeetings($onid)
    {
        $bookings_query = "

        SELECT
        meb_event.id,
        meb_event.name,
        meb_event.location,
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
        WHERE (meb_user.onid = ? OR meb_user2.onid = ?)
        AND meb_timeslot.start_time > now()
        ORDER BY meb_timeslot.start_time ASC

        ;";

        $bookings = $this->database->prepare($bookings_query);
        $bookings->bind_param("ss", $onid, $onid);
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
     * @param string $onid
     * @return array
     */
    public function getUpcomingMeetingsByCreator($onid)
    {
        $bookings_query = "

        SELECT
        meb_event.id,
        meb_event.name,
        meb_event.location,
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
        WHERE meb_user2.onid = ?
        AND meb_timeslot.start_time > now()
        ORDER BY meb_timeslot.start_time ASC

        ;";

        $bookings = $this->database->prepare($bookings_query);
        $bookings->bind_param("s", $onid);
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
     * @param string $onid
     * @return array
     */
    public function getPastMeetings($onid)
    {
        $bookings_query = "

        SELECT
        meb_event.id,
        meb_event.name,
        meb_event.location,
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
        WHERE (meb_user.onid = ? OR meb_user2.onid = ?)
        AND meb_timeslot.start_time < now()
        ORDER BY meb_timeslot.start_time DESC

        ;";

        $bookings = $this->database->prepare($bookings_query);

        $bookings->bind_param("ss", $onid, $onid);
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
     * @param string $onid
     * @param string $search_term
     * @return array
     */
    public function getMeetingsBySearchTerm($onid, $search_term)
    {
        $bookings_query = "

        SELECT
        meb_event.id,
        meb_event.name,
        meb_event.location,
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
        WHERE (meb_user.onid = ? OR meb_user2.onid = ?)
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
        $bookings->bind_param("ssssss", $onid, $onid, $partial_match, $partial_match, $partial_match, $partial_match);
        $bookings->execute();

        $result = $bookings->get_result();
        $list = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $bookings->close();

        return $list;
    }

    /**
     * Get meeting by id and ONID to prevent unauthorized access.
     *
     * @param id $id
     * @param string $onid
     * @return mixed
     */
    public function getMeetingById($id, $onid)
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
        meb_timeslot.start_time,
        meb_timeslot.end_time,
        meb_files.path AS attendee_file,
        meb_user.email AS attendee_email,
        CONCAT(meb_user.first_name, ' ', meb_user.last_name) AS attendee_name,
        meb_user.onid AS attendee_onid,
        meb_user2.email AS creator_email,
        CONCAT(meb_user2.first_name, ' ', meb_user2.last_name) AS creator_name,
        meb_user2.onid AS creator_onid
        FROM meb_booking
        INNER JOIN meb_timeslot ON meb_timeslot.id = meb_booking.fk_timeslot_id
        INNER JOIN meb_event ON meb_event.id = meb_timeslot.fk_event_id
        INNER JOIN meb_files ON meb_files.fk_booking_id = meb_booking.id
        INNER JOIN meb_user ON meb_user.id = meb_booking.fk_user_id
        INNER JOIN meb_user AS meb_user2 ON meb_user2.id = meb_event.fk_event_creator
        WHERE (meb_user.onid = ? OR meb_user2.onid = ?)
        AND meb_event.id = ?
        LIMIT 1

        ;";

        $bookings = $this->database->prepare($bookings_query);

        $bookings->bind_param("ssi", $onid, $onid, $id);
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
        meb_timeslot.start_time,
        meb_timeslot.end_time,
        meb_files.path AS attendee_file,
        meb_user.email AS attendee_email,
        CONCAT(meb_user.first_name, ' ', meb_user.last_name) AS attendee_name,
        meb_user.onid AS attendee_onid,
        meb_user2.email AS creator_email,
        CONCAT(meb_user2.first_name, ' ', meb_user2.last_name) AS creator_name,
        meb_user2.onid AS creator_onid
        FROM meb_booking
        INNER JOIN meb_timeslot ON meb_timeslot.id = meb_booking.fk_timeslot_id
        INNER JOIN meb_event ON meb_event.id = meb_timeslot.fk_event_id
        INNER JOIN meb_files ON meb_files.fk_booking_id = meb_booking.id
        INNER JOIN meb_user ON meb_user.id = meb_booking.fk_user_id
        INNER JOIN meb_user AS meb_user2 ON meb_user2.id = meb_event.fk_event_creator
        WHERE meb_event.hash = ?
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
        meb_user.onid AS attendee_onid,
        meb_user2.email AS creator_email,
        CONCAT(meb_user2.first_name, ' ', meb_user2.last_name) AS creator_name,
        meb_user2.onid AS creator_onid
        FROM meb_booking
        INNER JOIN meb_timeslot ON meb_timeslot.id = meb_booking.fk_timeslot_id
        INNER JOIN meb_event ON meb_event.id = meb_timeslot.fk_event_id
        INNER JOIN meb_files ON meb_files.fk_booking_id = meb_booking.id
        INNER JOIN meb_user ON meb_user.id = meb_booking.fk_user_id
        INNER JOIN meb_user AS meb_user2 ON meb_user2.id = meb_event.fk_event_creator
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
}

$database = new DatabaseInterface();
$database->connectAsReadOnlyUser();
