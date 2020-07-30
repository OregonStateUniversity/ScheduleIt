<?php

/**
 * SendEmail
 * php version 7.2.28
 */
class SendEmail
{
    /**
     * @var object
     */
    private $send_email;

    /**
     * Send invite confirmation email.
     *
     * @param object $meeting
     * @return void
     */
    public function inviteConfirmation($meeting)
    {
        $to = $meeting['attendee_email'];
        $subject = $meeting['name'] . ' timeslot confirmation';
        $headers = 'From: ' . SITE_NAME . ' <no-reply@oregonstate.edu>' . "\r\n" .
            'Reply-To: ' . $meeting['creator_name'] . '<' . $meeting['creator_email'] . '>' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        $message = 'Hi ' . $meeting['attendee_name'] . ',' . "\r\n\r\n";
        $message .= 'You have reserved a timeslot for ' . $meeting['name'] . '.' . "\r\n\r\n";
        $message .= 'Date: ' . date('D, F j, Y g:ia', strtotime($meeting['start_time'])) . '-' . date('g:ia', strtotime($meeting['end_time'])) . "\r\n";
        $message .= 'Location: ' . $meeting['location'] . "\r\n";
        $message .= 'Creator: ' . $meeting['creator_name'] . "\r\n\r\n";
        $message .= 'Meeting Info: ' . $_SERVER['HTTP_ORIGIN'] . SITE_DIR . '/invite?key=' . $meeting['event_hash'] . "\r\n";

        mail($to, $subject, $message, $headers);
    }
}

$send_email = new SendEmail();
