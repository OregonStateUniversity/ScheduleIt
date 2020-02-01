<?php

    $errorMessages = array(
        '400' => 'Oops! Something went wrong.',
        '403' => 'You do not have permission to access the requested resource.',
        '404' => 'The requested content was not found.'
    );

    function render_error($twig, $errorCode, $errorMessage) {
        echo $twig -> render(
            'views/error.twig',
            [ 'error_code' => $errorCode, 'error_message' => $errorMessage ]
        );
    }

?>