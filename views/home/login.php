<?php

$redirect_url = SITE_DIR . '/meetings';

// This page is only for local environment
if (ENVIRONMENT != 'development') {
    header('Location: ' . $redirect_url);
}

require_once ABSPATH . 'config/database.php';

session_start();

$error = null;

if (
    !empty($_POST['onid'])
) {
    $user = $database->getUserByONID($_POST['onid']);

    if ($user) {
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
} elseif ($_SESSION['user_onid']) {
    header('Location: ' . $redirect_url);
}

echo $twig->render('home/login.twig', [
  'error' => $error,
  'onid' => !empty($_SESSION['user_onid']) ? $_SESSION['user_onid'] : '',
  'redirect_url' => $redirect_url,
]);
