<?php

require_once dirname(__FILE__) . '/scheduleit.config.php';

// set up session

require_once ABSPATH . 'config/session.php';

// set up connection to database via MySQLi

require_once ABSPATH . 'config/database.php';

// set up twig

require_once ABSPATH . 'config/twig.php';

// include functions for rendering view for errors

require_once ABSPATH . 'config/render_error.php';

// get error code from URL

$errorCode = $_GET["code"];

// render page using twig

render_error($twig, $errorCode, $errorMessages[$errorCode]);
