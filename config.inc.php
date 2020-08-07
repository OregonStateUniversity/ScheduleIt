<?php

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

if (!file_exists(ABSPATH . '.env')) {
    die('ERROR: .env is missing');
}

if (!file_exists(ABSPATH . '.htaccess')) {
    echo('ERROR: .htaccess is missing');
}

require_once ABSPATH . 'config/env.php';

if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', $_ENV['ENVIRONMENT']);
}

require_once ABSPATH . 'constants.inc.php';
require_once ABSPATH . 'config/database.php';

// Set up Twig
require_once ABSPATH . 'config/twig.php';

// Central authentication service settings
$cas_context = '/idp/profile/cas/';
$cas_host = 'login.oregonstate.edu';
$cas_logout_url = 'https://' . $cas_host . '/idp/logout.jsp';
$cas_port = 443;
$cas_validate_url = 'https://' . $cas_host . $cas_context . 'serviceValidate';
