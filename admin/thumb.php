<?php
require_once('./auth.php'); //provides $thumb_* by way of config.php

$dir = '../images/';
$slidearg = $_GET['slide'];

if($slidearg && file_exists($dir.$slidearg)) {
    $slide = $dir.$slidearg;
} else {
    $slide = 'placeholder.png';
}

$mime = getimagesize($slide)['mime'];

$im = new Imagick($slide);
$im->scaleImage($thumb_width, $thumb_height, true);

header('Content-type:', $mime);
echo $im;

?>
