<?php

// start session
require_once dirname(__FILE__) . '/config/env.php';

// This page is only for local development.
if ($_ENV['ENVIRONMENT'] != 'development') {
    exit();
}

session_start();

if (
    !empty($_POST['user']) &&
    !empty($_POST['firstName']) &&
    !empty($_POST['lastName']) &&
    !empty($_POST['email'])
) {
    $_SESSION['user'] = $_POST['user'];
    $_SESSION['firstName'] = $_POST['firstName'];
    $_SESSION['lastName'] = $_POST['lastName'];
    $_SESSION['email'] = $_POST['email'];
}

// set up connection to database via MySQLi

require_once dirname(__FILE__) . '/config/database.php';

// set up twig

require_once dirname(__FILE__) . '/config/twig.php';

// render page using twig

echo $twig->render('views/login.twig', [
  'loggedIn' => !empty($_POST['user']) && !empty($_POST['firstName']) && !empty($_POST['lastName']) && !empty($_POST['email']),
  'user' => !empty($_SESSION['user']) ? $_SESSION['user'] : '',
  'firstName' => !empty($_SESSION['firstName']) ? $_SESSION['firstName'] : '',
  'lastName' => !empty($_SESSION['lastName']) ? $_SESSION['lastName'] : '',
  'email' => !empty($_SESSION['email']) ? $_SESSION['email'] : ''
]);
