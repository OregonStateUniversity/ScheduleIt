<?php

require_once ABSPATH . 'config/database.php';
require_once ABSPATH . 'config/session.php';
require_once ABSPATH . 'scheduleit/config/twig.php';

echo $twig->render('settings/index.twig', [
    'settings_page' => true,
    'title' => 'Settings',
]);
