<?php

session_start();

$_SESSION = array();

session_destroy();

if (ENVIRONMENT == 'development') {
    header('Location: ' . SITE_DIR . '/');
} else {
    header('Location: ' . $cas_logout_url);
}
