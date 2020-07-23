<?php

$redirect_url = SITE_DIR . '/meetings';

// This page is only for local environment
if (ENVIRONMENT != 'development') {
    header('Location: ' . $redirect_url);
}

require_once ABSPATH . 'scheduleit/config/database.php';

session_start();


if (
    !empty($_POST['user'])
) {
    $user = $database->getUserByONID($_POST['user']);

    if ($user) {
        $_SESSION['user'] = $user['onid'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['email'] = $user['email'];

        echo $_POST['redirect_url'];

        header('refresh:3; url=' . $redirect_url);
    } else {
        $error = 'No user with this ONID exists.';
    }
}

echo $twig->render('home/login.twig', [
  'error' => $error,
  'email' => !empty($_SESSION['email']) ? $_SESSION['email'] : '',
  'first_name' => !empty($_SESSION['first_name']) ? $_SESSION['first_name'] : '',
  'last_name' => !empty($_SESSION['last_name']) ? $_SESSION['last_name'] : '',
  'user' => !empty($_SESSION['user']) ? $_SESSION['user'] : '',
  'redirect_url' => $redirect_url,
]);
