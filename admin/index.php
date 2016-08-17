<?php
require_once('./auth.php'); // also provides all vars from config.php

header('Content-Type: text/html; charset=UTF-8');

$db = new mysqli($db_host, $db_user, $db_pass, $db_name);
if($db->connect_errno) {
    echo 'Failed to connect to db. The error was: '.$db->connect_error;
    exit(0);
}

$htmlfile = './admin.html';

$html = explode('¤¤', file_get_contents($htmlfile));
$html_body = $html[0];
$html_slide = $html[1];

$dir = '../images/';
$filelist = scandir($dir);

$slides = '';

foreach($filelist as $slide) {
    
    if(strpos($slide, '.') == 0) {
        continue;
    }
    
    $slides .= str_replace('¤slide', $slide, $html_slide);
}


print str_replace('¤slides', $slides, $html_body);

?>
