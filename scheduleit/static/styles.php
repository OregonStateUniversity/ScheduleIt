<?php

    // This file loads all of the CSS files in the css directory.
    header('Content-type: text/css');
    $path_to_css = __DIR__ . '/css';
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
