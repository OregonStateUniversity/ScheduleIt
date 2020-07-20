<?php

require_once ABSPATH . 'config/session.php';

echo $twig->render('settings/index.twig', [
    'settings_page' => true,
    'title' => 'Settings',
]);
