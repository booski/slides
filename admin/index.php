<?php
require_once('./auth.php');

header('Content-Type: text/html; charset=UTF-8');

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
