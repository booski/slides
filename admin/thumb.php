<?php
require_once('./auth.php');

$dir = '../images/';
$slidearg = $_GET['slide'];

if($slidearg && file_exists($dir.$slidearg)) {
    $slide = $dir.$slidearg;
} else {
    $slide = 'placeholder.png';
}

$mime = getimagesize($slide)['mime'];

$im = new Imagick($slide);
$im->scaleImage(0, 120);

header('Content-type:', $mime);
echo $im;

?>
