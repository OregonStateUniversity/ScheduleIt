<?php

require_once ABSPATH . 'config/session.php';
require_once ABSPATH . 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(ABSPATH . 'scheduleit/views');
$twig = new \Twig\Environment($loader);
$twig->addGlobal('site_name', SITE_NAME);
$twig->addGlobal('site_url', SITE_DIR);
$twig->addGlobal('user_email', $_SESSION['email']);
$twig->addGlobal('user_firstname', $_SESSION['firstName']);
$twig->addGlobal('user_lastname', $_SESSION['lastName']);
