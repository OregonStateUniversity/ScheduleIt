<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

$loader = new Twig_Loader_Filesystem(dirname(__DIR__) . '/templates');
$twig = new Twig_Environment($loader);
