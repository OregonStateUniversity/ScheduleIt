<?php

session_start();

$msg = new \Plasticbrain\FlashMessages\FlashMessages();

$msg->setCssClassMap([
    $msg::INFO    => 'flash-alert alert-info',
    $msg::SUCCESS => 'flash-alert alert-success',
    $msg::WARNING => 'flash-alert alert-warning',
    $msg::ERROR   => 'flash-alert alert-danger',
]);

// Redirect user to login page in local environment
if (ENVIRONMENT == 'development') {
    if (!isset($_SESSION['user_onid'])) {
        header('Location: ' . SITE_DIR . '/login');
    }
} else {
    // If session is new, get user data
    // session should persist until browser gets closed
    if (!isset($_SESSION['user_onid'])) {
        // Set up CAS client and force user to log in
        require_once ABSPATH . 'config/cas.php';

        // We use the user's ONID, first name, last name and e-mail
        // However, many more attributes are available
        $_SESSION['user'] = $_SESSION['phpCAS']['user'];

        $user_attributes = $_SESSION['phpCAS']['attributes'];

        $_SESSION['first_name'] = $user_attributes['firstname'];
        $_SESSION['last_name'] = $user_attributes['lastname'];
        $_SESSION['email'] = $user_attributes['email'];

        // TODO: Remove other sessions when we remove old pages
        $_SESSION['user_email'] = $user_attributes['email'];
        $_SESSION['user_firstname'] = $user_attributes['firstname'];
        $_SESSION['user_lastname'] = $user_attributes['lastname'];
        $_SESSION['user_onid'] = $_SESSION['phpCAS']['user'];

        // Discard everything else
        unset($_SESSION['phpCAS']);
    }
}

$database->connectAsAdministrator();

$user = $database->getUserByONID($_SESSION['user_onid']);

if (!$user) {
    // Add user to database if user does not exist
    $database->addUser(
        $_SESSION['user_onid'],
        $_SESSION['user_email'],
        $_SESSION['user_firstname'],
        $_SESSION['user_lastname']
    );

    $user = $database->getUserByONID($_SESSION['user_onid']);
}

$_SESSION['user_id'] = $user['id'];

$current_url = array_filter(explode('/', $_SERVER['REQUEST_URI']));

if (in_array('scheduleit', $current_url)) {
    require_once ABSPATH . 'scheduleit/config/twig.php';
} else {
    require_once ABSPATH . 'config/twig.php';
}

$twig->addGlobal('user_email', $_SESSION['user_email']);
$twig->addGlobal('user_id', $_SESSION['user_id']);
$twig->addGlobal('user_firstname', $_SESSION['user_firstname']);
$twig->addGlobal('user_lastname', $_SESSION['user_lastname']);
$twig->addGlobal('user_onid', $_SESSION['user_onid']);
$twig->addGlobal('msg', $msg);
