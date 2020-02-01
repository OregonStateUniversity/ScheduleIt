<?php

// start session 

session_start();

// if session is new, get user data
// session should persist until browser gets closed

if (!isset($_SESSION['user'])) {

    // set up CAS client and force user to log in

    require_once 'php/cas.php';

    // we use the user's ONID, first name, last name and e-mail
    // however, many more attributes are available

    $_SESSION['user'] = $_SESSION['phpCAS']['user'];

    $allAttributes = $_SESSION['phpCAS']['attributes'];

    $_SESSION['firstName'] = $allAttributes['firstname'];
    $_SESSION['lastName'] = $allAttributes['lastname'];
    $_SESSION['email'] = $allAttributes['email'];

    // discard everything else 

    unset($_SESSION['phpCAS']);

}

// add user to database if user does not exist

require_once 'php/database.php';

$database -> connectAsAdministrator();

$database -> addUser(
    $_SESSION['user'], $_SESSION['email'], 
    $_SESSION['firstName'], $_SESSION['lastName']
);

?>
