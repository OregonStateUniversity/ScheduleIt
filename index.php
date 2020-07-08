<?php

// set up twig

require_once dirname(__FILE__) . '/config/twig.php';

// render page using twig

echo $twig->render('views/home.twig');
