<?php

require_once ABSPATH . 'scheduleit/config/database.php';

/**
 * FileUpload
 * php version 7.2.28
 */
class FileUpload
{
    /**
     * @var object
     */
    private $file_upload;

    /**
     * Set up interface.
     *
     * @param object $database
     */
    public function __construct($database)
    {
        $this->db = $database;
    }

    /**
     * Upload file to server and update booking record.
     *
     * @param string $onid
     * @param string $meeting_hash
     * @param int $booking_id
     * @param string $field_name
     * @return object
     */
    public function upload($onid, $meeting_hash, $booking_id, $field_name = 'file')
    {
        // Check file size ** final size TBD **
        if ($_SERVER['CONTENT_LENGTH'] > UPLOAD_SIZE_LIMIT) {
            return [
                'error' => true,
                'message' => 'File upload is too large.'
            ];
        }

        $uploads_abspath = UPLOADS_ABSPATH;
        $uploaded_file_dir = $uploads_abspath . $meeting_hash;

        $uploaded_filename = $_FILES[$field_name]['name'];
        $ext = pathinfo($uploaded_filename, PATHINFO_EXTENSION);

        $renamed_filename = $onid . '_upload' . '.' . $ext;
        $new_file_abspath = $uploaded_file_dir . '/' . $renamed_filename;

        // TODO: Use SITE_DIR when we remove old pages
        $url = '../uploads/' . $meeting_hash . '/' . $renamed_filename;

        // To enable more file types, just add extensions to schedule.config.php
        $allowed_extensions = unserialize(UPLOAD_ALLOWED_FILETYPES);
        $is_allowed = in_array($ext, $allowed_extensions);

        if (!$is_allowed) {
            return [
                'error' => true,
                'message' => 'This file type is not allowed. Accepted file types: ' . implode(', ', $allowed_extensions)
            ];
        }

        // If directory for event's files doesn't exist, create it
        if (!file_exists($uploaded_file_dir)) {
            mkdir($uploaded_file_dir, 0755, true);
        }

        // If there is error with file upload, don't add path to database
        if ($_FILES[$field_name]['error'] > 0) {
            return [
                'error' => true,
                'message' => 'Error: ' . $_FILES[$field_name]['error']
            ];
        }

        move_uploaded_file($_FILES[$field_name]['tmp_name'], $new_file_abspath);
        chmod($new_file_abspath, 0644);

        // If there is no error with file upload, add path to database
        if ($booking_id) {
            $result = $this->db->addFile($url, $booking_id);
        } else {
            $result = $this->db->addEventFile($url, $meeting_hash);
        }

        if ($result > 0) {
            shell_exec('chmod 755 ' . UPLOADS_ABSPATH);
            shell_exec('chmod -R 644 ' . UPLOADS_ABSPATH . $meeting_hash);
            shell_exec('chmod 755 ' . UPLOADS_ABSPATH . $meeting_hash);
            return [
                'message' => 'Your file has been uploaded.'
            ];
        } else {
            // Remove file on database failure
            unlink($new_file_abspath);

            return [
                'error' => true,
                'message' => 'Your file could not be uploaded.'
            ];
        }
    }
}

$file_upload = new FileUpload($database);
