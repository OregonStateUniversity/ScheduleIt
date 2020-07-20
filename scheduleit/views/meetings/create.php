<?php

require_once ABSPATH . 'config/session.php';

echo $twig->render('meetings/create.twig', [
    'title' => 'Create Meeting',
]);
