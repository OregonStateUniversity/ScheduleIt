<?php

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

if (!defined('MEETINGS_START_TIME')) {
    define('MEETINGS_START_TIME', '07:00');
}

if (!defined('MEETINGS_END_TIME')) {
    define('MEETINGS_END_TIME', '20:00');
}

if (!defined('SITE_DIR')) {
    define('SITE_DIR', $_ENV['SITE_DIR']);
}

if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'Schedule-It');
}

if (!defined('SITE_URL')) {
    $protocol = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
    define('SITE_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . $_ENV['SITE_DIR']);
}

if (!defined('UPLOADS_ABSPATH')) {
    define('UPLOADS_ABSPATH', ABSPATH . 'uploads/');
}

if (!defined('UPLOADS_URL')) {
    define('UPLOADS_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . $_ENV['SITE_DIR'] . '/uploads/');
}

if (!defined('UPLOAD_ALLOWED_FILETYPES')) {
    define('UPLOAD_ALLOWED_FILETYPES', serialize(['txt', 'zip', 'pdf', 'docx', 'xlsx' ,'pptx']));
}

if (!defined('UPLOAD_SIZE_LIMIT')) {
    define('UPLOAD_SIZE_LIMIT', 5000000);
}
