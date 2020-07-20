<?php

require_once dirname(__FILE__) . '/scheduleit.config.php';
require_once ABSPATH . 'config/session.php';

echo $twig->render('views/main.twig');
