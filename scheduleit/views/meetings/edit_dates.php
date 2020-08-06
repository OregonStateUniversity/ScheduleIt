<?php

require_once ABSPATH . 'config/session.php';
require_once ABSPATH . 'scheduleit/lib/send_email.php';

$meeting = $database->getMeetingById($meeting_id, $_SESSION['user_onid']);

if ($meeting) {
    $dates = [];
    $dates_saved = [];
    $date_objects =  $database->getDatesByMeetingId($meeting_id);
    $timeslots = $database->getTimeslotsByMeetingId($meeting_id);
    $meeting['duration'] = count($timeslots) > 0 ? $timeslots[0]['duration'] : 60;
    $meeting['slot_capacity'] = count($timeslots) > 0 ? $timeslots[0]['slot_capacity'] : 1;
    $timeslot_times = [];
    $timeslot_hashes = [];
    $timeslot_times_saved = [];

    foreach ($timeslots as $key => $timeslot) {
        array_push($timeslot_times, $timeslot['start_time']);
        array_push($timeslot_times_saved, $timeslot['start_time']);
        $timeslot_hashes[$timeslot['start_time']] = $timeslot['hash'];
    }

    foreach ($date_objects as $key => $date) {
        array_push($dates, $date['date']);
        array_push($dates_saved, $date['date']);
    }

    // Create time labels
    $time_labels = [];

    $start_time = strtotime(MEETINGS_START_TIME);
    $end_time = strtotime(MEETINGS_END_TIME);

    $current = time();
    $add_time = strtotime('+' . $meeting['duration'] . ' mins', $current);
    $diff = $add_time - $current;

    while ($start_time < $end_time) {
        array_push($time_labels, date('H:i:s', $start_time));
        $start_time += $diff;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $slot_capacity = !empty($_POST['slot_capacity']) ? intval($_POST['slot_capacity']) : 1;
        $duration = !empty($_POST['duration']) ? intval($_POST['duration']) : 60;
        $updated_timeslots = !empty($_POST['timeslots']) ? $_POST['timeslots'] : [];
        $deleted_timeslots = [];
        $current_timeslots = [];
        $new_timeslots = [];

        // Same duration
        if ($duration == $meeting['duration']) {
            foreach ($timeslot_times_saved as $key => $timeslot) {
                if (in_array($timeslot, $updated_timeslots)) {
                    array_push($current_timeslots, $timeslot);
                } else {
                    array_push($deleted_timeslots, $timeslot);
                }
            }

            foreach ($updated_timeslots as $key => $timeslot) {
                if (!in_array($timeslot, $current_timeslots)) {
                    array_push($new_timeslots, $timeslot);
                }
            }
        } else {
            // Duration changed, delete all current timeslots and create all new ones
            $deleted_timeslots = $timeslot_times_saved;
            $new_timeslots = $updated_timeslots;
        }

        // Initialize error codes
        $insert_success = true;
        $delete_success = true;

        // Remove timeslots
        foreach ($deleted_timeslots as $key => $timeslot) {
            $timeslot_hash = $timeslot_hashes[$timeslot];
            $removed_users = $database->getAttendeesByTimeslot($timeslot_hash);
            $error_code = $database->deleteTimeslot($meeting['hash'], $timeslot_hash);

            if ($error_code != 0) {
                $delete_success = false;
            } else {
                // Notify users of removed timeslots
                foreach ($removed_users as $key => $user) {
                    $send_email->changedTimeslots($meeting, $user);
                }
            }
        }

        // Create timeslots
        foreach ($new_timeslots as $key => $timeslot) {
            $new_timeslot['duration'] = $duration;
            $new_timeslot['capacity'] = $slot_capacity;
            $new_timeslot['start_time'] = $timeslot;
            $new_timeslot['end_time'] = date('Y-m-d H:i:s', strtotime('+' . $duration . ' mins', strtotime($timeslot)));
            $error_code = $database->addTimeslot($meeting['hash'], $new_timeslot);

            if ($error_code != 0) {
                $insert_success = false;
            }
        }

        if ($insert_success && $delete_success) {
            $msg->success('"' . $meeting['name'] . '" has been updated.', SITE_DIR . '/meetings/' . $meeting_id);
        } else {
            $msg->error('Meeting dates could not be updated.');
        }
    }

    echo $twig->render('meetings/edit_dates.twig', [
        'dates' => $dates,
        'dates_saved' => $dates_saved,
        'dates_json' => json_encode($dates),
        'dates_saved_json' => json_encode($dates_saved),
        'edit_dates' => true,
        'meeting' => $meeting,
        'meetings_end_time' => MEETINGS_END_TIME,
        'meetings_start_time' => MEETINGS_START_TIME,
        'time_labels' => $time_labels,
        'timeslot_times' => $timeslot_times,
        'timeslot_times_saved' => $timeslot_times_saved,
        'title' => 'Edit Meeting Dates - ' . $meeting['name'],
    ]);
} else {
    http_response_code(404);
    echo $twig->render('errors/error_logged_in.twig', [
        'message' => 'Sorry, we couldn\'t find that meeting.',
        'title' => 'Meeting Not Found',
    ]);
}
