<?php

    // set up session

    require_once 'php/session.php';

    // set up connection to database via MySQLi

    require_once 'php/database.php';

    // set up twig

    require_once 'php/twig.php';

    // include functions for rendering view for errors

    require_once 'php/render_error.php';


    // get error code from URL

    $errorCode = ($_GET["code"]);

    // render page using twig

    render_error($twig, $errorCode, $errorMessages[$errorCode]);

?>