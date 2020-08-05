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
        $subject = '[Confirmation]: ' . $meeting['name'];
        $headers = 'From: ' . SITE_NAME . ' <no-reply@oregonstate.edu>' . "\r\n" .
            'Reply-To: ' . $meeting['creator_name'] . '<' . $meeting['creator_email'] . '>' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        $message = 'Hi ' . $meeting['attendee_name'] . ',' . "\r\n\r\n";
        $message .= 'You have reserved a timeslot for ' . $meeting['name'] . '.' . "\r\n\r\n";
        $message .= 'Date: ' . date('D, F j, Y g:ia', strtotime($meeting['start_time'])) . '-' . date('g:ia', strtotime($meeting['end_time'])) . "\r\n";
        $message .= 'Location: ' . $meeting['location'] . "\r\n";
        $message .= 'Creator: ' . $meeting['creator_name'] . "\r\n\r\n";
        $message .= 'Meeting Info: ' . SITE_URL . '/invite?key=' . $meeting['meeting_hash'] . "\r\n";

        mail($to, $subject, $message, $headers);
    }

    /**
     * Send invite email.
     *
     * @param string $creatorOnid
     * @param string $inviteOnid
     * @param string $eventName
     * @param string $creatorName
     * @param string $link
     * @return void
     */
    public function invitation($creatorOnid, $inviteOnid, $eventName, $creatorName, $link)
    {
        $to = $inviteOnid . '@oregonstate.edu';
        $subject = '[Invitation]: ' . $eventName;
        $headers = 'From: ' . SITE_NAME . ' <no-reply@oregonstate.edu>' . "\r\n" .
          'Reply-To: ' . $creatorName . '<' . $creatorOnid . '@oregonstate.edu' . '>' . "\r\n" .
          'X-MAiler: PHP/' . phpversion();

        $message = 'Hi ' . $inviteOnid . ', ' . "\r\n\r\n";
        $message .= $creatorName . ' has invited you to the "' . $eventName . '" meeting.' . "\r\n";
        $message .= 'Please follow the link below to reserve a spot.' . "\r\n\r\n";
        $message .= $link . "\r\n";

        mail($to, $subject, $message, $headers);
    }

    /**
     * Send removed attendee email.
     *
     * @param string $removeOnid
     * @param string $creatorName
     * @param string $creatorOnid
     * @param string $eventName
     * @return void
     */
    public function notifyRemovedAttendee($removeOnid, $creatorName, $creatorOnid, $eventName)
    {
        $to = $removeOnid . '@oregonstate.edu';
        $subject = '[Removed]: ' . trim($eventName);
        $headers = 'From: ' . SITE_NAME . ' <no-reply@oregonstate.edu>' . "\r\n" .
          'Reply-To: ' . $creatorName . '<' . $creatorOnid . '@oregonstate.edu' . '>' . "\r\n" .
          'X-MAiler: PHP/' . phpversion();

        $message = 'Hi ' . $removeOnid . ', ' . "\r\n";
        $message .= $creatorName . ' has removed you from the "' . trim($eventName) . '" meeting.' . "\r\n";
        $message .= 'If you have any question please contact them at ' . "\r\n";
        $message .= $creatorOnid . '@oregonstate.edu' . "\r\n";

        mail($to, $subject, $message, $headers);
    }

    /**
     * Send timeslots changed email.
     *
     * @param object $meeting
     * @return void
     */
    public function changedTimeslots($meeting, $user)
    {
        $to = $user['email'];
        $subject = '[Changed]: ' . $meeting['name'];
        $headers = 'From: ' . SITE_NAME . ' <no-reply@oregonstate.edu>' . "\r\n" .
            'Reply-To: ' . $meeting['creator_name'] . '<' . $meeting['creator_email'] . '>' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        $message = 'Hi ' . $user['name'] . ',' . "\r\n\r\n";
        $message .= 'The available timeslots for ' . $meeting['name'] . ' have changed and your reservation is no longer available. Please sign up for a new timeslot.' . "\r\n\r\n";
        $message .= 'Sign Up: ' . SITE_URL . '/invite?key=' . $meeting['meeting_hash'] . "\r\n";

        mail($to, $subject, $message, $headers);
    }
}

$send_email = new SendEmail();
