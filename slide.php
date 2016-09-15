<?php
require_once('./admin/config.php'); //provides $screen_*, $thumb_*

$uldir = './'.$uldir.'/';

$file = $_GET['name'];

if(isset($_GET['thumb'])) {

    $width = $thumb_width;
    $height = $thumb_height;

} else {

    $width = $screen_width;
    $height = $screen_height;
}

$im = '';
if(!$file) {
    
    $im = create_image($width, $height, 'black', 'gray', $width.' x '.$height);
    
} else if(!file_exists($uldir.$file)) {

    $im = create_image($width, $height, 'darkred', 'white', ":(\nNot found");
    
} else {

    $file_scaled = $uldir.$width.'_'.$height.'_'.$file;

    if(!file_exists($file_scaled)) {
    
        $im = new Imagick($uldir.$file);
        $im->scaleImage($width, $height, true);
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
