<?php

require_once ABSPATH . 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(ABSPATH . 'templates');
$twig = new \Twig\Environment($loader);
