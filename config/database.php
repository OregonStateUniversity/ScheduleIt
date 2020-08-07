<?php

require_once ABSPATH . 'config/env.php';

// include functions for generating hashes
require_once ABSPATH . 'lib/hash.php';

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
            meb_event.capacity,
            meb_event.mod_date
            FROM meb_event
            WHERE meb_event.fk_event_creator = ?
            AND (
                meb_event.name LIKE ?
                OR meb_event.location LIKE ?
            )
            ORDER BY meb_event.mod_date DESC

            ;";
            $events = $this->database->prepare($events_query);
            $partial_match = '%' . $search_term . '%';
            $events->bind_param("iss", $user_id, $partial_match, $partial_match);
        } else {
            $events_query = "

            SELECT
            meb_event.id,
            meb_event.hash,
            meb_event.name,
            meb_event.location,
            meb_event.open_slots,
            meb_event.capacity,
            meb_event.mod_date
            FROM meb_event
            WHERE meb_event.fk_event_creator = ?
            ORDER BY meb_event.mod_date DESC

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

        SELECT DISTINCT DATE_FORMAT(start_time, '%Y-%m-%d') AS date FROM meb_timeslot
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
     * Get timeslots for meeting.
     *
     * @param int $id
     * @return array
     */
    public function getTimeslotsByMeetingId($id)
    {
        $timeslots_query = "

        SELECT
        id,
        hash,
        duration,
        slot_capacity,
        start_time
        FROM meb_timeslot
        WHERE fk_event_id = ?

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
        DISTINCT(meb_timeslot.hash),
        meb_event.id,
        meb_event.hash AS meeting_hash,
        meb_event.name,
        meb_event.location,
        meb_event.fk_event_creator AS creator_id,
        meb_timeslot.start_time,
        meb_timeslot.end_time,
        meb_creator.email AS creator_email,
        CONCAT(meb_creator.first_name, ' ', meb_creator.last_name) AS creator_name,
        meb_creator.onid AS creator_onid
        FROM meb_timeslot
        INNER JOIN meb_event ON meb_event.id = meb_timeslot.fk_event_id
        INNER JOIN meb_user AS meb_creator ON meb_creator.id = meb_event.fk_event_creator
        INNER JOIN meb_booking ON meb_timeslot.id = meb_booking.fk_timeslot_id
        WHERE (meb_creator.id = ? OR meb_booking.fk_user_id = ?)
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
     * Get upcoming meetings created by user or where they're an attendee for the meetings page.
     *
     * @param int $user_id
     * @return array
     */
    public function getAllUpcomingMeetings($user_id)
    {
        $bookings_query = "

        SELECT
        DISTINCT(meb_timeslot.hash),
        meb_event.id,
        meb_event.hash AS meeting_hash,
        meb_event.name,
        meb_event.location,
        meb_event.description,
        meb_event.is_anon,
        meb_event.fk_event_creator AS creator_id,
        meb_timeslot.start_time,
        meb_timeslot.end_time,
        meb_user.email AS attendee_email,
        meb_files.path AS attendee_file,
        CONCAT(meb_user.first_name, ' ', meb_user.last_name) AS attendee_name,
        meb_user.onid AS attendee_onid,
        meb_event.event_file AS creator_file,
        meb_creator.email AS creator_email,
        CONCAT(meb_creator.first_name, ' ', meb_creator.last_name) AS creator_name,
        meb_creator.onid AS creator_onid
        FROM meb_timeslot
        INNER JOIN meb_event ON meb_event.id = meb_timeslot.fk_event_id
        INNER JOIN meb_user AS meb_creator ON meb_creator.id = meb_event.fk_event_creator
        INNER JOIN meb_booking ON meb_timeslot.id = meb_booking.fk_timeslot_id
        LEFT OUTER JOIN meb_user ON  meb_user.id = meb_booking.fk_user_id
        LEFT OUTER JOIN meb_files ON meb_files.fk_booking_id = meb_booking.id
        WHERE (meb_creator.id = ? OR ? IN (
            SELECT meb_booking2.fk_user_id
            FROM meb_timeslot AS meb_timeslot2
            INNER JOIN meb_booking AS meb_booking2 ON meb_booking2.fk_timeslot_id = meb_timeslot2.id
            INNER JOIN meb_event AS meb_event2 ON meb_event2.id = meb_timeslot2.fk_event_id
            WHERE meb_timeslot2.hash = meb_timeslot.hash
        ))
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
        DISTINCT(meb_timeslot.hash),
        meb_event.id,
        meb_event.hash AS meeting_hash,
        meb_event.name,
        meb_event.location,
        meb_event.description,
        meb_event.is_anon,
        meb_event.fk_event_creator AS creator_id,
        meb_timeslot.start_time,
        meb_timeslot.end_time,
        meb_files.path AS attendee_file,
        meb_user.email AS attendee_email,
        CONCAT(meb_user.first_name, ' ', meb_user.last_name) AS attendee_name,
        meb_user.onid AS attendee_onid,
        meb_event.event_file AS creator_file,
        meb_creator.email AS creator_email,
        CONCAT(meb_creator.first_name, ' ', meb_creator.last_name) AS creator_name,
        meb_creator.onid AS creator_onid
        FROM meb_timeslot
        INNER JOIN meb_event ON meb_event.id = meb_timeslot.fk_event_id
        INNER JOIN meb_user AS meb_creator ON meb_creator.id = meb_event.fk_event_creator
        INNER JOIN meb_booking ON meb_timeslot.id = meb_booking.fk_timeslot_id
        LEFT OUTER JOIN meb_user ON  meb_user.id = meb_booking.fk_user_id
        LEFT OUTER JOIN meb_files ON meb_files.fk_booking_id = meb_booking.id
        WHERE meb_creator.id = ?
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
        DISTINCT(meb_timeslot.hash),
        meb_event.id,
        meb_event.hash AS meeting_hash,
        meb_event.name,
        meb_event.location,
        meb_event.description,
        meb_event.is_anon,
        meb_event.fk_event_creator AS creator_id,
        meb_timeslot.start_time,
        meb_timeslot.end_time,
        meb_files.path AS attendee_file,
        meb_user.email AS attendee_email,
        CONCAT(meb_user.first_name, ' ', meb_user.last_name) AS attendee_name,
        meb_user.onid AS attendee_onid,
        meb_event.event_file AS creator_file,
        meb_creator.email AS creator_email,
        CONCAT(meb_creator.first_name, ' ', meb_creator.last_name) AS creator_name,
        meb_creator.onid AS creator_onid
        FROM meb_timeslot
        INNER JOIN meb_event ON meb_event.id = meb_timeslot.fk_event_id
        INNER JOIN meb_user AS meb_creator ON meb_creator.id = meb_event.fk_event_creator
        INNER JOIN meb_booking ON meb_timeslot.id = meb_booking.fk_timeslot_id
        LEFT OUTER JOIN meb_user ON  meb_user.id = meb_booking.fk_user_id
        LEFT OUTER JOIN meb_files ON meb_files.fk_booking_id = meb_booking.id
        WHERE (meb_creator.id = ? OR ? IN (
            SELECT meb_booking2.fk_user_id
            FROM meb_timeslot AS meb_timeslot2
            INNER JOIN meb_booking AS meb_booking2 ON meb_booking2.fk_timeslot_id = meb_timeslot2.id
            INNER JOIN meb_event AS meb_event2 ON meb_event2.id = meb_timeslot2.fk_event_id
            WHERE meb_timeslot2.hash = meb_timeslot.hash
        ))
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
        DISTINCT(meb_timeslot.hash),
        meb_event.id,
        meb_event.hash AS meeting_hash,
        meb_event.name,
        meb_event.location,
        meb_event.description,
        meb_event.fk_event_creator AS creator_id,
        meb_timeslot.start_time,
        meb_timeslot.end_time,
        meb_files.path AS attendee_file,
        meb_user.email AS attendee_email,
        CONCAT(meb_user.first_name, ' ', meb_user.last_name) AS attendee_name,
        meb_event.event_file AS creator_file,
        meb_creator.email AS creator_email,
        CONCAT(meb_creator.first_name, ' ', meb_creator.last_name) AS creator_name,
        meb_creator.onid AS creator_onid
        FROM meb_timeslot
        INNER JOIN meb_event ON meb_event.id = meb_timeslot.fk_event_id
        INNER JOIN meb_user AS meb_creator ON meb_creator.id = meb_event.fk_event_creator
        INNER JOIN meb_booking ON meb_timeslot.id = meb_booking.fk_timeslot_id
        LEFT OUTER JOIN meb_user ON  meb_user.id = meb_booking.fk_user_id
        LEFT OUTER JOIN meb_files ON meb_files.fk_booking_id = meb_booking.id
        WHERE (meb_user.id = ? OR meb_creator.id = ?)
        AND (
            meb_event.name LIKE ?
            OR meb_event.location LIKE ?
            OR CONCAT(meb_user.first_name, ' ', meb_user.last_name) LIKE ?
            OR CONCAT(meb_creator.first_name, ' ', meb_creator.last_name) LIKE ?
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
     * Get meeting by id for show and edit views.
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
        meb_event.fk_event_creator AS creator_id,
        meb_user.email AS creator_email,
        CONCAT(meb_user.first_name, ' ', meb_user.last_name) AS creator_name
        FROM meb_event
        INNER JOIN meb_user ON meb_user.id = meb_event.fk_event_creator
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
     * Get meeting attendees for show view.
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
        meb_event.hash,
        meb_event.location,
        meb_timeslot.start_time,
        meb_timeslot.end_time,
        meb_timeslot.hash AS timeslot_hash,
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
     * Add new timeslots when creating meeting.
     *
     * @param object $timeslots
     * @param int $meeting_id
     * @return int
     */
    private function addTimeslots($timeslots, $meeting_id, $duration, $capacity)
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
            $start_time,
            $end_time,
            $duration,
            $capacity,
            $spaces_available,
            $full,
            $meeting_id
        );

        $result = 0;
        $full = 0;

        foreach ($timeslots as $timeslot) {
            $start_time = $timeslot;
            $end_time = date('Y-m-d H:i:s', strtotime('+' . $duration . ' mins', strtotime($timeslot)));
            $spaces_available = $capacity;

            $hash = createTimeSlotHash($start_time, $end_time, $meeting_id);

            $statement->execute();
            $result += $statement->affected_rows;
        }

        $statement->close();

        return $result;
    }

    /**
     * Get timeslot attendees.
     *
     * @param string $timeslot_hash
     * @return array
     */
    public function getAttendeesByTimeslot($timeslot_hash)
    {
        $query = "

            SELECT
                meb_user.email AS attendee_email,
                CONCAT(meb_user.first_name, ' ', meb_user.last_name) AS attendee_name,
                meb_user.onid AS attendee_onid
            FROM meb_timeslot
            INNER JOIN meb_booking ON meb_booking.fk_timeslot_id = meb_timeslot.id
            INNER JOIN meb_event ON meb_event.id = meb_timeslot.fk_event_id
            INNER JOIN meb_user ON meb_booking.fk_user_id = meb_user.id
            WHERE meb_timeslot.hash = ?

        ";

        $statement = $this->database->prepare($query);

        $statement->bind_param("s", $timeslot_hash);
        $statement->execute();

        $result = $statement->get_result();

        $result_array = $result->fetch_all(MYSQLI_ASSOC);

        $statement->close();
        $result->free();

        return $result_array;
    }

    /**
     * Delete timeslot with stored procedure when editing meeting.
     *
     * @param string $meeting_hash
     * @param string $timeslot_hash
     * @return int
     */
    public function deleteTimeslot($meeting_hash, $timeslot_hash)
    {
        $meeting = $this->getMeetingByHash($meeting_hash);
        $meeting_mod_date = $meeting["mod_date"];

        $query = 'CALL meb_delete_slot(?, ?, ?, @res3)';

        $statement = $this->database->prepare($query);

        $statement->bind_param("sss", $meeting_mod_date, $meeting_hash, $timeslot_hash);
        $statement->execute();

        $query = "SELECT @res3";
        $result = $this->database->query($query);

        if ($result) {
            $result_array = $result->fetch_all(MYSQLI_NUM);
            $error_code = $result_array[0][0];
        } else {
            $error_code = -1;
        }

        $statement->close();
        $result->free();

        return $error_code;
    }

    /**
     * Add new timeslot with stored procedure when editing meeting.
     *
     * @param string $meeting_hash
     * @param object $timeslot
     * @return int
     */
    public function addTimeslot($meeting_hash, $timeslot)
    {
        $meeting = $this->getMeetingByHash($meeting_hash);
        $meeting_mod_date = $meeting["mod_date"];

        $query = 'CALL meb_add_slot(?, ?, ?, ?, ?, ?, ?, @res2)';

        $statement = $this->database->prepare($query);

        $start_time = $timeslot["start_time"];
        $end_time = $timeslot["end_time"];
        $duration = $timeslot["duration"];
        $capacity = $timeslot["capacity"];

        $timeslot_hash = createTimeSlotHash($start_time, $end_time, $meeting['id']);

        $statement->bind_param(
            'sssssii',
            $meeting_mod_date,
            $meeting_hash,
            $timeslot_hash,
            $start_time,
            $end_time,
            $duration,
            $capacity
        );

        $statement->execute();

        $query = "SELECT @res2";
        $result = $this->database->query($query);

        if ($result) {
            $result_array = $result->fetch_all(MYSQLI_NUM);
            $error_code = $result_array[0][0];
        } else {
            $error_code = -1;
        }

        $statement->close();
        $result->free();

        return $error_code;
    }

    /**
     * Add new meeting.
     *
     * @param int $user_id
     * @param object $meeting
     * @return int
     */
    public function addMeeting($user_id, $meeting)
    {
        $name = $meeting['name'];
        $location = $meeting['location'];
        $description = $meeting['description'];
        $is_anon = $meeting['is_anon'];
        $enable_upload = $meeting['enable_upload'];

        $hash = createEventHash($name, $description, $user_id, $location);
        $timeslots = $meeting['timeslots'];
        $duration = $meeting['duration'];
        $slot_capacity = $meeting['slot_capacity'];
        $capacity = $slot_capacity * count($timeslots);
        $open_slots = $capacity;

        $query = "

            INSERT INTO meb_event(
                hash,
                name,
                description,
                location,
                fk_event_creator,
                capacity,
                open_slots,
                is_anon,
                enable_upload
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)

        ";

        $statement = $this->database->prepare($query);

        $statement->bind_param(
            "ssssiiiii",
            $hash,
            $name,
            $description,
            $location,
            $user_id,
            $capacity,
            $open_slots,
            $is_anon,
            $enable_upload
        );

        $statement->execute();

        $new_event_id = $this->database->insert_id;
        $this->addTimeslots($timeslots, $new_event_id, $duration, $slot_capacity);

        $result = $new_event_id;

        $statement->close();

        return $result;
    }

    /**
     * Update meeting.
     *
     * @param int $user_id
     * @param object $meeting
     * @return int
     */
    public function updateMeeting($user_id, $meeting)
    {
        $id = $meeting['id'];
        $name = $meeting['name'];
        $location = $meeting['location'];
        $description = $meeting['description'];
        $is_anon = $meeting['is_anon'];
        $enable_upload = $meeting['enable_upload'];
        $capacity = $meeting['capacity'];

        $query = "

            UPDATE meb_event
            SET name = ?,
            location = ?,
            description = ?,
            is_anon = ?,
            enable_upload = ?,
            capacity = ?
            WHERE id = ?
            AND fk_event_creator = ?

        ";

        $statement = $this->database->prepare($query);

        $statement->bind_param(
            "sssiiiii",
            $name,
            $location,
            $description,
            $is_anon,
            $enable_upload,
            $capacity,
            $id,
            $user_id
        );

        $statement->execute();

        $result = $statement->affected_rows;

        $statement->close();

        return $result;
    }

    /**
     * Get meeting by hash for invite page.
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
        meb_event.mod_date,
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
     * Get booking and timeslot info for attendee on invite page.
     *
     * @param int $user_id
     * @param string $date
     * @return mixed
     */
    public function getMeetingForUserId($user_id, $hash)
    {
        $bookings_query = "

        SELECT
        meb_booking.id,
        meb_booking.fk_timeslot_id AS timeslot_id,
        meb_event.hash AS meeting_hash,
        meb_event.name,
        meb_event.location,
        meb_timeslot.hash AS timeslot_hash,
        meb_timeslot.start_time,
        meb_timeslot.end_time,
        meb_files.path AS attendee_file,
        meb_user.email AS attendee_email,
        CONCAT(meb_user.first_name, ' ', meb_user.last_name) AS attendee_name,
        meb_creator.email AS creator_email,
        CONCAT(meb_creator.first_name, ' ', meb_creator.last_name) AS creator_name
        FROM meb_booking
        INNER JOIN meb_timeslot ON meb_timeslot.id = meb_booking.fk_timeslot_id
        INNER JOIN meb_event ON meb_event.id = meb_timeslot.fk_event_id
        INNER JOIN meb_user ON meb_user.id = meb_booking.fk_user_id
        INNER JOIN meb_user AS meb_creator ON meb_creator.id = meb_event.fk_event_creator
        LEFT OUTER JOIN meb_files ON meb_files.fk_booking_id = meb_booking.id
        WHERE meb_booking.fk_user_id = ?
        AND meb_event.hash = ?
        LIMIT 1

        ;";

        $bookings = $this->database->prepare($bookings_query);
        $bookings->bind_param("is", $user_id, $hash);
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
     * Get aggregated available dates for invite page.
     *
     * @param string $hash
     * @return array
     */
    public function getAvailableDates($hash)
    {
        $timeslots_query = "

        SELECT DISTINCT DATE_FORMAT(start_time, '%Y-%m-%d') AS date
        FROM meb_timeslot
        INNER JOIN meb_event ON meb_event.id = meb_timeslot.fk_event_id
        WHERE meb_event.hash = ?
        AND meb_timeslot.is_full = false
        ORDER BY date ASC

        ;";

        $timeslots = $this->database->prepare($timeslots_query);
        $timeslots->bind_param("s", $hash);
        $timeslots->execute();

        $result = $timeslots->get_result();
        $list = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $timeslots->close();

        return $list;
    }

    /**
     * Get available timeslots for invite page.
     *
     * @param string $hash
     * @param string $date
     * @return array
     */
    public function getAvailableTimeslots($hash, $date)
    {
        $timeslots_query = "

        SELECT
        meb_timeslot.id,
        meb_timeslot.start_time,
        meb_timeslot.end_time
        FROM meb_timeslot
        INNER JOIN meb_event ON meb_event.id = meb_timeslot.fk_event_id
        WHERE meb_event.hash = ?
        AND DATE_FORMAT(meb_timeslot.start_time, '%Y-%m-%d') = ?
        AND meb_timeslot.is_full = false
        ORDER BY meb_timeslot.start_time ASC

        ;";

        $timeslots = $this->database->prepare($timeslots_query);
        $timeslots->bind_param("ss", $hash, $date);
        $timeslots->execute();

        $result = $timeslots->get_result();
        $list = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $timeslots->close();

        return $list;
    }

    /**
     * Add booking.
     *
     * @param id $user_id
     * @param id $timeslot_id
     * @return int
     */
    public function addBooking($user_id, $timeslot_id)
    {
        $query = "CALL meb_reserve_slot(?, ?, @res1)";

        $statement = $this->database->prepare($query);
        $statement->bind_param("ii", $timeslot_id, $user_id);
        $statement->execute();

        $query = "SELECT @res1";
        $result = $this->database->query($query);

        if ($result) {
            $resultArray = $result->fetch_all(MYSQLI_NUM);
            $errorCode = $resultArray[0][0];
        } else {
            $errorCode = -1;
        }

        $result->free();
        $statement->close();

        return $errorCode;
    }

    /**
     * Delete booking.
     *
     * @param string $onid
     * @param string $timeslot_hash
     * @return array
     */
    public function deleteBooking($onid, $timeslot_hash)
    {
        $query = "CALL meb_delete_reservation(?, ?, @res1)";

        $statement = $this->database->prepare($query);
        $statement->bind_param("ss", $timeslot_hash, $onid);
        $statement->execute();

        $query = "SELECT @res1";
        $result = $this->database->query($query);

        if ($result) {
            $resultArray = $result->fetch_all(MYSQLI_NUM);
            $errorCode = $resultArray[0];
        } else {
            $errorCode = -1;
        }

        $result->free();
        $statement->close();

        return $errorCode;
    }

    /**
     * Get invitations that the user does not have a booking for.
     *
     * @param string $onid
     * @return array of events
     */
    public function getInvites($onid)
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
        $getList->bind_param("ss", $onid, $onid);
        $getList->execute();

        $result = $getList->get_result();
        $list = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $getList->close();

        return $list;
    }

    /**
     * Delete invite.
     *
     * @param string $onid
     * @param int $event_id
     * @return int
     */
    public function deleteInvite($onid, $event_id)
    {
        $query = "

        DELETE
        FROM meb_invites
        WHERE user_onid = ?
        AND fk_event_id = ?

        ;";

        $statement = $this->database->prepare($query);
        $statement->bind_param("si", $onid, $event_id);
        $statement->execute();

        $result = $statement->affected_rows;

        $statement->close();

        return $result;
    }


    /**
     * Get attendee uploaded file.
     *
     * @param int $booking_id
     * @return mixed
     */
    public function getFile($booking_id)
    {
        $query = "

            SELECT id, path, fk_booking_id AS 'bookingID'
            FROM meb_files
            WHERE fk_booking_id = ?

        ";

        $statement = $this->database->prepare($query);

        $statement->bind_param("i", $booking_id);
        $statement->execute();

        $result = $statement->get_result();

        if ($result->num_rows > 0) {
            $resultArray = $result->fetch_all(MYSQLI_ASSOC);
            $file = $resultArray[0];
        } else {
            $file = null;
        }

        $statement->close();
        $result->free();

        return $file;
    }

    /**
     * Update attendee uploaded file.
     *
     * @param string $file_path
     * @param int $file_id
     * @return int
     */
    public function replaceFilePath($file_path, $file_id)
    {
        $query = "

            UPDATE meb_files
            SET path = ? WHERE id = ?

        ";

        $statement = $this->database->prepare($query);
        $statement->bind_param("si", $file_path, $file_id);
        $statement->execute();

        $result = $statement->affected_rows;
        $statement->close();

        return $result;
    }

    /**
     * Add attendee uploaded file.
     *
     * @param string $file_path
     * @param int $booking_id
     * @return int
     */
    public function addFile($file_path, $booking_id)
    {
        // If there exists file associated with booking, replace path for that file
        $file = $this->getFile($booking_id);

        if ($file) {
            $result = $this->replaceFilePath($file_path, $file['id']);
            return 1;
        }

        // Add file path associated with booking
        $query = "

            INSERT INTO meb_files(path, fk_booking_id)
            VALUES (?, ?)

        ";

        $statement = $this->database->prepare($query);
        $statement->bind_param("si", $file_path, $booking_id);
        $statement->execute();

        $result = $statement->affected_rows;
        $statement->close();

        return $result;
    }

    /**
     * Add creator uploaded file.
     *
     * @param string $file_path
     * @param string $meeting_hash
     * @return array
     */
    public function addEventFile($file_path, $meeting_hash)
    {
        $queryClearFile = "

           UPDATE `meb_event`
           SET event_file = null
           WHERE hash = ?;
       ";

        $statementClearFile = $this->database->prepare($queryClearFile);
        $statementClearFile->bind_param("s", $meeting_hash);
        $statementClearFile->execute();

        $statementClearFile->close();

        $queryUpdateFile = "

           UPDATE `meb_event`
           SET event_file = ?
           WHERE hash = ?;
       ";

        $statementUpdateFile = $this->database->prepare($queryUpdateFile);

        $statementUpdateFile->bind_param("ss", $file_path, $meeting_hash);
        $statementUpdateFile->execute();

        $result = $statementUpdateFile->affected_rows;

        $statementUpdateFile->close();

        return $result;
    }

    /**
     * Get list of onids that have not registered
     * for an event.
     *
     * @param object $eventId
     * @return string array
     */
    public function getNotRegistered($eventId)
    {

        $not_reg_query = "

       SELECT user_onid FROM meb_invites
       WHERE fk_event_id = ? AND user_onid NOT IN
         (SELECT DISTINCT user_onid FROM meb_invites
         INNER JOIN meb_user ON meb_user.onid = meb_invites.user_onid
         INNER JOIN meb_booking ON meb_booking.fk_user_id = meb_user.id
         INNER JOIN meb_timeslot ON meb_booking.fk_timeslot_id = meb_timeslot.id
         INNER JOIN meb_event ON meb_event.id = meb_timeslot.fk_event_id
         where meb_timeslot.fk_event_id = ?)
       ";

        $getList = $this->database->prepare($not_reg_query);

        $getList->bind_param("ii", $eventId, $eventId);
        $getList->execute();

        $list = $getList->get_result();

        $listArray = $list->fetch_all(MYSQLI_ASSOC);

        $getList->close();
        $list->free();
        return $listArray;
    }

     /**
     * add onid to list of inivted onids for an event
     *
     *
     * @param string $onid, int $eventId
     * @return none
     */
    public function insertInviteList($onid, $eventId)
    {
        $insert_meb_invites_query = "
         INSERT `meb_invites` (fk_event_id, user_onid)
         VALUES (?,?);
       ";

        $insert = $this->database->prepare($insert_meb_invites_query);

        $insert->bind_param("is", $eventId, $onid);
        $insert->execute();

        $insert->close();
    }

    /**
     * delete a booking
     *
     *
     * @param string $startTime, int $eventId, string $onid
     * @return int result
     */
    public function deleteBookingold($startTime, $eventId, $onid)
    {
        $delete_booking_query = "
        DELETE FROM meb_booking
        WHERE meb_booking.fk_timeslot_id IN
         (SELECT meb_timeslot.id as slotId FROM `meb_timeslot`
          INNER JOIN `meb_event` ON meb_timeslot.fk_event_id = meb_event.id
          WHERE meb_timeslot.start_time = ?
          AND meb_event.id = ?)
        AND meb_booking.fk_user_id IN
         (SELECT id FROM meb_user
          WHERE meb_user.onid = ?)
      ;";

        $delete = $this->database->prepare($delete_booking_query);

        $delete->bind_param("sis", $startTime, $eventId, $onid);
        $delete->execute();

        $result = $delete->affected_rows;
        $delete->close();

        return $result;
    }

    /**
     * get a slot id
     *
     *
     * @param string $startTime, int $eventId
     * @return int $id
     */
    public function getSlotHash($startTime, $eventId)
    {
        $hash_query = "
        SELECT meb_timeslot.hash FROM `meb_timeslot`
        INNER JOIN `meb_event` ON meb_timeslot.fk_event_id = meb_event.id
        WHERE meb_timeslot.start_time = ?
        AND meb_event.id = ?
      ;";

        $slotHash = $this->database->prepare($hash_query);

        $slotHash->bind_param("si", $startTime, $eventId);
        $slotHash->execute();

        $result = $slotHash->get_result();
        if ($result->num_rows > 0) {
            $resultArray = $result->fetch_all(MYSQLI_ASSOC);
            $hash = $resultArray[0];
        }

        $slotHash->close();
        $result->free();
        return $hash;
    }

    /**
     * Delete a meeting based on its hash value
     *
     * @param string $meetingHash
     * @return int $result
     */
    public function deleteMeeting($meetingHash)
    {
        $query = "DELETE FROM meb_event WHERE hash = ?;";

        $statement = $this->database->prepare($query);

        $statement->bind_param("s", $meetingHash);
        $statement->execute();

        $result = $statement->affected_rows;

        $statement->close();

        return $result;
    }
}

$database = new DatabaseInterface();
$database->connectAsReadOnlyUser();
