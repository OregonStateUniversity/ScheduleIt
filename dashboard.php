<?php

require_once dirname(__FILE__) . '/scheduleit.config.php';

// set up session

require_once ABSPATH . 'config/session.php';

// set up connection to database via MySQLi

require_once ABSPATH . 'config/database.php';

// set up twig

require_once ABSPATH . 'config/twig.php';

// render page using twig

echo $twig->render('views/main.twig');
