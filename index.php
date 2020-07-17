<?php

require_once dirname(__FILE__) . '/scheduleit.config.php';

require_once ABSPATH . 'config/twig.php';

// render page using twig

echo $twig->render('views/home.twig');
