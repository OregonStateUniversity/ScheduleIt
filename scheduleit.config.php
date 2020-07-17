<?php

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

require_once ABSPATH . 'config/env.php';

if (!defined('SITE_DIR')) {
    define('SITE_DIR', $_ENV['SITE_DIR']);
}

if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'Schedule-It');
}
