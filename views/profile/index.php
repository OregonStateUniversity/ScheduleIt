<?php

require_once ABSPATH . 'config/session.php';

echo $twig->render('profile/index.twig', [
    'profile_page' => true,
    'title' => 'Profile',
]);
