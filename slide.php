<?php
require_once('./admin/config.php'); //provides $screen_*

$im = '';

$db = new mysqli($db_host, $db_user, $db_pass, $db_name);

if(!$_GET['name']) {
    
    $im = new Imagick('./images/placeholder.png');
    
} else {

    $file = $uldir.$_GET['name'];
    if(file_exists($file)) {
        
        $im = new Imagick($file);

    }
}

if(!$im) {
    $im = new Imagick('./images/error.png');
}

$im->scaleImage($screen_width, $screen_height, true);

header('Content-type:', $im->getImageMimeType());
echo $im;

?>
