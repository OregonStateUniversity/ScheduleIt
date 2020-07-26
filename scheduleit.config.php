<?php

if (!file_exists(__DIR__ . '/.htaccess')) {
    die('.htaccess is missing');
}

if (!file_exists(__DIR__ . '/.env')) {
    die('.env is missing');
}

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

require_once ABSPATH . 'config/env.php';

if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', $_ENV['ENVIRONMENT']);
}

if (!defined('SITE_DIR')) {
    define('SITE_DIR', $_ENV['SITE_DIR']);
}

if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'Schedule-It');
}

// Set up Twig
if (strpos($_SERVER['REQUEST_URI'], 'scheduleit') == true) {
    require_once ABSPATH . 'scheduleit/config/database.php';
    require_once ABSPATH . 'scheduleit/config/twig.php';
} else {
    require_once ABSPATH . 'config/database.php';
    require_once ABSPATH . 'config/twig.php';
    // Include functions for rendering view for errors
    require_once ABSPATH . 'config/render_error.php';
}

// Central authentication service settings
$cas_context = '/idp/profile/cas/';
$cas_host = 'login.oregonstate.edu';
$cas_logout_url = 'https://' . $cas_host . '/idp/logout.jsp';
$cas_port = 443;
$cas_validate_url = 'https://' . $cas_host . $cas_context . 'serviceValidate';
