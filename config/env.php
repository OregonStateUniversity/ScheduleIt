<?php

require_once ABSPATH . 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(ABSPATH);
$dotenv->load();
