<?php
require_once('./admin/config.php'); //provides $screen_*

$dir = 'images/';
$filelist = scandir($dir);

if(sizeof($filelist) < 3) {
    $randfile = 'placeholder.png';
} else {
    $randint = mt_rand(2, count($filelist) - 1);
    $randfile = $dir.$filelist[$randint];
}

$mime = getimagesize($randfile)['mime'];

$im = new Imagick($randfile);
$im->scaleImage($screen_width, $screen_height, true);

header('Content-type:', $mime);
echo $im;

?>
