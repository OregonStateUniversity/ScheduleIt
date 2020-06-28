<?php

// set up session

require_once 'config/session.php';

// set up connection to database via MySQLi

require_once 'config/database.php';

// set up twig

require_once 'config/twig.php';

// render page using twig

echo $twig->render('views/create.twig');

?>
