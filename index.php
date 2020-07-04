<?php

// set up session

require_once dirname(__FILE__) . '/config/session.php';

// set up connection to database via MySQLi

require_once dirname(__FILE__) . '/config/database.php';

// set up twig

require_once dirname(__FILE__) . '/config/twig.php';

// render page using twig

echo $twig->render('views/main.twig');
