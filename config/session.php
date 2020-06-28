<?php
// start session
require_once 'env.php';

session_start();

// Set the user manually for local development
if ($_ENV['ENVIRONMENT'] == 'development') {
  if (
    !isset($_SESSION['user']) &&
    !isset($_SESSION['firstName']) &&
    !isset($_SESSION['lastName']) &&
    !isset($_SESSION['email'])
  ) {
    header("Location: login.php");
    exit();
  }
} else {
  // if session is new, get user data
  // session should persist until browser gets closed

  if (!isset($_SESSION['user'])) {
    // set up CAS client and force user to log in

    require_once dirname(__FILE__).'/cas.php';

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
}

// add user to database if user does not exist

require_once dirname(__FILE__).'/database.php';

$database->connectAsAdministrator();

$database->addUser(
  $_SESSION['user'],
  $_SESSION['email'],
  $_SESSION['firstName'],
  $_SESSION['lastName']
);

?>
