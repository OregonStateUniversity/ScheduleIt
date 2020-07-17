<?php

    // This file loads all of the JS files in the js directory.
    header('Content-type: text/javascript');
    $path_to_css = __DIR__ . '/js';
function get_files($dir = '.', $sort = 0)
{
    $files = scandir($dir, $sort);
    $files = array_diff($files, array('.', '..'));
    return $files;
}
    $files = get_files($path_to_css, 1);
    sort($files);
foreach ($files as $file) {
    include_once($path_to_css . '/' . $file);
}
