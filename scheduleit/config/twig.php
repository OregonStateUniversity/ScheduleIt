<?php

require_once ABSPATH . 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(ABSPATH . 'scheduleit/views');
$twig = new \Twig\Environment($loader);
$twig->addGlobal('site_name', SITE_NAME);
$twig->addGlobal('site_url', SITE_DIR);

$basename_filter = new \Twig\TwigFilter('basename', function ($string) {
    return basename($string);
});

$twig->addFilter($basename_filter);
