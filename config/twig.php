<?php

require_once ABSPATH . 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(ABSPATH . 'views');
$twig = new \Twig\Environment($loader);
$twig->addGlobal('site_name', SITE_NAME);
$twig->addGlobal('site_url', SITE_URL);
$twig->addGlobal('upload_allowed_filetypes', unserialize(UPLOAD_ALLOWED_FILETYPES));
$twig->addGlobal('uploads_url', UPLOADS_URL);

$basename_filter = new \Twig\TwigFilter('basename', function ($string) {
    return basename($string);
});

$twig->addFilter($basename_filter);

$join_filter = new \Twig\TwigFilter('join', function ($array) {
    return implode(', ', $array);
});

$twig->addFilter($join_filter);

$count_filter = new \Twig\TwigFilter('count', function ($array) {
    return count($array);
});

$twig->addFilter($count_filter);
