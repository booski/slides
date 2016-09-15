<?php
require_once('./admin/config.php'); //provides $screen_*

$uldir = './'.$uldir.'/';

$db = new mysqli($db_host, $db_user, $db_pass, $db_name);

$file = $_GET['name'];

$im = '';
if(!$file) {
    
    $im = create_image($screen_width, $screen_height, 'black', 'gray', $screen_width.' x '.$screen_height);
    
} else if(!file_exists($uldir.$file)) {

    $im = create_image($screen_width, $screen_height, 'darkred', 'white', ":(\nNot found");
    
} else {

    $file_scaled = $uldir.$screen_width.'_'.$screen_height.'_'.$file;

    if(!file_exists($file_scaled)) {
    
        $im = new Imagick($uldir.$file);
        $im->scaleImage($screen_width, $screen_height, true);
        $im->writeImage($file_scaled);
    
    } else {
        
        $im = new Imagick($file_scaled);
    }
}

header('Content-type:', $im->getImageMimeType());
echo $im;
return 0;

######## FUNCTIONS ########

function create_image($width, $height, $bgcolor, $textcolor, $text) {

    $draw = new ImagickDraw();
    $draw->setFontSize(min($width, $height)/5);
    $draw->setFillColor(new ImagickPixel($textcolor));
    $draw->setTextAntialias(true);
    $draw->setGravity(Imagick::GRAVITY_CENTER);
    
    $im = new Imagick();
    $im->newImage($width, $height, $bgcolor, 'png');
    $im->annotateImage($draw, 0, 0, 0, $text);

    return $im;
}

?>
