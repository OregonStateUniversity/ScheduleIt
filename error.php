<?php

// set up session

require_once 'config/session.php';

// set up connection to database via MySQLi

require_once 'config/database.php';

// set up twig

require_once 'config/twig.php';

// include functions for rendering view for errors

require_once 'config/render_error.php';

// get error code from URL

$errorCode = $_GET["code"];

// render page using twig

render_error($twig, $errorCode, $errorMessages[$errorCode]);

?>
