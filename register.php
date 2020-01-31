<?php

    require 'vendor/autoload.php';

    $loader = new Twig_Loader_Filesystem('register_templates');
    $twig = new Twig_Environment($loader);

    echo $twig -> render('views/register_index.twig'); 

?>