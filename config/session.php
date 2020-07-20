<?php

require_once ABSPATH . 'config/database.php';
require_once ABSPATH . 'config/env.php';

session_start();

// Redirect user to login page in local environment
if ($_ENV['ENVIRONMENT'] == 'development') {
    if (!isset($_SESSION['user'])) {
        header("Location: " . SITE_DIR . '/login');
    }
} else {
    // If session is new, get user data
    // session should persist until browser gets closed
    if (!isset($_SESSION['user'])) {
        // Set up CAS client and force user to log in
        require_once ABSPATH . 'config/cas.php';

        // We use the user's ONID, first name, last name and e-mail
        // However, many more attributes are available
        $_SESSION['user'] = $_SESSION['phpCAS']['user'];

        $user_attributes = $_SESSION['phpCAS']['attributes'];

        $_SESSION['first_name'] = $user_attributes['firstname'];
        $_SESSION['last_name'] = $user_attributes['lastname'];
        $_SESSION['email'] = $user_attributes['email'];

        // Discard everything else
        unset($_SESSION['phpCAS']);
    }
}

$database->connectAsAdministrator();

// Add user to database if user does not exist
$database->addUser(
    $_SESSION['user'],
    $_SESSION['email'],
    $_SESSION['first_name'],
    $_SESSION['last_name']
);

$current_url = array_filter(explode("/", $_SERVER['REQUEST_URI']));

if (in_array('scheduleit', $current_url)) {
    require_once ABSPATH . 'scheduleit/config/twig.php';
} else {
    require_once ABSPATH . 'config/twig.php';
}
$twig->addGlobal('user_email', $_SESSION['email']);
$twig->addGlobal('user_firstname', $_SESSION['first_name']);
$twig->addGlobal('user_lastname', $_SESSION['last_name']);
