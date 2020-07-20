<?php

echo $twig->render('errors/error_logged_out.twig', [
    'message' => 'Sorry, we couldn\'t find that page.',
    'title' => 'Page Not Found'
]);
