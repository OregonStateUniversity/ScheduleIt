<?php

require_once dirname(__FILE__) . '/scheduleit.config.php';
require_once ABSPATH . 'config/session.php';

// get error code from URL
$errorCode = $_GET["code"];

render_error($twig, $errorCode, $errorMessages[$errorCode]);
