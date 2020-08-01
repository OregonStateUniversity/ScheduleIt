<?php

$redirect_url = SITE_DIR . '/meetings';

// This page is only for local environment
if (ENVIRONMENT != 'development') {
    header('Location: ' . $redirect_url);
}

require_once ABSPATH . 'scheduleit/config/database.php';

session_start();

$error = null;

if (
    !empty($_POST['user'])
) {
    $user = $database->getUserByONID($_POST['user']);

    if ($user) {
        // TODO: Remove when we remove old pages
        $_SESSION['user'] = $user['onid'];

        $_SESSION['user_onid'] = $user['onid'];
        $_SESSION['user_firstname'] = $user['first_name'];
        $_SESSION['user_lastname'] = $user['last_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_id'] = $user['id'];

        echo $_POST['redirect_url'];

        header('Location: ' . $redirect_url);
    } else {
        $error = 'No user with this ONID exists.';
    }
}

echo $twig->render('home/login.twig', [
  'error' => $error,
  'onid' => !empty($_SESSION['user_onid']) ? $_SESSION['user_onid'] : '',
  'redirect_url' => $redirect_url,
]);
