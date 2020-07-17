<?php

require_once ABSPATH . 'config/database.php';
require_once ABSPATH . 'config/session.php';
require_once ABSPATH . 'scheduleit/config/twig.php';

echo $twig->render('profile/index.twig', [
    'profile_page' => true,
    'title' => 'Profile',
]);
