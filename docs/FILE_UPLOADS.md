# File Uploads

Users are allowed to upload files as meeting creators and attendees. These files are saved to the `uploads` directory. You must already have this directory existing in the root of this application.

## Troubleshooting

If files are not saving to `uploads`, one of these reasons may be the cause.

### Files Too Large

In `constants.inc.php`, we specify `UPLOAD_SIZE_LIMIT` in bytes. However, this value cannot be larger than the limits set on your server. Your limits can be found in your server's `php.ini` under [post_max_size](https://www.php.net/manual/en/ini.core.php#ini.post-max-size) and [upload_max_filesize](https://www.php.net/manual/en/ini.core.php#ini.upload-max-filesize).

### Files Uploading as Apache User

If files uploads are not saving, it may be that your current user doesn't have permission to write to the `uploads` directory. Depending on your server settings, your server may upload files as the owner of the directory (the user used to install the application), or as the `apache` user. To work around this, you either need to change the owner of the `uploads` directory to the `apache` user or change Apache to run as the current user. Both changes require `sudo` access.

If you do not have `sudo` access, a non-optimal workaround is to change the permissions of `uploads` to 0777. However, this is a security risk.
